<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchmakerInterestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'post_id'      => $this->post_id,
            'user_id'      => $this->user_id,
            'user'         => $this->whenLoaded('user', fn () => [
                'id'           => $this->user->id,
                'display_name' => $this->user->display_name,
                'city'         => $this->user->city,
            ]),
            'note_ar'      => $this->note_ar,
            'status'       => $this->status,
            'responded_at' => $this->responded_at?->toIso8601String(),
            'created_at'   => $this->created_at->toIso8601String(),
        ];
    }
}
