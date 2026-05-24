<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'message',
        'type',
        'file_url',
        'file_name',
        'is_edited',
        'edited_at',
        'deleted_for',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
        'deleted_for' => 'array',
    ];

    protected $appends = ['is_read', 'formatted_time'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ChatMessageRead::class, 'message_id');
    }

    public function markAsRead($userId)
    {
        if ($this->user_id !== $userId) {
            ChatMessageRead::firstOrCreate([
                'message_id' => $this->id,
                'user_id' => $userId,
            ], [
                'read_at' => now(),
            ]);
        }
    }

    public function isReadByUser($userId): bool
    {
        if ($this->user_id === $userId) {
            return true;
        }

        return $this->reads()->where('user_id', $userId)->exists();
    }

    public function getIsReadAttribute(): bool
    {
        if (auth()->check()) {
            return $this->isReadByUser(auth()->id());
        }
        return false;
    }

    public function getFormattedTimeAttribute(): string
    {
        $now = now();
        $diff = $this->created_at->diff($now);

        if ($diff->days > 7) {
            return $this->created_at->format('d.m.Y H:i');
        } elseif ($diff->days > 1) {
            return $this->created_at->format('l H:i');
        } elseif ($diff->days == 1) {
            return 'Kecha ' . $this->created_at->format('H:i');
        } elseif ($diff->h > 0) {
            return $diff->h . ' soat oldin';
        } elseif ($diff->i > 0) {
            return $diff->i . ' daqiqa oldin';
        } else {
            return 'Hozirgina';
        }
    }

    public function scopeUnreadForUser($query, $userId)
    {
        return $query->where('user_id', '!=', $userId)
            ->whereDoesntHave('reads', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
    }

    /**
     * Check if message is deleted for a specific user
     */
    public function isDeletedForUser($userId): bool
    {
        if ($this->trashed()) {
            return true; // Soft deleted = deleted for everyone
        }

        $deletedFor = $this->deleted_for ?? [];
        return in_array($userId, $deletedFor);
    }

    /**
     * Delete message for a specific user (one-sided delete)
     */
    public function deleteForUser($userId): bool
    {
        $deletedFor = $this->deleted_for ?? [];

        if (!in_array($userId, $deletedFor)) {
            $deletedFor[] = $userId;
            $this->deleted_for = $deletedFor;
            $this->save();
        }

        // If all participants have deleted the message, soft delete it
        $participantIds = $this->conversation->users->pluck('id')->toArray();
        $allDeleted = empty(array_diff($participantIds, $deletedFor));

        if ($allDeleted) {
            $this->delete(); // Soft delete
        }

        return true;
    }

    /**
     * Delete message for everyone (two-sided delete)
     */
    public function deleteForEveryone(): bool
    {
        // Only the sender can delete for everyone
        return $this->delete(); // Soft delete immediately
    }

    /**
     * Scope to exclude messages deleted for a user
     */
    public function scopeNotDeletedForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('deleted_for')
                ->orWhereRaw('NOT JSON_CONTAINS(deleted_for, ?, "$")', [json_encode($userId)]);
        });
    }
}