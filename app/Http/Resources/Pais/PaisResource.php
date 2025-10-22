<?php

namespace App\Http\Resources\Pais;

use Illuminate\Http\Resources\Json\JsonResource;

class PaisResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'sigla' => $this->sigla,
            'bacen' => $this->bacen,
            'continente_id' => $this->continente_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
