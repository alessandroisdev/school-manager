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
                ->addColumn('contact', function($student) {
                    $phone = $student->phone ? '<i class="bi bi-whatsapp text-success me-1"></i> ' . $student->phone : '<span class="text-muted small">Sem contato</span>';
                    $email = $student->email ? '<br><small class="text-muted"><i class="bi bi-envelope"></i> ' . $student->email . '</small>' : '';
                    return $phone . $email;
                })
                ->addColumn('status_badge', function($student) {
                    if ($student->status === 'active') {
                        return '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">Ativo</span>';
                    } elseif ($student->status === 'inactive') {
                        return '<span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 border border-secondary border-opacity-25">Inativo</span>';
                    } else {
                        return '<span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 border border-warning border-opacity-25">Transferido</span>';
                    }
                })
                ->addColumn('actions', function($student) {
                    $editUrl = route('students.edit', $student);
                    $deleteUrl = route('students.destroy', $student);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '
                        <div class="text-end text-nowrap">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-light text-primary fw-bold me-2"><i class="bi bi-pencil-square"></i> Editar</a>
                            <form action="' . $deleteUrl . '" method="POST" class="d-inline-block form-delete">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold"><i class="bi bi-trash"></i> Excluir</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['name_avatar', 'contact', 'status_badge', 'actions'])
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
            'gender' => 'nullable|string|max:20',
            'blood_type' => 'nullable|string|max:5',
            'medical_notes' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address_zipcode' => 'nullable|string|max:20',
            'address_street' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'address_neighborhood' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:255',
            'address_state' => 'nullable|string|max:2',
            'status' => 'nullable|string|in:active,inactive,transferred',
        ]);

        $validated['unit_id'] = $unitId;

        Student::create($validated);

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
            'gender' => 'nullable|string|max:20',
            'blood_type' => 'nullable|string|max:5',
            'medical_notes' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address_zipcode' => 'nullable|string|max:20',
            'address_street' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'address_neighborhood' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:255',
            'address_state' => 'nullable|string|max:2',
            'status' => 'nullable|string|in:active,inactive,transferred',
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
