<?php

use App\Application\UseCases\Academic\AssignScheduleUseCase;
use App\Domains\Academic\Models\AcademicYear;
use App\Domains\Academic\Models\Grade;
use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Shift;
use App\Domains\Academic\Models\Subject;
use App\Domains\Academic\Models\TeacherAssignment;
use App\Domains\Academic\Models\TimeSlot;
use App\Domains\HR\Models\Employee;
use App\Domains\HR\Models\Teacher;
use App\Domains\Shared\Models\School;
use App\Domains\Shared\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('assigns schedule successfully when there are no collisions', function () {
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

    $employee = Employee::create([
        'unit_id' => $unit->id,
        'name' => 'Prof. Raimundo',
        'document' => '11122233344',
        'position' => 'Professor',
    ]);
    
    $teacher = Teacher::create([
        'employee_id' => $employee->id,
        'specialty' => 'Matemática',
    ]);

    $subject = Subject::create([
        'unit_id' => $unit->id,
        'name' => 'Matemática',
    ]);

    $assignment = TeacherAssignment::create([
        'teacher_id' => $teacher->id,
        'school_class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'assigned_workload' => 4,
    ]);

    $timeSlot = TimeSlot::create([
        'unit_id' => $unit->id,
        'name' => '1ª Aula',
        'start_time' => '07:00',
        'end_time' => '07:50',
    ]);

    $useCase = new AssignScheduleUseCase();
    $schedule = $useCase->execute($assignment, $timeSlot->id, 1); // 1 = Monday

    expect($schedule)->not->toBeNull();
    expect($schedule->teacher_id)->toBe($teacher->id);
    expect($schedule->day_of_week)->toBe(1);
});

it('throws exception when teacher is already assigned at the same time', function () {
    $school = School::create(['name' => 'Rede Teste', 'is_active' => true]);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste', 'is_active' => true]);
    $academicYear = AcademicYear::create(['unit_id' => $unit->id, 'year' => 2026, 'start_date' => '2026-02-01', 'end_date' => '2026-12-15']);
    $grade = Grade::create(['unit_id' => $unit->id, 'name' => '1º Ano']);
    $shift = Shift::create(['unit_id' => $unit->id, 'name' => 'Matutino']);

    $schoolClassA = SchoolClass::create(['unit_id' => $unit->id, 'academic_year_id' => $academicYear->id, 'grade_id' => $grade->id, 'shift_id' => $shift->id, 'name' => 'Turma A']);
    $schoolClassB = SchoolClass::create(['unit_id' => $unit->id, 'academic_year_id' => $academicYear->id, 'grade_id' => $grade->id, 'shift_id' => $shift->id, 'name' => 'Turma B']);

    $employee = Employee::create(['unit_id' => $unit->id, 'name' => 'Prof. Raimundo', 'document' => '11122233344']);
    $teacher = Teacher::create(['employee_id' => $employee->id]);
    $subject = Subject::create(['unit_id' => $unit->id, 'name' => 'Matemática']);

    $assignmentA = TeacherAssignment::create(['teacher_id' => $teacher->id, 'school_class_id' => $schoolClassA->id, 'subject_id' => $subject->id]);
    $assignmentB = TeacherAssignment::create(['teacher_id' => $teacher->id, 'school_class_id' => $schoolClassB->id, 'subject_id' => $subject->id]);

    $timeSlot = TimeSlot::create(['unit_id' => $unit->id, 'name' => '1ª Aula', 'start_time' => '07:00', 'end_time' => '07:50']);

    $useCase = new AssignScheduleUseCase();
    $useCase->execute($assignmentA, $timeSlot->id, 1);

    // Should fail
    $useCase->execute($assignmentB, $timeSlot->id, 1);
})->throws(Exception::class, "Colisão de Horário: O professor já está alocado em outra turma neste horário.");
