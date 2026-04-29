<?php

namespace App\Interfaces\Http\Controllers\Admin;

use App\Domains\Shared\Models\School;
use App\Domains\Shared\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Unit::with('school')->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('unit_info', function($unit) {
                    return '<div class="fw-bold text-dark mb-0"><i class="bi bi-building me-1"></i> ' . $unit->name . '</div>
                            <div class="small text-muted" style="font-size: 0.75rem;">' . ($unit->school->name ?? 'Matriz') . '</div>';
                })
                ->addColumn('contact', function($unit) {
                    $phone = $unit->phone ? '<i class="bi bi-telephone text-muted me-1"></i> ' . $unit->phone : '<span class="text-muted small">Sem telefone</span>';
                    $email = $unit->email ? '<br><small class="text-muted"><i class="bi bi-envelope"></i> ' . $unit->email . '</small>' : '';
                    return $phone . $email;
                })
                ->addColumn('status_badge', function($unit) {
                    if ($unit->is_active) {
                        return '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">Ativa</span>';
                    } else {
                        return '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 border border-danger border-opacity-25">Inativa</span>';
                    }
                })
                ->addColumn('actions', function($unit) {
                    $editUrl = route('admin.units.edit', $unit);
                    $deleteUrl = route('admin.units.destroy', $unit);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '
                        <div class="text-end text-nowrap">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-light text-primary fw-bold me-2"><i class="bi bi-pencil-square"></i> Editar</a>
                            <form action="' . $deleteUrl . '" method="POST" class="d-inline-block form-delete" onsubmit="return confirm(\'Excluir esta unidade?\');">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold"><i class="bi bi-trash"></i> Excluir</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['unit_info', 'contact', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('admin.units.index');
    }

    public function create()
    {
        $schools = School::all();
        return view('admin.units.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'document' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Unit::create($validated);

        return redirect()->route('admin.units.index')->with('success', 'Unidade criada com sucesso!');
    }

    public function edit(Unit $unit)
    {
        $schools = School::all();
        return view('admin.units.edit', compact('unit', 'schools'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'document' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $unit->update($validated);

        return redirect()->route('admin.units.index')->with('success', 'Unidade atualizada com sucesso!');
    }

    public function destroy(Unit $unit)
    {
        if (Unit::count() <= 1) {
            return back()->withErrors('Não é possível excluir a única unidade do sistema.');
        }
        
        $unit->delete();
        
        // Se a unidade ativa for excluída, removemos da sessão
        if (session('active_unit_id') == $unit->id) {
            session()->forget('active_unit_id');
        }

        return redirect()->route('admin.units.index')->with('success', 'Unidade excluída com sucesso!');
    }
}
