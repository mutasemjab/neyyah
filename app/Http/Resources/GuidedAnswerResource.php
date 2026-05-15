<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GuidedAnswerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'question_id' => $this->question_id,
            'user_id'     => $this->user_id,
            'answer_text' => $this->answer_text,
            'answered_at' => $this->answered_at?->toIso8601String(),
        ];
    }
}
