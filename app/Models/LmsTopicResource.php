<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsTopicResource extends Model
{
    protected $fillable = [
        'topic_id',
        'resource_type',
        'resource_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'external_link',
        'description',
        'order_number',
        'is_mandatory',
        'is_downloadable'
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_downloadable' => 'boolean',
        'order_number' => 'integer',
        'file_size' => 'integer'
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(LmsCourseTopic::class, 'topic_id');
    }

    // Polymorphic relationship for existing resources
    public function material(): BelongsTo
    {
        return $this->belongsTo(LmsMaterial::class, 'resource_id')->where('resource_type', 'material');
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(LmsVideo::class, 'resource_id')->where('resource_type', 'video');
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(LmsPracticeTest::class, 'resource_id')->where('resource_type', 'test');
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('resource_type', $type);
    }
}
