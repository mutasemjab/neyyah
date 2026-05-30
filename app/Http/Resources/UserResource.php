<?php

namespace App\Http\Resources;

use App\Models\Conversation;
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

        $rawName     = ($this->display_name !== null && $this->display_name !== '') ? $this->display_name : null;
        $displayName = $rawName ?? 'مستخدم';
        if (!$isOwnProfile && $privacy?->hide_real_name && $rawName) {
            $displayName = mb_substr($rawName, 0, 1) . '.';
        }

        $lastActiveAt = null;
        if ($isOwnProfile || ($privacy?->show_last_active)) {
            $lastActiveAt = $this->last_active_at?->toIso8601String();
        }

        $showAge  = $isOwnProfile || ($privacy?->show_age  ?? true);
        $showCity = $isOwnProfile || ($privacy?->show_city ?? true);

        // Determine whether the viewer can see unblurred photos
        $photosToMatchesOnly = !$isOwnProfile && ($privacy?->show_photos_to_matches_only ?? false);
        $viewerIsMatch       = false;
        if ($photosToMatchesOnly) {
            $viewerId    = auth()->id();
            $viewerIsMatch = Conversation::where(function ($q) use ($viewerId) {
                $q->where('user1_id', $viewerId)->orWhere('user2_id', $viewerId);
            })->where(function ($q) {
                $q->where('user1_id', $this->id)->orWhere('user2_id', $this->id);
            })->exists();
        }
        $showOriginalPhotos = $isOwnProfile || !$photosToMatchesOnly || $viewerIsMatch;

        // Build images: replace url with blurred_url when originals are restricted
        $images = $this->whenLoaded('profileImages', function () use ($showOriginalPhotos) {
            return $this->profileImages->map(fn ($img) => [
                'url'         => $showOriginalPhotos ? $img->url : ($img->blurred_url ?? $img->url),
                'blurred_url' => $img->blurred_url,
                'sort_order'  => $img->sort_order,
            ])->values();
        });

        $role = null;
        if ($isOwnProfile) {
            if ($this->relationLoaded('matchmakerProfile') && $this->matchmakerProfile) {
                $role = 'matchmaker';
            } elseif ($this->relationLoaded('consultantProfile') && $this->consultantProfile) {
                $role = 'consultant';
            } else {
                $role = 'user';
            }
        }

        return [
            'id'                    => $this->id,
            'role'                  => $role,
            'display_name'          => $displayName,
            'age'                   => $showAge && $this->birth_date ? Carbon::parse($this->birth_date)->age : null,
            'city'                  => $showCity ? $this->city : null,
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
            'images'                => $images,
            'interests'             => $this->whenLoaded('interests', fn () => $this->interests->pluck('label')),
            'intent_card'           => new IntentCardResource($this->whenLoaded('intentCard')),
            'privacy_settings'      => new PrivacySettingResource($this->whenLoaded('privacySettings')),
            'firebase_uid'          => $isOwnProfile ? $this->firebase_uid : null,
        ];
    }
}
