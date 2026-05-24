<?php

namespace App\Models\CampusTour;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MapSettings extends Model
{
    protected $table = 'campus_tour_map_settings';

    protected $fillable = [
        'map_type',
        'base_image',
        'image_width',
        'image_height',
        'center_lat',
        'center_lng',
        'default_zoom',
        'min_zoom',
        'max_zoom',
        'tile_url',
        'bounds',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'center_lat' => 'float',
        'center_lng' => 'float',
        'bounds' => 'array',
        'settings' => 'array',
    ];

    public const MAP_TYPES = [
        'image' => 'Rasm asosida xarita',
        'osm' => 'OpenStreetMap',
        'google' => 'Google Maps',
    ];

    public function getBaseImageUrlAttribute(): string
    {
        return $this->base_image ? Storage::url($this->base_image) : '';
    }

    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    public static function getOrCreate(): self
    {
        $settings = static::first();

        if (!$settings) {
            $settings = static::create([
                'map_type' => 'image',
                'default_zoom' => 16,
                'min_zoom' => 14,
                'max_zoom' => 19,
                'is_active' => true,
            ]);
        }

        return $settings;
    }

    public function isImageBased(): bool
    {
        return $this->map_type === 'image';
    }

    public function isOsm(): bool
    {
        return $this->map_type === 'osm';
    }

    public function getDefaultTileUrl(): string
    {
        return $this->tile_url ?: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    }
}
