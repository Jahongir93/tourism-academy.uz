<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HemisAuthenticated
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

        if (!$user->hemis_id) {
            return redirect()->route('dashboard')
                ->with('warning', 'Bu sahifaga kirish uchun HEMIS orqali autentifikatsiya talab qilinadi.');
        }

        return $next($request);
    }
}