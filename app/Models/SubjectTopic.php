<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'topic_number',
        'title_uz',
        'title_ru',
        'title_en',
        'description_uz',
        'description_ru',
        'description_en',
        'topic_type',
        'hours',
        'week_number',
        'learning_outcomes',
        'keywords',
        'references',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hours' => 'integer',
        'week_number' => 'integer',
        'topic_number' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the subject that owns the topic
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the title attribute (defaults to title_uz)
     */
    public function getTitleAttribute()
    {
        return $this->title_uz ?? $this->title_ru ?? $this->title_en;
    }

    /**
     * Get the description attribute (defaults to description_uz)
     */
    public function getDescriptionAttribute()
    {
        return $this->description_uz ?? $this->description_ru ?? $this->description_en;
    }

    /**
     * Get topic type label
     */
    public function getTopicTypeTextAttribute()
    {
        return match($this->topic_type) {
            'lecture' => 'Ma\'ruza',
            'practice' => 'Amaliyot',
            'lab' => 'Laboratoriya',
            'seminar' => 'Seminar',
            'independent' => 'Mustaqil ta\'lim',
            default => $this->topic_type
        };
    }

    /**
     * Scope to get active topics
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get topics by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('topic_type', $type);
    }

    /**
     * Scope to order topics
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('topic_number');
    }
}
