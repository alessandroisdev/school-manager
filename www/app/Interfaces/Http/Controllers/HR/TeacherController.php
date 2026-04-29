<?php

namespace App\Interfaces\Http\Controllers\HR;

use App\Domains\HR\Models\Employee;
use App\Domains\HR\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');
        
        if ($request->ajax()) {
            $query = Teacher::with('employee')->whereHas('employee', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            })->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('name_avatar', function($teacher) {
                    $employee = $teacher->employee;
                    $initials = substr($employee->name, 0, 2);
                    return '<div class="d-flex align-items-center">
                                <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center fw-bold text-uppercase" style="width: 40px; height: 40px;">
                                    ' . $initials . '
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark mb-0">' . $employee->name . '</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">' . ($teacher->specialty ?? 'Professor') . '</div>
                                </div>
                            </div>';
                })
                ->addColumn('workload', function($teacher) {
                    return $teacher->max_workload . 'h / sem';
                })
                ->addColumn('status_badge', function($teacher) {
                    if ($teacher->employee->is_active) {
                        return '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">Ativo</span>';
                    } else {
                        return '<span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 border border-secondary border-opacity-25">Inativo</span>';
                    }
                })
                ->addColumn('actions', function($teacher) {
                    $editUrl = route('hr.teachers.edit', $teacher);
                    $deleteUrl = route('hr.teachers.destroy', $teacher);
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
                ->rawColumns(['name_avatar', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('hr.teachers.index');
    }

    public function create()
    {
        return view('hr.teachers.create');
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|string|max:50|unique:employees,document',
            'hire_date' => 'nullable|date',
            'specialty' => 'nullable|string|max:255',
            'max_workload' => 'required|integer|min:1|max:160',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function() use ($validated, $unitId) {
            $employee = Employee::create([
                'unit_id' => $unitId,
                'name' => $validated['name'],
                'document' => $validated['document'],
                'position' => 'Professor(a)',
                'hire_date' => $validated['hire_date'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            Teacher::create([
                'employee_id' => $employee->id,
                'specialty' => $validated['specialty'] ?? null,
                'max_workload' => $validated['max_workload'],
            ]);
        });

        return redirect()->route('hr.teachers.index')->with('success', 'Professor cadastrado com sucesso!');
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('employee');
        return view('hr.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|string|max:50|unique:employees,document,' . $teacher->employee_id,
            'hire_date' => 'nullable|date',
            'specialty' => 'nullable|string|max:255',
            'max_workload' => 'required|integer|min:1|max:160',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function() use ($validated, $teacher) {
            $teacher->employee->update([
                'name' => $validated['name'],
                'document' => $validated['document'],
                'hire_date' => $validated['hire_date'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $teacher->update([
                'specialty' => $validated['specialty'] ?? null,
                'max_workload' => $validated['max_workload'],
            ]);
        });

        return redirect()->route('hr.teachers.index')->with('success', 'Dados do professor atualizados!');
    }

    public function destroy(Teacher $teacher)
    {
        DB::transaction(function() use ($teacher) {
            $employee = $teacher->employee;
            $teacher->delete();
            if ($employee) {
                $employee->delete();
            }
        });

        return redirect()->route('hr.teachers.index')->with('success', 'Professor removido com sucesso!');
    }
}
