<?php

namespace App\Interfaces\Http\Controllers\Enrollment;

use App\Application\UseCases\Enrollment\ApprovePreEnrollmentUseCase;
use App\Domains\Enrollment\Models\PreEnrollment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;

class PreEnrollmentController extends Controller
{
    public function __construct(
        protected ApprovePreEnrollmentUseCase $approveUseCase
    ) {}

    public function index()
    {
        $preEnrollments = PreEnrollment::with(['academicYear', 'grade'])->paginate(15);
        return response()->json($preEnrollments);
    }

    public function approve(Request $request, PreEnrollment $preEnrollment)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
        ]);

        try {
            $enrollment = $this->approveUseCase->execute($preEnrollment, $request->school_class_id);
            return response()->json(['message' => 'Matrícula aprovada com sucesso!', 'enrollment' => $enrollment]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
