<?php

namespace App\Models\Forum;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'user_id',
        'parent_id',
        'content',
        'is_best_answer',
        'is_edited',
        'edited_at'
    ];

    protected $casts = [
        'is_best_answer' => 'boolean',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
    ];

    protected $dates = ['edited_at'];

    public function topic()
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_id');
    }

    public function likes()
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    public function reports()
    {
        return $this->morphMany(ForumReport::class, 'reportable');
    }

    public function markAsEdited()
    {
        $this->update([
            'is_edited' => true,
            'edited_at' => now()
        ]);
    }

    public function markAsBestAnswer()
    {
        // Remove best answer from other posts in same topic
        $this->topic->posts()->update(['is_best_answer' => false]);

        // Mark this as best answer
        $this->update(['is_best_answer' => true]);

        // Mark topic as solved
        $this->topic->update(['is_solved' => true]);
    }

    public function isLikedByUser($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return false;

        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function scopeRootPosts($query)
    {
        return $query->whereNull('parent_id');
    }
}