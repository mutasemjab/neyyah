<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class MatchRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'reason_for_interest',
        'life_goals',
        'marriage_expectations',
        'status',
        'accepted_at',
        'declined_at',
        'expires_at',
        'conversation_id',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    // ── Relationships ─────────────────────────────

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    // ── Scopes ────────────────────────────────────

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    public function scopeExpired(Builder $q): Builder
    {
        return $q->where('status', 'pending')
                 ->where('expires_at', '<', now());
    }

    // ── Actions ───────────────────────────────────

    /**
     * Accept this request → create a conversation.
     */
    public function accept(): Conversation
    {
        $this->update([
            'status'      => 'accepted',
            'accepted_at' => now(),
        ]);

        // Ensure smaller ID is always user_a (prevents duplicate pairs)
        [$a, $b] = $this->from_user_id < $this->to_user_id
            ? [$this->from_user_id, $this->to_user_id]
            : [$this->to_user_id, $this->from_user_id];

        $conversation = Conversation::firstOrCreate(
            ['user_a_id' => $a, 'user_b_id' => $b],
            [
                'match_request_id' => $this->id,
                'last_activity_at' => now(),
            ]
        );

        $this->update(['conversation_id' => $conversation->id]);

        // Mark the daily suggestion as request_sent
        DailySuggestion::where('user_id', $this->to_user_id)
            ->where('suggested_user_id', $this->from_user_id)
            ->whereDate('suggestion_date', today())
            ->update(['status' => 'request_sent']);

        return $conversation;
    }

    public function decline(): void
    {
        $this->update([
            'status'      => 'declined',
            'declined_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    // ── Boot ──────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (MatchRequest $r) {
            // Auto-set expiry to 7 days
            $r->expires_at ??= now()->addDays(7);
        });
    }
}
