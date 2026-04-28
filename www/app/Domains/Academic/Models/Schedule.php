<?php

namespace App\Domains\Academic\Models;

use App\Domains\HR\Models\Teacher;
use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes, HasUnitScope;

    protected $fillable = [
        'unit_id',
        'teacher_assignment_id',
        'teacher_id',
        'school_class_id',
        'time_slot_id',
        'day_of_week',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function teacherAssignment(): BelongsTo
    {
        return $this->belongsTo(TeacherAssignment::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
