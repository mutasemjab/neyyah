<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Matchmaker extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'bio_ar', 'specializations', 'years_experience',
        'verification_status', 'verified_at', 'rejection_reason_ar',
        'rating_avg', 'ratings_count', 'success_cases', 'is_active',
    ];

    protected $casts = [
        'specializations'     => 'array',
        'verified_at'         => 'datetime',
        'rating_avg'          => 'float',
        'ratings_count'       => 'integer',
        'success_cases'       => 'integer',
        'years_experience'    => 'integer',
        'is_active'           => 'boolean',
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

    public function posts(): HasMany
    {
        return $this->hasMany(MatchmakerPost::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(MatchmakerPackage::class);
    }

    public function privateRequests(): HasMany
    {
        return $this->hasMany(PrivateMatchRequest::class);
    }

    public function isApproved(): bool
    {
        return $this->verification_status === 'approved' && $this->is_active;
    }
}
