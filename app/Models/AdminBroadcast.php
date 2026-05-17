<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class AdminBroadcast extends Model
{
    use HasUlids;

    protected $fillable = ['title_ar', 'body_ar', 'target', 'user_ids', 'user_count', 'sent_by'];

    protected $casts = [
        'user_ids' => 'array',
    ];
}
