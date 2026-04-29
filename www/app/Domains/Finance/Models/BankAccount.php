<?php

namespace App\Domains\Finance\Models;

use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasUnitScope, SoftDeletes;

    protected $fillable = [
        'unit_id',
        'name',
        'bank_code',
        'agency',
        'account',
        'wallet',
        'fine_percentage',
        'interest_percentage',
        'instruction_lines',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
