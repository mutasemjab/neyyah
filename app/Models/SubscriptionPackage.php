<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SubscriptionPackage extends Model
{
    protected $table = 'subscription_packages';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name_ar',
        'coins_per_month',
        'price_jd',
        'is_popular',
        'features_ar',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'features_ar'     => 'array',
        'is_popular'      => 'boolean',
        'is_active'       => 'boolean',
        'coins_per_month' => 'integer',
        'sort_order'      => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::ulid();
        });
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'package_id');
    }
}
