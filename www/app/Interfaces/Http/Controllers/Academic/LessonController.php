<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\Lesson;
use App\Domains\Academic\Models\AttendanceRecord;
use App\Domains\Academic\Models\TeacherAssignment;
use App\Domains\Enrollment\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    // Exibe a tela de lançar frequências (recebe o ID do Diário/Assignment)
    public function index($assignmentId)
    {
        $assignment = TeacherAssignment::with(['schoolClass', 'subject', 'teacher.employee'])->findOrFail($assignmentId);
        
        // Carrega todos os alunos matriculados ativos nesta turma
        $enrollments = Enrollment::with('student')
            ->where('school_class_id', $assignment->school_class_id)
            ->whereIn('status', ['active', 'ativa'])
            ->get();
            
        // Carrega as aulas já dadas
        $lessons = Lesson::where('school_class_id', $assignment->school_class_id)
            ->where('subject_id', $assignment->subject_id)
            ->latest('date')
            ->get();

        return view('academic.diary.lessons', compact('assignment', 'enrollments', 'lessons'));
    }

    // Salva a aula e a chamada
    public function store(Request $request, $assignmentId)
    {
        $assignment = TeacherAssignment::findOrFail($assignmentId);

        $validated = $request->validate([
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'attendance' => 'required|array', // ['student_id' => 'status']
            'attendance.*' => 'in:presente,falta,justificado'
        ]);

        DB::transaction(function() use ($validated, $assignment) {
            $lesson = Lesson::create([
                'unit_id' => session('active_unit_id'),
                'school_class_id' => $assignment->school_class_id,
                'subject_id' => $assignment->subject_id,
                'teacher_id' => $assignment->teacher_id,
                'date' => $validated['date'],
                'notes' => $validated['notes'],
            ]);

            foreach ($validated['attendance'] as $studentId => $status) {
                AttendanceRecord::create([
                    'lesson_id' => $lesson->id,
                    'student_id' => $studentId,
                    'status' => $status
                ]);
            }
        });

        return redirect()->back()->with('success', 'Aula e Frequência registradas com sucesso!');
    }
}
