<?php

namespace App\Interfaces\Http\Controllers\Evaluation;

use App\Application\UseCases\Evaluation\RegisterGradesUseCase;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;

class GradeEntryController extends Controller
{
    public function __construct(
        protected RegisterGradesUseCase $registerGradesUseCase
    ) {}

    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'evaluation_id' => 'required|exists:evaluations,id',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.score' => 'required|numeric|min:0',
            'grades.*.feedback' => 'nullable|string',
        ]);

        try {
            $this->registerGradesUseCase->execute($validated['evaluation_id'], $validated['grades']);
            return response()->json(['message' => 'Notas lançadas com sucesso!'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
