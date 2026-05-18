<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsultantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'user_id'             => $this->user_id,
            'user'                => $this->whenLoaded('user', fn () => [
                'id'           => $this->user->id,
                'display_name' => $this->user->display_name ?: 'مستخدم',
                'city'         => $this->user->city,
                'gender'       => $this->user->gender,
            ]),
            'title_ar'            => $this->title_ar,
            'specializations'     => $this->specializations ?? [],
            'years_experience'    => $this->years_experience,
            'session_price'       => $this->session_price,
            'meeting_types'       => $this->meeting_types ?? [],
            'bio_ar'              => $this->bio_ar,
            'verification_status' => $this->verification_status,
            'rating_avg'          => round($this->rating_avg, 2),
            'ratings_count'       => $this->ratings_count,
            'is_active'           => $this->is_active,
            'verified_at'         => $this->verified_at?->toIso8601String(),
            'availabilities'      => $this->whenLoaded('availabilities', fn () =>
                ConsultantAvailabilityResource::collection($this->availabilities)
            ),
            'created_at'          => $this->created_at->toIso8601String(),
        ];
    }
}
