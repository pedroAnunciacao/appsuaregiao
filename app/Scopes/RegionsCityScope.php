<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Comment;

class RegionsCityScope implements Scope
{
    public function apply(Builder $query, Model $model): void
    {
        if (!request()->pais_regiao_cidade_id) {
            return;
        }

        if (!$model instanceof Comment) {
            $query->where('pais_regiao_cidade_id', request()->pais_regiao_cidade_id);
        }
    }
}
