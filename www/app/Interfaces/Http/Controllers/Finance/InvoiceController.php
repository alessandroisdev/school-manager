<?php

namespace App\Interfaces\Http\Controllers\Finance;

use App\Domains\Finance\Enums\InvoiceStatus;
use App\Domains\Finance\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index()
    {
        $unitId = session('active_unit_id');

        // Buscar as faturas com o relacionamento de aluno e turma (através da matrícula)
        $invoices = Invoice::with(['student', 'enrollment.schoolClass'])
            ->where('unit_id', $unitId)
            ->orderBy('due_date', 'asc')
            ->paginate(15);

        // Estatísticas rápidas
        $totalPending = Invoice::where('unit_id', $unitId)
            ->whereIn('status', [InvoiceStatus::PENDING, InvoiceStatus::OVERDUE])
            ->sum('amount');
            
        $totalReceived = Invoice::where('unit_id', $unitId)
            ->where('status', InvoiceStatus::PAID)
            ->whereMonth('paid_at', Carbon::now()->month)
            ->sum('amount');

        return view('finance.invoices.index', compact('invoices', 'totalPending', 'totalReceived'));
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
