<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CmsNews extends Model
{
    protected $table = 'cms_news';
    
    protected $fillable = [
        'title_uz', 'title_ru', 'title_en',
        'slug', 'excerpt_uz', 'excerpt_ru', 'excerpt_en',
        'content_uz', 'content_ru', 'content_en',
        'featured_image', 'gallery', 'attachments', 'category_id',
        'author_id', 'status', 'is_featured', 'is_breaking',
        'views_count', 'tags', 'meta_description', 'meta_keywords',
        'published_at'
    ];

    protected $casts = [
        'gallery' => 'array',
        'attachments' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_breaking' => 'boolean',
        'views_count' => 'integer',
        'published_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title_uz);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CmsNewsCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where(function ($q) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', now());
                     });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBreaking($query)
    {
        return $query->where('is_breaking', true);
    }

    public function incrementViewCount()
    {
        $this->increment('views_count');
    }
}