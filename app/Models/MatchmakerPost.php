<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MatchmakerPost extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'matchmaker_id', 'gender', 'age_from', 'age_to', 'city',
        'nationality_ar', 'profession_ar', 'religiosity_level',
        'education_level', 'bio_ar', 'requirements_ar',
        'is_active', 'interests_count',
    ];

    protected $casts = [
        'age_from'       => 'integer',
        'age_to'         => 'integer',
        'is_active'      => 'boolean',
        'interests_count' => 'integer',
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

    public function interests(): HasMany
    {
        return $this->hasMany(MatchmakerInterest::class, 'post_id');
    }
}
