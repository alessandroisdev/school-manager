<?php

namespace App\Domains\Attendance\Models;

use App\Domains\Enrollment\Models\Student;
use App\Domains\Attendance\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class AttendanceRecord extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'lesson_id',
        'student_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
