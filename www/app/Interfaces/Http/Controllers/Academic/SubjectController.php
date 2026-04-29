<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');
        
        if ($request->ajax()) {
            $query = Subject::where('unit_id', $unitId)->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('name_avatar', function($subject) {
                    $initials = mb_substr($subject->name, 0, 2, 'UTF-8');
                    return '<div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold text-uppercase" style="width: 40px; height: 40px;">
                                    ' . $initials . '
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark mb-0">' . $subject->name . '</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">' . ($subject->code ?? 'Sem Código') . '</div>
                                </div>
                            </div>';
                })
                ->addColumn('workload', function($subject) {
                    return $subject->workload ? $subject->workload . 'h' : '-';
                })
                ->addColumn('status_badge', function($subject) {
                    if ($subject->is_active) {
                        return '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">Ativa</span>';
                    } else {
                        return '<span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 border border-secondary border-opacity-25">Inativa</span>';
                    }
                })
                ->addColumn('actions', function($subject) {
                    $editUrl = route('academic.subjects.edit', $subject);
                    $deleteUrl = route('academic.subjects.destroy', $subject);
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

        return view('academic.subjects.index');
    }

    public function create()
    {
        return view('academic.subjects.create');
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'workload' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['unit_id'] = $unitId;

        Subject::create($validated);

        return redirect()->route('academic.subjects.index')->with('success', 'Disciplina cadastrada com sucesso!');
    }

    public function edit(Subject $subject)
    {
        return view('academic.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'workload' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $subject->update($validated);

        return redirect()->route('academic.subjects.index')->with('success', 'Dados da disciplina atualizados!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('academic.subjects.index')->with('success', 'Disciplina removida com sucesso!');
    }
}
