<?php

namespace App\Interfaces\Http\Controllers\Evaluation;

use App\Domains\Evaluation\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::with('evaluationType')->paginate(15);
        return response()->json($evaluations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'evaluation_type_id' => 'required|exists:evaluation_types,id',
            'teacher_id' => 'required|exists:teachers,id',
            'name' => 'required|string',
            'date' => 'required|date',
            'max_score' => 'required|numeric|min:1',
            'weight' => 'required|numeric|min:0.1',
        ]);

        $evaluation = Evaluation::create($validated);

        return response()->json(['message' => 'Avaliação criada com sucesso!', 'evaluation' => $evaluation], 201);
    }
}
