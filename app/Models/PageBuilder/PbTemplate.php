<?php

namespace App\Models\PageBuilder;

use Illuminate\Database\Eloquent\Model;

class PbTemplate extends Model
{
    protected $table = 'pb_templates';

    protected $fillable = [
        'name',
        'category',
        'description',
        'thumbnail',
        'content',
        'is_premium',
        'usage_count'
    ];

    protected $casts = [
        'content' => 'array',
        'is_premium' => 'boolean'
    ];
}