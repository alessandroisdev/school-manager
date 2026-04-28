<?php

namespace App\Application\UseCases\Auth;

use App\Domains\Auth\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LoginUseCase
{
    public function execute(string $email, string $password): User
    {
        if (!Auth::attempt(['email' => $email, 'password' => $password, 'is_active' => true])) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        $units = $user->units;
        if ($units->isNotEmpty()) {
            Session::put('current_unit_id', $units->first()->id);
        }

        return $user;
    }
}
