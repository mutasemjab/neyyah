<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchmakerPostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'matchmaker_id'     => $this->matchmaker_id,
            'matchmaker'        => $this->whenLoaded('matchmaker', fn () => new MatchmakerResource($this->matchmaker)),
            'gender'            => $this->gender,
            'age_from'          => $this->age_from,
            'age_to'            => $this->age_to,
            'city'              => $this->city,
            'nationality_ar'    => $this->nationality_ar,
            'profession_ar'     => $this->profession_ar,
            'religiosity_level' => $this->religiosity_level,
            'education_level'   => $this->education_level,
            'bio_ar'            => $this->bio_ar,
            'requirements_ar'   => $this->requirements_ar,
            'is_active'         => $this->is_active,
            'interests_count'   => $this->interests_count,
            'my_interest'       => $this->whenLoaded('interests', function () use ($request) {
                if (!$request->user()) return null;
                $interest = $this->interests->firstWhere('user_id', $request->user()->id);
                return $interest ? new MatchmakerInterestResource($interest) : null;
            }),
            'created_at'        => $this->created_at->toIso8601String(),
        ];
    }
}
