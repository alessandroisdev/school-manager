<?php

use App\Application\UseCases\HR\HireTeacherUseCase;
use App\Domains\Shared\Models\School;
use App\Domains\Shared\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('hires a teacher and creates an employee without user', function () {
    $school = School::create(['name' => 'Rede Teste', 'is_active' => true]);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste', 'is_active' => true]);

    $data = [
        'unit_id' => $unit->id,
        'name' => 'Professor Girafales',
        'document' => '12345678901',
        'create_user' => false,
        'specialty' => 'Matemática',
        'max_workload' => 40,
    ];

    $useCase = new HireTeacherUseCase();
    $teacher = $useCase->execute($data);

    expect($teacher)->not->toBeNull();
    expect($teacher->employee->name)->toBe('Professor Girafales');
    expect($teacher->employee->user_id)->toBeNull();
    expect($teacher->specialty)->toBe('Matemática');
});

it('hires a teacher and creates a user when requested', function () {
    $school = School::create(['name' => 'Rede Teste']);
    $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste']);

    $data = [
        'unit_id' => $unit->id,
        'name' => 'Professor Xavier',
        'document' => '98765432100',
        'email' => 'xavier@mutants.com',
        'create_user' => true,
        'specialty' => 'Física',
    ];

    $useCase = new HireTeacherUseCase();
    $teacher = $useCase->execute($data);

    expect($teacher->employee->user_id)->not->toBeNull();
    expect($teacher->employee->user->email)->toBe('xavier@mutants.com');
});
