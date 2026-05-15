<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'date_of_birth',
        'gender',
        'city',
        'country',
        'latitude',
        'longitude',
        'location_updated_at',
        'religiosity',
        'education',
        'job_title',
        'employer',
        'income_range',
        'is_smoker',
        'marriage_timeline',
        'bio',
        'completion_pct',
        'seriousness_score',
        'is_ready_for_marriage',
    ];

    protected $casts = [
        'date_of_birth'         => 'date',
        'latitude'              => 'float',
        'longitude'             => 'float',
        'location_updated_at'   => 'datetime',
        'is_smoker'             => 'boolean',
        'is_ready_for_marriage' => 'boolean',
        'completion_pct'        => 'float',
        'seriousness_score'     => 'float',
    ];

    // ── Relationships ─────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ─────────────────────────────────

    /** Age in years calculated from date_of_birth */
    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    /**
     * Approximate proximity label — never expose exact coordinates.
     * Offsets by ~500m before computing to protect exact location.
     */
    public function proximityLabelTo(UserProfile $other): string
    {
        if (!$this->latitude || !$other->latitude) {
            return 'same_city';
        }

        // Add ~500m fuzzy offset
        $lat1 = $this->latitude  + (rand(-50, 50) / 10000);
        $lon1 = $this->longitude + (rand(-50, 50) / 10000);
        $lat2 = $other->latitude  + (rand(-50, 50) / 10000);
        $lon2 = $other->longitude + (rand(-50, 50) / 10000);

        $km = $this->haversineKm($lat1, $lon1, $lat2, $lon2);

        return match (true) {
            $km <= 5   => 'nearby',
            $km <= 20  => 'same_area',
            $km <= 100 => 'same_city',
            default    => 'different_city',
        };
    }

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    // ── Computed helpers ──────────────────────────

    /**
     * Recalculate and save completion_pct.
     * Called after any profile update.
     */
    public function recalculateCompletion(): void
    {
        $fields = [
            'display_name'     => 10,
            'date_of_birth'    => 10,
            'city'             => 5,
            'religiosity'      => 10,
            'education'        => 10,
            'income_range'     => 5,
            'marriage_timeline'=> 10,
            'bio'              => 15,
        ];

        $total = array_sum($fields);
        $earned = 0;

        foreach ($fields as $field => $weight) {
            if (!empty($this->$field)) $earned += $weight;
        }

        // Bonus for photo
        if ($this->user->photos()->where('is_approved', true)->exists()) {
            $earned += 15;
            $total  += 15;
        }

        // Bonus for interests
        if ($this->user->interests()->exists()) {
            $earned += 10;
            $total  += 10;
        }

        $this->completion_pct = round($earned / $total, 2);
        $this->save();
    }

    /**
     * Recalculate seriousness score (0–1).
     * Factors: completion, ready badge, response rate, last_active.
     */
    public function recalculateSeriousness(): void
    {
        $score = 0.0;
        $score += $this->completion_pct * 0.4;                      // 40% weight
        $score += $this->is_ready_for_marriage ? 0.2 : 0.0;         // 20%

        $daysSinceActive = $this->user->last_active_at
            ? now()->diffInDays($this->user->last_active_at) : 30;
        $activityScore = max(0, (30 - $daysSinceActive) / 30) * 0.2; // 20%
        $score += $activityScore;

        // Response rate to match requests
        $totalReceived = $this->user->receivedRequests()->count();
        if ($totalReceived > 0) {
            $responded = $this->user->receivedRequests()
                ->whereIn('status', ['accepted', 'declined'])->count();
            $score += ($responded / $totalReceived) * 0.2;           // 20%
        }

        $this->seriousness_score = round(min(1.0, $score), 2);
        $this->save();
    }
}
