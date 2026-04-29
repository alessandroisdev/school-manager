<?php

namespace App\Interfaces\Http\Controllers;

use App\Domains\Enrollment\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StudentPortalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Em produção, buscaríamos pelo user_id do aluno autenticado
        // $student = Student::where('user_id', $user->id)->first();
        
        // Para o MVP (Como logamos com Admin testando papéis falsos), pegamos o último aluno matriculado
        $student = Student::where('unit_id', session('active_unit_id'))->latest()->first();

        if (!$student) {
            return view('student_portal.dashboard')->with('error', 'Nenhum perfil de aluno vinculado a esta conta.');
        }

        // Reutilizamos a lógica do Boletim (StudentController@show)
        $enrollments = \App\Domains\Enrollment\Models\Enrollment::with(['schoolClass.grade', 'schoolClass.shift'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['active', 'ativa'])
            ->get();
            
        $reportCards = [];

        foreach ($enrollments as $enrollment) {
            $classId = $enrollment->school_class_id;
            
            $assignments = \App\Domains\Academic\Models\TeacherAssignment::with('subject')
                ->where('school_class_id', $classId)
                ->get();
                
            $subjectsData = [];
            $totalClasses = 0;
            $totalAbsences = 0;
            
            foreach ($assignments as $assignment) {
                $subject = $assignment->subject;
                
                $totalScore = \App\Domains\Academic\Models\GradeEntry::where('student_id', $student->id)
                    ->whereHas('evaluation', function($q) use ($classId, $subject) {
                        $q->where('school_class_id', $classId)
                          ->where('subject_id', $subject->id);
                    })->sum('score');
                    
                $absences = \App\Domains\Academic\Models\AttendanceRecord::where('student_id', $student->id)
                    ->where('status', 'falta')
                    ->whereHas('lesson', function($q) use ($classId, $subject) {
                        $q->where('school_class_id', $classId)
                          ->where('subject_id', $subject->id);
                    })->count();
                    
                $lessonsCount = \App\Domains\Academic\Models\Lesson::where('school_class_id', $classId)
                    ->where('subject_id', $subject->id)
                    ->count();
                    
                $subjectsData[] = [
                    'subject_name' => $subject->name,
                    'total_score' => $totalScore,
                    'absences' => $absences,
                    'lessons_count' => $lessonsCount
                ];
                
                $totalClasses += $lessonsCount;
                $totalAbsences += $absences;
            }
            
            $globalAttendance = 100;
            if ($totalClasses > 0) {
                $globalAttendance = (($totalClasses - $totalAbsences) / $totalClasses) * 100;
            }
            
            $reportCards[] = [
                'enrollment' => $enrollment,
                'subjects' => $subjectsData,
                'global_attendance' => round($globalAttendance, 1),
                'total_absences' => $totalAbsences
            ];
        }

        return view('student_portal.dashboard', compact('student', 'reportCards'));
    }
}
