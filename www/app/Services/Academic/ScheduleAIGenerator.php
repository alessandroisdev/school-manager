<?php

namespace App\Services\Academic;

use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Schedule;
use App\Domains\Academic\Models\TeacherAssignment;
use App\Domains\Academic\Models\TimeSlot;
use App\Domains\HR\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleAIGenerator
{
    protected $unitId;

    public function __construct($unitId)
    {
        $this->unitId = $unitId;
    }

    /**
     * Limpa todo o rascunho de horários da unidade atual
     */
    public function clearDraft()
    {
        Schedule::where('unit_id', $this->unitId)
            ->where('status', 'draft')
            ->delete();
    }

    /**
     * Limpa o rascunho de um professor específico
     */
    public function clearTeacherDraft($teacherId)
    {
        Schedule::where('unit_id', $this->unitId)
            ->where('teacher_id', $teacherId)
            ->where('status', 'draft')
            ->delete();
    }

    /**
     * Gera o cronograma (horários) baseado nas Alocações (TeacherAssignments).
     * Usa uma heurística Gulosa (Greedy) para alocar os horários sem conflito.
     */
    public function generateSchedule($teacherId = null)
    {
        $assignments = TeacherAssignment::where('status', 'draft')
            ->whereHas('schoolClass', function ($q) {
                $q->where('unit_id', $this->unitId);
            });

        if ($teacherId) {
            $assignments->where('teacher_id', $teacherId);
        }

        $assignments = $assignments->get();
        $successCount = 0;
        $warnings = [];

        // Verifica se existem TimeSlots cadastrados
        $hasTimeSlots = \App\Domains\Academic\Models\TimeSlot::where('unit_id', $this->unitId)->exists();
        if (!$hasTimeSlots) {
            return [
                'success' => false,
                'warnings' => ['Erro Crítico: Nenhum Horário de Aula (Time Slot) foi cadastrado nesta unidade. Para a IA funcionar, você precisa cadastrar os horários de início e fim de cada aula por turno.']
            ];
        }

        $daysOfWeek = [1, 2, 3, 4, 5]; // Segunda a Sexta

        foreach ($assignments as $assignment) {
            // Dividimos por 40 para achar a quantidade de aulas semanais. Se for menor que 1, assume 1.
            $weeklyLessons = max(1, round($assignment->assigned_workload / 40));
            $classId = $assignment->school_class_id;
            $shiftId = $assignment->schoolClass->shift_id;
            $tId = $assignment->teacher_id;

            // Encontrar horários já alocados para abater do workload se estivermos rodando parcialmente
            $alreadyAssigned = Schedule::where('teacher_assignment_id', $assignment->id)->count();
            $weeklyLessons -= $alreadyAssigned;

            if ($weeklyLessons <= 0) {
                continue;
            }

            // Buscar TimeSlots do turno da turma
            $timeSlots = TimeSlot::where('unit_id', $this->unitId)->where('shift_id', $shiftId)->orderBy('start_time')->get();

            $allocatedForThisAssignment = 0;

            foreach ($daysOfWeek as $day) {
                foreach ($timeSlots as $slot) {
                    if ($allocatedForThisAssignment >= $weeklyLessons) {
                        break 2; // Já alocou tudo que precisava para essa assignment
                    }

                    // Verificar colisão de Turma (Turma já tem aula nesse dia/horário?)
                    $classBusy = Schedule::where('school_class_id', $classId)
                        ->where('day_of_week', $day)
                        ->where('time_slot_id', $slot->id)
                        ->exists();

                    // Verificar colisão de Professor (Professor já tem aula nesse dia/horário em qualquer turma?)
                    $teacherBusy = Schedule::where('teacher_id', $tId)
                        ->where('day_of_week', $day)
                        ->where('time_slot_id', $slot->id)
                        ->exists();

                    if (!$classBusy && !$teacherBusy) {
                        Schedule::create([
                            'unit_id' => $this->unitId,
                            'teacher_assignment_id' => $assignment->id,
                            'teacher_id' => $tId,
                            'school_class_id' => $classId,
                            'time_slot_id' => $slot->id,
                            'day_of_week' => $day,
                            'status' => 'draft',
                        ]);
                        $allocatedForThisAssignment++;
                        $successCount++;
                    }
                }
            }

            if ($allocatedForThisAssignment < $weeklyLessons) {
                $teacherName = $assignment->teacher->employee->name ?? 'Professor N/A';
                $className = $assignment->schoolClass->name ?? 'Turma N/A';
                $subjectName = $assignment->subject->name ?? 'Disciplina N/A';
                $warnings[] = "Não foi possível alocar todas as aulas para {$teacherName} na turma {$className} ({$subjectName}). Faltaram " . ($weeklyLessons - $allocatedForThisAssignment) . " aulas na semana.";
            }
        }

        return [
            'success' => true,
            'allocated_slots' => $successCount,
            'warnings' => $warnings
        ];
    }

    /**
     * Analisa turmas e professores para sugerir alocações (TeacherAssignments)
     */
    public function getSuggestions()
    {
        $suggestions = [];

        // 1. Professores com carga horária muito baixa em relação ao max_workload
        $teachers = Teacher::with(['employee', 'assignments'])->whereHas('employee', function($q) {
            $q->where('unit_id', $this->unitId)->where('is_active', true);
        })->get();

        foreach ($teachers as $teacher) {
            $assignedWorkload = $teacher->assignments->sum('assigned_workload');
            if ($assignedWorkload < $teacher->max_workload) {
                $suggestions['teachers'][] = [
                    'teacher_id' => $teacher->id,
                    'name' => $teacher->employee->name,
                    'specialty' => $teacher->specialty,
                    'available_hours' => $teacher->max_workload - $assignedWorkload
                ];
            }
        }

        // 2. Turmas com disciplinas faltando (Considerando uma grade ideal genérica ou comparando com as disciplinas ativas)
        // Como o sistema não tem uma 'Grade Curricular Base' forte vinculada por grade, sugerimos com base em disciplinas sem professor
        $classes = SchoolClass::where('unit_id', $this->unitId)->with('teacherAssignments')->get();
        $allSubjects = \App\Domains\Academic\Models\Subject::where('unit_id', $this->unitId)->get();

        foreach ($classes as $class) {
            $assignedSubjectIds = $class->teacherAssignments->pluck('subject_id')->toArray();
            $missingSubjects = [];
            foreach ($allSubjects as $subject) {
                if (!in_array($subject->id, $assignedSubjectIds)) {
                    $missingSubjects[] = $subject->name;
                }
            }
            if (count($missingSubjects) > 0) {
                $suggestions['classes'][] = [
                    'class_id' => $class->id,
                    'name' => $class->name,
                    'missing_subjects' => $missingSubjects
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Publica a grade (Muda de draft para published)
     */
    public function publishSchedule()
    {
        Schedule::where('unit_id', $this->unitId)
            ->where('status', 'draft')
            ->update(['status' => 'published']);

        TeacherAssignment::whereHas('schoolClass', function ($q) {
                $q->where('unit_id', $this->unitId);
            })
            ->where('status', 'draft')
            ->update(['status' => 'published']);
    }

    /**
     * Aloca professores automaticamente para disciplinas vazias nas turmas
     */
    public function autoAllocateTeachers()
    {
        $classes = SchoolClass::where('unit_id', $this->unitId)->with('teacherAssignments')->get();
        $allSubjects = \App\Domains\Academic\Models\Subject::where('unit_id', $this->unitId)->get();
        
        $teachers = Teacher::with(['employee', 'assignments', 'subjects'])->whereHas('employee', function($q) {
            $q->where('unit_id', $this->unitId)->where('is_active', true);
        })->get();

        $allocationsCount = 0;

        foreach ($classes as $class) {
            $assignedSubjectIds = $class->teacherAssignments->pluck('subject_id')->toArray();
            
            foreach ($allSubjects as $subject) {
                if (!in_array($subject->id, $assignedSubjectIds)) {
                    // Tentar achar um professor disponivel que lecione essa materia
                    foreach ($teachers as $teacher) {
                        // Verifica se o professor ensina essa disciplina
                        if (!$teacher->subjects->contains('id', $subject->id)) {
                            continue;
                        }

                        $currentWorkload = TeacherAssignment::where('teacher_id', $teacher->id)->sum('assigned_workload');
                        if ($currentWorkload + $subject->workload <= $teacher->max_workload) {
                            
                            TeacherAssignment::create([
                                'teacher_id' => $teacher->id,
                                'school_class_id' => $class->id,
                                'subject_id' => $subject->id,
                                'assigned_workload' => $subject->workload,
                                'status' => 'draft',
                            ]);
                            
                            $allocationsCount++;
                            break; // Vai para a proxima materia
                        }
                    }
                }
            }
        }
        
        return $allocationsCount;
    }
}
