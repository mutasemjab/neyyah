<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OtpCode extends Model
{
    protected $table = 'otp_codes';

    public $timestamps = false;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $hidden = ['code', 'hashed_code'];

    protected $fillable = [
        'phone',
        'code',
        'expires_at',
        'used_at',
        'created_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::ulid();
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }
    public function isMaxAttempts(): bool
    {
        return $this->attempts >= 5;
    }

    public function verify(string $code): bool
    {
        if ($this->is_used || $this->isExpired() || $this->isMaxAttempts()) {
            return false;
        }

        if (hash_equals($this->hashed_code, bcrypt($code))) {
            $this->update(['is_used' => true, 'used_at' => now()]);
            return true;
        }

        $this->increment('attempts');
        return false;
    }
}
