<?php

namespace App\Interfaces\Http\Controllers;

use App\Domains\Enrollment\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StudentController extends Controller
{
    public function index()
    {
        $unitId = session('active_unit_id');
        
        $students = Student::where('unit_id', $unitId)
            ->latest()
            ->paginate(10);

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');
        if (!$unitId) {
            return back()->withErrors('Nenhuma unidade ativa selecionada.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|string|max:50',
            'birth_date' => 'required|date',
        ]);

        Student::create([
            'unit_id' => $unitId,
            'name' => $validated['name'],
            'document' => $validated['document'],
            'birth_date' => $validated['birth_date'],
        ]);

        return redirect()->route('students.index')->with('success', 'Aluno cadastrado com sucesso!');
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|string|max:50',
            'birth_date' => 'required|date',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Dados do aluno atualizados!');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Aluno removido com sucesso!');
    }
}
