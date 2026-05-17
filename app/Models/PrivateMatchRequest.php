<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PrivateMatchRequest extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'matchmaker_id', 'package_id', 'status',
        'payment_reference', 'price', 'personal_details',
        'partner_preferences', 'notes_ar', 'expires_at',
        'cancelled_at', 'cancelled_reason_ar',
    ];

    protected $casts = [
        'personal_details'   => 'array',
        'partner_preferences' => 'array',
        'price'              => 'float',
        'expires_at'         => 'datetime',
        'cancelled_at'       => 'datetime',
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

    public function matchmaker(): BelongsTo
    {
        return $this->belongsTo(Matchmaker::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(MatchmakerPackage::class, 'package_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(PrivateCandidate::class, 'request_id');
    }
}
