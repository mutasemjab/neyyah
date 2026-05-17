<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'session_id'    => $this->session_id,
            'user_id'       => $this->user_id,
            'consultant_id' => $this->consultant_id,
            'rating'        => $this->rating,
            'review_ar'     => $this->review_ar,
            'created_at'    => $this->created_at->toIso8601String(),
        ];
    }
}
