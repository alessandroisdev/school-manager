<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TeacherPortalController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');
        
        // Em um sistema real, filtraríamos pelo professor logado: 
        // ->where('teacher_id', auth()->user()->teacher->id)
        // Como não temos login ainda, mostramos todos os diários disponíveis.

        if ($request->ajax()) {
            $query = TeacherAssignment::with(['teacher.employee', 'schoolClass.grade', 'schoolClass.shift', 'subject'])
                ->whereHas('schoolClass', function($q) use ($unitId) {
                    $q->where('unit_id', $unitId);
                })->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('diary_name', function($assignment) {
                    $class = $assignment->schoolClass;
                    return '<div class="d-flex align-items-center">
                                <div class="rounded bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold me-3" style="width: 48px; height: 48px; font-size: 1.2rem;">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-6">' . $class->name . ' - ' . $assignment->subject->name . '</div>
                                    <div class="small text-muted">' . $class->grade->name . ' • ' . $class->shift->name . '</div>
                                </div>
                            </div>';
                })
                ->addColumn('teacher_name', function($assignment) {
                    return '<span class="text-secondary"><i class="bi bi-person me-1"></i> ' . $assignment->teacher->employee->name . '</span>';
                })
                ->addColumn('actions', function($assignment) {
                    $lessonsUrl = route('academic.diary.lessons', $assignment->id);
                    $evaluationsUrl = route('academic.diary.evaluations', $assignment->id);

                    return '
                        <div class="text-end text-nowrap">
                            <a href="' . $lessonsUrl . '" class="btn btn-sm btn-outline-primary fw-bold me-2"><i class="bi bi-check2-square me-1"></i> Frequência</a>
                            <a href="' . $evaluationsUrl . '" class="btn btn-sm btn-outline-info fw-bold"><i class="bi bi-award me-1"></i> Notas</a>
                        </div>
                    ';
                })
                ->rawColumns(['diary_name', 'teacher_name', 'actions'])
                ->make(true);
        }

        return view('academic.diary.index');
    }
}
