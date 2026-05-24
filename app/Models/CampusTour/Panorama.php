<?php

namespace App\Models\CampusTour;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Panorama extends Model
{
    protected $table = 'campus_tour_panoramas';

    protected $fillable = [
        'title',
        'title_ru',
        'title_en',
        'description',
        'description_ru',
        'description_en',
        'image_path',
        'thumbnail_path',
        'building_id',
        'order',
        'is_active',
        'is_featured',
        'hotspots',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'hotspots' => 'array',
        'metadata' => 'array',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image_path ? Storage::url($this->image_path) : '';
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail_path) {
            return Storage::url($this->thumbnail_path);
        }
        return $this->image_url;
    }

    public function getLocalizedTitle(): string
    {
        $locale = app()->getLocale();
        return match($locale) {
            'ru' => $this->title_ru ?: $this->title,
            'en' => $this->title_en ?: $this->title,
            default => $this->title,
        };
    }

    public function getLocalizedDescription(): ?string
    {
        $locale = app()->getLocale();
        return match($locale) {
            'ru' => $this->description_ru ?: $this->description,
            'en' => $this->description_en ?: $this->description,
            default => $this->description,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }
}
