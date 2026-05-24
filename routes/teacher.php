<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\LMSController;
use App\Http\Controllers\Teacher\GroupController;
use App\Http\Controllers\Teacher\JournalController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\CurriculumController;
use App\Http\Controllers\Teacher\VedomostController;
use App\Http\Controllers\Teacher\ScheduleController;
use App\Http\Controllers\Teacher\GradeController;
use App\Http\Controllers\Teacher\AssignmentController;
use App\Http\Controllers\Teacher\ProfileController;

// Teacher routes - faqat Teacher va SuperAdmin uchun
Route::prefix('teacher')->middleware(['auth', 'role:teacher|Teacher|superadmin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    Route::get('/api/dashboard-stats', [TeacherDashboardController::class, 'getDashboardStats'])->name('teacher.api.dashboard-stats');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('teacher.profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('teacher.profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('teacher.profile.change-password');

    // LMS moduli
    Route::prefix('lms')->name('teacher.lms.')->group(function () {
        Route::get('/materials', [LMSController::class, 'materials'])->name('materials');
        Route::get('/videos', [LMSController::class, 'videos'])->name('videos');
        Route::get('/tests', [LMSController::class, 'tests'])->name('tests');
        Route::get('/upload', [LMSController::class, 'upload'])->name('upload');
        Route::post('/upload', [LMSController::class, 'storeUpload'])->name('store-upload');
        Route::get('/material/{id}', [LMSController::class, 'showMaterial'])->name('material.show');
        Route::get('/material/{id}/edit', [LMSController::class, 'editMaterial'])->name('material.edit');
        Route::put('/material/{id}', [LMSController::class, 'updateMaterial'])->name('material.update');
        Route::delete('/material/{id}', [LMSController::class, 'deleteMaterial'])->name('material.delete');
    });

    // Guruhlar moduli
    Route::prefix('groups')->name('teacher.groups.')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('index');
        Route::get('/students', [GroupController::class, 'students'])->name('students');
        Route::get('/messages', [GroupController::class, 'messages'])->name('messages');
        Route::post('/messages/send', [GroupController::class, 'sendMessage'])->name('send-message');
        Route::get('/statistics', [GroupController::class, 'statistics'])->name('statistics');
        Route::get('/{id}', [GroupController::class, 'show'])->name('show');
        Route::get('/{id}/students', [GroupController::class, 'groupStudents'])->name('group-students');
    });

    // Jurnal moduli
    Route::prefix('journal')->name('teacher.journal.')->group(function () {
        Route::get('/', [JournalController::class, 'index'])->name('index');
        Route::get('/grades', [JournalController::class, 'grades'])->name('grades');
        Route::post('/grades', [JournalController::class, 'storeGrades'])->name('store-grades');
        Route::get('/topics', [JournalController::class, 'topics'])->name('topics');
        Route::post('/topics', [JournalController::class, 'storeTopics'])->name('store-topics');
        Route::get('/export', [JournalController::class, 'export'])->name('export');
        Route::get('/group/{groupId}', [JournalController::class, 'groupJournal'])->name('group');
        Route::post('/quick-grade', [JournalController::class, 'quickGrade'])->name('quick-grade');
    });

    // Davomat moduli
    Route::prefix('attendance')->name('teacher.attendance.')->group(function () {
        Route::get('/mark', [AttendanceController::class, 'mark'])->name('mark');
        Route::post('/mark', [AttendanceController::class, 'storeMark'])->name('store-mark');
        Route::get('/today', [AttendanceController::class, 'today'])->name('today');
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
        Route::get('/group/{groupId}', [AttendanceController::class, 'groupAttendance'])->name('group');
        Route::post('/bulk-mark', [AttendanceController::class, 'bulkMark'])->name('bulk-mark');
        Route::get('/export/{groupId}', [AttendanceController::class, 'exportAttendance'])->name('export');
    });

    // O'quv reja moduli
    Route::prefix('curriculum')->name('teacher.curriculum.')->group(function () {
        Route::get('/view', [CurriculumController::class, 'view'])->name('view');
        Route::get('/create', [CurriculumController::class, 'create'])->name('create');
        Route::post('/store', [CurriculumController::class, 'store'])->name('store');
        Route::get('/edit', [CurriculumController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CurriculumController::class, 'update'])->name('update');
        Route::get('/materials', [CurriculumController::class, 'materials'])->name('materials');
        Route::post('/materials/upload', [CurriculumController::class, 'uploadMaterial'])->name('upload-material');
        Route::delete('/materials/{id}', [CurriculumController::class, 'deleteMaterial'])->name('delete-material');
    });

    // Vedmost moduli
    Route::prefix('vedomost')->name('teacher.vedomost.')->group(function () {
        Route::get('/create', [VedomostController::class, 'create'])->name('create');
        Route::post('/store', [VedomostController::class, 'store'])->name('store');
        Route::get('/list', [VedomostController::class, 'list'])->name('list');
        Route::get('/fill/{id}', [VedomostController::class, 'fill'])->name('fill');
        Route::post('/fill/{id}', [VedomostController::class, 'storeFill'])->name('store-fill');
        Route::get('/submit/{id}', [VedomostController::class, 'submit'])->name('submit');
        Route::post('/submit/{id}', [VedomostController::class, 'processSubmit'])->name('process-submit');
        Route::get('/view/{id}', [VedomostController::class, 'view'])->name('view');
        Route::get('/export/{id}', [VedomostController::class, 'export'])->name('export');
    });

    // Dars jadvali
    Route::prefix('schedule')->name('teacher.schedule.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::get('/week', [ScheduleController::class, 'weekSchedule'])->name('week');
        Route::get('/month', [ScheduleController::class, 'monthSchedule'])->name('month');
        Route::get('/export', [ScheduleController::class, 'export'])->name('export');
        Route::post('/request-change', [ScheduleController::class, 'requestChange'])->name('request-change');
    });

    // Baholar moduli - web.php dagi routelar ishlatiladi
    // See routes/web.php for grades routes to avoid duplication

    // Topshiriqlar moduli
    Route::prefix('assignments')->name('teacher.assignments.')->group(function () {
        Route::get('/', [AssignmentController::class, 'index'])->name('index');
        Route::get('/create', [AssignmentController::class, 'create'])->name('create');
        Route::post('/store', [AssignmentController::class, 'store'])->name('store');
        Route::get('/{id}', [AssignmentController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AssignmentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AssignmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [AssignmentController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/submissions', [AssignmentController::class, 'submissions'])->name('submissions');
        Route::get('/submission/{submissionId}', [AssignmentController::class, 'viewSubmission'])->name('view-submission');
        Route::post('/submission/{submissionId}/grade', [AssignmentController::class, 'gradeSubmission'])->name('grade-submission');
    });
});