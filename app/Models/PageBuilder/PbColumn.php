<?php

namespace App\Models\PageBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PbColumn extends Model
{
    protected $table = 'pb_columns';

    protected $fillable = [
        'section_id',
        'order',
        'width',
        'responsive_width',
        'settings'
    ];

    protected $casts = [
        'responsive_width' => 'array',
        'settings' => 'array'
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PbSection::class, 'section_id');
    }

    public function elements(): HasMany
    {
        return $this->hasMany(PbElement::class, 'column_id')->orderBy('order');
    }
}