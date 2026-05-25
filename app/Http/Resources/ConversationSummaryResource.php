<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConversationSummaryResource extends JsonResource
{
    public function toArray($request): array
    {
        $authId       = auth()->id();
        $partnerModel = $this->user1_id === $authId ? $this->user2 : $this->user1;

        return [
            'id'              => $this->id,
            'partner_id'      => $this->getPartnerId($authId),
            'partner'         => $partnerModel ? new UserResource($partnerModel) : null,
            'stage'           => $this->stage,
            'is_chat_unlocked'=> $this->is_chat_unlocked,
            'expires_at'       => $this->expires_at?->toIso8601String(),
            'last_activity_at' => $this->updated_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
        ];
    }
}
