<?php

namespace App\Jobs\Finance;

use App\Domains\Enrollment\Models\Student;
use App\Domains\Finance\Models\ClassPricing;
use App\Domains\Finance\Models\Invoice;
use App\Domains\Finance\Models\InvoiceCampaign;
use App\Domains\Finance\Models\InvoiceCampaignItem;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessInvoiceBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $unitId;
    protected $defaultBankId;

    public function __construct(InvoiceCampaign $campaign, $unitId, $defaultBankId)
    {
        $this->campaign = $campaign;
        $this->unitId = $unitId;
        $this->defaultBankId = $defaultBankId;
    }

    public function handle(): void
    {
        $this->campaign->update(['status' => 'processing']);

        $startMonth = Carbon::now()->month;
        $startYear = Carbon::now()->year;

        // Pega todos os alunos enturmados
        $students = Student::where('unit_id', $this->unitId)
            ->with(['enrollments' => function($q) {
                $q->whereIn('status', ['active', 'ativa'])->with('schoolClass');
            }])->get();

        $totalItems = 0;

        foreach ($students as $student) {
            $enrollment = $student->enrollments->first();
            if (!$enrollment) continue;

            $class = $enrollment->schoolClass;
            
            // Buscar Precificação
            $pricing = ClassPricing::where('unit_id', $this->unitId)
                ->where('grade_id', $class->grade_id)
                ->where('shift_id', $class->shift_id)
                ->first();

            if (!$pricing) continue;

            // Verifica se já existe carnê
            $existing = Invoice::where('student_id', $student->id)
                ->where('enrollment_id', $enrollment->id)->exists();
            
            if ($existing) continue;

            // Calcular valor com desconto individual
            $annualAmount = $pricing->annual_amount;
            
            if ($enrollment->discount_percentage > 0) {
                $annualAmount = $annualAmount - ($annualAmount * ($enrollment->discount_percentage / 100));
            }
            if ($enrollment->discount_amount > 0) {
                $annualAmount = max(0, $annualAmount - $enrollment->discount_amount);
            }

            // Define o Banco
            $bankId = $enrollment->bank_account_id ?? $this->defaultBankId;

            // Calcular parcelas
            $installments = $pricing->installments_count > 0 ? $pricing->installments_count : 12;
            $amountPerInstallment = round($annualAmount / $installments, 2);

            $firstInvoice = null;

            DB::transaction(function() use ($student, $enrollment, $bankId, $installments, $amountPerInstallment, $startYear, $startMonth, $pricing, &$firstInvoice) {
                for ($i = 1; $i <= $installments; $i++) {
                    $dueDate = Carbon::create($startYear, $startMonth, 1)->addMonths($i - 1)->day($pricing->default_due_day);
                    
                    $invoice = Invoice::create([
                        'unit_id' => $this->unitId,
                        'student_id' => $student->id,
                        'enrollment_id' => $enrollment->id,
                        'bank_account_id' => $bankId,
                        'amount' => $amountPerInstallment,
                        'installment_number' => $i,
                        'due_date' => $dueDate,
                        'status' => 'pending',
                        'description' => "Mensalidade Escolar - Parcela {$i}/{$installments}",
                    ]);

                    if ($i === 1) {
                        $firstInvoice = $invoice;
                    }
                }
            });

            // Cria o item da campanha para envio de email / ZIP
            // Tenta pegar o email do aluno, se não tiver, pega do responsável principal
            $targetEmail = $student->email;
            if (empty($targetEmail)) {
                $guardian = $student->guardians->first();
                $targetEmail = $guardian ? $guardian->email : null;
            }

            $campaignItem = InvoiceCampaignItem::create([
                'invoice_campaign_id' => $this->campaign->id,
                'student_id' => $student->id,
                'invoice_id' => $firstInvoice ? $firstInvoice->id : null,
                'email' => $targetEmail,
                'status' => 'pending',
            ]);

            $totalItems++;

            // Despachar job para envio do email do carnê (passando os 12 boletos ou o link do painel)
            if (!empty($targetEmail)) {
                SendInvoiceEmailJob::dispatch($campaignItem);
            } else {
                $campaignItem->update([
                    'status' => 'failed',
                    'error_message' => 'Aluno e Responsável não possuem e-mail cadastrado.',
                ]);
            }
        }

        $this->campaign->update([
            'total_items' => $totalItems,
            'status' => $totalItems > 0 ? 'processing' : 'completed' // if 0, nothing to do
        ]);

        // Finalmente, podemos agendar a geração do ZIP.
        // O ideal é gerar o ZIP apenas quando todos os emails finalizarem, mas como isso é paralelo,
        // geramos o ZIP agora (pode demorar) e marcamos a campanha como completa ao final do ZIP.
        if ($totalItems > 0) {
            GenerateCarnetZipJob::dispatch($this->campaign);
        }
    }
}
