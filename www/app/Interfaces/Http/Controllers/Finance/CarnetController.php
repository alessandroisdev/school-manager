<?php

namespace App\Interfaces\Http\Controllers\Finance;

use App\Domains\Enrollment\Models\Student;
use App\Domains\Finance\Models\BankAccount;
use App\Domains\Finance\Models\ClassPricing;
use App\Domains\Finance\Models\Invoice;
use App\Domains\Shared\Models\UnitSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CarnetController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $unitId = session('active_unit_id');
        $student = Student::with(['enrollments' => function($q) {
            $q->whereIn('status', ['active', 'ativa'])->latest();
        }])->findOrFail($request->student_id);

        $enrollment = $student->enrollments->first();

        if (!$enrollment) {
            return back()->with('warning', 'O aluno precisa ter uma matrícula ativa (estar enturmado) para gerar o carnê.');
        }

        $class = $enrollment->schoolClass;

        // Buscar Precificação
        $pricing = ClassPricing::where('unit_id', $unitId)
            ->where('grade_id', $class->grade_id)
            ->where('shift_id', $class->shift_id)
            ->first();

        if (!$pricing) {
            return back()->with('warning', 'Não há precificação (Class Pricing) configurada para a Série e Turno desta turma.');
        }

        // Buscar Conta Bancária Padrão
        $bankAccount = BankAccount::where('unit_id', $unitId)->where('is_active', true)->first();
        if (!$bankAccount) {
            return back()->with('warning', 'Nenhuma conta bancária configurada para emitir o boleto.');
        }

        // Verifica se já existe carnê
        $existing = Invoice::where('student_id', $student->id)->where('enrollment_id', $enrollment->id)->exists();
        if ($existing) {
            return back()->with('warning', 'Este aluno já possui faturas/carnês gerados para a matrícula atual.');
        }

        // Calcular parcelas
        $installments = $pricing->installments_count > 0 ? $pricing->installments_count : 12;
        $amountPerInstallment = round($pricing->annual_amount / $installments, 2);

        $unitSettings = UnitSetting::where('unit_id', $unitId)->first();
        $startMonth = Carbon::now()->month;
        $startYear = Carbon::now()->year;

        DB::transaction(function() use ($unitId, $student, $enrollment, $installments, $amountPerInstallment, $pricing, $bankAccount, $startMonth, $startYear) {
            for ($i = 1; $i <= $installments; $i++) {
                $dueDate = Carbon::create($startYear, $startMonth, 1)->addMonths($i - 1)->day($pricing->default_due_day);
                
                Invoice::create([
                    'unit_id' => $unitId,
                    'student_id' => $student->id,
                    'enrollment_id' => $enrollment->id,
                    'bank_account_id' => $bankAccount->id,
                    'amount' => $amountPerInstallment,
                    'installment_number' => $i,
                    'due_date' => $dueDate,
                    'status' => 'pending',
                    'description' => "Mensalidade Escolar - Parcela {$i}/{$installments}",
                ]);
            }
        });

        return back()->with('success', "Carnê de {$installments} parcelas (R$ " . number_format($amountPerInstallment, 2, ',', '.') . "/mês) gerado com sucesso!");
    }

    public function batchGenerate(Request $request)
    {
        $unitId = session('active_unit_id');
        
        // Buscar Conta Bancária Padrão (se existir)
        $bankAccount = \App\Domains\Finance\Models\BankAccount::where('unit_id', $unitId)
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();
            
        if (!$bankAccount) {
            // Se não tem padrão, pega qualquer uma ativa
            $bankAccount = \App\Domains\Finance\Models\BankAccount::where('unit_id', $unitId)->where('is_active', true)->first();
        }

        if (!$bankAccount) {
            return back()->with('warning', 'Nenhuma conta bancária configurada para emitir o boleto.');
        }

        // Criar Campanha de Mala Direta
        $campaignName = 'Faturamento Lote - ' . \Carbon\Carbon::now()->format('m/Y');
        $campaign = \App\Domains\Finance\Models\InvoiceCampaign::create([
            'unit_id' => $unitId,
            'name' => $campaignName,
            'status' => 'pending',
            'total_items' => 0,
            'processed_items' => 0,
        ]);

        // Despachar Job Principal
        \App\Jobs\Finance\ProcessInvoiceBatchJob::dispatch($campaign, $unitId, $bankAccount->id);

        return redirect()->route('finance.invoice-campaigns.index')->with('success', 'O processamento em lote foi iniciado em segundo plano! Acompanhe o progresso da Mala Direta abaixo.');
    }
}
