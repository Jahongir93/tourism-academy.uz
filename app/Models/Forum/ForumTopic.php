<?php

namespace App\Models\Forum;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ForumTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'content',
        'views',
        'is_pinned',
        'is_locked',
        'is_solved',
        'last_reply_at'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_solved' => 'boolean',
        'last_reply_at' => 'datetime',
    ];

    protected $dates = ['last_reply_at'];

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(ForumPost::class, 'topic_id');
    }

    public function latestPost()
    {
        return $this->hasOne(ForumPost::class, 'topic_id')->latest();
    }

    public function likes()
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    public function subscriptions()
    {
        return $this->hasMany(ForumSubscription::class, 'topic_id');
    }

    public function reports()
    {
        return $this->morphMany(ForumReport::class, 'reportable');
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value) . '-' . uniqid();
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function isLikedByUser($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return false;

        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isSubscribedByUser($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return false;

        return $this->subscriptions()->where('user_id', $userId)->exists();
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeNotLocked($query)
    {
        return $query->where('is_locked', false);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->orderBy('last_reply_at', 'desc');
    }
}