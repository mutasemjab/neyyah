<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentPage extends Model
{
    protected $primaryKey = 'type';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['type', 'title_ar', 'content_ar', 'items'];

    protected $casts = [
        'items' => 'array',
    ];
}
