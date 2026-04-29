<?php

namespace Database\Seeders;

use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Enrollment\Models\Enrollment;
use App\Domains\Enrollment\Models\Student;
use App\Domains\Finance\Models\Invoice;
use App\Domains\Shared\Models\Unit;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $unit = Unit::first();

        if (!$unit) {
            $this->command->error('Nenhuma unidade encontrada. Rode o InitialSetupSeeder primeiro.');
            return;
        }

        // --- SETUP DEMO USERS ---
        $this->command->info('Criando usuários de demonstração (Super Admin, Diretor, Secretaria, Professor, Aluno, Responsável)...');

        $roles = [
            'admin' => 'Administrador Global',
            'diretor' => 'Direção',
            'secretaria' => 'Secretaria',
            'professor' => 'Professor',
            'aluno' => 'Aluno',
            'responsavel' => 'Responsável'
        ];
        
        foreach ($roles as $slug => $name) {
            \App\Domains\Auth\Models\Role::firstOrCreate(['name' => $slug]);
        }

        $demoUsers = [
            [
                'name' => 'Alessandro (Super Admin)',
                'email' => 'admin@schoolmanager.com',
                'username' => 'admin',
                'password' => \Illuminate\Support\Facades\Hash::make('123456789'),
                'role' => 'admin'
            ],
            [
                'name' => 'Diretor Escolar',
                'email' => 'direcao@schoolmanager.com',
                'username' => 'diretor',
                'password' => \Illuminate\Support\Facades\Hash::make('123456789'),
                'role' => 'diretor'
            ],
            [
                'name' => 'Secretaria Acadêmica',
                'email' => 'secretaria@schoolmanager.com',
                'username' => 'secretaria',
                'password' => \Illuminate\Support\Facades\Hash::make('123456789'),
                'role' => 'secretaria'
            ],
            [
                'name' => 'Professor Teste',
                'email' => 'professor@schoolmanager.com',
                'username' => 'professor',
                'password' => \Illuminate\Support\Facades\Hash::make('123456789'),
                'role' => 'professor'
            ]
        ];

        foreach ($demoUsers as $data) {
            $user = \App\Domains\Auth\Models\User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'password' => $data['password'],
                    'is_active' => true
                ]
            );
            $role = \App\Domains\Auth\Models\Role::where('name', $data['role'])->first();
            if (!$user->roles->contains($role->id)) {
                $user->roles()->attach($role->id);
            }
            if (!$user->units->contains($unit->id)) {
                $user->units()->attach($unit->id);
            }
        }

        // Verifica se há alguma turma, se não houver, cria uma fake
        $schoolClass = SchoolClass::where('unit_id', $unit->id)->first();
        if (!$schoolClass) {
            $grade = \App\Domains\Academic\Models\Grade::firstOrCreate(['unit_id' => $unit->id, 'name' => '1º Ano (Demo)']);
            $shift = \App\Domains\Academic\Models\Shift::firstOrCreate(['unit_id' => $unit->id, 'name' => 'Matutino (Demo)'], ['start_time' => '07:30', 'end_time' => '12:00']);
            $year = \App\Domains\Academic\Models\AcademicYear::where('unit_id', $unit->id)->first();

            $schoolClass = SchoolClass::create([
                'unit_id' => $unit->id,
                'academic_year_id' => $year->id,
                'grade_id' => $grade->id,
                'shift_id' => $shift->id,
                'name' => 'Turma A (Demo)',
                'capacity' => 50
            ]);
        }

        $this->command->info('Iniciando geração de 50 Alunos Fakes...');

        for ($i = 0; $i < 50; $i++) {
            $isDemoUser = ($i === 0);
            
            // 1. Criar Aluno
            $registration = date('Y') . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $studentDocument = $isDemoUser ? '00011122233' : $faker->cpf(false);
            
            $studentUser = \App\Domains\Auth\Models\User::firstOrCreate(
                ['username' => $registration],
                [
                    'name' => $isDemoUser ? 'Aluno Teste' : $faker->name(),
                    'email' => $isDemoUser ? 'aluno@schoolmanager.com' : $faker->unique()->safeEmail(),
                    'password' => \Illuminate\Support\Facades\Hash::make($studentDocument)
                ]
            );
            $studentRole = \App\Domains\Auth\Models\Role::where('name', 'aluno')->first();
            $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);
            $studentUser->units()->syncWithoutDetaching([$unit->id]);

            $student = Student::create([
                'unit_id' => $unit->id,
                'user_id' => $studentUser->id,
                'name' => $studentUser->name,
                'registration' => $registration,
                'document' => $studentDocument,
                'birth_date' => $faker->dateTimeBetween('-15 years', '-6 years')->format('Y-m-d'),
                'email' => $studentUser->email,
            ]);

            // Pai/Responsavel
            $guardianDocument = $isDemoUser ? '99988877766' : $faker->cpf(false);
            $guardianUser = \App\Domains\Auth\Models\User::firstOrCreate(
                ['username' => $guardianDocument],
                [
                    'name' => $isDemoUser ? 'Pai do Aluno Teste' : 'Resp. ' . $studentUser->name,
                    'email' => $isDemoUser ? 'pai@schoolmanager.com' : null,
                    'password' => \Illuminate\Support\Facades\Hash::make($guardianDocument)
                ]
            );
            $guardianRole = \App\Domains\Auth\Models\Role::where('name', 'responsavel')->first();
            $guardianUser->roles()->syncWithoutDetaching([$guardianRole->id]);
            $guardianUser->units()->syncWithoutDetaching([$unit->id]);

            $guardian = \App\Domains\Enrollment\Models\Guardian::create([
                'user_id' => $guardianUser->id,
                'name' => $guardianUser->name,
                'document' => $guardianDocument,
                'email' => $guardianUser->email,
                'phone' => $faker->cellphoneNumber(),
            ]);

            $student->guardians()->attach($guardian->id, ['relationship' => 'Pai']);

            // 2. Matricular na turma existente
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'school_class_id' => $schoolClass->id,
                'status' => 'active',
            ]);

            // 3. Gerar 6 mensalidades para o aluno
            for ($month = 1; $month <= 6; $month++) {
                $dueDate = \Carbon\Carbon::now()->startOfYear()->addMonths($month - 1)->setDay(5);
                $status = 'pending';
                $paidAt = null;

                // Faturas passadas tem 80% de chance de estarem pagas
                if ($dueDate->isPast() && rand(1, 100) <= 80) {
                    $status = 'paid';
                    $paidAt = clone $dueDate;
                    $paidAt->subDays(rand(1, 5));
                }

                Invoice::create([
                    'unit_id' => $unit->id,
                    'student_id' => $student->id,
                    'enrollment_id' => $enrollment->id,
                    'amount' => 850.00,
                    'due_date' => $dueDate,
                    'status' => $status,
                    'paid_at' => $paidAt,
                ]);
            }
        }

        $this->command->info('Dados de demonstração gerados com sucesso!');
    }
}
