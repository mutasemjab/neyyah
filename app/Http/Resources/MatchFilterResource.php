<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchFilterResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'min_age'            => $this->min_age,
            'max_age'            => $this->max_age,
            'cities'             => $this->cities ?? [],
            'religiosity_levels' => $this->religiosity_levels ?? [],
            'education_levels'   => $this->education_levels ?? [],
            'no_smokers'         => $this->no_smokers,
            'max_distance_km'    => $this->max_distance_km,
            'marriage_timelines' => $this->marriage_timelines ?? [],
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}
