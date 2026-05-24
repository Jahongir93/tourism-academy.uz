<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from request or session
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            if (in_array($locale, ['uz', 'ru', 'en'])) {
                session(['locale' => $locale]);
                app()->setLocale($locale);
            }
        } elseif (session()->has('locale')) {
            app()->setLocale(session('locale'));
        } else {
            // Default to Uzbek
            app()->setLocale('uz');
            session(['locale' => 'uz']);
        }

        return $next($request);
    }
}
