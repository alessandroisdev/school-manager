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
            // 1. Criar Aluno
            $student = Student::create([
                'unit_id' => $unit->id,
                'name' => $faker->name(),
                'document' => $faker->cpf(false),
                'birth_date' => $faker->dateTimeBetween('-15 years', '-6 years')->format('Y-m-d'),
            ]);

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
