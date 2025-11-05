<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'reason' => $this->reason,
            'created_at' => $this->created_at->format('d/m/Y'),
            'resolved_at' => $this->resolved_at,
        ];
    }
}
