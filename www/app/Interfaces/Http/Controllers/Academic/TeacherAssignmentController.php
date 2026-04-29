<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Subject;
use App\Domains\Academic\Models\TeacherAssignment;
use App\Domains\HR\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class TeacherAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');
        
        if ($request->ajax()) {
            $query = TeacherAssignment::with(['teacher.employee', 'schoolClass.grade', 'subject'])
                ->whereHas('schoolClass', function($q) use ($unitId) {
                    $q->where('unit_id', $unitId);
                })->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('teacher_name', function($assignment) {
                    $employee = $assignment->teacher->employee;
                    return '<div class="fw-bold text-info"><i class="bi bi-person-video3 me-1"></i> ' . $employee->name . '</div>';
                })
                ->addColumn('class_info', function($assignment) {
                    $class = $assignment->schoolClass;
                    return '<div class="fw-bold text-dark">' . $class->name . '</div>
                            <div class="small text-muted">' . $class->grade->name . '</div>';
                })
                ->addColumn('subject_info', function($assignment) {
                    return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1"><i class="bi bi-book-half me-1"></i> ' . $assignment->subject->name . '</span>';
                })
                ->addColumn('workload', function($assignment) {
                    return $assignment->assigned_workload . 'h';
                })
                ->addColumn('actions', function($assignment) {
                    $deleteUrl = route('academic.assignments.destroy', $assignment);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '
                        <div class="text-end text-nowrap">
                            <form action="' . $deleteUrl . '" method="POST" class="d-inline-block form-delete">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold"><i class="bi bi-trash"></i> Desalocar</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['teacher_name', 'class_info', 'subject_info', 'actions'])
                ->make(true);
        }

        return view('academic.assignments.index');
    }

    public function create()
    {
        $unitId = session('active_unit_id');
        
        $classes = SchoolClass::with(['grade', 'shift'])->where('unit_id', $unitId)->get();
        $subjects = Subject::where('unit_id', $unitId)->where('is_active', true)->get();
        $teachers = Teacher::with('employee')->whereHas('employee', function($q) use ($unitId) {
            $q->where('unit_id', $unitId)->where('is_active', true);
        })->get();

        return view('academic.assignments.create', compact('classes', 'subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'assigned_workload' => 'required|integer|min:1|max:160',
        ]);

        // Verifica a restrição "unique_assignment" (mesmo professor, mesma turma, mesma matéria)
        $exists = TeacherAssignment::where('teacher_id', $validated['teacher_id'])
            ->where('school_class_id', $validated['school_class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Este professor já está alocado para esta disciplina nesta mesma turma.');
        }

        TeacherAssignment::create($validated);

        return redirect()->route('academic.assignments.index')->with('success', 'Professor alocado com sucesso!');
    }

    public function destroy(TeacherAssignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('academic.assignments.index')->with('success', 'Alocação cancelada com sucesso!');
    }
}
