<?php

namespace App\Services;

use App\Models\User;

class MatchingService
{
    /**
     * Calculate compatibility between two users.
     */
    public function calculateCompatibility(User $auth, User $candidate): array
    {
        $score   = 0.5;
        $factors = [];

        // Same city +0.15
        if ($auth->city && $candidate->city && $auth->city === $candidate->city) {
            $score     += 0.15;
            $factors[]  = 'same_city';
        }

        // Same religiosity +0.10
        if ($auth->religiosity_level && $candidate->religiosity_level
            && $auth->religiosity_level === $candidate->religiosity_level) {
            $score     += 0.10;
            $factors[]  = 'same_religiosity';
        }

        // Same marriage_timeline +0.10
        if ($auth->marriage_timeline && $candidate->marriage_timeline
            && $auth->marriage_timeline === $candidate->marriage_timeline) {
            $score     += 0.10;
            $factors[]  = 'same_marriage_timeline';
        }

        // Same education +0.08
        if ($auth->education_level && $candidate->education_level
            && $auth->education_level === $candidate->education_level) {
            $score     += 0.08;
            $factors[]  = 'same_education';
        }

        // Children intent +0.07
        $authIntent      = $auth->intentCard?->children_intent;
        $candidateIntent = $candidate->intentCard?->children_intent;

        if ($authIntent && $candidateIntent && $authIntent === $candidateIntent) {
            $score     += 0.07;
            $factors[]  = 'same_children_intent';
        }

        return [
            'score'   => round(min(1.0, $score), 4),
            'factors' => $factors,
        ];
    }

    /**
     * Get proximity label between two users based on haversine distance.
     */
    public function getProximityLabel(User $a, User $b): string
    {
        if (!$a->latitude || !$a->longitude || !$b->latitude || !$b->longitude) {
            if ($a->city && $b->city) {
                return $a->city === $b->city ? 'same_city' : 'different_city';
            }

            return 'unknown';
        }

        $distance = $this->haversine(
            (float) $a->latitude,
            (float) $a->longitude,
            (float) $b->latitude,
            (float) $b->longitude
        );

        if ($distance < 2) {
            return 'nearby';
        }

        if ($distance < 10) {
            return 'same_area';
        }

        if ($a->city && $b->city && $a->city === $b->city) {
            return 'same_city';
        }

        return 'different_city';
    }

    /**
     * Recalculate profile completion percentage (0.0-1.0).
     */
    public function recalculateCompletion(User $user): float
    {
        $points = 0;
        $total  = 100;

        if ($user->display_name) {
            $points += 10;
        }

        if ($user->birth_date) {
            $points += 10;
        }

        if ($user->city) {
            $points += 5;
        }

        if ($user->bio) {
            $points += 10;
        }

        if ($user->religiosity_level) {
            $points += 8;
        }

        if ($user->education_level) {
            $points += 7;
        }

        if ($user->income_range) {
            $points += 5;
        }

        if ($user->marriage_timeline) {
            $points += 8;
        }

        $imageCount = $user->profileImages()->count();

        if ($imageCount >= 1) {
            $points += 10;
        }

        if ($imageCount >= 3) {
            $points += 5;
        }

        if ($user->intentCard) {
            $points += 15;
        }

        $interestCount = $user->interests()->count();

        if ($interestCount >= 3) {
            $points += 7;
        }

        return round($points / $total, 4);
    }

    /**
     * Recalculate seriousness score (0.0-1.0).
     */
    public function recalculateSeriousness(User $user): float
    {
        $completion = $user->completion_pct;

        $score = $completion * 0.4;

        if ($user->is_verified) {
            $score += 0.2;
        }

        if ($user->is_ready_for_marriage) {
            $score += 0.15;
        }

        $imageCount = $user->profileImages()->count();

        if ($imageCount >= 2) {
            $score += 0.1;
        }

        if ($user->intentCard) {
            $score += 0.15;
        }

        return round(min(1.0, $score), 4);
    }

    /**
     * Recalculate both scores and save to user.
     */
    public function recalculateAndSave(User $user): void
    {
        $user->load(['profileImages', 'interests', 'intentCard']);

        $completion  = $this->recalculateCompletion($user);
        $user->completion_pct = $completion;

        $seriousness = $this->recalculateSeriousness($user);
        $user->seriousness_score = $seriousness;

        $user->timestamps = false;
        $user->save();
        $user->timestamps = true;
    }

    /**
     * Haversine formula to calculate distance between two lat/lng points in km.
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
