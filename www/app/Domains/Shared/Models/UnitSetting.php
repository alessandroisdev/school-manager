<?php

namespace App\Domains\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitSetting extends Model
{
    protected $fillable = [
        'unit_id',
        'calculation_rule',
        'passing_grade',
        'passing_attendance',
    ];

    protected function casts(): array
    {
        return [
            'passing_grade' => 'float',
            'passing_attendance' => 'float',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
