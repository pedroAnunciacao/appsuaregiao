<?php

namespace App\Http\Resources\PaisesRegiao;

use Illuminate\Http\Resources\Json\JsonResource;

class PaisesRegiaoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'sigla' => $this->sigla,
            'pais_id' => $this->pais_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
