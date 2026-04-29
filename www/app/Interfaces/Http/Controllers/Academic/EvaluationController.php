<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\Evaluation;
use App\Domains\Academic\Models\EvaluationType;
use App\Domains\Academic\Models\GradeEntry;
use App\Domains\Academic\Models\TeacherAssignment;
use App\Domains\Enrollment\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    // Exibe a tela de lançar notas (recebe o ID do Diário/Assignment)
    public function index($assignmentId)
    {
        $assignment = TeacherAssignment::with(['schoolClass', 'subject', 'teacher.employee'])->findOrFail($assignmentId);
        
        $enrollments = Enrollment::with('student')
            ->where('school_class_id', $assignment->school_class_id)
            ->whereIn('status', ['active', 'ativa'])
            ->get();
            
        $evaluations = Evaluation::where('school_class_id', $assignment->school_class_id)
            ->where('subject_id', $assignment->subject_id)
            ->latest('date')
            ->get();

        // Pega ou cria um Tipo de Avaliação padrão "Prova" se não existir para o MVP
        $defaultType = EvaluationType::firstOrCreate(
            ['unit_id' => session('active_unit_id'), 'name' => 'Prova Oficial'],
            ['description' => 'Avaliação formal escrita']
        );

        return view('academic.diary.evaluations', compact('assignment', 'enrollments', 'evaluations', 'defaultType'));
    }

    // Salva a prova e as notas
    public function store(Request $request, $assignmentId)
    {
        $assignment = TeacherAssignment::findOrFail($assignmentId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'evaluation_type_id' => 'required|exists:evaluation_types,id',
            'max_score' => 'required|numeric|min:0.1',
            'weight' => 'required|numeric|min:0.1',
            'grades' => 'required|array', // ['student_id' => 'score']
            'grades.*' => 'nullable|numeric|min:0'
        ]);

        DB::transaction(function() use ($validated, $assignment) {
            $evaluation = Evaluation::create([
                'unit_id' => session('active_unit_id'),
                'school_class_id' => $assignment->school_class_id,
                'subject_id' => $assignment->subject_id,
                'teacher_id' => $assignment->teacher_id,
                'evaluation_type_id' => $validated['evaluation_type_id'],
                'name' => $validated['name'],
                'date' => $validated['date'],
                'max_score' => $validated['max_score'],
                'weight' => $validated['weight'],
            ]);

            foreach ($validated['grades'] as $studentId => $score) {
                // Só lança nota se o professor tiver digitado algo
                if ($score !== null && $score !== '') {
                    // Impede lançar nota maior que a máxima permitida pela avaliação
                    $finalScore = min($score, $validated['max_score']);
                    
                    GradeEntry::create([
                        'evaluation_id' => $evaluation->id,
                        'student_id' => $studentId,
                        'score' => $finalScore
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Avaliação e Notas registradas com sucesso!');
    }
}
