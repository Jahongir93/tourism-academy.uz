<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LmsLibraryCategory extends Model
{
    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_en',
        'description',
        'slug',
        'parent_id',
        'icon',
        'color',
        'order_number',
        'books_count',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'books_count' => 'integer',
        'order_number' => 'integer'
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name_uz);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LmsLibraryCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(LmsLibraryCategory::class, 'parent_id');
    }

    public function books(): HasMany
    {
        return $this->hasMany(LmsLibraryBook::class, 'category_id');
    }

    public function activeBooks(): HasMany
    {
        return $this->books()->where('is_active', true);
    }

    public function updateBooksCount()
    {
        $this->books_count = $this->activeBooks()->count();
        $this->save();
    }

    public function getFullPathAttribute()
    {
        $path = [$this->name_uz];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name_uz);
            $parent = $parent->parent;
        }
        
        return implode(' / ', $path);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}