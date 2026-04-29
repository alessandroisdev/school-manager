<?php

namespace App\Domains\Finance\Models;

use App\Domains\Enrollment\Models\Enrollment;
use App\Domains\Enrollment\Models\Student;
use App\Domains\Finance\Enums\InvoiceStatus;
use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Invoice extends Model implements AuditableContract
{
    use SoftDeletes, HasUnitScope, Auditable;

    protected $fillable = [
        'unit_id',
        'student_id',
        'enrollment_id',
        'amount',
        'installment_number',
        'barcode',
        'digitable_line',
        'pix_qr_code',
        'pix_key',
        'bank_account_id',
        'due_date',
        'status',
        'paid_at',
        'description',
        'payment_method'
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'status' => InvoiceStatus::class,
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

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
