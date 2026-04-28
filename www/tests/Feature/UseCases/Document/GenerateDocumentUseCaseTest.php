<?php

use App\Application\UseCases\Document\GenerateDocumentUseCase;
use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Document\Models\DocumentTemplate;
use App\Domains\Enrollment\Models\Student;
use App\Domains\Shared\Models\School;
use App\Domains\Shared\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('generates a document rendering blade template with student data', function () {
    $school = School::create(['name' => 'Rede Teste', 'is_active' => true]);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste', 'is_active' => true]);

    $schoolClass = SchoolClass::create(['unit_id' => $unit->id, 'name' => 'Turma A']);
    $student = Student::create(['unit_id' => $unit->id, 'name' => 'Chico Bento']);
    
    // Attach student to class
    $student->schoolClasses()->attach($schoolClass->id);

    // Create a template using blade syntax
    $template = DocumentTemplate::create([
        'unit_id' => $unit->id,
        'name' => 'Declaração de Matrícula',
        'type' => 'declaracao',
        'content' => 'Eu atesto que o(a) aluno(a) {{ $student->name }} estuda na turma {{ $schoolClass->name }}.'
    ]);

    $useCase = new GenerateDocumentUseCase();
    $document = $useCase->execute($unit->id, $template->id, $student->id);

    expect($document)->not->toBeNull();
    expect($document->title)->toBe('Declaração de Matrícula - Chico Bento');
    
    // The blade should have compiled the variables into the string
    expect($document->generated_content)->toBe('Eu atesto que o(a) aluno(a) Chico Bento estuda na turma Turma A.');
});
