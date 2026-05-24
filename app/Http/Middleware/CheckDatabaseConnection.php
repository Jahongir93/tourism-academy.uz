<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\DatabaseFallbackService;

class CheckDatabaseConnection
{
    protected $fallbackService;

    public function __construct(DatabaseFallbackService $fallbackService)
    {
        $this->fallbackService = $fallbackService;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$this->fallbackService->isConnected()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Database connection is not available. Running in fallback mode.',
                    'fallback_mode' => true
                ], 503);
            }

            if (config('database_fallback.demo_mode')) {
                session()->flash('warning', 'Running in demo mode. Some features may be limited.');
            }
        }

        return $next($request);
    }
}