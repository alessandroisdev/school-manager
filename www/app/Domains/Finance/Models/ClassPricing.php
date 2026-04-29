<?php

namespace App\Domains\Finance\Models;

use App\Domains\Academic\Models\Grade;
use App\Domains\Academic\Models\Shift;
use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassPricing extends Model
{
    use HasUnitScope;

    protected $fillable = [
        'unit_id',
        'grade_id',
        'shift_id',
        'annual_amount',
        'installments_count',
        'default_due_day',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
