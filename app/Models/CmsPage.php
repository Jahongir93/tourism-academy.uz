<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CmsPage extends Model
{
    protected $fillable = [
        'title_uz', 'title_ru', 'title_en',
        'slug', 'meta_description_uz', 'meta_description_ru', 'meta_description_en',
        'meta_keywords', 'content_uz', 'content_ru', 'content_en',
        'featured_image', 'template', 'parent_id', 'order_position',
        'status', 'is_homepage', 'show_in_menu', 'show_in_footer',
        'custom_css', 'custom_js', 'page_builder_data',
        'og_title', 'og_description', 'og_image',
        'created_by', 'updated_by', 'published_at', 'views_count'
    ];

    protected $casts = [
        'content_uz' => 'array',
        'content_ru' => 'array',
        'content_en' => 'array',
        'custom_css' => 'array',
        'custom_js' => 'array',
        'page_builder_data' => 'array',
        'is_homepage' => 'boolean',
        'show_in_menu' => 'boolean',
        'show_in_footer' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer'
    ];

    protected static function booted()
    {
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title_uz);
            }
            
            if ($page->is_homepage) {
                self::where('is_homepage', true)->update(['is_homepage' => false]);
            }
        });
        
        static::updating(function ($page) {
            if ($page->is_homepage) {
                self::where('id', '!=', $page->id)
                    ->where('is_homepage', true)
                    ->update(['is_homepage' => false]);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CmsPage::class, 'parent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(CmsMenuItem::class, 'page_id');
    }

    public function incrementViewCount()
    {
        $this->increment('views_count');
    }

    public function getTitleAttribute($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $field = "title_{$locale}";
        return $this->$field ?: $this->title_uz;
    }

    public function getContentAttribute($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $field = "content_{$locale}";
        return $this->$field ?: $this->content_uz;
    }

    public function getMetaDescriptionAttribute($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $field = "meta_description_{$locale}";
        return $this->$field ?: $this->meta_description_uz;
    }

    public function getUrlAttribute()
    {
        if ($this->is_homepage) {
            return '/';
        }
        return '/' . $this->slug;
    }

    public function getBreadcrumbsAttribute()
    {
        $breadcrumbs = [];
        $page = $this;
        
        while ($page) {
            array_unshift($breadcrumbs, [
                'title' => $page->title,
                'url' => $page->url
            ]);
            $page = $page->parent;
        }
        
        return $breadcrumbs;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where(function ($q) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', now());
                     });
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeInMenu($query)
    {
        return $query->where('show_in_menu', true);
    }

    public function scopeInFooter($query)
    {
        return $query->where('show_in_footer', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function isPublished()
    {
        return $this->status === 'published' && 
               (!$this->published_at || $this->published_at->isPast());
    }
}