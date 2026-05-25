<?php

namespace App\Http\Resources;

use App\Models\Block;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray($request): array
    {
        $authId    = auth()->id();
        $partnerId = $this->getPartnerId($authId);

        $partnerModel        = $this->user1_id === $authId ? $this->user2 : $this->user1;
        $isBlockedByPartner  = $partnerId
            ? Block::where('blocker_id', $partnerId)->where('blocked_id', $authId)->exists()
            : false;

        return [
            'id'                      => $this->id,
            'partner_id'              => $partnerId,
            'partner'                 => $partnerModel ? new UserResource($partnerModel) : null,
            'is_blocked_by_partner'   => $isBlockedByPartner,
            'stage'                   => $this->stage,
            'questions_completed_u1'  => $this->questions_completed_u1,
            'questions_completed_u2'  => $this->questions_completed_u2,
            'is_chat_unlocked'        => $this->is_chat_unlocked,
            'firebase_channel_id'     => $this->firebase_channel_id,
            'expires_at'              => $this->expires_at?->toIso8601String(),
            'last_activity_at'        => $this->updated_at?->toIso8601String(),
            'request_id'              => $this->request_id,
            'created_at'              => $this->created_at?->toIso8601String(),
            'updated_at'              => $this->updated_at?->toIso8601String(),
        ];
    }
}
