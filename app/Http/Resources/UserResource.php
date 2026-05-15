<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        $isOwnProfile = $this->id === auth()->id();
        $privacy      = $this->whenLoaded('privacySettings') instanceof \Illuminate\Http\Resources\MissingValue
            ? null
            : $this->privacySettings;

        // Try to load privacy if not already loaded
        if (!$isOwnProfile && !$this->relationLoaded('privacySettings')) {
            $privacy = $this->privacySettings;
        }

        $displayName = $this->display_name;
        if (!$isOwnProfile && $privacy?->hide_real_name && $this->display_name) {
            $displayName = mb_substr($this->display_name, 0, 1) . '.';
        }

        $lastActiveAt = null;
        if ($isOwnProfile || ($privacy?->show_last_active)) {
            $lastActiveAt = $this->last_active_at?->toIso8601String();
        }

        return [
            'id'                    => $this->id,
            'display_name'          => $displayName,
            'age'                   => $this->birth_date ? Carbon::parse($this->birth_date)->age : null,
            'city'                  => $this->city,
            'country'               => 'الأردن',
            'gender'                => $this->gender,
            'bio'                   => $this->bio,
            'religiosity_level'     => $this->religiosity_level,
            'education_level'       => $this->education_level,
            'income_range'          => $this->income_range,
            'is_smoker'             => $this->is_smoker,
            'marriage_timeline'     => $this->marriage_timeline,
            'completion_pct'        => $this->completion_pct,
            'seriousness_score'     => $this->seriousness_score,
            'is_ready_for_marriage' => $this->is_ready_for_marriage,
            'is_verified'           => $this->is_verified,
            'last_active_at'        => $lastActiveAt,
            'images'                => ProfileImageResource::collection($this->whenLoaded('profileImages')),
            'interests'             => $this->whenLoaded('interests', fn () => $this->interests->pluck('label')),
            'intent_card'           => new IntentCardResource($this->whenLoaded('intentCard')),
            'privacy'               => $isOwnProfile ? new PrivacySettingResource($this->whenLoaded('privacySettings')) : null,
            'firebase_uid'          => $isOwnProfile ? $this->firebase_uid : null,
        ];
    }
}
