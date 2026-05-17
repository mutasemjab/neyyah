<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsultantAvailabilityResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'consultant_id' => $this->consultant_id,
            'day_of_week'  => $this->day_of_week,
            'start_time'   => $this->start_time,
            'end_time'     => $this->end_time,
        ];
    }
}
