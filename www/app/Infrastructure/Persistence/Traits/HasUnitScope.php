<?php

namespace App\Infrastructure\Persistence\Traits;

use App\Infrastructure\Persistence\Scopes\UnitScope;

trait HasUnitScope
{
    protected static function booted()
    {
        static::addGlobalScope(new UnitScope());
    }
}
