<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ErrorLogController extends Controller
{
    /**
     * Log frontend errors
     */
    public function logFrontendError(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable|string',
            'message' => 'required|string',
            'source' => 'nullable|string',
            'line' => 'nullable|integer',
            'column' => 'nullable|integer',
            'stack' => 'nullable|string',
            'url' => 'nullable|string',
            'userAgent' => 'nullable|string',
            'viewport' => 'nullable|array',
            'timestamp' => 'nullable|string',
        ]);

        // Log the frontend error
        Log::channel('frontend')->error('Frontend Error', [
            'type' => $validated['type'] ?? 'Unknown',
            'message' => $validated['message'],
            'source' => $validated['source'] ?? 'Unknown',
            'line' => $validated['line'] ?? 0,
            'column' => $validated['column'] ?? 0,
            'stack' => $validated['stack'] ?? 'No stack trace',
            'url' => $validated['url'] ?? $request->header('referer'),
            'userAgent' => $validated['userAgent'] ?? $request->userAgent(),
            'viewport' => $validated['viewport'] ?? null,
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'timestamp' => $validated['timestamp'] ?? now()->toISOString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Error logged successfully'
        ]);
    }

    /**
     * View error logs (admin only)
     */
    public function viewLogs(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasRole(['SuperAdmin', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $logFile = storage_path('logs/laravel.log');
        $frontendLogFile = storage_path('logs/frontend.log');

        $logs = [];

        // Read Laravel logs
        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            $logs['laravel'] = $this->parseLogFile($content);
        }

        // Read Frontend logs
        if (file_exists($frontendLogFile)) {
            $content = file_get_contents($frontendLogFile);
            $logs['frontend'] = $this->parseLogFile($content);
        }

        return view('admin.error-logs', compact('logs'));
    }

    /**
     * Parse log file content
     */
    private function parseLogFile($content)
    {
        $lines = explode("\n", $content);
        $errors = [];
        $currentError = null;

        foreach ($lines as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                if ($currentError) {
                    $errors[] = $currentError;
                }
                $currentError = [
                    'timestamp' => $matches[1],
                    'message' => $line
                ];
            } elseif ($currentError) {
                $currentError['message'] .= "\n" . $line;
            }
        }

        if ($currentError) {
            $errors[] = $currentError;
        }

        return array_slice(array_reverse($errors), 0, 50); // Last 50 errors
    }

    /**
     * Clear error logs (admin only)
     */
    public function clearLogs(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasRole(['SuperAdmin', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $type = $request->input('type', 'all');

        if ($type === 'all' || $type === 'laravel') {
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
            }
        }

        if ($type === 'all' || $type === 'frontend') {
            $frontendLogFile = storage_path('logs/frontend.log');
            if (file_exists($frontendLogFile)) {
                file_put_contents($frontendLogFile, '');
            }
        }

        return redirect()->back()->with('success', 'Loglar tozalandi');
    }
}
