<?php

namespace App\Interfaces\Http\Controllers\Evaluation;

use App\Application\UseCases\Evaluation\CalculateTermPerformanceUseCase;
use App\Domains\Evaluation\Models\StudentTermPerformance;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReportCardController extends Controller
{
    public function __construct(
        protected CalculateTermPerformanceUseCase $calculateTermPerformanceUseCase
    ) {}

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'student_id' => 'required|exists:students,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $performance = $this->calculateTermPerformanceUseCase->execute(
            $validated['unit_id'],
            $validated['student_id'],
            $validated['school_class_id'],
            $validated['subject_id'],
            $validated['term_id']
        );

        return response()->json(['message' => 'Desempenho calculado!', 'performance' => $performance]);
    }

    public function show(Request $request, int $studentId, int $termId)
    {
        $performances = StudentTermPerformance::where('student_id', $studentId)
            ->where('term_id', $termId)
            ->with(['subject'])
            ->get();
            
        return response()->json($performances);
    }
}
