<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MatchFilter extends Model
{
    protected $table = 'match_filters';

    public $timestamps = false;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'min_age',
        'max_age',
        'cities',
        'religiosity_levels',
        'education_levels',
        'no_smokers',
        'max_distance_km',
        'marriage_timelines',
        'updated_at',
    ];

    protected $casts = [
        'cities'              => 'array',
        'religiosity_levels'  => 'array',
        'education_levels'    => 'array',
        'marriage_timelines'  => 'array',
        'no_smokers'          => 'boolean',
        'min_age'             => 'integer',
        'max_age'             => 'integer',
        'max_distance_km'     => 'integer',
        'updated_at'          => 'datetime',
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
}
