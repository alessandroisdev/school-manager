<?php

namespace App\Interfaces\Http\Controllers\HR;

use App\Domains\HR\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');
        
        if ($request->ajax()) {
            $query = Employee::where('unit_id', $unitId)->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->editColumn('name', function($employee) {
                    $initials = mb_substr($employee->name, 0, 2, 'UTF-8');
                    return '<div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold text-uppercase" style="width: 40px; height: 40px;">
                                    ' . $initials . '
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark mb-0">' . $employee->name . '</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">' . ($employee->position ?? 'Colaborador') . '</div>
                                </div>
                            </div>';
                })
                ->addColumn('hire_date_formatted', function($employee) {
                    return $employee->hire_date ? $employee->hire_date->format('d/m/Y') : '-';
                })
                ->addColumn('status_badge', function($employee) {
                    if ($employee->is_active) {
                        return '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">Ativo</span>';
                    } else {
                        return '<span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 border border-secondary border-opacity-25">Inativo</span>';
                    }
                })
                ->addColumn('actions', function($employee) {
                    $editUrl = route('hr.employees.edit', $employee);
                    $deleteUrl = route('hr.employees.destroy', $employee);
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
                ->rawColumns(['name', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('hr.employees.index');
    }

    public function create()
    {
        return view('hr.employees.create');
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|string|max:20|unique:employees,document',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'is_active' => 'boolean',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0'
        ]);

        $validated['unit_id'] = $unitId;

        Employee::create($validated);

        return redirect()->route('hr.employees.index')->with('success', 'Colaborador cadastrado com sucesso!');
    }

    public function edit(Employee $employee)
    {
        return view('hr.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|string|max:50|unique:employees,document,' . $employee->id,
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'is_active' => 'boolean',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0'
        ]);

        $employee->update($validated);

        return redirect()->route('hr.employees.index')->with('success', 'Dados do colaborador atualizados!');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('hr.employees.index')->with('success', 'Colaborador removido com sucesso!');
    }
}
