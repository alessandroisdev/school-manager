<?php

namespace Tests\Feature\UseCases\Finance;

use App\Application\UseCases\Finance\GenerateEnrollmentInvoicesUseCase;
use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Enrollment\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Models\Enrollment;
use App\Domains\Enrollment\Models\Student;
use App\Domains\Finance\Enums\InvoiceStatus;
use App\Domains\Finance\Models\FeeTemplate;
use App\Domains\Shared\Models\Unit;
use App\Domains\Academic\Models\Grade;
use App\Domains\Academic\Models\AcademicYear;
use App\Domains\Academic\Models\Term;
use App\Domains\Academic\Models\Shift;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateEnrollmentInvoicesUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_invoices_successfully()
    {
        $school = \App\Domains\Shared\Models\School::create(['name' => 'Escola Matriz', 'document' => '123']);
        $unit = Unit::create(['school_id' => $school->id, 'name' => 'Unidade Teste']);
        
        $grade = Grade::create(['unit_id' => $unit->id, 'name' => 'Grade']);
        $year = AcademicYear::create(['unit_id' => $unit->id, 'year' => 2026, 'start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
        $term = Term::create(['unit_id' => $unit->id, 'academic_year_id' => $year->id, 'name' => 'Semestre', 'start_date' => '2026-01-01', 'end_date' => '2026-06-30']);
        $shift = Shift::create(['unit_id' => $unit->id, 'name' => 'Matutino', 'start_time' => '07:00:00', 'end_time' => '12:00:00']);

        $student = Student::create([
            'unit_id' => $unit->id,
            'name' => 'Joao Faturamento',
            'document' => '123456789',
            'birth_date' => '2010-01-01',
        ]);
        
        $class = SchoolClass::create([
            'unit_id' => $unit->id,
            'name' => '1 Ano',
            'grade_id' => $grade->id,
            'academic_year_id' => $year->id,
            'term_id' => $term->id,
            'shift_id' => $shift->id,
            'capacity' => 30,
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'school_class_id' => $class->id,
            'status' => EnrollmentStatus::Ativa,
        ]);

        $template = FeeTemplate::create([
            'unit_id' => $unit->id,
            'name' => 'Plano Anual 10x',
            'total_amount' => 12500.50,
            'installments_count' => 10,
        ]);

        $useCase = new GenerateEnrollmentInvoicesUseCase();
        
        $firstDueDate = Carbon::create(2026, 2, 10);
        $invoices = $useCase->execute($enrollment, $template, $firstDueDate);

        $this->assertCount(10, $invoices);
        
        $this->assertDatabaseHas('invoices', [
            'unit_id' => $unit->id,
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'amount' => 1250.05,
            'due_date' => '2026-02-10',
            'status' => InvoiceStatus::PENDING->value,
        ]);
        
        $this->assertDatabaseHas('invoices', [
            'due_date' => '2026-11-10',
            'amount' => 1250.05,
        ]);
        
        $totalInvoicesSum = collect($invoices)->sum('amount');
        $this->assertEqualsWithDelta(12500.50, $totalInvoicesSum, 0.01);
    }
}
