<?php

namespace App\Application\UseCases\Evaluation;

use App\Domains\Evaluation\Models\Evaluation;
use App\Domains\Evaluation\Models\GradeEntry;
use Illuminate\Support\Facades\DB;
use Exception;

class RegisterGradesUseCase
{
    /**
     * @param int $evaluationId
     * @param array $gradesData [['student_id' => 1, 'score' => 8.5, 'feedback' => 'Bom trabalho'], ...]
     */
    public function execute(int $evaluationId, array $gradesData): bool
    {
        return DB::transaction(function () use ($evaluationId, $gradesData) {
            $evaluation = Evaluation::findOrFail($evaluationId);

            $upsertData = [];
            $now = now();

            foreach ($gradesData as $grade) {
                // Validação de negócio crítica: A nota não pode ser maior que o máximo da prova
                if ($grade['score'] > $evaluation->max_score) {
                    throw new Exception("A nota informada ({$grade['score']}) ultrapassa a nota máxima da avaliação ({$evaluation->max_score}).");
                }
                
                if ($grade['score'] < 0) {
                    throw new Exception("A nota informada não pode ser negativa.");
                }

                $upsertData[] = [
                    'evaluation_id' => $evaluation->id,
                    'student_id' => $grade['student_id'],
                    'score' => $grade['score'],
                    'feedback' => $grade['feedback'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Usamos upsert para inserir novas notas ou atualizar as notas de alunos que já haviam sido lançadas
            GradeEntry::upsert(
                $upsertData,
                ['evaluation_id', 'student_id'], // unique columns
                ['score', 'feedback', 'updated_at'] // columns to update
            );

            return true;
        });
    }
}
