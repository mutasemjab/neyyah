<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CoinTransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'amount'         => $this->amount,
            'type'           => $this->type,
            'description_ar' => $this->description_ar,
            'balance_after'  => $this->balance_after,
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
