<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ─────────────────────────────────────────────
//  Block
// ─────────────────────────────────────────────
class Block extends Model
{
    protected $fillable = ['blocker_id', 'blocked_id', 'reason'];

    public function blocker(): BelongsTo { return $this->belongsTo(User::class, 'blocker_id'); }
    public function blocked(): BelongsTo { return $this->belongsTo(User::class, 'blocked_id'); }
}

// ─────────────────────────────────────────────
//  Report
// ─────────────────────────────────────────────
class Report extends Model
{
    protected $fillable = [
        'reporter_id', 'reported_user_id', 'reason', 'details',
        'status', 'reviewed_by', 'admin_notes', 'reviewed_at',
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function reporter(): BelongsTo       { return $this->belongsTo(User::class, 'reporter_id'); }
    public function reportedUser(): BelongsTo   { return $this->belongsTo(User::class, 'reported_user_id'); }
    public function reviewer(): BelongsTo       { return $this->belongsTo(User::class, 'reviewed_by'); }
}

// ─────────────────────────────────────────────
//  Notification
// ─────────────────────────────────────────────
class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title_ar', 'body_ar',
        'related_type', 'related_id',
        'fcm_token', 'push_sent', 'push_sent_at', 'read_at',
    ];

    protected $casts = [
        'push_sent'    => 'boolean',
        'push_sent_at' => 'datetime',
        'read_at'      => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function markRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Convenience: create a typed notification and optionally send push.
     */
    public static function send(
        User $recipient,
        string $type,
        string $titleAr,
        string $bodyAr,
        ?Model $related = null,
        bool $sendPush = true
    ): self {
        $notif = self::create([
            'user_id'      => $recipient->id,
            'type'         => $type,
            'title_ar'     => $titleAr,
            'body_ar'      => $bodyAr,
            'related_type' => $related ? class_basename($related) : null,
            'related_id'   => $related?->id,
        ]);

        if ($sendPush) {
            // TODO: dispatch PushNotificationJob::dispatch($notif);
        }

        return $notif;
    }
}

// ─────────────────────────────────────────────
//  OtpCode
// ─────────────────────────────────────────────
class OtpCode extends Model
{
    protected $table = 'otp_codes';

    protected $fillable = [
        'phone', 'code', 'hashed_code', 'attempts', 'is_used', 'expires_at', 'used_at',
    ];

    protected $hidden = ['code', 'hashed_code'];

    protected $casts = [
        'is_used'    => 'boolean',
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public function isExpired(): bool { return now()->isAfter($this->expires_at); }
    public function isMaxAttempts(): bool { return $this->attempts >= 5; }

    public function verify(string $code): bool
    {
        if ($this->is_used || $this->isExpired() || $this->isMaxAttempts()) {
            return false;
        }

        if (hash_equals($this->hashed_code, bcrypt($code))) {
            $this->update(['is_used' => true, 'used_at' => now()]);
            return true;
        }

        $this->increment('attempts');
        return false;
    }
}

// ─────────────────────────────────────────────
//  IdentityVerification
// ─────────────────────────────────────────────
class IdentityVerification extends Model
{
    protected $table = 'identity_verifications';

    protected $fillable = [
        'user_id', 'id_type', 'document_front_path', 'document_back_path',
        'selfie_path', 'status', 'reviewed_by', 'rejection_reason', 'reviewed_at',
    ];

    protected $hidden = ['document_front_path', 'document_back_path', 'selfie_path'];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function approve(User $admin): void
    {
        $this->update(['status' => 'approved', 'reviewed_by' => $admin->id, 'reviewed_at' => now()]);
        $this->user->update(['is_verified' => true]);
    }

    public function reject(User $admin, string $reason): void
    {
        $this->update([
            'status'           => 'rejected',
            'reviewed_by'      => $admin->id,
            'reviewed_at'      => now(),
            'rejection_reason' => $reason,
        ]);
    }
}
