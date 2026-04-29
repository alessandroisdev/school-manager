<?php

namespace App\Domains\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitSetting extends Model
{
    protected $fillable = [
        'unit_id',
        'school_type',
        'calculation_rule',
        'passing_grade',
        'passing_attendance',
        'default_class_capacity',
        'current_academic_year',
        'default_due_day',
        'late_fee_interest',
        'evaluation_type',
        'attendance_type',
        'late_fee_penalty',
        'discount_before_due',
        'currency',
        'unit_logo',
        'primary_color',
        'receipt_header',
        'receipt_footer',
        'timezone',
        'enable_student_portal',
        'enable_teacher_portal',
    ];

    protected function casts(): array
    {
        return [
            'passing_grade' => 'float',
            'passing_attendance' => 'float',
            'late_fee_interest' => 'float',
            'late_fee_penalty' => 'float',
            'discount_before_due' => 'float',
            'enable_student_portal' => 'boolean',
            'enable_teacher_portal' => 'boolean',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
