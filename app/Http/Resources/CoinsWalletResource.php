<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CoinsWalletResource extends JsonResource
{
    private const DAILY_FREE_TOTAL = 5;

    public function toArray($request): array
    {
        $used      = $this->daily_free_used ?? 0;
        $remaining = max(0, self::DAILY_FREE_TOTAL - $used);

        $canClaimToday = !$this->daily_free_reset_at || now()->gte($this->daily_free_reset_at);

        return [
            'balance'              => $this->balance,
            'daily_free_used'      => $used,
            'daily_free_total'     => self::DAILY_FREE_TOTAL,
            'daily_free_remaining' => $remaining,
            'daily_free_reset_at'  => $this->daily_free_reset_at?->toIso8601String(),
            'can_claim_today'      => $canClaimToday,
        ];
    }
}
