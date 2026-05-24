<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsCourseTopic extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'week_number',
        'order_number',
        'duration_minutes',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'week_number' => 'integer',
        'order_number' => 'integer',
        'duration_minutes' => 'integer'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(LmsTopicResource::class, 'topic_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrderedByWeek($query)
    {
        return $query->orderBy('week_number')->orderBy('order_number');
    }
}
