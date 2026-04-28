<?php

namespace App\Application\UseCases\Evaluation;

use App\Domains\Academic\Models\Term;
use App\Domains\Attendance\Enums\AttendanceStatus;
use App\Domains\Attendance\Models\Lesson;
use App\Domains\Evaluation\Enums\PerformanceStatus;
use App\Domains\Evaluation\Models\Evaluation;
use App\Domains\Evaluation\Models\StudentTermPerformance;
use App\Domains\Shared\Models\UnitSetting;
use Illuminate\Support\Facades\DB;

class CalculateTermPerformanceUseCase
{
    public function execute(int $unitId, int $studentId, int $schoolClassId, int $subjectId, int $termId): StudentTermPerformance
    {
        return DB::transaction(function () use ($unitId, $studentId, $schoolClassId, $subjectId, $termId) {
            $settings = UnitSetting::where('unit_id', $unitId)->first() 
                ?? new UnitSetting(['calculation_rule' => 'simple', 'passing_grade' => 6.0, 'passing_attendance' => 75.0]);

            // 1. Calcular Média de Notas
            $evaluations = Evaluation::where('unit_id', $unitId)
                ->where('school_class_id', $schoolClassId)
                ->where('subject_id', $subjectId)
                ->where('term_id', $termId)
                ->with(['gradeEntries' => function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                }])
                ->get();

            $totalScore = 0;
            $totalWeight = 0;

            foreach ($evaluations as $eval) {
                $gradeEntry = $eval->gradeEntries->first();
                $score = $gradeEntry ? $gradeEntry->score : 0;
                
                if ($settings->calculation_rule === 'weighted') {
                    $totalScore += ($score * $eval->weight);
                    $totalWeight += $eval->weight;
                } else {
                    $totalScore += $score;
                    $totalWeight += 1; // Media Simples
                }
            }

            $average = $totalWeight > 0 ? ($totalScore / $totalWeight) : 0;

            // 2. Calcular Frequência
            $term = Term::find($termId);
            
            $lessonsQuery = Lesson::where('unit_id', $unitId)
                ->where('school_class_id', $schoolClassId)
                ->where('subject_id', $subjectId);
                
            if ($term) {
                $lessonsQuery->whereBetween('date', [$term->start_date, $term->end_date]);
            }
            
            $lessons = $lessonsQuery->get();
            $totalLessons = $lessons->count();
            $attendedLessons = 0;

            if ($totalLessons > 0) {
                $lessonIds = $lessons->pluck('id');
                // Aluno presente ou justificado conta como frequência positiva
                $attendedLessons = DB::table('attendance_records')
                    ->whereIn('lesson_id', $lessonIds)
                    ->where('student_id', $studentId)
                    ->whereIn('status', [AttendanceStatus::Presente->value, AttendanceStatus::Justificado->value])
                    ->count();
            }

            $attendancePercentage = $totalLessons > 0 ? ($attendedLessons / $totalLessons) * 100 : 100;

            // 3. Definir Status
            $status = PerformanceStatus::Aprovado;
            
            if ($attendancePercentage < $settings->passing_attendance) {
                $status = PerformanceStatus::Reprovado; // Faltou muito
            } elseif ($average < $settings->passing_grade) {
                $status = PerformanceStatus::Recuperacao; // Media baixa
            }

            // 4. Salvar
            $performance = StudentTermPerformance::updateOrCreate(
                [
                    'unit_id' => $unitId,
                    'student_id' => $studentId,
                    'school_class_id' => $schoolClassId,
                    'subject_id' => $subjectId,
                    'term_id' => $termId,
                ],
                [
                    'calculated_average' => round($average, 2),
                    'attendance_percentage' => round($attendancePercentage, 2),
                    'status' => $status->value,
                ]
            );

            return $performance;
        });
    }
}
