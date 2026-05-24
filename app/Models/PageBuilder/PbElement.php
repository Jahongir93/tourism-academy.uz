<?php

namespace App\Models\PageBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PbElement extends Model
{
    protected $table = 'pb_elements';

    protected $fillable = [
        'column_id',
        'type',
        'order',
        'content',
        'settings',
        'animations',
        'responsive_settings',
        'is_visible'
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'animations' => 'array',
        'responsive_settings' => 'array',
        'is_visible' => 'boolean'
    ];

    public function column(): BelongsTo
    {
        return $this->belongsTo(PbColumn::class, 'column_id');
    }

    public function elementType(): BelongsTo
    {
        return $this->belongsTo(PbElementType::class, 'type', 'name');
    }
}