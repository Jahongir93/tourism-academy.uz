<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'user_id',
        'parent_id',
        'title',
        'content',
        'attachments',
        'post_type',
        'is_pinned',
        'is_locked',
        'view_count',
        'reply_count',
        'like_count',
        'is_answered',
        'best_answer_id'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_answered' => 'boolean',
        'view_count' => 'integer',
        'reply_count' => 'integer',
        'like_count' => 'integer'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LmsForumPost::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(LmsForumPost::class, 'parent_id');
    }

    public function bestAnswer(): BelongsTo
    {
        return $this->belongsTo(LmsForumPost::class, 'best_answer_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(LmsForumPostReaction::class, 'post_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(LmsForumPostReaction::class, 'post_id')->where('type', 'like');
    }

    public function dislikes(): HasMany
    {
        return $this->hasMany(LmsForumPostReaction::class, 'post_id')->where('type', 'dislike');
    }

    public function userReaction($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->reactions()->where('user_id', $userId)->first();
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementReplyCount()
    {
        $this->increment('reply_count');
    }

    public function incrementLikeCount()
    {
        $this->increment('like_count');
    }

    public function decrementLikeCount()
    {
        $this->decrement('like_count');
    }

    public function isMainPost(): bool
    {
        return is_null($this->parent_id);
    }
}