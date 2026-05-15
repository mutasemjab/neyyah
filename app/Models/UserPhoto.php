<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ─────────────────────────────────────────────
//  UserPhoto
// ─────────────────────────────────────────────
class UserPhoto extends Model
{
    protected $fillable = [
        'user_id', 'path', 'blurred_path', 'thumbnail_path',
        'sort_order', 'is_approved', 'is_visible', 'visibility',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_visible'  => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns the correct image URL based on privacy settings.
     * If blurImages=true OR visibility=blurred → return blurred_path.
     */
    public function urlFor(User $viewer): string
    {
        $privacy = $this->user->privacySettings;
        $shouldBlur = $privacy?->blur_images ?? true;

        if ($shouldBlur && $this->visibility === 'blurred') {
            return $this->blurred_path ?? $this->path;
        }

        return $this->path;
    }
}
