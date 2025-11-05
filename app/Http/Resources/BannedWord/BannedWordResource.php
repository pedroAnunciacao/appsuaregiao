<?php

namespace App\Http\Resources\BannedWord;


use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserResource;

class BannedWordResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'body' => $this->body,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
