<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IntentCardResource extends JsonResource
{
    public function toArray($request): array
    {
        if (!$this->resource) {
            return [];
        }

        return [
            'children_intent'         => $this->children_intent,
            'open_to_working_partner' => $this->open_to_working_partner,
            'living_preference'       => $this->living_preference,
            'target_timeline'         => $this->target_timeline,
            'additional_notes'        => $this->additional_notes,
        ];
    }
}
