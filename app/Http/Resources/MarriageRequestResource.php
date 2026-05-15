<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MarriageRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'from_user_id'           => $this->from_user_id,
            'from_profile'           => $this->whenLoaded('fromUser', fn () => new UserResource($this->fromUser)),
            'to_user_id'             => $this->to_user_id,
            'to_profile'             => $this->whenLoaded('toUser', fn () => new UserResource($this->toUser)),
            'reason_for_interest'    => $this->reason_for_interest,
            'life_goals'             => $this->life_goals,
            'marriage_expectations'  => $this->marriage_expectations,
            'status'                 => $this->status,
            'sent_at'                => $this->sent_at?->toIso8601String(),
            'responded_at'           => $this->responded_at?->toIso8601String(),
        ];
    }
}
