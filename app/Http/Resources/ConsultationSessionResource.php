<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationSessionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'user_id'             => $this->user_id,
            'consultant_id'       => $this->consultant_id,
            'consultant'          => $this->whenLoaded('consultant', fn () => new ConsultantResource($this->consultant)),
            'session_date'        => $this->session_date->toDateString(),
            'start_time'          => $this->start_time,
            'end_time'            => $this->end_time,
            'meeting_type'        => $this->meeting_type,
            'status'              => $this->status,
            'payment_reference'   => $this->payment_reference,
            'price'               => $this->price,
            'notes_ar'            => $this->notes_ar,
            'cancelled_at'        => $this->cancelled_at?->toIso8601String(),
            'cancelled_reason_ar' => $this->cancelled_reason_ar,
            'review'              => $this->whenLoaded('review', fn () =>
                $this->review ? new SessionReviewResource($this->review) : null
            ),
            'created_at'          => $this->created_at->toIso8601String(),
            'updated_at'          => $this->updated_at->toIso8601String(),
        ];
    }
}
