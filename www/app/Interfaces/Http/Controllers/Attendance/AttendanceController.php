<?php

namespace App\Interfaces\Http\Controllers\Attendance;

use App\Application\UseCases\Attendance\RegisterAttendanceUseCase;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;

class AttendanceController extends Controller
{
    public function __construct(
        protected RegisterAttendanceUseCase $registerAttendanceUseCase
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson.unit_id' => 'required|exists:units,id',
            'lesson.school_class_id' => 'required|exists:school_classes,id',
            'lesson.subject_id' => 'required|exists:subjects,id',
            'lesson.teacher_id' => 'required|exists:teachers,id',
            'lesson.date' => 'required|date',
            'lesson.notes' => 'nullable|string',
            
            'records' => 'required|array',
            'records.*.student_id' => 'required|exists:students,id',
            'records.*.status' => 'required|in:presente,falta,justificado',
        ]);

        try {
            $lesson = $this->registerAttendanceUseCase->execute($validated['lesson'], $validated['records']);
            return response()->json(['message' => 'Chamada registrada com sucesso!', 'lesson' => $lesson], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
