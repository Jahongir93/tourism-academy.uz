<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CmsNewsCategory extends Model
{
    protected $fillable = [
        'name_uz', 'name_ru', 'name_en',
        'slug', 'description', 'color', 'icon',
        'order_position', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_position' => 'integer'
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name_uz);
            }
        });
    }

    public function news(): HasMany
    {
        return $this->hasMany(CmsNews::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}