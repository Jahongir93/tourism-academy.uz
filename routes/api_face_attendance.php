<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaceAttendanceController;

/*
|--------------------------------------------------------------------------
| Face Recognition Attendance API Routes
|--------------------------------------------------------------------------
| Supports both web session auth and API token auth (sanctum)
*/

// Web routes (for browser-based requests with session auth)
Route::prefix('api/face-attendance')->middleware(['web', 'auth'])->group(function () {

    // Student face registration
    Route::post('/student/register', [FaceAttendanceController::class, 'registerStudentFace']);
    Route::delete('/student/{studentId}/face', [FaceAttendanceController::class, 'deleteStudentFace']);
    Route::get('/student/{studentId}/face-status', [FaceAttendanceController::class, 'checkFaceStatus']);
    Route::get('/student/{studentId}/photos', [FaceAttendanceController::class, 'getStudentPhotos']);
    Route::get('/student/{studentId}', [FaceAttendanceController::class, 'getStudentInfo']);

    // Face recognition and attendance marking
    Route::post('/recognize-and-mark', [FaceAttendanceController::class, 'recognizeAndMark']);
    Route::post('/mark-attendance', [FaceAttendanceController::class, 'markStudentAttendance']);

    // Attendance management
    Route::get('/today-attendance', [FaceAttendanceController::class, 'getTodayAttendance']);
    Route::get('/attendance-history', [FaceAttendanceController::class, 'getAttendanceHistory']);
    Route::get('/export-excel', [FaceAttendanceController::class, 'exportToExcel']);

    // Groups and students
    Route::get('/groups', [FaceAttendanceController::class, 'getGroups']);
    Route::get('/enrolled-students', [FaceAttendanceController::class, 'getEnrolledStudents']);

    // Schedule check
    Route::get('/check-schedule', [FaceAttendanceController::class, 'checkSchedule']);

    // Statistics
    Route::get('/stats', [FaceAttendanceController::class, 'getStats']);

    // Staff/Employee attendance routes
    Route::prefix('staff')->group(function () {
        Route::post('/register', [FaceAttendanceController::class, 'registerStaffFace']);
        Route::delete('/{staffId}/face', [FaceAttendanceController::class, 'deleteStaffFace']);
        Route::get('/{staffId}/face-status', [FaceAttendanceController::class, 'checkStaffFaceStatus']);
        Route::get('/{staffId}', [FaceAttendanceController::class, 'getStaffInfo']);
        Route::post('/recognize-and-mark', [FaceAttendanceController::class, 'recognizeAndMarkStaff']);
        Route::get('/today-attendance', [FaceAttendanceController::class, 'getStaffTodayAttendance']);
        Route::get('/list', [FaceAttendanceController::class, 'getStaffList']);
    });
});

// API routes with sanctum (for mobile/external API clients)
Route::prefix('api/face-attendance/v2')->middleware(['auth:sanctum'])->group(function () {

    // ==========================================
    // New Student-Based Routes
    // ==========================================

    // Student face registration
    Route::post('/student/register', [FaceAttendanceController::class, 'registerStudentFace']);
    Route::delete('/student/{studentId}/face', [FaceAttendanceController::class, 'deleteStudentFace']);
    Route::get('/student/{studentId}/face-status', [FaceAttendanceController::class, 'checkFaceStatus']);

    // Face recognition and attendance marking
    Route::post('/recognize-and-mark', [FaceAttendanceController::class, 'recognizeAndMark']);

    // Attendance management
    Route::get('/today-attendance', [FaceAttendanceController::class, 'getTodayAttendance']);
    Route::get('/attendance-history', [FaceAttendanceController::class, 'getAttendanceHistory']);
    Route::get('/export-excel', [FaceAttendanceController::class, 'exportToExcel']);

    // Groups and students
    Route::get('/groups', [FaceAttendanceController::class, 'getGroups']);
    Route::get('/enrolled-students', [FaceAttendanceController::class, 'getEnrolledStudents']);

    // Schedule check
    Route::get('/check-schedule', [FaceAttendanceController::class, 'checkSchedule']);

    // Statistics
    Route::get('/stats', [FaceAttendanceController::class, 'getStats']);

    // ==========================================
    // Legacy Routes (backward compatibility)
    // ==========================================

    // Face Registration Routes
    Route::prefix('face')->group(function () {
        Route::post('/register', [FaceAttendanceController::class, 'registerFace']);
        Route::put('/update/{userId?}', [FaceAttendanceController::class, 'updateFace']);
        Route::delete('/delete/{userId?}', [FaceAttendanceController::class, 'deleteFace']);
        Route::post('/recognize', [FaceAttendanceController::class, 'recognizeFace']);
        Route::get('/status/{userId?}', [FaceAttendanceController::class, 'checkFaceStatus']);
        Route::get('/users', [FaceAttendanceController::class, 'getUsersWithFaces']);
    });

    // Attendance Routes
    Route::prefix('attendance')->group(function () {
        Route::post('/check-in', [FaceAttendanceController::class, 'checkIn']);
        Route::post('/check-out', [FaceAttendanceController::class, 'checkOut']);
        Route::get('/history/{userId?}', [FaceAttendanceController::class, 'getAttendanceHistory']);
        Route::get('/today', [FaceAttendanceController::class, 'getTodayAttendance']);
        Route::get('/stats', [FaceAttendanceController::class, 'getAttendanceStats']);
    });
});