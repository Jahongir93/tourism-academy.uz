<?php

namespace App\Models\CampusTour;

use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    protected $table = 'campus_tour_routes';

    protected $fillable = [
        'title',
        'title_ru',
        'title_en',
        'description',
        'description_ru',
        'description_en',
        'type',
        'icon',
        'color',
        'start_point',
        'start_point_ru',
        'start_point_en',
        'end_point',
        'end_point_ru',
        'end_point_en',
        'duration',
        'distance',
        'price',
        'map_embed_url',
        'directions',
        'directions_ru',
        'directions_en',
        'order',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'distance' => 'float',
        'price' => 'float',
        'metadata' => 'array',
    ];

    public const TYPES = [
        'bus' => ['label' => 'Avtobus', 'icon' => 'fa-bus', 'color' => '#3498db'],
        'metro' => ['label' => 'Metro', 'icon' => 'fa-train-subway', 'color' => '#e74c3c'],
        'walk' => ['label' => 'Piyoda', 'icon' => 'fa-walking', 'color' => '#27ae60'],
        'taxi' => ['label' => 'Taksi', 'icon' => 'fa-taxi', 'color' => '#f39c12'],
        'train' => ['label' => 'Temir yo\'l', 'icon' => 'fa-train', 'color' => '#9b59b6'],
        'other' => ['label' => 'Boshqa', 'icon' => 'fa-route', 'color' => '#95a5a6'],
    ];

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

    public function getLocalizedStartPoint(): string
    {
        $locale = app()->getLocale();
        return match($locale) {
            'ru' => $this->start_point_ru ?: $this->start_point,
            'en' => $this->start_point_en ?: $this->start_point,
            default => $this->start_point,
        };
    }

    public function getLocalizedEndPoint(): string
    {
        $locale = app()->getLocale();
        return match($locale) {
            'ru' => $this->end_point_ru ?: $this->end_point,
            'en' => $this->end_point_en ?: $this->end_point,
            default => $this->end_point,
        };
    }

    public function getLocalizedDirections(): ?string
    {
        $locale = app()->getLocale();
        return match($locale) {
            'ru' => $this->directions_ru ?: $this->directions,
            'en' => $this->directions_en ?: $this->directions,
            default => $this->directions,
        };
    }

    public function getTypeInfoAttribute(): array
    {
        return self::TYPES[$this->type] ?? self::TYPES['other'];
    }

    public function getTypeIconAttribute(): string
    {
        return $this->icon ?: ($this->type_info['icon'] ?? 'fa-route');
    }

    public function getTypeColorAttribute(): string
    {
        return $this->color ?: ($this->type_info['color'] ?? '#95a5a6');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type_info['label'] ?? 'Boshqa';
    }

    public function getFormattedPriceAttribute(): string
    {
        if (!$this->price) {
            return 'Bepul';
        }
        return number_format($this->price, 0, '.', ' ') . ' so\'m';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
