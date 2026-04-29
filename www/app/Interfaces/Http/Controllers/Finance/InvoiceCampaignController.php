<?php

namespace App\Interfaces\Http\Controllers\Finance;

use App\Domains\Finance\Models\InvoiceCampaign;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InvoiceCampaignController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = InvoiceCampaign::where('unit_id', session('active_unit_id'))->latest();
            
            return DataTables::of($query)
                ->addColumn('status_badge', function($campaign) {
                    $badges = [
                        'pending' => '<span class="badge bg-secondary text-light">Aguardando Fila</span>',
                        'processing' => '<span class="badge bg-warning text-dark"><i class="spinner-border spinner-border-sm me-1"></i> Processando...</span>',
                        'completed' => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Concluída</span>',
                        'failed' => '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Falha</span>',
                    ];
                    return $badges[$campaign->status] ?? $campaign->status;
                })
                ->addColumn('progress', function($campaign) {
                    if ($campaign->total_items == 0) return '0%';
                    $pct = round(($campaign->processed_items / $campaign->total_items) * 100);
                    return '
                    <div class="d-flex align-items-center">
                        <span class="me-2 small fw-bold">'.$pct.'%</span>
                        <div class="progress" style="height: 6px; width: 100px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: '.$pct.'%;"></div>
                        </div>
                    </div>';
                })
                ->addColumn('actions', function($campaign) {
                    $showUrl = route('finance.invoice.campaigns.show', $campaign);
                    $actions = '<a href="'.$showUrl.'" class="btn btn-sm btn-outline-primary shadow-sm fw-bold"><i class="bi bi-eye me-1"></i> Detalhes</a>';
                    
                    if ($campaign->status === 'completed' && $campaign->zip_path) {
                        $downloadUrl = asset('storage/' . $campaign->zip_path);
                        $actions .= ' <a href="'.$downloadUrl.'" class="btn btn-sm btn-outline-success shadow-sm fw-bold" target="_blank" download><i class="bi bi-file-zip me-1"></i> Baixar ZIP</a>';
                    }
                    
                    return '<div class="text-end text-nowrap">' . $actions . '</div>';
                })
                ->rawColumns(['status_badge', 'progress', 'actions'])
                ->make(true);
        }

        return view('finance.invoice.campaigns.index');
    }

    public function show(Request $request, InvoiceCampaign $invoiceCampaign)
    {
        if ($invoiceCampaign->unit_id != session('active_unit_id')) {
            abort(403);
        }

        if ($request->ajax()) {
            $query = $invoiceCampaign->items()->with('student');
            
            return DataTables::of($query)
                ->addColumn('student_name', function($item) {
                    return $item->student->name;
                })
                ->addColumn('status_badge', function($item) {
                    $badges = [
                        'pending' => '<span class="badge bg-secondary text-light">Na Fila</span>',
                        'sent' => '<span class="badge bg-success text-light">Enviado</span>',
                        'failed' => '<span class="badge bg-danger text-light" title="'.$item->error_message.'">Erro (Passe o mouse)</span>',
                    ];
                    return $badges[$item->status] ?? $item->status;
                })
                ->rawColumns(['status_badge'])
                ->make(true);
        }

        // Estatísticas
        $stats = [
            'total' => $invoiceCampaign->total_items,
            'sent' => $invoiceCampaign->items()->where('status', 'sent')->count(),
            'failed' => $invoiceCampaign->items()->where('status', 'failed')->count(),
            'pending' => $invoiceCampaign->items()->where('status', 'pending')->count(),
        ];

        return view('finance.invoice.campaigns.show', compact('invoiceCampaign', 'stats'));
    }

    public function retry(InvoiceCampaign $invoiceCampaign)
    {
        if ($invoiceCampaign->unit_id != session('active_unit_id')) {
            abort(403);
        }

        $failedItems = $invoiceCampaign->items()->where('status', 'failed')->get();

        foreach ($failedItems as $item) {
            $item->update(['status' => 'pending', 'error_message' => null]);
            \App\Jobs\Finance\SendInvoiceEmailJob::dispatch($item);
        }

        return back()->with('success', $failedItems->count() . ' emails recolocados na fila de reprocessamento.');
    }
}
