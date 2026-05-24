<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtpSetting;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OtpSettingsController extends Controller
{
    /**
     * Display OTP settings page
     */
    public function index()
    {
        $settings = OtpSetting::first() ?? new OtpSetting();
        $providers = OtpSetting::getSmsProviders();

        return view('admin.settings.otp.index', compact('settings', 'providers'));
    }

    /**
     * Update OTP settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'sms_provider' => 'required|in:eskiz,playmobile,custom',
            'sms_api_url' => 'required_if:sms_provider,custom|nullable|url',
            'sms_api_key' => 'nullable|string|max:500',
            'sms_api_secret' => 'nullable|string|max:500',
            'sms_sender_name' => 'required|string|max:50',
            'otp_length' => 'required|integer|min:4|max:8',
            'otp_expiry_minutes' => 'required|integer|min:1|max:30',
            'max_attempts' => 'required|integer|min:1|max:10',
            'resend_cooldown_seconds' => 'required|integer|min:30|max:300',
            'max_otp_per_hour' => 'required|integer|min:1|max:20',
            'max_otp_per_day' => 'required|integer|min:1|max:50',
            'sms_template_uz' => 'required|string|max:500',
            'sms_template_ru' => 'nullable|string|max:500',
            'sms_template_en' => 'nullable|string|max:500',
            'email_subject_uz' => 'required|string|max:200',
            'email_subject_ru' => 'nullable|string|max:200',
            'email_subject_en' => 'nullable|string|max:200',
            'email_template_uz' => 'required|string|max:2000',
            'email_template_ru' => 'nullable|string|max:2000',
            'email_template_en' => 'nullable|string|max:2000',
            'test_otp_code' => 'nullable|string|min:4|max:8',
        ]);

        $settings = OtpSetting::first();

        if (!$settings) {
            $settings = new OtpSetting();
        }

        // Get provider URL if not custom
        $providers = OtpSetting::getSmsProviders();
        $smsApiUrl = $request->sms_provider === 'custom'
            ? $request->sms_api_url
            : ($providers[$request->sms_provider]['url'] ?? $request->sms_api_url);

        $settings->fill([
            'sms_provider' => $request->sms_provider,
            'sms_api_url' => $smsApiUrl,
            'sms_api_key' => $request->sms_api_key,
            'sms_api_secret' => $request->sms_api_secret,
            'sms_sender_name' => $request->sms_sender_name,
            'otp_length' => $request->otp_length,
            'otp_expiry_minutes' => $request->otp_expiry_minutes,
            'max_attempts' => $request->max_attempts,
            'resend_cooldown_seconds' => $request->resend_cooldown_seconds,
            'max_otp_per_hour' => $request->max_otp_per_hour,
            'max_otp_per_day' => $request->max_otp_per_day,
            'sms_template_uz' => $request->sms_template_uz,
            'sms_template_ru' => $request->sms_template_ru ?? $request->sms_template_uz,
            'sms_template_en' => $request->sms_template_en ?? $request->sms_template_uz,
            'email_subject_uz' => $request->email_subject_uz,
            'email_subject_ru' => $request->email_subject_ru ?? $request->email_subject_uz,
            'email_subject_en' => $request->email_subject_en ?? $request->email_subject_uz,
            'email_template_uz' => $request->email_template_uz,
            'email_template_ru' => $request->email_template_ru ?? $request->email_template_uz,
            'email_template_en' => $request->email_template_en ?? $request->email_template_uz,
            'sms_enabled' => $request->has('sms_enabled'),
            'email_verification_enabled' => $request->has('email_verification_enabled'),
            'is_test_mode' => $request->has('is_test_mode'),
            'test_otp_code' => $request->test_otp_code,
        ]);

        $settings->save();

        // Clear cache
        OtpSetting::clearCache();

        return redirect()->route('admin.settings.otp.index')
            ->with('success', 'OTP sozlamalari muvaffaqiyatli saqlandi!');
    }

    /**
     * Test SMS sending
     */
    public function testSms(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string|regex:/^\+998[0-9]{9}$/',
        ]);

        try {
            $otpService = app(OTPService::class);
            $testOtp = '123456';
            $result = $otpService->sendOTP($request->test_phone, $testOtp);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => "Test SMS muvaffaqiyatli yuborildi: {$request->test_phone}",
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'SMS yuborishda xatolik yuz berdi. Loglarni tekshiring.',
            ]);
        } catch (\Exception $e) {
            Log::error('Test SMS error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Eskiz token (for Eskiz provider)
     */
    public function getEskizToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $response = \Illuminate\Support\Facades\Http::post('https://notify.eskiz.uz/api/auth/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']['token'])) {
                    return response()->json([
                        'success' => true,
                        'token' => $data['data']['token'],
                        'message' => 'Token muvaffaqiyatli olindi!',
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Token olishda xatolik: ' . ($response->json()['message'] ?? 'Noma\'lum xato'),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage(),
            ], 500);
        }
    }
}
