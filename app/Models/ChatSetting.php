<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ChatSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'is_public',
        'description',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Sensitive keys that should be encrypted
     */
    protected static array $encryptedKeys = [
        'telegram_bot_token',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type, $key);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', bool $isPublic = false, ?string $description = null): self
    {
        // Encrypt sensitive data
        if (in_array($key, static::$encryptedKeys)) {
            $value = Crypt::encryptString($value);
        }

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'is_public' => $isPublic,
                'description' => $description,
            ]
        );
    }

    /**
     * Cast value to appropriate type
     */
    protected static function castValue($value, string $type, string $key)
    {
        // Decrypt sensitive data
        if (in_array($key, static::$encryptedKeys)) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }

        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Get Telegram bot token
     */
    public static function getTelegramBotToken(): ?string
    {
        return static::get('telegram_bot_token');
    }

    /**
     * Get Telegram bot username
     */
    public static function getTelegramBotUsername(): ?string
    {
        return static::get('telegram_bot_username');
    }

    /**
     * Get Telegram webhook URL
     */
    public static function getTelegramWebhookUrl(): ?string
    {
        // Auto-generate from APP_URL if not explicitly set
        $saved = static::get('telegram_webhook_url');
        if ($saved) {
            return $saved;
        }

        // Auto-generate
        return url('/api/telegram/webhook');
    }

    /**
     * Check if Telegram is enabled
     */
    public static function isTelegramEnabled(): bool
    {
        return (bool) static::get('telegram_enabled', false);
    }

    /**
     * Get all public settings
     */
    public static function getPublicSettings(): array
    {
        return static::where('is_public', true)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => static::castValue($setting->value, $setting->type, $setting->key)];
            })
            ->toArray();
    }
}
