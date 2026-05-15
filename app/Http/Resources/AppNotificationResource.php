<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppNotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'type'       => $this->type,
            'title_ar'   => $this->title_ar,
            'body_ar'    => $this->body_ar,
            'data'       => $this->data,
            'is_read'    => $this->is_read,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
