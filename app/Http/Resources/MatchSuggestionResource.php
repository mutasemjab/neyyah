<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchSuggestionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                     => $this->resource['id'] ?? $this->id ?? null,
            'profile'                => isset($this->resource['profile']) ? new UserResource($this->resource['profile']) : null,
            'compatibility_score'    => $this->resource['compatibility_score'] ?? null,
            'compatibility_factors'  => $this->resource['compatibility_factors'] ?? [],
            'proximity'              => $this->resource['proximity'] ?? null,
            'status'                 => $this->resource['status'] ?? 'suggested',
            'suggested_at'           => $this->resource['suggested_at'] ?? now()->toIso8601String(),
        ];
    }
}
