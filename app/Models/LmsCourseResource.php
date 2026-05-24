<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsCourseResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'resource_type',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'external_url',
        'duration',
        'week_number',
        'order_number',
        'is_mandatory',
        'is_downloadable',
        'is_published',
        'available_from',
        'available_until',
        'view_count',
        'download_count',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_mandatory' => 'boolean',
        'is_downloadable' => 'boolean',
        'is_published' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function isAvailable(): bool
    {
        $now = now();
        
        if (!$this->is_published) {
            return false;
        }
        
        if ($this->available_from && $now < $this->available_from) {
            return false;
        }
        
        if ($this->available_until && $now > $this->available_until) {
            return false;
        }
        
        return true;
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        if (!$bytes) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDurationFormattedAttribute()
    {
        $seconds = $this->duration;
        if (!$seconds) return '0:00';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getIconAttribute()
    {
        $icons = [
            'video' => 'fa-video',
            'document' => 'fa-file-alt',
            'presentation' => 'fa-file-powerpoint',
            'audio' => 'fa-music',
            'link' => 'fa-link',
            'assignment' => 'fa-tasks',
            'quiz' => 'fa-question-circle'
        ];
        
        return $icons[$this->resource_type] ?? 'fa-file';
    }

    public function getColorAttribute()
    {
        $colors = [
            'video' => 'text-red-500',
            'document' => 'text-blue-500',
            'presentation' => 'text-orange-500',
            'audio' => 'text-purple-500',
            'link' => 'text-green-500',
            'assignment' => 'text-yellow-500',
            'quiz' => 'text-indigo-500'
        ];
        
        return $colors[$this->resource_type] ?? 'text-gray-500';
    }
}