<?php

namespace App\Interfaces\Http\Controllers\Secretariat;

use App\Domains\Protocol\Models\DocumentProtocol;
use App\Domains\Protocol\Models\ProtocolAttachment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ProtocolController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');

        if ($request->ajax()) {
            $query = DocumentProtocol::with('receiver')
                ->where('unit_id', $unitId)
                ->orderByRaw("FIELD(status, 'pending', 'in_progress', 'resolved', 'archived')")
                ->orderBy('priority', 'desc')
                ->orderBy('due_date', 'asc');
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('protocol_info', function($protocol) {
                    $priorityBadge = '';
                    if ($protocol->priority === 'high') {
                        $priorityBadge = '<span class="badge bg-danger ms-2"><i class="bi bi-exclamation-triangle"></i> Alta Prioridade</span>';
                    } elseif ($protocol->priority === 'medium') {
                        $priorityBadge = '<span class="badge bg-warning text-dark ms-2">Média</span>';
                    }
                    
                    return '<div class="fw-bold text-primary">' . $protocol->protocol_number . $priorityBadge . '</div>
                            <div class="small text-muted"><strong>De:</strong> ' . $protocol->sender . '</div>
                            <div class="small text-dark fw-bold">' . $protocol->subject . '</div>';
                })
                ->addColumn('dates', function($protocol) {
                    $received = Carbon::parse($protocol->received_date)->format('d/m/Y');
                    $due = $protocol->due_date ? Carbon::parse($protocol->due_date)->format('d/m/Y') : 'Sem prazo';
                    
                    $dueClass = 'text-muted';
                    if ($protocol->due_date && $protocol->status !== 'resolved' && $protocol->status !== 'archived') {
                        if (Carbon::parse($protocol->due_date)->isPast()) {
                            $dueClass = 'text-danger fw-bold';
                        } elseif (Carbon::parse($protocol->due_date)->diffInDays(now()) <= 3) {
                            $dueClass = 'text-warning fw-bold';
                        }
                    }

                    return '<div class="small"><span class="text-muted">Entrada:</span> ' . $received . '</div>
                            <div class="small"><span class="text-muted">Prazo:</span> <span class="' . $dueClass . '">' . $due . '</span></div>';
                })
                ->addColumn('status_badge', function($protocol) {
                    $badges = [
                        'pending' => '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill">Pendente</span>',
                        'in_progress' => '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill">Em Análise</span>',
                        'resolved' => '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">Resolvido</span>',
                        'archived' => '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill">Arquivado</span>',
                    ];
                    return $badges[$protocol->status] ?? $protocol->status;
                })
                ->addColumn('actions', function($protocol) {
                    $showUrl = route('secretariat.protocols.show', $protocol);
                    return '<div class="text-end">
                                <a href="' . $showUrl . '" class="btn btn-sm btn-primary fw-bold"><i class="bi bi-eye"></i> Abrir</a>
                            </div>';
                })
                ->rawColumns(['protocol_info', 'dates', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('secretariat.protocols.index');
    }

    public function create()
    {
        return view('secretariat.protocols.create');
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');
        
        $validated = $request->validate([
            'sender' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'received_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:received_date',
            'priority' => 'required|in:low,medium,high',
            'description' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,docx' // 10MB limit
        ]);

        // Generate Protocol Number PRT-2026-00000X
        $year = Carbon::parse($validated['received_date'])->year;
        $count = DocumentProtocol::where('unit_id', $unitId)->whereYear('received_date', $year)->count();
        $number = 'PRT-' . $year . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

        $protocol = DocumentProtocol::create([
            'unit_id' => $unitId,
            'protocol_number' => $number,
            'sender' => $validated['sender'],
            'subject' => $validated['subject'],
            'received_date' => $validated['received_date'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'status' => 'pending',
            'received_by_id' => auth()->id()
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('uploads/units/' . $unitId . '/protocols/' . $protocol->id, 'public');
                ProtocolAttachment::create([
                    'protocol_id' => $protocol->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize()
                ]);
            }
        }

        return redirect()->route('secretariat.protocols.show', $protocol)->with('success', 'Protocolo recebido e registrado: ' . $number);
    }

    public function show(DocumentProtocol $protocol)
    {
        if ($protocol->unit_id != session('active_unit_id')) abort(403);
        $protocol->load(['attachments', 'receiver']);
        
        return view('secretariat.protocols.show', compact('protocol'));
    }

    public function updateStatus(Request $request, DocumentProtocol $protocol)
    {
        if ($protocol->unit_id != session('active_unit_id')) abort(403);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,archived',
            'note' => 'nullable|string'
        ]);

        $protocol->status = $validated['status'];
        if ($request->filled('note')) {
            $protocol->description = $protocol->description . "\n\n[" . Carbon::now()->format('d/m/Y H:i') . "]: " . $validated['note'];
        }
        $protocol->save();

        return back()->with('success', 'Status do protocolo atualizado!');
    }
}
