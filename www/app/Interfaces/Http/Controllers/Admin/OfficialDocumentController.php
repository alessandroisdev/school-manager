<?php

namespace App\Interfaces\Http\Controllers\Admin;

use App\Domains\OfficialDocument\Models\OfficialDocument;
use App\Domains\OfficialDocument\Models\OfficialDocumentCategory;
use App\Domains\OfficialDocument\Models\OfficialDocumentSigner;
use App\Domains\OfficialDocument\Services\NumberingService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class OfficialDocumentController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');

        if ($request->ajax()) {
            $query = OfficialDocument::with(['category', 'creator'])
                ->where('unit_id', $unitId)
                ->orderBy('year', 'desc')
                ->orderBy('number', 'desc');
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('doc_number', function($doc) {
                    return '<div class="fw-bold text-primary">' . $doc->full_number . '</div>
                            <div class="small text-muted">' . Carbon::parse($doc->date)->format('d/m/Y') . '</div>';
                })
                ->addColumn('subject_info', function($doc) {
                    return '<div class="fw-bold text-dark">' . $doc->subject . '</div>
                            <div class="small text-muted">Destino: ' . ($doc->recipient ?? 'Não especificado') . '</div>';
                })
                ->addColumn('status_badge', function($doc) {
                    if ($doc->status === 'published') {
                        return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill"><i class="bi bi-check-circle me-1"></i> Publicado</span>';
                    } elseif ($doc->status === 'cancelled') {
                        return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill"><i class="bi bi-x-circle me-1"></i> Cancelado</span>';
                    }
                    return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill"><i class="bi bi-pencil me-1"></i> Rascunho</span>';
                })
                ->addColumn('actions', function($doc) {
                    $editUrl = route('admin.official-documents.edit', $doc);
                    $pdfUrl = route('admin.official-documents.pdf', $doc);
                    $cancelUrl = route('admin.official-documents.cancel', $doc);
                    $csrf = csrf_field();

                    $html = '<div class="text-end text-nowrap">';
                    
                    if ($doc->status !== 'cancelled') {
                        $html .= '<a href="' . $pdfUrl . '" target="_blank" class="btn btn-sm btn-light text-success fw-bold me-2" title="Gerar PDF (ABNT)"><i class="bi bi-printer"></i></a>';
                    }
                    
                    if ($doc->status === 'draft') {
                        $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-light text-primary fw-bold me-2"><i class="bi bi-pencil-square"></i></a>';
                        $html .= '<form action="' . route('admin.official-documents.publish', $doc) . '" method="POST" class="d-inline-block me-2">
                                    ' . $csrf . '
                                    <button type="submit" class="btn btn-sm btn-primary fw-bold" onclick="return confirm(\'Ao publicar, o ofício não poderá mais ser alterado, apenas cancelado. Continuar?\')"><i class="bi bi-check2-all"></i> Publicar</button>
                                  </form>';
                    }
                    
                    if ($doc->status === 'published') {
                        $html .= '<form action="' . $cancelUrl . '" method="POST" class="d-inline-block">
                                    ' . $csrf . '
                                    <button type="submit" class="btn btn-sm btn-light text-danger fw-bold" onclick="return confirm(\'Deseja realmente cancelar este Ofício?\')"><i class="bi bi-x-circle"></i> Cancelar</button>
                                  </form>';
                    }

                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['doc_number', 'subject_info', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('admin.official-documents.index');
    }

    public function create()
    {
        $unitId = session('active_unit_id');
        $categories = OfficialDocumentCategory::where('unit_id', $unitId)->get();
        $signers = OfficialDocumentSigner::where('unit_id', $unitId)->where('is_active', true)->get();
        
        if ($categories->isEmpty()) {
            return redirect()->route('admin.official-categories.index')->with('error', 'Crie pelo menos uma Categoria de Ofício antes de emitir documentos.');
        }

        return view('admin.official-documents.create', compact('categories', 'signers'));
    }

    public function store(Request $request, NumberingService $numberingService)
    {
        $unitId = session('active_unit_id');
        
        $validated = $request->validate([
            'category_id' => 'required|exists:official_document_categories,id',
            'date' => 'required|date',
            'recipient' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'signer_id' => 'nullable|exists:official_document_signers,id'
        ]);

        $year = Carbon::parse($validated['date'])->year;
        
        // Serviço de Lock para numeração sequencial
        $number = $numberingService->getNextNumber($unitId, $validated['category_id'], $year);
        $fullNumber = $numberingService->formatFullNumber($number, $year, $validated['category_id']);

        $docData = [
            'unit_id' => $unitId,
            'category_id' => $validated['category_id'],
            'year' => $year,
            'number' => $number,
            'full_number' => $fullNumber,
            'date' => $validated['date'],
            'recipient' => $validated['recipient'],
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'status' => 'draft',
            'created_by_id' => auth()->id()
        ];

        if ($request->filled('signer_id')) {
            $signer = OfficialDocumentSigner::where('unit_id', $unitId)->find($request->signer_id);
            if ($signer) {
                $docData['signer_name'] = $signer->name;
                $docData['signer_title'] = $signer->title;
                $docData['signature_image_path'] = $signer->signature_image_path;
            }
        }

        $document = OfficialDocument::create($docData);

        return redirect()->route('admin.official-documents.index')->with('success', 'Ofício gerado como RASCUNHO com sucesso! Número: ' . $fullNumber);
    }

    public function edit(OfficialDocument $officialDocument)
    {
        if ($officialDocument->unit_id != session('active_unit_id')) abort(403);
        if ($officialDocument->status !== 'draft') {
            return redirect()->route('admin.official-documents.index')->with('error', 'Apenas documentos em rascunho podem ser editados.');
        }

        $categories = OfficialDocumentCategory::where('unit_id', session('active_unit_id'))->get();
        $signers = OfficialDocumentSigner::where('unit_id', session('active_unit_id'))->where('is_active', true)->get();

        return view('admin.official-documents.edit', compact('officialDocument', 'categories', 'signers'));
    }

    public function update(Request $request, OfficialDocument $officialDocument)
    {
        if ($officialDocument->unit_id != session('active_unit_id')) abort(403);
        if ($officialDocument->status !== 'draft') abort(403);

        $validated = $request->validate([
            'date' => 'required|date',
            'recipient' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'signer_id' => 'nullable|exists:official_document_signers,id'
        ]);

        $docData = [
            'date' => $validated['date'],
            'recipient' => $validated['recipient'],
            'subject' => $validated['subject'],
            'content' => $validated['content'],
        ];

        if ($request->filled('signer_id')) {
            $signer = OfficialDocumentSigner::where('unit_id', session('active_unit_id'))->find($request->signer_id);
            if ($signer) {
                $docData['signer_name'] = $signer->name;
                $docData['signer_title'] = $signer->title;
                $docData['signature_image_path'] = $signer->signature_image_path;
            }
        } else {
            $docData['signer_name'] = null;
            $docData['signer_title'] = null;
            $docData['signature_image_path'] = null;
        }

        $officialDocument->update($docData);

        return redirect()->route('admin.official-documents.index')->with('success', 'Rascunho atualizado com sucesso!');
    }

    public function publish(OfficialDocument $officialDocument)
    {
        if ($officialDocument->unit_id != session('active_unit_id')) abort(403);
        
        $officialDocument->update(['status' => 'published']);
        return redirect()->route('admin.official-documents.index')->with('success', 'Documento publicado. Agora ele tem validade legal e não pode mais ser alterado.');
    }

    public function cancel(OfficialDocument $officialDocument)
    {
        if ($officialDocument->unit_id != session('active_unit_id')) abort(403);
        
        $officialDocument->update(['status' => 'cancelled']);
        return redirect()->route('admin.official-documents.index')->with('success', 'Documento cancelado!');
    }

    public function generatePdf(OfficialDocument $officialDocument)
    {
        if ($officialDocument->unit_id != session('active_unit_id')) abort(403);

        // Buscar configurações da unidade (logo, nome, cidade)
        $unit = \App\Domains\Shared\Models\Unit::with('setting')->find(session('active_unit_id'));
        
        // Pega URL da logo da escola se houver, ou null
        $logoUrl = null;
        if ($unit->setting && $unit->setting->logo_path) {
            // No domPDF local path works best, so we use public_path
            $logoUrl = public_path('storage/' . $unit->setting->logo_path);
        }

        $signatureUrl = null;
        if ($officialDocument->signature_image_path) {
            $signatureUrl = public_path('storage/' . $officialDocument->signature_image_path);
        }

        $city = $unit->setting ? $unit->setting->address_city : 'Cidade';
        $state = $unit->setting ? $unit->setting->address_state : 'UF';
        
        // Formata data Ex: Brasília, 29 de abril de 2026.
        setlocale(LC_TIME, 'pt_BR.utf-8', 'pt_BR', 'pt-br', 'portuguese');
        $dateFormatted = Carbon::parse($officialDocument->date)->isoFormat('D [de] MMMM [de] YYYY');

        $html = view('admin.official-documents.pdf', compact('officialDocument', 'unit', 'logoUrl', 'signatureUrl', 'city', 'state', 'dateFormatted'))->render();

        // Configuração ABNT (Manual PR)
        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        $pdf->setOption(['dpi' => 150, 'defaultFont' => 'Helvetica']); // Carlito/Calibri fallback is Helvetica/Arial in dompdf

        return $pdf->stream(str_replace('/', '-', $officialDocument->full_number) . '.pdf');
    }
}
