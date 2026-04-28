<?php

namespace App\Interfaces\Http\Controllers\Academic;

use App\Domains\Academic\Models\SchoolClass;
use Illuminate\Routing\Controller;

class SchoolClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::with(['academicYear', 'grade', 'shift'])->paginate(15);
        return response()->json($classes);
    }

    public function show(SchoolClass $schoolClass)
    {
        $schoolClass->load(['academicYear', 'grade', 'shift', 'enrollments.student']);
        return response()->json($schoolClass);
    }
}
