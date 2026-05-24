<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'message',
        'is_from_admin',
        'admin_id',
        'is_read',
        'ip_address',
    ];

    protected $casts = [
        'is_from_admin' => 'boolean',
        'is_read' => 'boolean',
    ];

    /**
     * Get the user who sent the message (if logged in)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who responded
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope for a specific session
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Get sender name
     */
    public function getSenderNameAttribute()
    {
        if ($this->is_from_admin) {
            return $this->admin ? $this->admin->name : 'Admin';
        }

        return $this->user ? $this->user->name : ($this->guest_name ?: 'Mehmon');
    }
}
