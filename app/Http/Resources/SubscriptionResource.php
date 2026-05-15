<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'package_id'        => $this->package_id,
            'package'           => $this->whenLoaded('package', fn () => new SubscriptionPackageResource($this->package)),
            'starts_at'         => $this->starts_at?->toIso8601String(),
            'ends_at'           => $this->ends_at?->toIso8601String(),
            'is_active'         => $this->is_active,
            'payment_reference' => $this->payment_reference,
            'created_at'        => $this->created_at?->toIso8601String(),
        ];
    }
}
