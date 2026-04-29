<?php

namespace App\Interfaces\Http\Controllers\Admin;

use App\Domains\Document\Models\DocumentPartial;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DocumentPartialController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');
        
        if ($request->ajax()) {
            $query = DocumentPartial::where('unit_id', $unitId)->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->editColumn('type', function($partial) {
                    return $partial->type === 'header' 
                        ? '<span class="badge bg-info text-dark">Cabeçalho</span>' 
                        : '<span class="badge bg-secondary">Rodapé</span>';
                })
                ->addColumn('actions', function($partial) {
                    $editUrl = route('admin.partials.edit', $partial);
                    $deleteUrl = route('admin.partials.destroy', $partial);
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
                ->rawColumns(['type', 'actions'])
                ->make(true);
        }

        return view('admin.partials.index');
    }

    public function create()
    {
        return view('admin.partials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:header,footer',
            'content' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['unit_id'] = session('active_unit_id');
        $validated['is_active'] = $request->has('is_active');

        DocumentPartial::create($validated);

        return redirect()->route('admin.partials.index')->with('success', 'Bloco criado com sucesso!');
    }

    public function edit(DocumentPartial $documentPartial)
    {
        return view('admin.partials.edit', compact('documentPartial'));
    }

    public function update(Request $request, DocumentPartial $documentPartial)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:header,footer',
            'content' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $documentPartial->update($validated);

        return redirect()->route('admin.partials.index')->with('success', 'Bloco atualizado com sucesso!');
    }

    public function destroy(DocumentPartial $documentPartial)
    {
        $documentPartial->delete();
        return redirect()->route('admin.partials.index')->with('success', 'Bloco removido!');
    }
}
