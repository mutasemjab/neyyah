<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Consultant extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'title_ar', 'specializations', 'years_experience',
        'session_price', 'meeting_types', 'bio_ar',
        'verification_status', 'verified_at', 'rejection_reason_ar',
        'rating_avg', 'ratings_count', 'is_active',
    ];

    protected $casts = [
        'specializations'  => 'array',
        'meeting_types'    => 'array',
        'session_price'    => 'float',
        'years_experience' => 'integer',
        'verified_at'      => 'datetime',
        'rating_avg'       => 'float',
        'ratings_count'    => 'integer',
        'is_active'        => 'boolean',
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

    public function availabilities(): HasMany
    {
        return $this->hasMany(ConsultantAvailability::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ConsultationSession::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(SessionReview::class);
    }

    public function isApproved(): bool
    {
        return $this->verification_status === 'approved' && $this->is_active;
    }
}
