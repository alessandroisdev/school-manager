<?php

use App\Application\UseCases\Attendance\RegisterAttendanceUseCase;
use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Subject;
use App\Domains\Attendance\Enums\AttendanceStatus;
use App\Domains\Attendance\Models\Lesson;
use App\Domains\Enrollment\Models\Student;
use App\Domains\HR\Models\Employee;
use App\Domains\HR\Models\Teacher;
use App\Domains\Shared\Models\School;
use App\Domains\Shared\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers attendance for a class successfully', function () {
    $school = School::create(['name' => 'Rede Teste', 'is_active' => true]);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste', 'is_active' => true]);

    $schoolClass = SchoolClass::create(['unit_id' => $unit->id, 'name' => 'Turma A']);
    $subject = Subject::create(['unit_id' => $unit->id, 'name' => 'Matemática']);
    $employee = Employee::create(['unit_id' => $unit->id, 'name' => 'Prof. Raimundo', 'document' => '111']);
    $teacher = Teacher::create(['employee_id' => $employee->id]);

    $student1 = Student::create(['unit_id' => $unit->id, 'name' => 'Chico Bento']);
    $student2 = Student::create(['unit_id' => $unit->id, 'name' => 'Rosinha']);

    $lessonData = [
        'unit_id' => $unit->id,
        'school_class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'date' => '2026-04-28',
        'notes' => 'Aula sobre frações',
    ];

    $recordsData = [
        ['student_id' => $student1->id, 'status' => AttendanceStatus::Presente->value],
        ['student_id' => $student2->id, 'status' => AttendanceStatus::Falta->value],
    ];

    $useCase = new RegisterAttendanceUseCase();
    $lesson = $useCase->execute($lessonData, $recordsData);

    expect($lesson)->not->toBeNull();
    expect($lesson->attendanceRecords()->count())->toBe(2);
    expect($lesson->attendanceRecords()->where('student_id', $student2->id)->first()->status->value)->toBe('falta');
});

it('prevents registering duplicate lesson on the same day for the same class and subject', function () {
    $school = School::create(['name' => 'Rede Teste', 'is_active' => true]);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste', 'is_active' => true]);

    $schoolClass = SchoolClass::create(['unit_id' => $unit->id, 'name' => 'Turma A']);
    $subject = Subject::create(['unit_id' => $unit->id, 'name' => 'Matemática']);
    $employee = Employee::create(['unit_id' => $unit->id, 'name' => 'Prof. Raimundo', 'document' => '111']);
    $teacher = Teacher::create(['employee_id' => $employee->id]);

    $lessonData = [
        'unit_id' => $unit->id,
        'school_class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'date' => '2026-04-28',
        'notes' => 'Aula 1',
    ];

    $useCase = new RegisterAttendanceUseCase();
    
    // First time succeeds
    $useCase->execute($lessonData, []);

    // Second time fails
    $useCase->execute($lessonData, []);
})->throws(Exception::class, "A chamada para esta disciplina e turma já foi registrada nesta data.");
