<?php

use App\Application\UseCases\Enrollment\ApprovePreEnrollmentUseCase;
use App\Domains\Academic\Models\AcademicYear;
use App\Domains\Academic\Models\Grade;
use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Shift;
use App\Domains\Enrollment\Enums\PreEnrollmentStatus;
use App\Domains\Enrollment\Models\PreEnrollment;
use App\Domains\Shared\Models\School;
use App\Domains\Shared\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('approves pre-enrollment and creates student, guardian, and enrollment', function () {
    $school = School::create(['name' => 'Rede Teste', 'is_active' => true]);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste', 'is_active' => true]);
    $academicYear = AcademicYear::create(['unit_id' => $unit->id, 'year' => 2026, 'start_date' => '2026-02-01', 'end_date' => '2026-12-15']);
    $grade = Grade::create(['unit_id' => $unit->id, 'name' => '1º Ano']);
    $shift = Shift::create(['unit_id' => $unit->id, 'name' => 'Matutino']);

    $schoolClass = SchoolClass::create([
        'unit_id' => $unit->id,
        'academic_year_id' => $academicYear->id,
        'grade_id' => $grade->id,
        'shift_id' => $shift->id,
        'name' => 'Turma A',
        'capacity' => 30,
    ]);

    $preEnrollment = PreEnrollment::create([
        'unit_id' => $unit->id,
        'academic_year_id' => $academicYear->id,
        'grade_id' => $grade->id,
        'student_name' => 'John Doe',
        'student_birth_date' => '2010-05-10',
        'guardian_name' => 'Jane Doe',
        'guardian_email' => 'jane@example.com',
        'guardian_phone' => '123456789',
        'status' => PreEnrollmentStatus::Pendente,
    ]);

    $useCase = new ApprovePreEnrollmentUseCase();
    $enrollment = $useCase->execute($preEnrollment, $schoolClass->id);

    expect($enrollment)->not->toBeNull();
    expect($enrollment->student->name)->toBe('John Doe');
    expect($enrollment->student->guardians->first()->name)->toBe('Jane Doe');
    
    $preEnrollment->refresh();
    expect($preEnrollment->status)->toBe(PreEnrollmentStatus::Aprovada);
});

it('throws exception if class capacity is reached', function () {
    $school = School::create(['name' => 'Rede Teste']);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste']);
    $academicYear = AcademicYear::create(['unit_id' => $unit->id, 'year' => 2026, 'start_date' => '2026-02-01', 'end_date' => '2026-12-15']);
    $grade = Grade::create(['unit_id' => $unit->id, 'name' => '1º Ano']);
    $shift = Shift::create(['unit_id' => $unit->id, 'name' => 'Matutino']);

    $schoolClass = SchoolClass::create([
        'unit_id' => $unit->id,
        'academic_year_id' => $academicYear->id,
        'grade_id' => $grade->id,
        'shift_id' => $shift->id,
        'name' => 'Turma Lotada',
        'capacity' => 1,
    ]);

    $preEnrollment1 = PreEnrollment::create([
        'unit_id' => $unit->id,
        'academic_year_id' => $academicYear->id,
        'grade_id' => $grade->id,
        'student_name' => 'John Doe',
        'student_birth_date' => '2010-05-10',
        'guardian_name' => 'Jane Doe',
        'status' => PreEnrollmentStatus::Pendente,
    ]);

    $preEnrollment2 = PreEnrollment::create([
        'unit_id' => $unit->id,
        'academic_year_id' => $academicYear->id,
        'grade_id' => $grade->id,
        'student_name' => 'Jane Doe Jr',
        'student_birth_date' => '2010-05-10',
        'guardian_name' => 'Jane Doe',
        'status' => PreEnrollmentStatus::Pendente,
    ]);

    $useCase = new ApprovePreEnrollmentUseCase();
    $useCase->execute($preEnrollment1, $schoolClass->id);

    $useCase->execute($preEnrollment2, $schoolClass->id);
})->throws(Exception::class, "Capacidade máxima da turma atingida.");
