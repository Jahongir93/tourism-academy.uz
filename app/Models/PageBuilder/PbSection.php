<?php

namespace App\Models\PageBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PbSection extends Model
{
    protected $table = 'pb_sections';

    protected $fillable = [
        'page_id',
        'order',
        'type',
        'settings',
        'responsive_settings',
        'is_visible'
    ];

    protected $casts = [
        'settings' => 'array',
        'responsive_settings' => 'array',
        'is_visible' => 'boolean'
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(PbPage::class, 'page_id');
    }

    public function columns(): HasMany
    {
        return $this->hasMany(PbColumn::class, 'section_id')->orderBy('order');
    }
}