<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CmsEvent extends Model
{
    protected $fillable = [
        'title_uz', 'title_ru', 'title_en',
        'slug', 'description_uz', 'description_ru', 'description_en',
        'content_uz', 'content_ru', 'content_en',
        'featured_image', 'gallery', 'start_date', 'end_date',
        'location', 'venue', 'coordinates', 'type', 'status',
        'is_featured', 'is_online', 'online_link',
        'requires_registration', 'max_participants', 'registered_count',
        'organizers', 'speakers', 'agenda', 'attachments',
        'created_by', 'views_count'
    ];

    protected $casts = [
        'gallery' => 'array',
        'coordinates' => 'array',
        'organizers' => 'array',
        'speakers' => 'array',
        'agenda' => 'array',
        'attachments' => 'array',
        'is_featured' => 'boolean',
        'is_online' => 'boolean',
        'requires_registration' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'views_count' => 'integer',
        'max_participants' => 'integer',
        'registered_count' => 'integer'
    ];

    protected static function booted()
    {
        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title_uz);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CmsEventRegistration::class, 'event_id');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
                     ->where('start_date', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing')
                     ->orWhere(function($q) {
                         $q->where('start_date', '<=', now())
                           ->where('end_date', '>=', now());
                     });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function incrementViewCount()
    {
        $this->increment('views_count');
    }

    public function canRegister()
    {
        if (!$this->requires_registration) {
            return false;
        }
        
        if ($this->max_participants && $this->registered_count >= $this->max_participants) {
            return false;
        }
        
        return $this->status === 'upcoming';
    }
}