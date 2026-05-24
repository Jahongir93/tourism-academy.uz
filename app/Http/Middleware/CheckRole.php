<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Support both pipe (|) and comma separators in role definitions
        $allRoles = [];
        foreach ($roles as $role) {
            // Split by pipe if present
            $splitRoles = explode('|', $role);
            $allRoles = array_merge($allRoles, $splitRoles);
        }

        foreach ($allRoles as $role) {
            if ($user->hasRole(trim($role))) {
                return $next($request);
            }
        }

        abort(403, 'Sizda bu sahifaga kirish huquqi yo\'q.');
    }
}