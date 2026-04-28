<?php

namespace App\Domains\Evaluation\Models;

use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluationType extends Model
{
    use SoftDeletes, HasUnitScope;

    protected $fillable = [
        'unit_id',
        'name',
        'default_weight',
    ];

    protected function casts(): array
    {
        return [
            'default_weight' => 'float',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
