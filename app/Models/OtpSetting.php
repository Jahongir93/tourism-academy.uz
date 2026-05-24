<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class OtpSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'sms_provider',
        'sms_api_url',
        'sms_api_key',
        'sms_api_secret',
        'sms_sender_name',
        'email_provider',
        'email_verification_enabled',
        'otp_length',
        'otp_expiry_minutes',
        'max_attempts',
        'resend_cooldown_seconds',
        'max_otp_per_hour',
        'max_otp_per_day',
        'sms_template_uz',
        'sms_template_ru',
        'sms_template_en',
        'email_subject_uz',
        'email_subject_ru',
        'email_subject_en',
        'email_template_uz',
        'email_template_ru',
        'email_template_en',
        'sms_enabled',
        'is_test_mode',
        'test_otp_code',
    ];

    protected $casts = [
        'sms_enabled' => 'boolean',
        'email_verification_enabled' => 'boolean',
        'is_test_mode' => 'boolean',
        'otp_length' => 'integer',
        'otp_expiry_minutes' => 'integer',
        'max_attempts' => 'integer',
        'resend_cooldown_seconds' => 'integer',
        'max_otp_per_hour' => 'integer',
        'max_otp_per_day' => 'integer',
    ];

    /**
     * Get the singleton settings instance
     */
    public static function getSettings(): self
    {
        try {
            return Cache::remember('otp_settings', 3600, function () {
                return self::first() ?? self::getDefaultSettings();
            });
        } catch (\Exception $e) {
            // Return default settings if database is not available
            return self::getDefaultSettings();
        }
    }

    /**
     * Get default settings without database
     */
    protected static function getDefaultSettings(): self
    {
        $settings = new self();
        $settings->sms_provider = 'eskiz';
        $settings->sms_api_url = 'https://notify.eskiz.uz/api';
        $settings->sms_sender_name = 'TourismAcad';
        $settings->otp_length = 6;
        $settings->otp_expiry_minutes = 5;
        $settings->max_attempts = 3;
        $settings->resend_cooldown_seconds = 60;
        $settings->max_otp_per_hour = 5;
        $settings->max_otp_per_day = 15;
        $settings->sms_enabled = true;
        $settings->email_verification_enabled = true;
        $settings->is_test_mode = true;
        $settings->sms_template_uz = 'Sizning tasdiqlash kodingiz: {otp}. Kod {minutes} daqiqa amal qiladi.';
        $settings->email_subject_uz = 'Tasdiqlash kodi';
        $settings->email_template_uz = '<p>Sizning tasdiqlash kodingiz: <strong>{otp}</strong></p><p>Kod {minutes} daqiqa amal qiladi.</p>';
        return $settings;
    }

    /**
     * Clear cached settings
     */
    public static function clearCache(): void
    {
        Cache::forget('otp_settings');
    }

    /**
     * Get SMS template by locale
     */
    public function getSmsTemplate(string $locale = 'uz'): string
    {
        $field = 'sms_template_' . $locale;
        return $this->$field ?? $this->sms_template_uz ?? 'Sizning tasdiqlash kodingiz: {otp}';
    }

    /**
     * Get Email subject by locale
     */
    public function getEmailSubject(string $locale = 'uz'): string
    {
        $field = 'email_subject_' . $locale;
        return $this->$field ?? $this->email_subject_uz ?? 'Tasdiqlash kodi';
    }

    /**
     * Get Email template by locale
     */
    public function getEmailTemplate(string $locale = 'uz'): string
    {
        $field = 'email_template_' . $locale;
        return $this->$field ?? $this->email_template_uz ?? '<p>Sizning tasdiqlash kodingiz: <strong>{otp}</strong></p>';
    }

    /**
     * Generate formatted message
     */
    public function formatMessage(string $template, string $otp): string
    {
        return str_replace(
            ['{otp}', '{minutes}'],
            [$otp, $this->otp_expiry_minutes],
            $template
        );
    }

    /**
     * Generate OTP code based on settings
     */
    public function generateOtpCode(): string
    {
        if ($this->is_test_mode && $this->test_otp_code) {
            return $this->test_otp_code;
        }

        $length = $this->otp_length ?? 6;
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return (string) rand($min, $max);
    }

    /**
     * Get available SMS providers
     */
    public static function getSmsProviders(): array
    {
        return [
            'eskiz' => [
                'name' => 'Eskiz SMS',
                'url' => 'https://notify.eskiz.uz/api',
                'description' => 'Eskiz SMS Gateway (O\'zbekiston)',
            ],
            'playmobile' => [
                'name' => 'PlayMobile',
                'url' => 'https://send.smsxabar.uz/broker-api/send',
                'description' => 'PlayMobile SMS Gateway',
            ],
            'custom' => [
                'name' => 'Boshqa provider',
                'url' => '',
                'description' => 'O\'zingizning SMS provideringiz',
            ],
        ];
    }
}
