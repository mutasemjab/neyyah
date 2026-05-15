<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GuidedQuestionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'text_ar'     => $this->text_ar,
            'hint_ar'     => $this->hint_ar,
            'category_ar' => $this->category_ar,
            'sort_order'  => $this->sort_order,
        ];
    }
}
