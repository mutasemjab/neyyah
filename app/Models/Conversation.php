<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Conversation extends Model
{
    protected $table = 'conversations';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user1_id',
        'user2_id',
        'request_id',
        'stage',
        'questions_completed_u1',
        'questions_completed_u2',
        'is_chat_unlocked',
        'firebase_channel_id',
        'expires_at',
    ];

    protected $casts = [
        'is_chat_unlocked'         => 'boolean',
        'expires_at'               => 'datetime',
        'questions_completed_u1'   => 'integer',
        'questions_completed_u2'   => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::ulid();
        });
    }

    // ── Helpers ───────────────────────────────────

    public function getPartnerIdAttribute(): ?string
    {
        $authId = auth()->id();
        if ($this->user1_id === $authId) {
            return $this->user2_id;
        }
        if ($this->user2_id === $authId) {
            return $this->user1_id;
        }

        return null;
    }

    public function getPartnerId(string $authId): string
    {
        return $this->user1_id === $authId ? $this->user2_id : $this->user1_id;
    }

    public function isParticipant(string $userId): bool
    {
        return $this->user1_id === $userId || $this->user2_id === $userId;
    }

    // ── Relationships ─────────────────────────────

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(MarriageRequest::class, 'request_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(GuidedAnswer::class);
    }
}
