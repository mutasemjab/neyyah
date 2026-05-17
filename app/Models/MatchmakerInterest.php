<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MatchmakerInterest extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'post_id', 'user_id', 'note_ar', 'status', 'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::ulid();
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(MatchmakerPost::class, 'post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
