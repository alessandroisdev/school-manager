<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');

        if ($request->ajax()) {
            $query = Grade::where('unit_id', $unitId)->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('name_badge', function($grade) {
                    return '<div class="fw-bold text-dark"><i class="bi bi-bookmark-fill text-primary me-2"></i>' . $grade->name . '</div>';
                })
                ->addColumn('actions', function($grade) {
                    $editUrl = route('academic.grades.edit', $grade);
                    $deleteUrl = route('academic.grades.destroy', $grade);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '
                        <div class="text-end text-nowrap">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-light text-primary fw-bold me-2"><i class="bi bi-pencil-square"></i> Editar</a>
                            <form action="' . $deleteUrl . '" method="POST" class="d-inline-block" onsubmit="return confirm(\'Tem certeza que deseja excluir esta série?\');">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold"><i class="bi bi-trash"></i> Excluir</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['name_badge', 'actions'])
                ->make(true);
        }

        return view('academic.grades.index');
    }

    public function create()
    {
        return view('academic.grades.create');
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');
        if (!$unitId) {
            return back()->withErrors('Nenhuma unidade ativa selecionada.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:grades,name,NULL,id,unit_id,' . $unitId,
        ], [
            'name.unique' => 'Já existe uma série com este nome nesta unidade.'
        ]);

        Grade::create([
            'unit_id' => $unitId,
            'name' => $validated['name'],
        ]);

        return redirect()->route('academic.grades.index')->with('success', 'Série/Ano cadastrado com sucesso!');
    }

    public function edit(Grade $grade)
    {
        return view('academic.grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:grades,name,' . $grade->id . ',id,unit_id,' . $grade->unit_id,
        ], [
            'name.unique' => 'Já existe uma série com este nome nesta unidade.'
        ]);

        $grade->update($validated);

        return redirect()->route('academic.grades.index')->with('success', 'Série/Ano atualizado com sucesso!');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return redirect()->route('academic.grades.index')->with('success', 'Série/Ano removido com sucesso!');
    }
}
