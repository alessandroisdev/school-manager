<?php

namespace App\Domains\Academic\Models;

use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes, HasUnitScope;

    protected $fillable = [
        'unit_id',
        'name',
        'workload',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
