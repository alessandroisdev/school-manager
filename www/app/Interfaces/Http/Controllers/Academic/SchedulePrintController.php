<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\Schedule;
use App\Domains\HR\Models\Teacher;
use App\Domains\Shared\Models\UnitSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SchedulePrintController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');
        $teacherId = $request->input('teacher_id'); // Se for impressão individual

        $settings = UnitSetting::where('unit_id', $unitId)->first();

        $query = Schedule::with(['teacher.employee', 'schoolClass.grade', 'timeSlot', 'teacherAssignment.subject'])
            ->where('unit_id', $unitId)
            ->where('status', 'published');

        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        $schedules = $query->get();

        // Agrupar por Turma para o cronograma geral
        $groupedByClass = $schedules->groupBy('school_class_id');

        // Agrupar por Professor para cronograma individual
        $groupedByTeacher = $schedules->groupBy('teacher_id');

        return view('academic.assignments.print', compact('settings', 'groupedByClass', 'groupedByTeacher', 'teacherId'));
    }
}
