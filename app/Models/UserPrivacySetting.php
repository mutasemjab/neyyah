<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPrivacySetting extends Model
{
    protected $table = 'user_privacy_settings';

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Display name respecting hide_real_name setting.
     * Non-accepted viewers see only first letter + dots.
     */
    public function maskedName(string $realName, bool $isAccepted = false): string
    {
        if (!$this->hide_real_name || $isAccepted) {
            return $realName;
        }

        // Return "أ. ن" style — first char + placeholder
        $firstChar = mb_substr($realName, 0, 1, 'UTF-8');
        return $firstChar . '. ×';
    }
}
