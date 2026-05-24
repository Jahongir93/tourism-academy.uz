<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ChatRoomMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'role',
        'is_muted',
        'muted_until',
        'last_seen_at',
        'unread_count'
    ];

    protected $casts = [
        'is_muted' => 'boolean',
        'muted_until' => 'datetime',
        'last_seen_at' => 'datetime',
        'unread_count' => 'integer'
    ];

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updateLastSeen()
    {
        $this->update([
            'last_seen_at' => now(),
            'unread_count' => 0
        ]);
    }

    public function incrementUnread()
    {
        $this->increment('unread_count');
    }

    public function mute($until = null)
    {
        $this->update([
            'is_muted' => true,
            'muted_until' => $until ?? now()->addHours(24)
        ]);
    }

    public function unmute()
    {
        $this->update([
            'is_muted' => false,
            'muted_until' => null
        ]);
    }

    public function checkMuteStatus()
    {
        if ($this->is_muted && $this->muted_until && $this->muted_until->isPast()) {
            $this->unmute();
            return false;
        }
        return $this->is_muted;
    }

    public function promote($newRole)
    {
        if (in_array($newRole, ['admin', 'moderator', 'member'])) {
            $this->update(['role' => $newRole]);
        }
    }
}