<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatBlockedUser extends Model
{
    protected $fillable = [
        'blocker_id',
        'blocked_id',
    ];

    /**
     * Get the user who blocked
     */
    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    /**
     * Get the user who is blocked
     */
    public function blocked(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }

    /**
     * Check if a user is blocked by another user
     */
    public static function isBlocked($blockerId, $blockedId): bool
    {
        return self::where('blocker_id', $blockerId)
            ->where('blocked_id', $blockedId)
            ->exists();
    }

    /**
     * Check if two users have blocked each other in any direction
     */
    public static function hasBlockBetween($user1Id, $user2Id): bool
    {
        return self::where(function($query) use ($user1Id, $user2Id) {
            $query->where('blocker_id', $user1Id)->where('blocked_id', $user2Id);
        })->orWhere(function($query) use ($user1Id, $user2Id) {
            $query->where('blocker_id', $user2Id)->where('blocked_id', $user1Id);
        })->exists();
    }
}
