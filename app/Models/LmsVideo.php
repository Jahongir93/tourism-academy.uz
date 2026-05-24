<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LmsVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'video_url',
        'video_path',
        'thumbnail_path',
        'duration',
        'video_type',
        'week_number',
        'order_number',
        'is_active',
        'view_count',
        'allow_download'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_download' => 'boolean',
        'duration' => 'integer',
        'week_number' => 'integer',
        'order_number' => 'integer',
        'view_count' => 'integer'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function views(): MorphMany
    {
        return $this->morphMany(LmsContentView::class, 'viewable');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function getDurationFormattedAttribute()
    {
        $seconds = $this->duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}