<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LmsMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'material_type',
        'week_number',
        'order_number',
        'is_active',
        'download_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'week_number' => 'integer',
        'order_number' => 'integer',
        'download_count' => 'integer'
    ];

    protected $appends = ['file_size_formatted'];

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

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) {
            return '0 KB';
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
}