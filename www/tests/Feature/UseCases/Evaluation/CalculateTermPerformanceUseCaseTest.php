<?php

use App\Application\UseCases\Evaluation\CalculateTermPerformanceUseCase;
use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Subject;
use App\Domains\Academic\Models\Term;
use App\Domains\Attendance\Enums\AttendanceStatus;
use App\Domains\Attendance\Models\AttendanceRecord;
use App\Domains\Attendance\Models\Lesson;
use App\Domains\Enrollment\Models\Student;
use App\Domains\Evaluation\Enums\PerformanceStatus;
use App\Domains\Evaluation\Models\Evaluation;
use App\Domains\Evaluation\Models\EvaluationType;
use App\Domains\Evaluation\Models\GradeEntry;
use App\Domains\HR\Models\Employee;
use App\Domains\HR\Models\Teacher;
use App\Domains\Shared\Models\School;
use App\Domains\Shared\Models\Unit;
use App\Domains\Shared\Models\UnitSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('calculates term performance correctly for weighted average', function () {
    $school = School::create(['name' => 'Rede Teste', 'is_active' => true]);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste', 'is_active' => true]);

    UnitSetting::create([
        'unit_id' => $unit->id,
        'calculation_rule' => 'weighted',
        'passing_grade' => 6.0,
        'passing_attendance' => 75.0,
    ]);

    $term = Term::create(['unit_id' => $unit->id, 'name' => '1 Bimestre', 'start_date' => '2026-01-01', 'end_date' => '2026-03-31']);
    $schoolClass = SchoolClass::create(['unit_id' => $unit->id, 'name' => 'Turma A']);
    $subject = Subject::create(['unit_id' => $unit->id, 'name' => 'Matemática']);
    $student = Student::create(['unit_id' => $unit->id, 'name' => 'Chico Bento']);
    $employee = Employee::create(['unit_id' => $unit->id, 'name' => 'Prof', 'document' => '111']);
    $teacher = Teacher::create(['employee_id' => $employee->id]);
    
    $evalType = EvaluationType::create(['unit_id' => $unit->id, 'name' => 'Prova']);

    // Prova 1: Peso 2, Nota 5 (5*2 = 10)
    $eval1 = Evaluation::create([
        'unit_id' => $unit->id, 'school_class_id' => $schoolClass->id, 'subject_id' => $subject->id,
        'term_id' => $term->id, 'evaluation_type_id' => $evalType->id, 'teacher_id' => $teacher->id,
        'name' => 'Prova 1', 'date' => '2026-02-01', 'max_score' => 10.0, 'weight' => 2.0
    ]);
    GradeEntry::create(['evaluation_id' => $eval1->id, 'student_id' => $student->id, 'score' => 5.0]);

    // Prova 2: Peso 3, Nota 8 (8*3 = 24)
    $eval2 = Evaluation::create([
        'unit_id' => $unit->id, 'school_class_id' => $schoolClass->id, 'subject_id' => $subject->id,
        'term_id' => $term->id, 'evaluation_type_id' => $evalType->id, 'teacher_id' => $teacher->id,
        'name' => 'Prova 2', 'date' => '2026-03-01', 'max_score' => 10.0, 'weight' => 3.0
    ]);
    GradeEntry::create(['evaluation_id' => $eval2->id, 'student_id' => $student->id, 'score' => 8.0]);

    // Total Score = 34. Total Weight = 5. Average = 6.8. Status should be Aprovado.
    
    // Attendance: 1 Lesson, 1 Present
    $lesson = Lesson::create([
        'unit_id' => $unit->id, 'school_class_id' => $schoolClass->id, 'subject_id' => $subject->id,
        'teacher_id' => $teacher->id, 'date' => '2026-02-15'
    ]);
    AttendanceRecord::create(['lesson_id' => $lesson->id, 'student_id' => $student->id, 'status' => AttendanceStatus::Presente->value]);

    $useCase = new CalculateTermPerformanceUseCase();
    $performance = $useCase->execute($unit->id, $student->id, $schoolClass->id, $subject->id, $term->id);

    expect($performance->calculated_average)->toBe(6.8);
    expect($performance->attendance_percentage)->toBe(100.0);
    expect($performance->status)->toBe(PerformanceStatus::Aprovado->value);
});

it('reproves by attendance even if grade is 10', function () {
    $school = School::create(['name' => 'Rede Teste', 'is_active' => true]);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste', 'is_active' => true]);

    UnitSetting::create([
        'unit_id' => $unit->id,
        'calculation_rule' => 'simple',
        'passing_grade' => 6.0,
        'passing_attendance' => 75.0,
    ]);

    $term = Term::create(['unit_id' => $unit->id, 'name' => '1 Bimestre', 'start_date' => '2026-01-01', 'end_date' => '2026-03-31']);
    $schoolClass = SchoolClass::create(['unit_id' => $unit->id, 'name' => 'Turma A']);
    $subject = Subject::create(['unit_id' => $unit->id, 'name' => 'Matemática']);
    $student = Student::create(['unit_id' => $unit->id, 'name' => 'Chico Bento']);
    $employee = Employee::create(['unit_id' => $unit->id, 'name' => 'Prof', 'document' => '111']);
    $teacher = Teacher::create(['employee_id' => $employee->id]);
    
    $evalType = EvaluationType::create(['unit_id' => $unit->id, 'name' => 'Prova']);

    $eval1 = Evaluation::create([
        'unit_id' => $unit->id, 'school_class_id' => $schoolClass->id, 'subject_id' => $subject->id,
        'term_id' => $term->id, 'evaluation_type_id' => $evalType->id, 'teacher_id' => $teacher->id,
        'name' => 'Prova 1', 'date' => '2026-02-01', 'max_score' => 10.0, 'weight' => 1.0
    ]);
    GradeEntry::create(['evaluation_id' => $eval1->id, 'student_id' => $student->id, 'score' => 10.0]);

    // Attendance: 2 Lessons, 1 Present, 1 Absent (50% attendance)
    $lesson1 = Lesson::create([
        'unit_id' => $unit->id, 'school_class_id' => $schoolClass->id, 'subject_id' => $subject->id,
        'teacher_id' => $teacher->id, 'date' => '2026-02-15'
    ]);
    AttendanceRecord::create(['lesson_id' => $lesson1->id, 'student_id' => $student->id, 'status' => AttendanceStatus::Presente->value]);

    $lesson2 = Lesson::create([
        'unit_id' => $unit->id, 'school_class_id' => $schoolClass->id, 'subject_id' => $subject->id,
        'teacher_id' => $teacher->id, 'date' => '2026-02-16'
    ]);
    AttendanceRecord::create(['lesson_id' => $lesson2->id, 'student_id' => $student->id, 'status' => AttendanceStatus::Falta->value]);

    $useCase = new CalculateTermPerformanceUseCase();
    $performance = $useCase->execute($unit->id, $student->id, $schoolClass->id, $subject->id, $term->id);

    expect($performance->calculated_average)->toBe(10.0);
    expect($performance->attendance_percentage)->toBe(50.0);
    expect($performance->status)->toBe(PerformanceStatus::Reprovado->value);
});
