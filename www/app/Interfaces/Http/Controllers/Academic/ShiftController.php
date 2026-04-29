<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');

        if ($request->ajax()) {
            $query = Shift::where('unit_id', $unitId)->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('name_badge', function($shift) {
                    return '<div class="fw-bold text-dark"><i class="bi bi-clock-history text-primary me-2"></i>' . $shift->name . '</div>';
                })
                ->addColumn('time_range', function($shift) {
                    $start = $shift->start_time ? substr($shift->start_time, 0, 5) : '--:--';
                    $end = $shift->end_time ? substr($shift->end_time, 0, 5) : '--:--';
                    return '<span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-sun me-1"></i> ' . $start . ' às ' . $end . '</span>';
                })
                ->addColumn('actions', function($shift) {
                    $editUrl = route('academic.shifts.edit', $shift);
                    $deleteUrl = route('academic.shifts.destroy', $shift);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '
                        <div class="text-end text-nowrap">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-light text-primary fw-bold me-2"><i class="bi bi-pencil-square"></i> Editar</a>
                            <form action="' . $deleteUrl . '" method="POST" class="d-inline-block" onsubmit="return confirm(\'Tem certeza que deseja excluir este turno?\');">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold"><i class="bi bi-trash"></i> Excluir</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['name_badge', 'time_range', 'actions'])
                ->make(true);
        }

        return view('academic.shifts.index');
    }

    public function create()
    {
        return view('academic.shifts.create');
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name,NULL,id,unit_id,' . $unitId,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [
            'name.unique' => 'Já existe um turno com este nome.',
            'end_time.after' => 'O horário de saída deve ser após o horário de entrada.'
        ]);

        Shift::create([
            'unit_id' => $unitId,
            'name' => $validated['name'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        return redirect()->route('academic.shifts.index')->with('success', 'Turno cadastrado com sucesso!');
    }

    public function edit(Shift $shift)
    {
        return view('academic.shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name,' . $shift->id . ',id,unit_id,' . $shift->unit_id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $shift->update($validated);

        return redirect()->route('academic.shifts.index')->with('success', 'Turno atualizado com sucesso!');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('academic.shifts.index')->with('success', 'Turno removido com sucesso!');
    }
}
