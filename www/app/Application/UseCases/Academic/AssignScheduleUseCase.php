<?php

namespace App\Application\UseCases\Academic;

use App\Domains\Academic\Models\Schedule;
use App\Domains\Academic\Models\TeacherAssignment;
use Exception;
use Illuminate\Support\Facades\DB;

class AssignScheduleUseCase
{
    public function execute(TeacherAssignment $assignment, int $timeSlotId, int $dayOfWeek): Schedule
    {
        return DB::transaction(function () use ($assignment, $timeSlotId, $dayOfWeek) {
            
            // 1. Verifica colisão de professor
            $teacherCollision = Schedule::where('teacher_id', $assignment->teacher_id)
                ->where('day_of_week', $dayOfWeek)
                ->where('time_slot_id', $timeSlotId)
                ->exists();
                
            if ($teacherCollision) {
                throw new Exception("Colisão de Horário: O professor já está alocado em outra turma neste horário.");
            }

            // 2. Verifica colisão de turma
            $classCollision = Schedule::where('school_class_id', $assignment->school_class_id)
                ->where('day_of_week', $dayOfWeek)
                ->where('time_slot_id', $timeSlotId)
                ->exists();
                
            if ($classCollision) {
                throw new Exception("Colisão de Horário: A turma já possui uma aula agendada neste horário.");
            }

            // A unidade do agendamento é herdada da turma
            // Precisamos garantir que a assignment e a turma já estejam carregados ou fetch unit_id do BD
            $unitId = $assignment->schoolClass->unit_id ?? 1;

            $schedule = Schedule::create([
                'unit_id' => $unitId,
                'teacher_assignment_id' => $assignment->id,
                'teacher_id' => $assignment->teacher_id,
                'school_class_id' => $assignment->school_class_id,
                'time_slot_id' => $timeSlotId,
                'day_of_week' => $dayOfWeek,
            ]);

            return $schedule;
        });
    }
}
