<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class MarriageRequest extends Model
{
    protected $table = 'marriage_requests';

    public $timestamps = false;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'reason_for_interest',
        'life_goals',
        'marriage_expectations',
        'status',
        'sent_at',
        'responded_at',
    ];

    protected $casts = [
        'sent_at'      => 'datetime',
        'responded_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::ulid();
            if (empty($model->sent_at)) {
                $model->sent_at = now();
            }
        });
    }

    // ── Scopes ────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('from_user_id', $userId)
              ->orWhere('to_user_id', $userId);
        });
    }

    // ── Relationships ─────────────────────────────

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class, 'request_id');
    }
}
