<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'message',
        'parent_id',
        'attachment',
        'attachment_type',
        'is_edited',
        'edited_at',
        'is_deleted'
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'edited_at' => 'datetime'
    ];

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ChatMessage::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ChatMessage::class, 'parent_id');
    }

    public function reactions()
    {
        return $this->hasMany(ChatReaction::class, 'message_id');
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function getReactionSummary()
    {
        return $this->reactions()
            ->selectRaw('emoji, COUNT(*) as count')
            ->groupBy('emoji')
            ->get();
    }

    public function hasUserReacted($emoji, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->reactions()
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->exists();
    }

    public function toggleReaction($emoji, $userId = null)
    {
        $userId = $userId ?? auth()->id();

        $existing = $this->reactions()
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        $this->reactions()->create([
            'user_id' => $userId,
            'emoji' => $emoji
        ]);

        return true;
    }

    public function markAsEdited()
    {
        $this->update([
            'is_edited' => true,
            'edited_at' => now()
        ]);
    }

    public function softDelete()
    {
        $this->update([
            'is_deleted' => true,
            'message' => '[Xabar o\'chirildi]'
        ]);
    }

    public function getFormattedTime()
    {
        if ($this->created_at->isToday()) {
            return $this->created_at->format('H:i');
        } elseif ($this->created_at->isYesterday()) {
            return 'Kecha ' . $this->created_at->format('H:i');
        } else {
            return $this->created_at->format('d.m.Y H:i');
        }
    }
}