<?php

namespace App\Domains\Enrollment\Models;

use App\Domains\Academic\Models\SchoolClass;
use App\Domains\Enrollment\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Enrollment extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'student_id',
        'school_class_id',
        'status',
        'bank_account_id',
        'discount_percentage',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'status' => EnrollmentStatus::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Finance\Models\BankAccount::class);
    }
}
