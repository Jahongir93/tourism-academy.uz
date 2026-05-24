<?php

namespace App\Models\CampusTour;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Building extends Model
{
    protected $table = 'campus_tour_buildings';

    protected $fillable = [
        'title',
        'title_ru',
        'title_en',
        'description',
        'description_ru',
        'description_en',
        'short_description',
        'icon',
        'color',
        'marker_x',
        'marker_y',
        'latitude',
        'longitude',
        'panorama_id',
        'image',
        'working_hours',
        'phone',
        'email',
        'floor_count',
        'order',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'marker_x' => 'float',
        'marker_y' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
        'metadata' => 'array',
    ];

    public function panorama(): BelongsTo
    {
        return $this->belongsTo(Panorama::class, 'panorama_id');
    }

    public function panoramas(): HasMany
    {
        return $this->hasMany(Panorama::class, 'building_id');
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image ? Storage::url($this->image) : '';
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

    public function getMarkerIconAttribute(): string
    {
        return $this->icon ?: 'fa-building';
    }

    public function hasCoordinates(): bool
    {
        return ($this->marker_x !== null && $this->marker_y !== null) ||
               ($this->latitude !== null && $this->longitude !== null);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    public function scopeWithCoordinates($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('marker_x')
              ->whereNotNull('marker_y');
        })->orWhere(function ($q) {
            $q->whereNotNull('latitude')
              ->whereNotNull('longitude');
        });
    }
}
