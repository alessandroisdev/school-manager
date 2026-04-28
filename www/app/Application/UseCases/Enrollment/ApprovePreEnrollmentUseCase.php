<?php

namespace App\Application\UseCases\Enrollment;

use App\Domains\Enrollment\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Enums\PreEnrollmentStatus;
use App\Domains\Enrollment\Models\Enrollment;
use App\Domains\Enrollment\Models\Guardian;
use App\Domains\Enrollment\Models\PreEnrollment;
use App\Domains\Enrollment\Models\Student;
use Illuminate\Support\Facades\DB;
use Exception;

class ApprovePreEnrollmentUseCase
{
    public function execute(PreEnrollment $preEnrollment, int $schoolClassId): Enrollment
    {
        if ($preEnrollment->status !== PreEnrollmentStatus::Pendente) {
            throw new Exception("Esta pré-matrícula não está pendente.");
        }

        return DB::transaction(function () use ($preEnrollment, $schoolClassId) {
            $schoolClass = \App\Domains\Academic\Models\SchoolClass::lockForUpdate()->findOrFail($schoolClassId);
            
            if ($schoolClass->enrollments()->count() >= $schoolClass->capacity) {
                throw new Exception("Capacidade máxima da turma atingida.");
            }

            $student = Student::create([
                'unit_id' => $preEnrollment->unit_id,
                'name' => $preEnrollment->student_name,
                'birth_date' => $preEnrollment->student_birth_date,
            ]);

            $guardian = Guardian::create([
                'name' => $preEnrollment->guardian_name,
                'email' => $preEnrollment->guardian_email,
                'phone' => $preEnrollment->guardian_phone,
            ]);

            $student->guardians()->attach($guardian->id, ['relationship' => 'Responsável Legal']);

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'school_class_id' => $schoolClassId,
                'status' => EnrollmentStatus::Ativa,
            ]);

            $preEnrollment->update(['status' => PreEnrollmentStatus::Aprovada]);

            return $enrollment;
        });
    }
}
