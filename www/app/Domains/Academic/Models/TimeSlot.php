<?php

namespace App\Domains\Academic\Models;

use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSlot extends Model
{
    use SoftDeletes, HasUnitScope;

    protected $fillable = [
        'unit_id',
        'shift_id',
        'name',
        'start_time',
        'end_time',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
