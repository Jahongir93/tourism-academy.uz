<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckHRRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Faqat HR Manager yoki SuperAdmin ruxsat beriladi
        if (!$user->hasRole(['hr_manager', 'superadmin'])) {
            abort(403, 'Sizda bu sahifaga kirish huquqi yo\'q. Faqat HR Manager va SuperAdmin kirishi mumkin.');
        }

        return $next($request);
    }
}