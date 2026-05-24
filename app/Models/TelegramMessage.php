<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramMessage extends Model
{
    protected $fillable = [
        'telegram_chat_id',
        'telegram_message_id',
        'telegram_user_id',
        'telegram_username',
        'telegram_first_name',
        'telegram_last_name',
        'message',
        'direction',
        'replied_by',
        'reply_message',
        'replied_at',
        'status',
    ];

    protected $casts = [
        'telegram_chat_id' => 'integer',
        'telegram_message_id' => 'integer',
        'replied_at' => 'datetime',
    ];

    public function repliedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeIncoming($query)
    {
        return $query->where('direction', 'incoming');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('direction', 'outgoing');
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->telegram_first_name ?? '') . ' ' . ($this->telegram_last_name ?? ''));
    }

    public function markAsRead(): void
    {
        $this->update(['status' => 'read']);
    }

    public function reply(string $message, int $userId): void
    {
        $this->update([
            'status' => 'replied',
            'reply_message' => $message,
            'replied_by' => $userId,
            'replied_at' => now(),
        ]);
    }
}
