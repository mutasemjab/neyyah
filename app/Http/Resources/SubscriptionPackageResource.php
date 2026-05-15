<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPackageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'name_ar'         => $this->name_ar,
            'coins_per_month' => $this->coins_per_month,
            'price_jd'        => number_format((float) $this->price_jd, 3),
            'is_popular'      => $this->is_popular,
            'features_ar'     => $this->features_ar,
            'sort_order'      => $this->sort_order,
        ];
    }
}
