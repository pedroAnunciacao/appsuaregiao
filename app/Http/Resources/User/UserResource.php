<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_banned' => $this->is_banned,
            'ban_reason' => $this->ban_reason,
            'ban_expires_at' => $this->ban_expires_at,
            'profile_photo' => $this->profile_photo,
            'pais_regiao_cidade_id' => $this->pais_regiao_cidade_id,
            'email' => $this->email,
            'ativado' => $this->deleted_at === null,
        ];
    }
}
