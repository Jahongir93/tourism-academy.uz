<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeRegisterController extends Controller
{
    /**
     * Xodimlar uchun ro'yxatga olish formasi
     */
    public function showRegistrationForm()
    {
        return view('auth.employee-register');
    }

    /**
     * Xodimni ro'yxatdan o'tkazish (pending holatda)
     * SECURITY FIX: Rate limiting applied via middleware in routes (BUG #44)
     */
    public function register(Request $request)
    {
        // SECURITY FIX: Enhanced validation (BUG #43, #45, #46)
        $request->validate([
            'full_name' => 'required|string|max:255|min:3',
            'email' => 'nullable|email|max:255|unique:users,email|unique:pending_registrations,email',
            // SECURITY FIX: Uzbek phone format validation (BUG #45)
            'phone' => [
                'nullable',
                'string',
                'regex:/^[+]998[0-9]{9}$/',
                'unique:users,phone',
                'unique:pending_registrations,phone',
            ],
            'user_type' => 'required|in:uzbek,foreign',
            'position' => 'required|string|max:255',
            'additional_info' => 'nullable|string|max:1000',
            // SECURITY FIX: Stronger password requirement (BUG #43)
            'password' => 'required|string|min:8|confirmed',
        ], [
            'full_name.required' => 'Ism va familiyangizni kiriting',
            'full_name.min' => 'Ism va familiya kamida 3 ta harfdan iborat bo\'lishi kerak',
            'email.email' => 'Email manzil formati noto\'g\'ri',
            'email.unique' => 'Bu email manzil allaqachon ro\'yxatdan o\'tgan',
            'phone.regex' => 'Telefon raqam formati noto\'g\'ri (+998XXXXXXXXX)',
            'phone.unique' => 'Bu telefon raqam allaqachon ro\'yxatdan o\'tgan',
            'user_type.required' => 'Fuqarolik turini tanlang',
            'position.required' => 'Lavozimingizni kiriting',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo\'lishi kerak',
            'password.confirmed' => 'Parollar mos kelmadi',
        ]);

        // SECURITY FIX: Enforce email OR phone requirement (BUG #46)
        if (!$request->email && !$request->phone) {
            return back()->withErrors(['error' => 'Email yoki telefon raqam kamida bittasini kiriting'])
                        ->withInput();
        }

        DB::beginTransaction();

        try {
            // SECURITY FIX: Hash password before storing (BUG #42 - CRITICAL)
            $hashedPassword = Hash::make($request->password);

            // Pending registratsiyaga saqlash
            PendingRegistration::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'type' => 'employee',
                'user_type' => $request->user_type,
                'position' => $request->position,
                'additional_info' => json_encode([
                    'password_hash' => $hashedPassword, // SECURITY FIX: Store hashed password (BUG #42)
                    'extra_info' => $request->additional_info,
                ]),
                'status' => 'pending',
            ]);

            DB::commit();

            // Muvaffaqiyat sahifasiga yo'naltirish
            return redirect()->route('registration.pending')
                ->with('success', 'Murojaatingiz muvaffaqiyatli yuborildi! Admin yoki HR tomonidan tasdiqlanishini kuting.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Employee registration error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except('password', 'password_confirmation')
            ]);
            return back()->withErrors(['error' => 'Ro\'yxatdan o\'tishda xatolik yuz berdi: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Pending registratsiya sahifasi
     */
    public function showPendingPage()
    {
        return view('auth.registration-pending');
    }
}
