<?php

namespace App\Domains\Finance\Models;

use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class FeeTemplate extends Model implements AuditableContract
{
    use SoftDeletes, HasUnitScope, Auditable;

    protected $fillable = [
        'unit_id',
        'name',
        'total_amount',
        'installments_count',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
