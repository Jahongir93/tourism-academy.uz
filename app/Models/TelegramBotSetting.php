<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramBotSetting extends Model
{
    protected $fillable = [
        'bot_token',
        'bot_username',
        'webhook_url',
        'is_active',
        'welcome_message',
        'auto_replies',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_replies' => 'array',
    ];

    /**
     * Get the first settings record or create a new one
     */
    public static function getSettings(): self
    {
        return self::first() ?? new self();
    }

    /**
     * Check if bot is active
     */
    public function isActive(): bool
    {
        return $this->is_active && !empty($this->bot_token);
    }

    /**
     * Get auto reply for a keyword
     */
    public function getAutoReply(string $keyword): ?string
    {
        $replies = $this->auto_replies ?? [];
        return $replies[strtolower($keyword)] ?? null;
    }
}
