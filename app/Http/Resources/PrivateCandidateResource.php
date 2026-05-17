<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivateCandidateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'request_id'        => $this->request_id,
            'candidate_details' => $this->candidate_details,
            'note_ar'           => $this->note_ar,
            'status'            => $this->status,
            'responded_at'      => $this->responded_at?->toIso8601String(),
            'created_at'        => $this->created_at->toIso8601String(),
        ];
    }
}
