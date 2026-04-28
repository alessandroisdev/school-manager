<?php

namespace App\Application\UseCases\Attendance;

use App\Domains\Attendance\Models\AttendanceRecord;
use App\Domains\Attendance\Models\Lesson;
use Illuminate\Support\Facades\DB;
use Exception;

class RegisterAttendanceUseCase
{
    /**
     * @param array $lessonData ['unit_id', 'school_class_id', 'subject_id', 'teacher_id', 'date', 'notes']
     * @param array $recordsData [['student_id' => 1, 'status' => 'presente'], ...]
     */
    public function execute(array $lessonData, array $recordsData): Lesson
    {
        return DB::transaction(function () use ($lessonData, $recordsData) {
            
            // 1. Validar se a aula já existe (evitar dupla chamada no mesmo dia, disciplina e turma)
            $existingLesson = Lesson::where('school_class_id', $lessonData['school_class_id'])
                ->where('subject_id', $lessonData['subject_id'])
                ->where('date', $lessonData['date'])
                ->exists();

            if ($existingLesson) {
                throw new Exception("A chamada para esta disciplina e turma já foi registrada nesta data.");
            }

            // 2. Criar a Lesson
            $lesson = Lesson::create($lessonData);

            // 3. Criar os registros de presença em lote (batch) para performance
            $recordsToInsert = [];
            $now = now();
            foreach ($recordsData as $record) {
                $recordsToInsert[] = [
                    'lesson_id' => $lesson->id,
                    'student_id' => $record['student_id'],
                    'status' => $record['status'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            AttendanceRecord::insert($recordsToInsert);

            return $lesson;
        });
    }
}
