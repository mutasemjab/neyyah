<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// ─────────────────────────────────────────────
//  UserIntentCard
// ─────────────────────────────────────────────
class UserIntentCard extends Model
{
    protected $table = 'user_intent_cards';

    protected $fillable = [
        'user_id',
        'children_intent',
        'open_to_working_partner',
        'living_preference',
        'target_timeline',
        'additional_notes',
    ];

    protected $casts = [
        'open_to_working_partner' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * How many intent card fields match between two users?
     * Returns a 0.0–1.0 compatibility sub-score.
     */
    public function intentCompatibilityWith(UserIntentCard $other): float
    {
        $matches = 0;
        $total   = 3;

        if ($this->children_intent === $other->children_intent
            || $other->children_intent === 'open'
            || $this->children_intent === 'open') {
            $matches++;
        }

        if ($this->living_preference === $other->living_preference
            || $other->living_preference === 'flexible'
            || $this->living_preference === 'flexible') {
            $matches++;
        }

        if ($this->target_timeline === $other->target_timeline) {
            $matches++;
        }

        return round($matches / $total, 2);
    }
}

// ─────────────────────────────────────────────
//  Interest
// ─────────────────────────────────────────────
class Interest extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'icon', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_interests');
    }
}
