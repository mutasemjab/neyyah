<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title_ar',
        'body_ar',
        'related_type',
        'related_id',
        'fcm_token',
        'push_sent',
        'push_sent_at',
        'read_at',
    ];

    protected $casts = [
        'push_sent'    => 'boolean',
        'push_sent_at' => 'datetime',
        'read_at'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
