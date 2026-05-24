<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'created_by',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class, 'conversation_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_participants', 'conversation_id', 'user_id')
            ->withPivot('last_seen_at', 'unread_count', 'is_admin')
            ->withTimestamps();
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latest();
    }

    public function getUnreadCountForUser($userId)
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        return $participant ? $participant->unread_count : 0;
    }

    public function markAsReadForUser($userId)
    {
        $this->participants()->where('user_id', $userId)->update([
            'last_seen_at' => now(),
            'unread_count' => 0,
        ]);
    }

    public function incrementUnreadForOthers($userId)
    {
        $this->participants()
            ->where('user_id', '!=', $userId)
            ->increment('unread_count');
    }

    public function getNameForUser($userId)
    {
        if ($this->type === 'group') {
            return $this->name;
        }

        // For private chats, return the other user's name
        $otherUser = $this->users()->where('users.id', '!=', $userId)->first();
        return $otherUser ? $otherUser->name : 'Unknown';
    }

    public function getAvatarForUser($userId)
    {
        if ($this->type === 'group') {
            return null; // Group avatar logic can be added
        }

        // For private chats, return the other user's avatar
        $otherUser = $this->users()->where('users.id', '!=', $userId)->first();
        return $otherUser ? $otherUser->avatar : null;
    }
}