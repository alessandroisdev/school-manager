<?php

namespace App\Application\UseCases\HR;

use App\Domains\HR\Models\Employee;
use App\Domains\HR\Models\Teacher;
use App\Domains\Auth\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HireTeacherUseCase
{
    public function execute(array $data): Teacher
    {
        return DB::transaction(function () use ($data) {
            $user = null;

            if ($data['create_user'] ?? false) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password'] ?? 'senha_padrao_123'),
                ]);
            }

            $employee = Employee::create([
                'unit_id' => $data['unit_id'],
                'user_id' => $user?->id,
                'name' => $data['name'],
                'document' => $data['document'],
                'position' => 'Professor',
                'hire_date' => $data['hire_date'] ?? now(),
            ]);

            $teacher = Teacher::create([
                'employee_id' => $employee->id,
                'specialty' => $data['specialty'] ?? null,
                'max_workload' => $data['max_workload'] ?? 40,
            ]);

            return $teacher;
        });
    }
}
