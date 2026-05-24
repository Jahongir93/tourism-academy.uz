<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*',
        'api/support-chat/*',
        '*/api/support-chat/*',
        'hemis/callback',
        'webhook/*',
        'database/status',
        'fallback/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // Support chat API uchun CSRF ni o'tkazib yuborish
        $path = $request->path();
        if (str_contains($path, 'api/support-chat')) {
            return $next($request);
        }

        // Database ulangan yoki yo'qligini tekshirish
        try {
            $fallbackService = app(\App\Services\DatabaseFallbackService::class);

            // Agar database ulanmagan bo'lsa va bu GET so'rov bo'lsa, CSRF tekshirmaslik
            if (!$fallbackService->isConnected() && $request->isMethod('GET')) {
                return $next($request);
            }

            // Session driver file bo'lsa va database ulanmagan bo'lsa
            if (config('session.driver') === 'file' && !$fallbackService->isConnected()) {
                // CSRF tokenni yangilash
                $request->session()->regenerateToken();
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return parent::handle($request, $next);
    }
}