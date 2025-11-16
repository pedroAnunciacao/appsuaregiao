<?php

namespace App\Http\Resources\Continentes;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Pais\PaisResource;

class ContinentesResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'paises' => PaisResource::collection($this->whenLoaded('paises')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
