<?php

namespace App\Interfaces\Http\Controllers;

use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Enrollment\Models\Enrollment;
use App\Domains\Enrollment\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');
        
        if ($request->ajax()) {
            $query = Enrollment::with(['student', 'schoolClass.grade', 'schoolClass.shift'])
                ->whereHas('schoolClass', function($q) use ($unitId) {
                    $q->where('unit_id', $unitId);
                })->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('student_name', function($enrollment) {
                    $student = $enrollment->student;
                    $initials = mb_substr($student->name, 0, 2, 'UTF-8');
                    return '<div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold text-uppercase" style="width: 40px; height: 40px;">
                                    ' . $initials . '
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark mb-0">' . $student->name . '</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">' . $student->document . '</div>
                                </div>
                            </div>';
                })
                ->addColumn('class_info', function($enrollment) {
                    $class = $enrollment->schoolClass;
                    return '<div class="fw-bold text-dark">' . $class->name . '</div>
                            <div class="small text-muted"><i class="bi bi-bookmark me-1"></i> ' . $class->grade->name . '</div>
                            <div class="small text-muted"><i class="bi bi-clock me-1"></i> ' . $class->shift->name . '</div>';
                })
                ->addColumn('status_badge', function($enrollment) {
                    $badgeClass = match($enrollment->status->value ?? $enrollment->status) {
                        'active', 'ativa' => 'bg-success text-success',
                        'inactive', 'trancada', 'transferida' => 'bg-warning text-warning',
                        'concluída' => 'bg-info text-info',
                        default => 'bg-secondary text-secondary'
                    };
                    $statusName = ucfirst($enrollment->status->value ?? $enrollment->status);
                    
                    return '<span class="badge ' . $badgeClass . ' bg-opacity-10 rounded-pill px-3 py-2 border border-opacity-25 border-' . explode('-', $badgeClass)[0] . '">' . $statusName . '</span>';
                })
                ->addColumn('actions', function($enrollment) {
                    $deleteUrl = route('enrollments.destroy', $enrollment);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '
                        <div class="text-end text-nowrap">
                            <form action="' . $deleteUrl . '" method="POST" class="d-inline-block form-delete">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold"><i class="bi bi-trash"></i> Cancelar Matrícula</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['student_name', 'class_info', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('enrollments.index');
    }

    public function create()
    {
        $unitId = session('active_unit_id');
        
        // Pega apenas alunos da unidade que AINDA NÃO estão matriculados em turmas ativas neste ano letivo.
        // Para simplificar o MVP, vamos pegar todos da unidade.
        $students = Student::where('unit_id', $unitId)->orderBy('name')->get();
        
        // Pega as turmas com a contagem atual de matrículas para mostrar as vagas
        $classes = SchoolClass::with(['grade', 'shift'])
            ->where('unit_id', $unitId)
            ->withCount(['enrollments' => function($q) {
                $q->whereIn('status', ['active', 'ativa']);
            }])
            ->get();

        return view('enrollments.create', compact('students', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'status' => 'required|string',
        ]);

        $class = SchoolClass::findOrFail($validated['school_class_id']);
        $currentEnrollments = Enrollment::where('school_class_id', $class->id)
            ->whereIn('status', ['active', 'ativa'])
            ->count();

        if ($currentEnrollments >= $class->capacity) {
            return back()->with('error', 'A turma selecionada já atingiu sua capacidade máxima de alunos (' . $class->capacity . ' vagas).');
        }

        // Verifica duplicidade básica
        $exists = Enrollment::where('student_id', $validated['student_id'])
            ->where('school_class_id', $validated['school_class_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Este aluno já está matriculado nesta turma.');
        }

        Enrollment::create($validated);

        return redirect()->route('enrollments.index')->with('success', 'Matrícula efetuada com sucesso!');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('enrollments.index')->with('success', 'Matrícula cancelada com sucesso!');
    }
}
