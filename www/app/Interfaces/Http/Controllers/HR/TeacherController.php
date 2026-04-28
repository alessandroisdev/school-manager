<?php

namespace App\Interfaces\Http\Controllers\HR;

use App\Application\UseCases\HR\HireTeacherUseCase;
use App\Domains\HR\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TeacherController extends Controller
{
    public function __construct(
        protected HireTeacherUseCase $hireTeacherUseCase
    ) {}

    public function index()
    {
        $teachers = Teacher::with('employee')->paginate(15);
        return response()->json($teachers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|string',
            'document' => 'required|string|unique:employees,document',
            'email' => 'nullable|email|unique:users,email',
            'create_user' => 'boolean',
            'specialty' => 'nullable|string',
            'max_workload' => 'integer|min:1',
        ]);

        $teacher = $this->hireTeacherUseCase->execute($validated);

        return response()->json(['message' => 'Professor contratado com sucesso!', 'teacher' => $teacher], 201);
    }
}
