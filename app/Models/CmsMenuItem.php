<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsMenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'title_uz', 'title_ru', 'title_en',
        'type', 'url', 'page_id', 'parent_id',
        'order_position', 'icon', 'css_class', 'target',
        'is_active', 'attributes'
    ];

    protected $casts = [
        'attributes' => 'array',
        'is_active' => 'boolean',
        'order_position' => 'integer'
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(CmsMenu::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CmsMenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CmsMenuItem::class, 'parent_id');
    }

    public function getTitleAttribute($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $field = "title_{$locale}";
        return $this->$field ?: $this->title_uz;
    }

    public function getUrlAttribute($value)
    {
        if ($this->type === 'page' && $this->page) {
            return $this->page->url;
        }
        return $value;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function hasChildren()
    {
        return $this->children()->exists();
    }

    public function isActive()
    {
        $currentUrl = request()->path();
        $itemUrl = ltrim($this->url, '/');
        
        if ($itemUrl === $currentUrl) {
            return true;
        }
        
        if ($this->hasChildren()) {
            foreach ($this->children as $child) {
                if ($child->isActive()) {
                    return true;
                }
            }
        }
        
        return false;
    }
}