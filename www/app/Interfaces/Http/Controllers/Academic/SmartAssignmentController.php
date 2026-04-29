<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\Grade;
use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Enrollment\Models\Enrollment;
use App\Domains\Enrollment\Models\Student;
use App\Domains\Shared\Models\UnitSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SmartAssignmentController extends Controller
{
    public function index()
    {
        $unitId = session('active_unit_id');

        // Busca alunos ATIVOS que NÃO possuem matrícula ativa
        $unenrolledStudents = Student::where('status', 'active')
            ->whereDoesntHave('enrollments', function($q) {
                $q->whereIn('status', ['active', 'ativa']);
            })
            ->get();

        $grades = Grade::with(['classes' => function($q) {
            $q->withCount(['enrollments' => function($q2) {
                $q2->whereIn('status', ['active', 'ativa']);
            }]);
        }])->get();

        return view('academic.smart_tools.index', compact('unenrolledStudents', 'grades'));
    }

    public function autoEnroll(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'grade_id' => 'required|exists:grades,id'
        ]);

        $unitId = session('active_unit_id');
        $studentIds = $validated['student_ids'];
        $gradeId = $validated['grade_id'];

        // Pega as turmas da série escolhida
        $classes = SchoolClass::where('grade_id', $gradeId)
            ->withCount(['enrollments' => function($q) {
                $q->whereIn('status', ['active', 'ativa']);
            }])
            ->get();

        if ($classes->isEmpty()) {
            return back()->withErrors('Não existem turmas criadas para esta série.');
        }

        // Pega limite padrao das configuracoes se a turma nao tiver
        $defaultCapacity = UnitSetting::where('unit_id', $unitId)->where('key', 'default_class_capacity')->value('value') ?? 30;

        $logs = [];
        $enrolledCount = 0;

        DB::transaction(function() use ($studentIds, $classes, $defaultCapacity, &$logs, &$enrolledCount) {
            // Algoritmo Round-Robin para balanceamento homogêneo
            foreach ($studentIds as $studentId) {
                // Ordena turmas pela que tem MENOS alunos no momento
                $classes = $classes->sortBy('enrollments_count');
                
                $targetClass = $classes->first();
                $classCapacity = $targetClass->capacity ?? $defaultCapacity;

                if ($targetClass->enrollments_count < $classCapacity) {
                    Enrollment::create([
                        'student_id' => $studentId,
                        'school_class_id' => $targetClass->id,
                        'enrollment_date' => now(),
                        'status' => 'active',
                    ]);

                    $targetClass->enrollments_count++;
                    $enrolledCount++;
                } else {
                    $logs[] = "Aluno ID $studentId não foi enturmado pois todas as turmas atingiram a capacidade máxima.";
                }
            }
        });

        if (count($logs) > 0) {
            return redirect()->back()->with('warning', "$enrolledCount alunos enturmados. Alguns alunos não couberam nas turmas existentes.");
        }

        return redirect()->back()->with('success', "Sucesso! A IA enturmou e balanceou $enrolledCount alunos nas turmas!");
    }
}
