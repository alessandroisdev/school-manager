<?php

namespace App\Domains\Finance\Models;

use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceCampaign extends Model
{
    use HasUnitScope;

    protected $fillable = [
        'unit_id',
        'name',
        'status', // pending, processing, completed, failed
        'zip_path',
        'total_items',
        'processed_items',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceCampaignItem::class);
    }
}
