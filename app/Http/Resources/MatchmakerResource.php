<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchmakerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'user_id'             => $this->user_id,
            'user'                => $this->whenLoaded('user', fn () => [
                'id'           => $this->user->id,
                'display_name' => $this->user->display_name,
                'city'         => $this->user->city,
                'gender'       => $this->user->gender,
            ]),
            'bio_ar'              => $this->bio_ar,
            'specializations'     => $this->specializations ?? [],
            'years_experience'    => $this->years_experience,
            'verification_status' => $this->verification_status,
            'rating_avg'          => round($this->rating_avg, 2),
            'ratings_count'       => $this->ratings_count,
            'success_cases'       => $this->success_cases,
            'is_active'           => $this->is_active,
            'verified_at'         => $this->verified_at?->toIso8601String(),
            'created_at'          => $this->created_at->toIso8601String(),
        ];
    }
}
