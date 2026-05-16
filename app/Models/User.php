<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use App\Models\Block;
use App\Models\Report;
use App\Models\IdentityVerification;
use App\Models\Conversation;
use App\Models\Report as ModelsReport;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'phone',
        'phone_verified_at',
        'status',
        'display_name',
        'birth_date',
        'gender',
        'city',
        'bio',
        'religiosity_level',
        'education_level',
        'income_range',
        'is_smoker',
        'marriage_timeline',
        'completion_pct',
        'seriousness_score',
        'is_ready_for_marriage',
        'is_verified',
        'latitude',
        'longitude',
        'last_active_at',
        'firebase_uid',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'birth_date'            => 'date',
        'phone_verified_at'     => 'datetime',
        'last_active_at'        => 'datetime',
        'is_smoker'             => 'boolean',
        'is_ready_for_marriage' => 'boolean',
        'is_verified'           => 'boolean',
        'completion_pct'        => 'float',
        'seriousness_score'     => 'float',
        'latitude'              => 'float',
        'longitude'             => 'float',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($user) {
            $user->id = (string) Str::ulid();
        });
    }

    // ── Accessors ─────────────────────────────────

    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }

        return Carbon::parse($this->birth_date)->age;
    }

    // ── Relationships ─────────────────────────────

    public function profileImages(): HasMany
    {
        return $this->hasMany(ProfileImage::class)->orderBy('sort_order');
    }

    public function interests(): HasMany
    {
        return $this->hasMany(UserInterest::class);
    }

    public function intentCard(): HasOne
    {
        return $this->hasOne(IntentCard::class);
    }

    public function privacySettings(): HasOne
    {
        return $this->hasOne(PrivacySetting::class);
    }

    public function matchFilters(): HasOne
    {
        return $this->hasOne(MatchFilter::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(CoinsWallet::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function sentRequests(): HasMany
    {
        return $this->hasMany(MarriageRequest::class, 'from_user_id');
    }

    public function receivedRequests(): HasMany
    {
        return $this->hasMany(MarriageRequest::class, 'to_user_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    public function allConversations(): Builder
    {
        return Conversation::where('user1_id', $this->id)
            ->orWhere('user2_id', $this->id);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'blocker_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function identityVerification()
    {
        return $this->hasOne(IdentityVerification::class);
    }
}
