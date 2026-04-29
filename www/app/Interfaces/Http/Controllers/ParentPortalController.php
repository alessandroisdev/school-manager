<?php

namespace App\Interfaces\Http\Controllers;

use App\Domains\Enrollment\Models\Guardian;
use App\Domains\Enrollment\Models\Student;
use App\Domains\Shared\Models\Communication;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ParentPortalController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Find guardian profile
        $guardian = Guardian::where('user_id', $user->id)->first();
        if (!$guardian) {
            return view('parent-portal.dashboard', ['error' => 'Perfil de Responsável não encontrado.']);
        }

        // Get children
        $students = $guardian->students()->with(['enrollments' => function($q) {
            $q->whereIn('status', ['active', 'ativa'])->with('schoolClass.grade', 'schoolClass.shift');
        }])->get();

        // Selected child
        $selectedStudentId = $request->get('student_id') ?? ($students->first() ? $students->first()->id : null);
        $selectedStudent = $students->firstWhere('id', $selectedStudentId);

        // Load Financial and Academic data for selected child
        $invoices = collect();
        $reportCards = [];
        
        if ($selectedStudent) {
            // Financeiro
            $invoices = \App\Domains\Finance\Models\Invoice::where('student_id', $selectedStudent->id)
                ->orderBy('due_date')
                ->get();
                
            // Acadêmico (Boletim - simplificado, igual StudentController)
            foreach ($selectedStudent->enrollments as $enrollment) {
                $classId = $enrollment->school_class_id;
                $assignments = \App\Domains\Academic\Models\TeacherAssignment::with('subject')
                    ->where('school_class_id', $classId)->get();
                    
                $subjectsData = [];
                $totalClasses = 0; $totalAbsences = 0;
                
                foreach ($assignments as $assignment) {
                    $subject = $assignment->subject;
                    $totalScore = \App\Domains\Academic\Models\GradeEntry::where('student_id', $selectedStudent->id)
                        ->whereHas('evaluation', function($q) use ($classId, $subject) {
                            $q->where('school_class_id', $classId)->where('subject_id', $subject->id);
                        })->sum('score');
                        
                    $absences = \App\Domains\Academic\Models\AttendanceRecord::where('student_id', $selectedStudent->id)
                        ->where('status', 'falta')
                        ->whereHas('lesson', function($q) use ($classId, $subject) {
                            $q->where('school_class_id', $classId)->where('subject_id', $subject->id);
                        })->count();
                        
                    $lessonsCount = \App\Domains\Academic\Models\Lesson::where('school_class_id', $classId)
                        ->where('subject_id', $subject->id)->count();
                        
                    $subjectsData[] = [
                        'subject_name' => $subject->name,
                        'total_score' => $totalScore,
                        'absences' => $absences,
                        'lessons_count' => $lessonsCount
                    ];
                    $totalClasses += $lessonsCount; $totalAbsences += $absences;
                }
                
                $globalAttendance = $totalClasses > 0 ? (($totalClasses - $totalAbsences) / $totalClasses) * 100 : 100;
                $reportCards[] = [
                    'enrollment' => $enrollment,
                    'subjects' => $subjectsData,
                    'global_attendance' => round($globalAttendance, 1),
                    'total_absences' => $totalAbsences
                ];
            }
        }

        // Comunicados (Mural de Avisos)
        $communications = Communication::where('unit_id', session('active_unit_id'))
            ->whereIn('target_audience', ['parents', 'all'])
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->get();

        return view('parent-portal.dashboard', compact('guardian', 'students', 'selectedStudent', 'invoices', 'reportCards', 'communications'));
    }
}
