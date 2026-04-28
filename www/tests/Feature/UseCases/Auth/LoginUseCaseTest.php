<?php

use App\Application\UseCases\Auth\LoginUseCase;
use App\Domains\Auth\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('authenticates a valid user successfully', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'is_active' => true,
    ]);

    $useCase = new LoginUseCase();
    $loggedInUser = $useCase->execute($user->email, 'password');

    expect($loggedInUser->id)->toBe($user->id);
});

it('throws validation exception for invalid credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $useCase = new LoginUseCase();
    $useCase->execute($user->email, 'wrong-password');
})->throws(ValidationException::class);
