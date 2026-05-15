<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PrivacySetting extends Model
{
    protected $table = 'privacy_settings';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'hide_from_contacts',
        'anonymous_browsing',
        'blur_images',
        'hide_real_name',
        'show_last_active',
        'allow_location_detect',
    ];

    protected $casts = [
        'hide_from_contacts'   => 'boolean',
        'anonymous_browsing'   => 'boolean',
        'blur_images'          => 'boolean',
        'hide_real_name'       => 'boolean',
        'show_last_active'     => 'boolean',
        'allow_location_detect'=> 'boolean',
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
