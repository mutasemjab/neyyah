<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileImageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'url'         => $this->url,
            'blurred_url' => $this->blurred_url,
            'sort_order'  => $this->sort_order,
        ];
    }
}
