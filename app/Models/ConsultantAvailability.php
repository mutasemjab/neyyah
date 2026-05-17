<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ConsultantAvailability extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'consultant_id', 'day_of_week', 'start_time', 'end_time',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::ulid();
        });
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Consultant::class);
    }
}
