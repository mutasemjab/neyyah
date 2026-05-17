<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivateMatchRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'user_id'             => $this->user_id,
            'matchmaker_id'       => $this->matchmaker_id,
            'matchmaker'          => $this->whenLoaded('matchmaker', fn () => new MatchmakerResource($this->matchmaker)),
            'package_id'          => $this->package_id,
            'package'             => $this->whenLoaded('package', fn () => new MatchmakerPackageResource($this->package)),
            'status'              => $this->status,
            'payment_reference'   => $this->payment_reference,
            'price'               => $this->price,
            'personal_details'    => $this->personal_details,
            'partner_preferences' => $this->partner_preferences,
            'notes_ar'            => $this->notes_ar,
            'expires_at'          => $this->expires_at?->toIso8601String(),
            'cancelled_at'        => $this->cancelled_at?->toIso8601String(),
            'cancelled_reason_ar' => $this->cancelled_reason_ar,
            'candidates_count'    => $this->whenLoaded('candidates', fn () => $this->candidates->count()),
            'created_at'          => $this->created_at->toIso8601String(),
            'updated_at'          => $this->updated_at->toIso8601String(),
        ];
    }
}
