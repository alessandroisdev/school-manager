<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TimeSlotController extends Controller
{
    public function index()
    {
        $slots = TimeSlot::orderBy('start_time')->get();
        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'name' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $slot = TimeSlot::create($validated);
        return response()->json($slot, 201);
    }
}
