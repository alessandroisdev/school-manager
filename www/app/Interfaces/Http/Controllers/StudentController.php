<?php

namespace App\Interfaces\Http\Controllers;

use App\Domains\Enrollment\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');
        
        if ($request->ajax()) {
            $query = Student::where('unit_id', $unitId)->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('name_avatar', function($student) {
                    $initials = substr($student->name, 0, 2);
                    return '<div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold text-uppercase" style="width: 40px; height: 40px;">
                                    ' . $initials . '
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark mb-0">' . $student->name . '</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">Matrícula ativa</div>
                                </div>
                            </div>';
                })
                ->addColumn('birth_date_formatted', function($student) {
                    return $student->birth_date ? $student->birth_date->format('d/m/Y') : '-';
                })
                ->addColumn('actions', function($student) {
                    $editUrl = route('students.edit', $student);
                    $deleteUrl = route('students.destroy', $student);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '
                        <div class="text-end text-nowrap">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-light text-primary fw-bold me-2"><i class="bi bi-pencil-square"></i> Editar</a>
                            <form action="' . $deleteUrl . '" method="POST" class="d-inline-block" onsubmit="return confirm(\'Tem certeza que deseja excluir o aluno?\');">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold"><i class="bi bi-trash"></i> Excluir</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['name_avatar', 'actions'])
                ->make(true);
        }

        return view('students.index');
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
