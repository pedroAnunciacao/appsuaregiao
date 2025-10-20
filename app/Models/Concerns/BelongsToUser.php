<?php

namespace App\Models\Concerns;

use App\Models\User;
use App\Scopes\RegionsCityScope;

trait BelongsToUser
{
    public static function bootBelongsToUser(): void
    {
        static::addGlobalScope(new RegionsCityScope());
        static::creating(function ($model): void {
            if (!$model->getAttribute('user_id') && !$model->relationLoaded('user')) {
                if (request()->user_id) {
                    $model->setAttribute('user_id', request()->user_id);
                }
            }

            if (!$model->getAttribute('pais_regiao_cidade_id') && !$model->relationLoaded('paisRegiaoCidade')) {
                if (request()->pais_regiao_cidade_id) {
                    $model->setAttribute('pais_regiao_cidade_id', request()->pais_regiao_cidade_id);
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
