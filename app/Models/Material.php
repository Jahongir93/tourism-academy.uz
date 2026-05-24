<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'title',
        'description',
        'type',
        'file_path',
        'file_size',
        'file_type',
        'video_url',
        'external_link',
        'group_ids',
        'views_count',
        'downloads_count',
    ];

    protected $casts = [
        'group_ids' => 'array',
        'views_count' => 'integer',
        'downloads_count' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Get the teacher/employee that owns the material
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    /**
     * Get the subject that owns the material
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get material type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'document' => 'Hujjat',
            'video' => 'Video',
            'presentation' => 'Taqdimot',
            'link' => 'Havola',
            default => $this->type
        };
    }

    /**
     * Get material icon
     */
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'document' => 'fas fa-file-alt',
            'video' => 'fas fa-video',
            'presentation' => 'fas fa-file-powerpoint',
            'link' => 'fas fa-link',
            default => 'fas fa-file'
        };
    }

    /**
     * Get material color
     */
    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'document' => 'primary',
            'video' => 'danger',
            'presentation' => 'warning',
            'link' => 'info',
            default => 'secondary'
        };
    }
}
