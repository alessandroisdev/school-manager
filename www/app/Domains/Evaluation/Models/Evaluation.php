<?php

namespace App\Domains\Evaluation\Models;

use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Academic\Models\Subject;
use App\Domains\Academic\Models\Term;
use App\Domains\HR\Models\Teacher;
use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use SoftDeletes, HasUnitScope;

    protected $fillable = [
        'unit_id',
        'school_class_id',
        'subject_id',
        'term_id',
        'evaluation_type_id',
        'teacher_id',
        'name',
        'date',
        'max_score',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'max_score' => 'float',
            'weight' => 'float',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
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

    public function evaluationType(): BelongsTo
    {
        return $this->belongsTo(EvaluationType::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function gradeEntries(): HasMany
    {
        return $this->hasMany(GradeEntry::class);
    }
}
