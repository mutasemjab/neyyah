<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SessionReview extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'session_id', 'user_id', 'consultant_id', 'rating', 'review_ar',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::ulid();
        });
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ConsultationSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Consultant::class);
    }
}
