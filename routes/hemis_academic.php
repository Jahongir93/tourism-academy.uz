<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hemis\HemisSyncController;
use App\Http\Controllers\Academic\GPAController;
use App\Http\Controllers\Academic\VedomostController;
use App\Http\Controllers\Academic\AcademicDebtController;

/*
|--------------------------------------------------------------------------
| HEMIS Integration & Academic Routes
|--------------------------------------------------------------------------
|
| Routes for HEMIS integration, GPA calculator, vedomost (grade sheets),
| and academic debt monitoring
|
*/

// HEMIS Sync Routes
Route::prefix('hemis')->middleware(['auth', 'role:SuperAdmin|HR'])->group(function () {
    Route::get('/sync', [HemisSyncController::class, 'index'])->name('hemis.sync.index');
    Route::post('/sync/students', [HemisSyncController::class, 'syncStudents'])->name('hemis.sync.students');
    Route::post('/sync/teachers', [HemisSyncController::class, 'syncTeachers'])->name('hemis.sync.teachers');
    Route::post('/sync/grades', [HemisSyncController::class, 'syncGrades'])->name('hemis.sync.grades');
    Route::post('/sync/full', [HemisSyncController::class, 'fullSync'])->name('hemis.sync.full');
    Route::get('/curriculum', [HemisSyncController::class, 'getCurriculum'])->name('hemis.curriculum');
    Route::get('/schedule', [HemisSyncController::class, 'getSchedule'])->name('hemis.schedule');
    Route::get('/calendar', [HemisSyncController::class, 'getAcademicCalendar'])->name('hemis.calendar');
    Route::get('/test-connection', [HemisSyncController::class, 'testConnection'])->name('hemis.test');

    // HEMIS Settings
    Route::get('/settings', [HemisSyncController::class, 'settings'])->name('hemis.settings');
    Route::post('/settings', [HemisSyncController::class, 'updateSettings'])->name('hemis.settings.update');
});

// GPA Calculator Routes
Route::prefix('academic/gpa')->middleware('auth')->group(function () {
    Route::get('/', [GPAController::class, 'index'])->name('gpa.index');
    Route::get('/semester/{student}', [GPAController::class, 'semesterGPA'])->name('gpa.semester');
    Route::get('/trend/{student}', [GPAController::class, 'trend'])->name('gpa.trend');
    Route::get('/transcript/{student}', [GPAController::class, 'transcript'])->name('gpa.transcript');
    Route::get('/transcript/{student}/download', [GPAController::class, 'downloadTranscript'])->name('gpa.transcript.download');
    Route::get('/failing/{student}', [GPAController::class, 'failingSubjects'])->name('gpa.failing');
    Route::get('/performance/{student}', [GPAController::class, 'subjectPerformance'])->name('gpa.performance');
    Route::get('/graduation/{student}', [GPAController::class, 'graduationStatus'])->name('gpa.graduation');
});

// Vedomost (Grade Sheet) Routes
Route::prefix('academic/vedomost')->middleware('auth')->group(function () {
    Route::get('/', [VedomostController::class, 'index'])->name('vedomost.index');
    Route::get('/{id}/fill', [VedomostController::class, 'fill'])->name('vedomost.fill');
    Route::post('/{id}/fill', [VedomostController::class, 'saveFill'])->name('vedomost.save-fill');
    Route::get('/show', [VedomostController::class, 'show'])->name('vedomost.show');
    Route::get('/create', [VedomostController::class, 'create'])->name('vedomost.create');
    Route::post('/store', [VedomostController::class, 'store'])->name('vedomost.store');
    Route::get('/group/{group}/students', [VedomostController::class, 'getGroupStudents'])->name('vedomost.group.students');
    Route::get('/export', [VedomostController::class, 'export'])->name('vedomost.export');
    Route::get('/print', [VedomostController::class, 'print'])->name('vedomost.print');
    Route::get('/statistics', [VedomostController::class, 'statistics'])->name('vedomost.statistics');

    // Column management routes
    Route::post('/{id}/add-column', [VedomostController::class, 'addColumn'])->name('vedomost.add-column');
    Route::delete('/{vedomostId}/remove-column/{columnId}', [VedomostController::class, 'removeColumn'])->name('vedomost.remove-column');

    // Export route
    Route::get('/{id}/export-word', [VedomostController::class, 'exportWord'])->name('vedomost.export-word');
});

// Academic Debt (Qarzdorlik) Routes
Route::prefix('academic/debt')->middleware('auth')->group(function () {
    Route::get('/', [AcademicDebtController::class, 'index'])->name('academic.debt.index');
    Route::get('/student/{student}', [AcademicDebtController::class, 'studentDebts'])->name('academic.debt.student');
    Route::get('/group/{group}', [AcademicDebtController::class, 'groupDebts'])->name('academic.debt.group');
    Route::get('/faculty/{faculty}', [AcademicDebtController::class, 'facultyDebts'])->name('academic.debt.faculty');
    Route::post('/retake', [AcademicDebtController::class, 'registerRetake'])->name('academic.debt.retake');
    Route::put('/retake/{retake}', [AcademicDebtController::class, 'updateRetake'])->name('academic.debt.retake.update');
    Route::get('/export', [AcademicDebtController::class, 'export'])->name('academic.debt.export');
    Route::post('/notify', [AcademicDebtController::class, 'notifyStudents'])->name('academic.debt.notify');
});
