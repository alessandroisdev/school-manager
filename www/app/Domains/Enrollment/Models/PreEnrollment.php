<?php

namespace App\Domains\Enrollment\Models;

use App\Domains\Academic\Models\AcademicYear;
use App\Domains\Academic\Models\Grade;
use App\Domains\Enrollment\Enums\PreEnrollmentStatus;
use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreEnrollment extends Model
{
    use SoftDeletes, HasUnitScope;

    protected $fillable = [
        'unit_id',
        'academic_year_id',
        'grade_id',
        'student_name',
        'student_birth_date',
        'guardian_name',
        'guardian_email',
        'guardian_phone',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'student_birth_date' => 'date',
            'status' => PreEnrollmentStatus::class,
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }
}
