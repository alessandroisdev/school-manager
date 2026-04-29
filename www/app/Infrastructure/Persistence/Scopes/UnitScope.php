<?php

namespace App\Infrastructure\Persistence\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class UnitScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Ignorar escopo no CLI (artisan commands, jobs)
        if (app()->runningInConsole()) {
            return;
        }

        // Se tiver sessao de unidade, filtra. Sendo um request web sem unidade, bloqueia tudo.
        if (Session::has('active_unit_id')) {
            $builder->where($model->getTable() . '.unit_id', Session::get('active_unit_id'));
        } else {
            $builder->whereRaw('1 = 0');
        }
    }
}
