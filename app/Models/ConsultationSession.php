<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class ConsultationSession extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'consultant_id', 'availability_id',
        'session_date', 'start_time', 'end_time', 'meeting_type',
        'status', 'payment_reference', 'price',
        'notes_ar', 'cancelled_at', 'cancelled_reason_ar',
    ];

    protected $casts = [
        'session_date' => 'date',
        'price'        => 'float',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::ulid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Consultant::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(SessionReview::class, 'session_id');
    }
}
