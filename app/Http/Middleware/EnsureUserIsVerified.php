<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->isVerified()) {
            if ($user->user_type === 'uzbek') {
                return redirect()->route('otp.verify')
                    ->with('warning', 'Iltimos, telefon raqamingizni tasdiqlang.');
            } else {
                return redirect()->route('verification.notice')
                    ->with('warning', 'Iltimos, elektron pochtangizni tasdiqlang.');
            }
        }

        return $next($request);
    }
}