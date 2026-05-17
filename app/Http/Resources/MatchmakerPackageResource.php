<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchmakerPackageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                    => $this->id,
            'matchmaker_id'         => $this->matchmaker_id,
            'name_ar'               => $this->name_ar,
            'price'                 => $this->price,
            'duration_days'         => $this->duration_days,
            'candidate_limit'       => $this->candidate_limit,
            'consultation_sessions' => $this->consultation_sessions,
            'priority_support'      => $this->priority_support,
            'description_ar'        => $this->description_ar,
            'is_active'             => $this->is_active,
            'sort_order'            => $this->sort_order,
            'created_at'            => $this->created_at->toIso8601String(),
        ];
    }
}
