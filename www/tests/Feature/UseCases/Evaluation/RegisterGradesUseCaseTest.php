<?php

use App\Application\UseCases\Evaluation\RegisterGradesUseCase;
use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Subject;
use App\Domains\Evaluation\Models\Evaluation;
use App\Domains\Evaluation\Models\EvaluationType;
use App\Domains\Enrollment\Models\Student;
use App\Domains\HR\Models\Employee;
use App\Domains\HR\Models\Teacher;
use App\Domains\Shared\Models\School;
use App\Domains\Shared\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers grades for an evaluation successfully', function () {
    $school = School::create(['name' => 'Rede Teste', 'is_active' => true]);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste', 'is_active' => true]);

    $schoolClass = SchoolClass::create(['unit_id' => $unit->id, 'name' => 'Turma A']);
    $subject = Subject::create(['unit_id' => $unit->id, 'name' => 'Matemática']);
    $employee = Employee::create(['unit_id' => $unit->id, 'name' => 'Prof. Raimundo', 'document' => '111']);
    $teacher = Teacher::create(['employee_id' => $employee->id]);

    $evalType = EvaluationType::create(['unit_id' => $unit->id, 'name' => 'Prova Bimestral']);

    $evaluation = Evaluation::create([
        'unit_id' => $unit->id,
        'school_class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'evaluation_type_id' => $evalType->id,
        'teacher_id' => $teacher->id,
        'name' => 'Prova 1',
        'date' => '2026-04-28',
        'max_score' => 10.0,
    ]);

    $student1 = Student::create(['unit_id' => $unit->id, 'name' => 'Chico Bento']);
    $student2 = Student::create(['unit_id' => $unit->id, 'name' => 'Rosinha']);

    $gradesData = [
        ['student_id' => $student1->id, 'score' => 8.5, 'feedback' => 'Muito bom!'],
        ['student_id' => $student2->id, 'score' => 9.5],
    ];

    $useCase = new RegisterGradesUseCase();
    $result = $useCase->execute($evaluation->id, $gradesData);

    expect($result)->toBeTrue();
    expect($evaluation->gradeEntries()->count())->toBe(2);
    expect($evaluation->gradeEntries()->where('student_id', $student1->id)->first()->score)->toBe(8.5);
});

it('throws exception if score is greater than max_score', function () {
    $school = School::create(['name' => 'Rede Teste']);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste']);

    $schoolClass = SchoolClass::create(['unit_id' => $unit->id, 'name' => 'Turma A']);
    $subject = Subject::create(['unit_id' => $unit->id, 'name' => 'Matemática']);
    $employee = Employee::create(['unit_id' => $unit->id, 'name' => 'Prof. Raimundo', 'document' => '111']);
    $teacher = Teacher::create(['employee_id' => $employee->id]);

    $evalType = EvaluationType::create(['unit_id' => $unit->id, 'name' => 'Prova Bimestral']);

    $evaluation = Evaluation::create([
        'unit_id' => $unit->id,
        'school_class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
        'evaluation_type_id' => $evalType->id,
        'teacher_id' => $teacher->id,
        'name' => 'Prova 1',
        'date' => '2026-04-28',
        'max_score' => 10.0,
    ]);

    $student1 = Student::create(['unit_id' => $unit->id, 'name' => 'Chico Bento']);

    $gradesData = [
        ['student_id' => $student1->id, 'score' => 11.0], // max is 10.0
    ];

    $useCase = new RegisterGradesUseCase();
    $useCase->execute($evaluation->id, $gradesData);
})->throws(Exception::class, "A nota informada (11) ultrapassa a nota máxima da avaliação (10).");
