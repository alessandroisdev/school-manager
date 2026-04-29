<?php

namespace App\Interfaces\Http\Controllers\Finance;

use App\Domains\Finance\Enums\InvoiceStatus;
use App\Domains\Finance\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');

        if ($request->ajax()) {
            $query = Invoice::with(['student', 'enrollment.schoolClass'])
                ->where('unit_id', $unitId)
                ->orderBy('due_date', 'asc');
                
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->has('bank_account_id') && $request->bank_account_id != '') {
                $query->where('bank_account_id', $request->bank_account_id);
            }
                
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('student_class', function($invoice) {
                    $className = $invoice->enrollment->schoolClass->name ?? 'Sem Turma';
                    return '<div class="fw-bold text-dark mb-0">' . $invoice->student->name . '</div>
                            <div class="small text-muted" style="font-size: 0.75rem;">' . $className . '</div>';
                })
                ->addColumn('due_date_formatted', function($invoice) {
                    return $invoice->due_date->format('d/m/Y');
                })
                ->addColumn('amount_formatted', function($invoice) {
                    return '<div class="fw-bold text-dark">R$ ' . number_format($invoice->amount, 2, ',', '.') . '</div>';
                })
                ->addColumn('status_badge', function($invoice) {
                    if($invoice->status->value === 'paid') {
                        return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Pago</span>';
                    } elseif($invoice->status->value === 'pending') {
                        if($invoice->due_date->isPast()) {
                            return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">Atrasado</span>';
                        }
                        return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">Pendente</span>';
                    }
                    return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill">Cancelado</span>';
                })
                ->addColumn('actions', function($invoice) {
                    if($invoice->status->value !== 'paid' && $invoice->status->value !== 'cancelled') {
                        $payUrl = route('finance.invoices.pay', $invoice);
                        $csrf = csrf_field();
                        $method = method_field('PATCH');
                        
                        return '<form action="' . $payUrl . '" method="POST" class="d-inline-block" onsubmit="return confirm(\'Confirmar o recebimento desta fatura?\');">
                                    ' . $csrf . $method . '
                                    <button type="submit" class="btn btn-sm btn-success fw-bold"><i class="bi bi-check-circle me-1"></i> Dar Baixa</button>
                                </form>';
                    }
                    return '<span class="text-muted small">Indisponível</span>';
                })
                ->rawColumns(['student_class', 'amount_formatted', 'status_badge', 'actions'])
                ->make(true);
        }

        // Estatísticas rápidas
        $totalPending = Invoice::where('unit_id', $unitId)
            ->whereIn('status', [InvoiceStatus::PENDING, InvoiceStatus::OVERDUE])
            ->sum('amount');
            
        $totalReceived = Invoice::where('unit_id', $unitId)
            ->where('status', InvoiceStatus::PAID)
            ->whereMonth('paid_at', Carbon::now()->month)
            ->sum('amount');

        $bankAccounts = \App\Domains\Finance\Models\BankAccount::where('unit_id', $unitId)->get();

        return view('finance.invoices.index', compact('totalPending', 'totalReceived', 'bankAccounts'));
    }

    public function pay(Invoice $invoice)
    {
        // O HasUnitScope já protege a query, mas garantimos
        if ($invoice->status === InvoiceStatus::PAID) {
            return back()->withErrors('Esta fatura já está paga.');
        }

        $invoice->update([
            'status' => InvoiceStatus::PAID,
            'paid_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Pagamento registrado com sucesso!');
    }
}
