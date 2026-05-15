<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class DailySuggestion extends Model
{
    protected $fillable = [
        'user_id',
        'suggested_user_id',
        'suggestion_date',
        'compatibility_score',
        'compatibility_factors',
        'proximity_label',
        'status',
        'viewed_at',
    ];

    protected $casts = [
        'suggestion_date'     => 'date',
        'compatibility_score' => 'float',
        'compatibility_factors'=> 'array',   // auto JSON encode/decode
        'viewed_at'           => 'datetime',
    ];

    // ── Relationships ─────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function suggestedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'suggested_user_id');
    }

    // ── Scopes ────────────────────────────────────

    public function scopeToday(Builder $q): Builder
    {
        return $q->whereDate('suggestion_date', today());
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    // ── Static: Compatibility algorithm ──────────

    /**
     * Compute a compatibility score and the matching factors
     * between two users. Returns ['score' => float, 'factors' => array].
     *
     * Weights:
     *   religiosity match    → 0.25
     *   timeline match       → 0.20
     *   intent card match    → 0.20
     *   proximity            → 0.15
     *   education proximity  → 0.10
     *   shared interests     → 0.10
     */
    public static function computeCompatibility(User $a, User $b): array
    {
        $score   = 0.0;
        $factors = [];

        $pA = $a->profile;
        $pB = $b->profile;
        $iA = $a->intentCard;
        $iB = $b->intentCard;

        // 1. Religiosity
        if ($pA && $pB && $pA->religiosity === $pB->religiosity) {
            $score += 0.25;
            $factors[] = 'same_values';
        }

        // 2. Timeline
        if ($pA && $pB && $pA->marriage_timeline === $pB->marriage_timeline) {
            $score += 0.20;
            $factors[] = 'same_timeline';
        }

        // 3. Intent card
        if ($iA && $iB) {
            $intentScore = $iA->intentCompatibilityWith($iB);
            $score += $intentScore * 0.20;
            if ($intentScore >= 0.67) $factors[] = 'same_goals';
        }

        // 4. Proximity
        if ($pA && $pB && $pA->latitude && $pB->latitude) {
            $label = $pA->proximityLabelTo($pB);
            $proximityWeight = match ($label) {
                'nearby'        => 0.15,
                'same_area'     => 0.12,
                'same_city'     => 0.10,
                'different_city'=> 0.02,
            };
            $score += $proximityWeight;
            if (in_array($label, ['nearby', 'same_area', 'same_city'])) {
                $factors[] = 'same_city';
            }
        } else {
            // No location data — give partial credit
            $score += 0.07;
        }

        // 5. Education proximity (within 1 level)
        $eduMap = ['high_school'=>0,'diploma'=>1,'bachelor'=>2,'master'=>3,'phd'=>4];
        if ($pA && $pB) {
            $diff = abs(($eduMap[$pA->education] ?? 2) - ($eduMap[$pB->education] ?? 2));
            if ($diff <= 1) {
                $score += 0.10;
                $factors[] = 'similar_education';
            }
        }

        // 6. Shared interests
        if ($a->interests && $b->interests) {
            $aIds = $a->interests->pluck('id')->toArray();
            $bIds = $b->interests->pluck('id')->toArray();
            $shared = count(array_intersect($aIds, $bIds));
            if ($shared >= 2) {
                $score += 0.10;
                $factors[] = 'similar_lifestyle';
            } elseif ($shared >= 1) {
                $score += 0.05;
            }
        }

        return [
            'score'   => round(min(1.0, $score), 2),
            'factors' => array_unique($factors),
        ];
    }

    // ── Mark viewed ───────────────────────────────

    public function markViewed(): void
    {
        $this->update(['status' => 'viewed', 'viewed_at' => now()]);
    }

    public function markDismissed(): void
    {
        $this->update(['status' => 'dismissed']);
    }

    public function markRequestSent(): void
    {
        $this->update(['status' => 'request_sent']);
    }
}
