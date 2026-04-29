<?php

namespace Database\Seeders;

use App\Domains\Auth\Enums\DefaultRoles;
use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use App\Domains\Shared\Models\School;
use App\Domains\Shared\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        foreach (DefaultRoles::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value]);
        }

        $adminRole = Role::where('name', 'admin')->first();

        $admin = User::firstOrCreate(
            ['email' => 'admin@schoolmanager.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('123456789'),
                'is_active' => true,
            ]
        );

        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        $school = School::firstOrCreate(
            ['document' => '00000000000000'],
            [
                'name' => 'Rede Principal SGE',
                'email' => 'contato@schoolmanager.com',
                'is_active' => true,
            ]
        );

        $unit = Unit::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Unidade Sede'],
            [
                'document' => '11111111111111',
                'email' => 'sede@schoolmanager.com',
                'is_active' => true,
            ]
        );

        $admin->units()->syncWithoutDetaching([$unit->id]);

        \App\Domains\Academic\Models\AcademicYear::firstOrCreate(
            ['unit_id' => $unit->id, 'year' => date('Y')],
            [
                'start_date' => date('Y-02-01'),
                'end_date' => date('Y-12-15'),
                'is_active' => true,
            ]
        );
    }
}
