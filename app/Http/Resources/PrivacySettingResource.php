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
            'hide_from_contacts'    => $this->hide_from_contacts,
            'anonymous_browsing'    => $this->anonymous_browsing,
            'blur_images'           => $this->blur_images,
            'hide_real_name'        => $this->hide_real_name,
            'show_last_active'      => $this->show_last_active,
            'allow_location_detect' => $this->allow_location_detect,
        ];
    }
}
