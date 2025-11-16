<?php

namespace App\Http\Resources\Regiao;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Cidades\CidadesResource;

class RegiaoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'sigla' => $this->sigla,
            'pais_id' => $this->pais_id,
            'cidades' => CidadesResource::collection($this->whenLoaded('cidades')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
