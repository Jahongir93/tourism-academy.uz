<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StreamingFaceController;

/*
|--------------------------------------------------------------------------
| Streaming Face Recognition API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api/face/stream')->middleware(['api'])->group(function () {
    // Stream processing endpoints
    Route::post('/process', [StreamingFaceController::class, 'processStreamFrame']);
    Route::post('/batch', [StreamingFaceController::class, 'processBatchFrames']);

    // Monitoring endpoints
    Route::get('/monitoring/data', [StreamingFaceController::class, 'getMonitoringData']);
    Route::get('/camera/status', [StreamingFaceController::class, 'getCameraStatus']);

    // Admin endpoints (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/manual-override', [StreamingFaceController::class, 'manualOverride']);
    });
});