<?php

namespace App\Domains\Evaluation\Models;

use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Subject;
use App\Domains\Academic\Models\Term;
use App\Domains\Enrollment\Models\Student;
use App\Domains\Evaluation\Enums\PerformanceStatus;
use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTermPerformance extends Model
{
    use HasUnitScope;

    protected $fillable = [
        'unit_id',
        'student_id',
        'school_class_id',
        'subject_id',
        'term_id',
        'calculated_average',
        'attendance_percentage',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'calculated_average' => 'float',
            'attendance_percentage' => 'float',
            'status' => PerformanceStatus::class,
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
