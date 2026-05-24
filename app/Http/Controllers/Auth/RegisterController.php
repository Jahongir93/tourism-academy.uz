<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    protected $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     * SECURITY FIX: Rate limiting applied via middleware in routes (BUG #33)
     */
    public function register(Request $request)
    {
        try {
            // SECURITY FIX: Enhanced phone validation for Uzbek users (BUG #34)
            $request->validate([
                'name' => 'required|string|max:255|min:3',
                'user_type' => 'required|in:uzbek,foreign',
                'email' => 'required_if:user_type,foreign|email|unique:users,email',
                'phone' => [
                    'required_if:user_type,uzbek',
                    'string',
                    'unique:users,phone',
                    'regex:/^[+]998[0-9]{9}$/', // SECURITY FIX: Uzbek phone format +998XXXXXXXXX
                ],
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:Student,Teacher',
            ]);

            DB::beginTransaction();

            $userData = [
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'status' => 'active',
            ];

            if ($request->user_type === 'uzbek') {
                $userData['phone'] = $request->phone;
            } else {
                $userData['email'] = $request->email;
            }

            $user = User::create($userData);

            // SECURITY FIX: Thread-safe role creation (BUG #36)
            $role = Role::lockForUpdate()->where('name', $request->role)->first();
            if (!$role) {
                $role = Role::create(['name' => $request->role]);
            }
            $user->assignRole($role);

            if ($request->user_type === 'uzbek') {
                $otp = $user->generateOTP();
                $this->otpService->sendOTP($user->phone, $otp);

                DB::commit();

                Auth::login($user);
                return redirect()->route('otp.verify');
            } else {
                event(new Registered($user));

                DB::commit();

                Auth::login($user);
                return redirect()->route('verification.notice');
            }
        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('General registration error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except('password', 'password_confirmation')
            ]);
            return back()->withErrors(['error' => 'Ro\'yxatdan o\'tishda xatolik yuz berdi: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Show OTP verification form
     */
    public function showOTPForm()
    {
        return view('auth.verify-otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        
        if ($user->verifyOTP($request->otp)) {
            return redirect($user->getDashboardRoute())
                ->with('success', 'Telefon raqamingiz muvaffaqiyatli tasdiqlandi!');
        }

        return back()->withErrors([
            'otp' => 'Tasdiqlash kodi noto\'g\'ri yoki muddati tugagan.',
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request)
    {
        $user = Auth::user();
        
        if ($user->user_type !== 'uzbek') {
            return back()->withErrors(['error' => 'OTP faqat O\'zbekiston fuqarolari uchun.']);
        }

        $otp = $user->generateOTP();
        $this->otpService->sendOTP($user->phone, $otp);
        
        return back()->with('success', 'Yangi tasdiqlash kodi yuborildi!');
    }
}