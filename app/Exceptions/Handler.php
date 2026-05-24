<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Database\QueryException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log detailed error information
            \Log::error('Exception occurred', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
        });

        $this->renderable(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch. Sahifani yangilang.',
                    'error' => 'csrf_token_mismatch'
                ], 419);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Sessiya muddati tugadi. Iltimos, sahifani yangilab qaytadan urinib ko\'ring.');
        });

        $this->renderable(function (QueryException $e, $request) {
            // Database connection error
            if ($e->getCode() == 2002 || $e->getCode() == 1045) {
                $fallbackService = app(\App\Services\DatabaseFallbackService::class);

                if (!$fallbackService->isConnected() && config('database_fallback.fallback_enabled')) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Ma\'lumotlar bazasi mavjud emas. Fallback rejim faol.',
                            'fallback_mode' => true
                        ], 503);
                    }

                    // Fallback sahifaga yo'naltirish
                    if (!$request->is('fallback*') && !$request->is('database/status')) {
                        return redirect()->route('fallback.index');
                    }
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Ma\'lumotlar bazasiga ulanib bo\'lmadi.',
                        'error' => 'database_connection_failed'
                    ], 503);
                }

                return response()->view('errors.database_offline', [], 503);
            }
        });

        // Catch all other exceptions and show detailed error in development
        $this->renderable(function (Throwable $e, $request) {
            if (config('app.debug')) {
                // In development mode, show detailed error
                if (!$request->expectsJson()) {
                    return $this->renderDetailedError($e);
                }
            }

            // In production, log and show user-friendly error
            if (!$request->expectsJson() && !in_array($e->getCode(), [404, 403, 401, 419])) {
                \Log::error('Unhandled Exception', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'url' => $request->fullUrl(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->view('errors.500', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode()
                ], 500);
            }
        });
    }

    /**
     * Render detailed error page for development
     */
    protected function renderDetailedError(Throwable $e)
    {
        $errorDetails = [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'trace' => collect(explode("\n", $e->getTraceAsString()))->take(15)->toArray(),
        ];

        return response()->view('errors.debug', compact('errorDetails'), 500);
    }
}