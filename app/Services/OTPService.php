<?php

namespace App\Services;

use App\Models\OtpSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OTPService
{
    protected $settings = null;

    /**
     * Get settings with lazy loading
     */
    protected function getSettings(): OtpSetting
    {
        if ($this->settings === null) {
            $this->settings = OtpSetting::getSettings();
        }
        return $this->settings;
    }

    /**
     * Send OTP via SMS
     */
    public function sendOTP(string $phone, string $otp): bool
    {
        try {
            $settings = $this->getSettings();

            // Check if SMS is enabled
            if (!$settings->sms_enabled) {
                Log::info("SMS disabled, OTP not sent to {$phone}");
                return true;
            }

            // Test mode - just log
            if ($settings->is_test_mode) {
                Log::info("TEST MODE - OTP for {$phone}: {$otp}");
                return true;
            }

            // Local environment - just log
            if (config('app.env') === 'local') {
                Log::info("LOCAL ENV - OTP sent to {$phone}: {$otp}");
                return true;
            }

            $phone = $this->formatPhoneNumber($phone);
            $locale = app()->getLocale();
            $template = $settings->getSmsTemplate($locale);
            $message = $settings->formatMessage($template, $otp);

            // Send based on provider
            switch ($settings->sms_provider) {
                case 'eskiz':
                    return $this->sendViaEskiz($phone, $message);
                case 'playmobile':
                    return $this->sendViaPlayMobile($phone, $message);
                default:
                    return $this->sendViaCustom($phone, $message);
            }

        } catch (\Exception $e) {
            Log::error("OTP sending error: " . $e->getMessage(), [
                'phone' => $phone,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send via Eskiz SMS Gateway
     */
    protected function sendViaEskiz(string $phone, string $message): bool
    {
        $settings = $this->getSettings();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $settings->sms_api_key,
        ])->post($settings->sms_api_url . '/message/sms/send', [
            'mobile_phone' => $phone,
            'message' => $message,
            'from' => $settings->sms_sender_name,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['status']) && $data['status'] === 'waiting') {
                Log::info("Eskiz SMS sent successfully to {$phone}", ['response' => $data]);
                return true;
            }
        }

        Log::error("Eskiz SMS failed to {$phone}: " . $response->body());
        return false;
    }

    /**
     * Send via PlayMobile SMS Gateway
     */
    protected function sendViaPlayMobile(string $phone, string $message): bool
    {
        $settings = $this->getSettings();
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($settings->sms_api_key . ':' . $settings->sms_api_secret),
            'Content-Type' => 'application/json',
        ])->post($settings->sms_api_url, [
            'messages' => [
                [
                    'recipient' => $phone,
                    'message-id' => uniqid('otp_'),
                    'sms' => [
                        'originator' => $settings->sms_sender_name,
                        'content' => [
                            'text' => $message,
                        ],
                    ],
                ],
            ],
        ]);

        if ($response->successful()) {
            Log::info("PlayMobile SMS sent successfully to {$phone}");
            return true;
        }

        Log::error("PlayMobile SMS failed to {$phone}: " . $response->body());
        return false;
    }

    /**
     * Send via custom SMS provider
     */
    protected function sendViaCustom(string $phone, string $message): bool
    {
        $settings = $this->getSettings();
        $response = Http::post($settings->sms_api_url, [
            'api_key' => $settings->sms_api_key,
            'phone' => $phone,
            'message' => $message,
            'from' => $settings->sms_sender_name,
        ]);

        if ($response->successful()) {
            Log::info("Custom SMS sent successfully to {$phone}");
            return true;
        }

        Log::error("Custom SMS failed to {$phone}: " . $response->body());
        return false;
    }

    /**
     * Format phone number to international format
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Remove + if present
        $phone = ltrim($phone, '+');

        // Add 998 if not present
        if (substr($phone, 0, 3) !== '998') {
            $phone = '998' . $phone;
        }

        return $phone;
    }

    /**
     * Send OTP via Email (for foreign users)
     */
    public function sendOTPEmail(string $email, string $otp): bool
    {
        try {
            $settings = $this->getSettings();

            if (!$settings->email_verification_enabled) {
                Log::info("Email verification disabled, OTP not sent to {$email}");
                return true;
            }

            // Test mode - just log
            if ($settings->is_test_mode) {
                Log::info("TEST MODE - Email OTP for {$email}: {$otp}");
                return true;
            }

            // Local environment - just log
            if (config('app.env') === 'local') {
                Log::info("LOCAL ENV - Email OTP sent to {$email}: {$otp}");
                return true;
            }

            $locale = app()->getLocale();
            $subject = $settings->getEmailSubject($locale);
            $template = $settings->getEmailTemplate($locale);
            $htmlContent = $settings->formatMessage($template, $otp);

            Mail::send([], [], function ($mail) use ($email, $subject, $htmlContent) {
                $mail->to($email)
                    ->subject($subject)
                    ->html($htmlContent);
            });

            Log::info("Email OTP sent successfully to {$email}");
            return true;

        } catch (\Exception $e) {
            Log::error("Email OTP sending error: " . $e->getMessage(), [
                'email' => $email,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Generate OTP code
     */
    public function generateOTP(): string
    {
        return $this->getSettings()->generateOtpCode();
    }

    /**
     * Get OTP expiry minutes
     */
    public function getExpiryMinutes(): int
    {
        return $this->getSettings()->otp_expiry_minutes ?? 5;
    }

    /**
     * Get max attempts
     */
    public function getMaxAttempts(): int
    {
        return $this->getSettings()->max_attempts ?? 3;
    }

    /**
     * Get resend cooldown seconds
     */
    public function getResendCooldown(): int
    {
        return $this->getSettings()->resend_cooldown_seconds ?? 60;
    }
}
