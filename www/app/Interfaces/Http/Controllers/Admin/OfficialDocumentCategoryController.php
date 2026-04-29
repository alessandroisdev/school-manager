<?php

namespace App\Interfaces\Http\Controllers\Admin;

use App\Domains\OfficialDocument\Models\OfficialDocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OfficialDocumentCategoryController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');

        if ($request->ajax()) {
            $query = OfficialDocumentCategory::where('unit_id', $unitId)->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('name_badge', function($category) {
                    return '<div class="fw-bold text-dark"><i class="bi bi-folder-fill text-warning me-2"></i>' . $category->name . '</div>';
                })
                ->addColumn('acronym_badge', function($category) {
                    return '<span class="badge bg-secondary">' . $category->acronym . '</span>';
                })
                ->addColumn('actions', function($category) {
                    $editUrl = route('admin.official-categories.edit', $category);
                    $deleteUrl = route('admin.official-categories.destroy', $category);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '
                        <div class="text-end text-nowrap">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-light text-primary fw-bold me-2"><i class="bi bi-pencil-square"></i> Editar</a>
                            <form action="' . $deleteUrl . '" method="POST" class="d-inline-block form-delete" onsubmit="return confirm(\'Atenção! Isso excluirá a categoria. Os ofícios já gerados perderão a referência de categoria. Continuar?\');">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold"><i class="bi bi-trash"></i> Excluir</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['name_badge', 'acronym_badge', 'actions'])
                ->make(true);
        }

        return view('admin.official-categories.index');
    }

    public function create()
    {
        return view('admin.official-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'acronym' => 'required|string|max:10|alpha_dash',
        ]);
        
        $validated['unit_id'] = session('active_unit_id');
        $validated['acronym'] = strtoupper($validated['acronym']);
        
        OfficialDocumentCategory::create($validated);
        
        return redirect()->route('admin.official-categories.index')->with('success', 'Categoria de Ofício criada com sucesso!');
    }

    public function edit(OfficialDocumentCategory $officialCategory)
    {
        if ($officialCategory->unit_id != session('active_unit_id')) abort(403);
        return view('admin.official-categories.edit', compact('officialCategory'));
    }

    public function update(Request $request, OfficialDocumentCategory $officialCategory)
    {
        if ($officialCategory->unit_id != session('active_unit_id')) abort(403);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'acronym' => 'required|string|max:10|alpha_dash',
        ]);
        
        $validated['acronym'] = strtoupper($validated['acronym']);
        
        $officialCategory->update($validated);
        
        return redirect()->route('admin.official-categories.index')->with('success', 'Categoria atualizada!');
    }

    public function destroy(OfficialDocumentCategory $officialCategory)
    {
        if ($officialCategory->unit_id != session('active_unit_id')) abort(403);
        $officialCategory->delete();
        return redirect()->route('admin.official-categories.index')->with('success', 'Categoria excluída!');
    }
}
