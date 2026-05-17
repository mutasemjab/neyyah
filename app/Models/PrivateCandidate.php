<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PrivateCandidate extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'request_id', 'candidate_user_id', 'candidate_details',
        'note_ar', 'status', 'responded_at',
    ];

    protected $casts = [
        'candidate_details' => 'array',
        'responded_at'      => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::ulid();
        });
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(PrivateMatchRequest::class, 'request_id');
    }

    public function candidateUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_user_id');
    }
}
