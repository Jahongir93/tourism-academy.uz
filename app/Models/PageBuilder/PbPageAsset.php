<?php

namespace App\Models\PageBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PbPageAsset extends Model
{
    protected $table = 'pb_page_assets';

    protected $fillable = [
        'page_id',
        'custom_css',
        'custom_js',
        'external_assets'
    ];

    protected $casts = [
        'external_assets' => 'array'
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(PbPage::class, 'page_id');
    }
}