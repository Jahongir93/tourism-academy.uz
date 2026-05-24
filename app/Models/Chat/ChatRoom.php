<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'icon',
        'color',
        'max_members',
        'is_active',
        'order_number',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_members' => 'integer',
        'order_number' => 'integer'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'room_id');
    }

    public function members()
    {
        return $this->hasMany(ChatRoomMember::class, 'room_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_room_members', 'room_id', 'user_id')
            ->withPivot(['role', 'is_muted', 'muted_until', 'last_seen_at', 'unread_count'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('type', 'public');
    }

    public function getLatestMessage()
    {
        return $this->messages()
            ->with('user')
            ->where('is_deleted', false)
            ->latest()
            ->first();
    }

    public function getMemberCount()
    {
        return $this->members()->count();
    }

    public function isMember($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->members()->where('user_id', $userId)->exists();
    }

    public function getUserRole($userId = null)
    {
        $userId = $userId ?? auth()->id();
        $member = $this->members()->where('user_id', $userId)->first();
        return $member ? $member->role : null;
    }

    public function isAdmin($userId = null)
    {
        return $this->getUserRole($userId) === 'admin';
    }

    public function isModerator($userId = null)
    {
        $role = $this->getUserRole($userId);
        return in_array($role, ['admin', 'moderator']);
    }

    public function join($userId = null, $role = 'member')
    {
        $userId = $userId ?? auth()->id();

        if ($this->max_members && $this->getMemberCount() >= $this->max_members) {
            return false;
        }

        return $this->members()->firstOrCreate([
            'user_id' => $userId
        ], [
            'role' => $role
        ]);
    }

    public function leave($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->members()->where('user_id', $userId)->delete();
    }
}