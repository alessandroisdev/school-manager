<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domains\Shared\Models\School;
use App\Domains\Shared\Models\Unit;
use App\Domains\Shared\Models\UnitSetting;
use App\Domains\Academic\Models\Grade;
use App\Domains\Academic\Models\Shift;
use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Subject;
use App\Domains\Auth\Models\User;
use App\Domains\HR\Models\Teacher;
use App\Domains\Academic\Models\TeacherAssignment;
use App\Domains\Enrollment\Models\Student;
use App\Domains\Enrollment\Models\Enrollment;
use App\Domains\Finance\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class CompleteSchoolSeeder extends Seeder
{
    public function run(): void
    {
        // Criação de Escolas e Unidades Fictícias Profissionais
        $school = School::firstOrCreate([
            'document' => '11.111.111/0001-11'
        ], [
            'name' => 'Colégio SGE'
        ]);

        $unitFundamental = Unit::create([
            'school_id' => $school->id,
            'name' => 'SGE - Unidade Fundamental',
            'document' => '11.111.111/0002-22',
            'email' => 'fundamental@colegiosge.com',
            'phone' => '(11) 3333-4444',
            'address' => 'Avenida Principal, 1000',
            'city' => 'São Paulo',
            'state' => 'SP'
        ]);

        $unitMedio = Unit::create([
            'school_id' => $school->id,
            'name' => 'SGE - Unidade Ensino Médio',
            'document' => '11.111.111/0003-33',
            'email' => 'medio@colegiosge.com',
            'phone' => '(11) 3333-5555',
            'address' => 'Rua dos Estudantes, 500',
            'city' => 'São Paulo',
            'state' => 'SP'
        ]);

        // Vincula as unidades ao Admin
        $admin = User::where('email', 'admin@schoolmanager.com')->first();
        if ($admin) {
            $admin->units()->syncWithoutDetaching([$unitFundamental->id, $unitMedio->id]);
        }

        // Criar Anos Letivos
        $yearFundamental = \App\Domains\Academic\Models\AcademicYear::create([
            'unit_id' => $unitFundamental->id,
            'year' => date('Y'),
            'start_date' => date('Y-02-01'),
            'end_date' => date('Y-12-15'),
            'is_active' => true
        ]);

        $yearMedio = \App\Domains\Academic\Models\AcademicYear::create([
            'unit_id' => $unitMedio->id,
            'year' => date('Y'),
            'start_date' => date('Y-02-01'),
            'end_date' => date('Y-12-15'),
            'is_active' => true
        ]);

        // Configurações
        foreach ([$unitFundamental, $unitMedio] as $unit) {
            UnitSetting::create([
                'unit_id' => $unit->id,
                'calculation_rule' => 'simple',
                'passing_grade' => 6.00,
                'passing_attendance' => 75.00,
                'default_class_capacity' => 40,
                'current_academic_year' => date('Y'),
                'default_due_day' => 5,
                'late_fee_interest' => 2.00
            ]);
        }

        // Turnos Fundamental
        $shiftMorningF = Shift::create(['unit_id' => $unitFundamental->id, 'name' => 'Manhã', 'start_time' => '07:00', 'end_time' => '12:00']);
        $shiftAfternoonF = Shift::create(['unit_id' => $unitFundamental->id, 'name' => 'Tarde', 'start_time' => '13:00', 'end_time' => '18:00']);
        // Turnos Médio
        $shiftMorningM = Shift::create(['unit_id' => $unitMedio->id, 'name' => 'Manhã', 'start_time' => '07:00', 'end_time' => '12:30']);

        // ========== FUNDAMENTAL ========== //
        $gradesFundamental = [];
        for ($i = 6; $i <= 9; $i++) {
            $gradesFundamental[] = Grade::create(['unit_id' => $unitFundamental->id, 'name' => $i . 'º Ano Fundamental']);
        }

        // Turmas Fundamental
        $classesFundamental = [];
        foreach ($gradesFundamental as $grade) {
            $classesFundamental[] = SchoolClass::create([
                'unit_id' => $unitFundamental->id,
                'academic_year_id' => $yearFundamental->id,
                'grade_id' => $grade->id,
                'shift_id' => $shiftMorningF->id,
                'name' => 'Turma A',
                'capacity' => 35
            ]);
            $classesFundamental[] = SchoolClass::create([
                'unit_id' => $unitFundamental->id,
                'academic_year_id' => $yearFundamental->id,
                'grade_id' => $grade->id,
                'shift_id' => $shiftAfternoonF->id,
                'name' => 'Turma B',
                'capacity' => 35
            ]);
        }

        // ========== ENSINO MÉDIO ========== //
        $gradesMedio = [];
        for ($i = 1; $i <= 3; $i++) {
            $gradesMedio[] = Grade::create(['unit_id' => $unitMedio->id, 'name' => $i . 'ª Série Ensino Médio']);
        }

        // Turmas Médio
        $classesMedio = [];
        foreach ($gradesMedio as $grade) {
            $classesMedio[] = SchoolClass::create([
                'unit_id' => $unitMedio->id,
                'academic_year_id' => $yearMedio->id,
                'grade_id' => $grade->id,
                'shift_id' => $shiftMorningM->id,
                'name' => 'Turma A',
                'capacity' => 40
            ]);
        }

        // Disciplinas
        $subjects = ['Matemática', 'Português', 'História', 'Geografia', 'Ciências/Biologia', 'Física', 'Química'];
        $subjectModels = [];
        foreach ($subjects as $sub) {
            $subjectModels[] = Subject::create(['unit_id' => $unitFundamental->id, 'name' => $sub]);
            $subjectModels[] = Subject::create(['unit_id' => $unitMedio->id, 'name' => $sub]);
        }

        // Professores (5 professores)
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Professor Teste $i",
                'email' => "prof$i@escola.com",
                'password' => Hash::make('password'),
            ]);
            // Associa às unidades
            $user->units()->sync([$unitFundamental->id, $unitMedio->id]);
            
            // Assume the role professor exists in db, skipping role assignment for brevity or doing it raw
            $roleId = DB::table('roles')->where('name', 'professor')->value('id');
            if ($roleId) {
                DB::table('role_user')->insert(['role_id' => $roleId, 'user_id' => $user->id]);
            }

            $employee = \App\Domains\HR\Models\Employee::create([
                'unit_id' => $unitFundamental->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'document' => '1112223334' . $i,
                'position' => 'Professor',
                'is_active' => true
            ]);

            $teacher = Teacher::create([
                'employee_id' => $employee->id,
                'specialty' => 'Geral'
            ]);

            // Aloca professor em Turmas e Disciplinas
            foreach (array_merge($classesFundamental, $classesMedio) as $class) {
                TeacherAssignment::create([
                    'teacher_id' => $teacher->id,
                    'school_class_id' => $class->id,
                    'subject_id' => $subjectModels[array_rand($subjectModels)]->id,
                    'assigned_workload' => 40
                ]);
            }
        }

        // ALUNOS & MATRÍCULAS & FINANCEIRO (Gerando 200 alunos)
        $faker = \Faker\Factory::create('pt_BR');

        $allClasses = array_merge($classesFundamental, $classesMedio);

        for ($i = 1; $i <= 200; $i++) {
            $class = $allClasses[array_rand($allClasses)];
            $unit_id = $class->unit_id;

            $student = Student::create([
                'unit_id' => $unit_id,
                'name' => $faker->name,
                'birth_date' => $faker->date('Y-m-d', '-10 years'),
                'status' => 'active',
            ]);

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'school_class_id' => $class->id,
                'status' => \App\Domains\Enrollment\Enums\EnrollmentStatus::Ativa,
            ]);

            // Gera faturas (Boletos)
            for ($month = 1; $month <= 12; $month++) {
                $dueDate = Carbon::create(date('Y'), $month, 5);
                $status = 'pending';
                $paidAt = null;

                if ($dueDate->isPast()) {
                    $status = 'paid';
                    $paidAt = $dueDate->copy()->subDays(rand(1, 4));
                }

                // Simula 10% de inadimplência no mês atual/passado recente
                if ($month == date('n') || $month == date('n') - 1) {
                    if (rand(1, 10) == 1) {
                        $status = 'overdue';
                        $paidAt = null;
                    }
                }

                Invoice::create([
                    'unit_id' => $unit_id,
                    'student_id' => $student->id,
                    'enrollment_id' => $enrollment->id,
                    'amount' => $unit_id == $unitFundamental->id ? 850.00 : 1200.00,
                    'due_date' => $dueDate,
                    'status' => $status,
                    'paid_at' => $paidAt,
                    'description' => "Mensalidade " . str_pad($month, 2, '0', STR_PAD_LEFT) . "/" . date('Y'),
                    'payment_method' => 'boleto'
                ]);
            }
        }

        echo "✅ CompleteSchoolSeeder executado! 2 Unidades, 200 Alunos, Professores e Financeiro gerados.\n";
    }
}
