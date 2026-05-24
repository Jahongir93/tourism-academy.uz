<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     * SECURITY FIX: Rate limiting applied via middleware in routes (BUG #29)
     */
    public function login(Request $request)
    {
        // SECURITY FIX: Enhanced validation
        $request->validate([
            'login' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'remember' => 'boolean',
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // SECURITY FIX: Regenerate session to prevent session fixation (BUG #32)
            $request->session()->regenerate();

            $user = Auth::user();

            // Verification check disabled - all users can login
            // if (!$user->isVerified() && empty($user->hemis_id)) {
            //     Auth::logout();
            //     $request->session()->invalidate();
            //     $request->session()->regenerateToken();
            //     return back()->withErrors([
            //         'login' => 'Sizning hisobingiz tasdiqlanmagan. Iltimos, avval hisobingizni tasdiqlang.',
            //     ])->withInput($request->only('login'));
            // }

            return redirect()->intended('/dashboard');
        }

        // SECURITY NOTE: Generic error message prevents user enumeration (BUG #31 - INTENTIONAL)
        throw ValidationException::withMessages([
            'login' => 'Kiritilgan ma\'lumotlar noto\'g\'ri.',
        ]);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}