<?php

namespace App\Http\Resources\PaisRegioCidade;

use Illuminate\Http\Resources\Json\JsonResource;

class PaisRegioCidadeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'regiao_id' => $this->regiao_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
