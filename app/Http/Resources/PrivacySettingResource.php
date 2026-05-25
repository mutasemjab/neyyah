<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivacySettingResource extends JsonResource
{
    public function toArray($request): array
    {
        if (!$this->resource) {
            return [];
        }

        return [
            'blur_images'                => (bool) ($this->blur_images ?? true),
            'hide_real_name'             => (bool) ($this->hide_real_name ?? true),
            'allow_location_detect'      => (bool) ($this->allow_location_detect ?? true),
            'hide_from_contacts'         => (bool) ($this->hide_from_contacts ?? true),
            'anonymous_browsing'         => (bool) ($this->anonymous_browsing ?? false),
            'show_last_active'           => (bool) ($this->show_last_active ?? false),
            'show_photos_to_matches_only'=> (bool) ($this->show_photos_to_matches_only ?? false),
        ];
    }
}
