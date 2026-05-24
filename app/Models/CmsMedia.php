<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CmsMedia extends Model
{
    protected $table = 'cms_media';
    
    protected $fillable = [
        'name', 'file_name', 'mime_type', 'path',
        'disk', 'collection', 'size', 'metadata',
        'thumbnails', 'alt_text', 'caption',
        'folder_id', 'uploaded_by', 'download_count'
    ];

    protected $casts = [
        'metadata' => 'array',
        'thumbnails' => 'array',
        'size' => 'integer',
        'download_count' => 'integer'
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(CmsMediaFolder::class, 'folder_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getIsImageAttribute()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getIsVideoAttribute()
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    public function getIsAudioAttribute()
    {
        return str_starts_with($this->mime_type, 'audio/');
    }

    public function getIsPdfAttribute()
    {
        return $this->mime_type === 'application/pdf';
    }

    public function getFormattedSizeAttribute()
    {
        $size = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }
}