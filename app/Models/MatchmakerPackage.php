<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MatchmakerPackage extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'matchmaker_id', 'name_ar', 'price', 'duration_days',
        'candidate_limit', 'consultation_sessions', 'priority_support',
        'description_ar', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price'                 => 'float',
        'duration_days'         => 'integer',
        'candidate_limit'       => 'integer',
        'consultation_sessions' => 'integer',
        'priority_support'      => 'boolean',
        'is_active'             => 'boolean',
        'sort_order'            => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::ulid();
        });
    }

    public function matchmaker(): BelongsTo
    {
        return $this->belongsTo(Matchmaker::class);
    }

    public function privateRequests(): HasMany
    {
        return $this->hasMany(PrivateMatchRequest::class, 'package_id');
    }
}
