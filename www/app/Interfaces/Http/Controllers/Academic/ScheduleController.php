<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Application\UseCases\Academic\AssignScheduleUseCase;
use App\Domains\Academic\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;

class ScheduleController extends Controller
{
    public function __construct(
        protected AssignScheduleUseCase $assignScheduleUseCase
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_assignment_id' => 'required|exists:teacher_assignments,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'day_of_week' => 'required|integer|min:0|max:6',
        ]);

        try {
            $assignment = TeacherAssignment::with(['schoolClass', 'subject'])->findOrFail($validated['teacher_assignment_id']);
            $schedule = $this->assignScheduleUseCase->execute(
                $assignment,
                $validated['time_slot_id'],
                $validated['day_of_week']
            );

            return response()->json(['message' => 'Horário agendado com sucesso!', 'schedule' => $schedule], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
