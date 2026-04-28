<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::paginate(15);
        return response()->json($subjects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|string',
            'workload' => 'required|integer|min:1',
        ]);

        $subject = Subject::create($validated);

        return response()->json(['message' => 'Disciplina criada com sucesso!', 'subject' => $subject], 201);
    }
}
