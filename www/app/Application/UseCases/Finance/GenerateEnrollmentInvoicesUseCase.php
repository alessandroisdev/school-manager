<?php

namespace App\Application\UseCases\Finance;

use App\Domains\Enrollment\Models\Enrollment;
use App\Domains\Finance\Enums\InvoiceStatus;
use App\Domains\Finance\Models\FeeTemplate;
use App\Domains\Finance\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateEnrollmentInvoicesUseCase
{
    public function execute(Enrollment $enrollment, FeeTemplate $feeTemplate, Carbon $firstDueDate): array
    {
        $studentUnitId = $enrollment->student->unit_id;

        if ($studentUnitId !== $feeTemplate->unit_id) {
            throw new \InvalidArgumentException('O aluno e o plano financeiro pertencem a unidades diferentes.');
        }

        if ($feeTemplate->installments_count <= 0) {
            throw new \InvalidArgumentException('O número de parcelas deve ser maior que zero.');
        }

        $installmentAmount = round($feeTemplate->total_amount / $feeTemplate->installments_count, 2);
        
        $totalCalculated = $installmentAmount * $feeTemplate->installments_count;
        $difference = round($feeTemplate->total_amount - $totalCalculated, 2);

        $invoices = [];

        DB::transaction(function () use ($enrollment, $feeTemplate, $firstDueDate, $installmentAmount, $difference, $studentUnitId, &$invoices) {
            $currentDueDate = $firstDueDate->copy();

            for ($i = 1; $i <= $feeTemplate->installments_count; $i++) {
                $amount = $installmentAmount;
                
                if ($i === $feeTemplate->installments_count && $difference != 0) {
                    $amount += $difference;
                }

                $invoice = Invoice::create([
                    'unit_id' => $studentUnitId,
                    'student_id' => $enrollment->student_id,
                    'enrollment_id' => $enrollment->id,
                    'amount' => $amount,
                    'due_date' => $currentDueDate->copy(),
                    'status' => InvoiceStatus::PENDING,
                ]);

                $invoices[] = $invoice;

                $currentDueDate->addMonth();
            }
        });

        return $invoices;
    }
}
