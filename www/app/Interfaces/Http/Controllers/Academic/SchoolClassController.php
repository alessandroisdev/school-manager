<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Grade;
use App\Domains\Academic\Models\Shift;
use App\Domains\Academic\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SchoolClassController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');

        if ($request->ajax()) {
            $query = SchoolClass::with(['grade', 'shift', 'academicYear'])
                ->where('unit_id', $unitId)
                ->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('name_badge', function($class) {
                    return '<div class="fw-bold text-dark mb-0"><i class="bi bi-door-open-fill text-primary me-2"></i>' . $class->name . '</div>
                            <div class="small text-muted ms-4">Capacidade: ' . $class->capacity . ' alunos</div>';
                })
                ->addColumn('details', function($class) {
                    $year = $class->academicYear ? $class->academicYear->year : '-';
                    $grade = $class->grade ? $class->grade->name : '-';
                    $shift = $class->shift ? $class->shift->name : '-';
                    
                    return '<div class="d-flex flex-column gap-1">
                                <span class="badge bg-light text-dark border w-auto text-start"><i class="bi bi-bookmark me-1"></i> ' . $grade . '</span>
                                <span class="badge bg-light text-dark border w-auto text-start"><i class="bi bi-clock me-1"></i> ' . $shift . '</span>
                                <span class="badge bg-light text-dark border w-auto text-start"><i class="bi bi-calendar me-1"></i> ' . $year . '</span>
                            </div>';
                })
                ->addColumn('actions', function($class) {
                    $editUrl = route('academic.classes.edit', $class);
                    $deleteUrl = route('academic.classes.destroy', $class);
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
                ->rawColumns(['name_badge', 'details', 'actions'])
                ->make(true);
        }

        return view('academic.classes.index');
    }

    public function create()
    {
        $unitId = session('active_unit_id');
        $academicYears = AcademicYear::where('unit_id', $unitId)->orderBy('year', 'desc')->get();
        $grades = Grade::where('unit_id', $unitId)->orderBy('name')->get();
        $shifts = Shift::where('unit_id', $unitId)->orderBy('name')->get();

        return view('academic.classes.create', compact('academicYears', 'grades', 'shifts'));
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'grade_id' => 'required|exists:grades,id',
            'shift_id' => 'required|exists:shifts,id',
            'name' => 'required|string|max:255|unique:school_classes,name,NULL,id,unit_id,' . $unitId,
            'capacity' => 'required|integer|min:1|max:100',
        ], [
            'name.unique' => 'Já existe uma turma com este nome.'
        ]);

        $validated['unit_id'] = $unitId;
        SchoolClass::create($validated);

        return redirect()->route('academic.classes.index')->with('success', 'Turma criada com sucesso!');
    }

    public function edit(SchoolClass $class)
    {
        $unitId = session('active_unit_id');
        $academicYears = AcademicYear::where('unit_id', $unitId)->orderBy('year', 'desc')->get();
        $grades = Grade::where('unit_id', $unitId)->orderBy('name')->get();
        $shifts = Shift::where('unit_id', $unitId)->orderBy('name')->get();

        return view('academic.classes.edit', compact('class', 'academicYears', 'grades', 'shifts'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'grade_id' => 'required|exists:grades,id',
            'shift_id' => 'required|exists:shifts,id',
            'name' => 'required|string|max:255|unique:school_classes,name,' . $class->id . ',id,unit_id,' . $class->unit_id,
            'capacity' => 'required|integer|min:1|max:100',
        ]);

        $class->update($validated);

        return redirect()->route('academic.classes.index')->with('success', 'Turma atualizada com sucesso!');
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();
        return redirect()->route('academic.classes.index')->with('success', 'Turma removida com sucesso!');
    }
}
