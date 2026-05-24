<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DekanController;

/*
|--------------------------------------------------------------------------
| Dean Routes
|--------------------------------------------------------------------------
|
| Dekanat boshqaruv paneli uchun routelar
|
*/

Route::middleware(['auth', 'role:Dekan|dekan|Dean|dean|SuperAdmin|admin'])->prefix('dean')->name('dean.')->group(function () {

    // Dashboard
    Route::get('/', [DekanController::class, 'dashboard'])->name('dashboard');

    // Talabalar
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [DekanController::class, 'studentsIndex'])->name('index');
        Route::get('/transfers', [DekanController::class, 'studentsTransfers'])->name('transfers');
        Route::get('/graduates', [DekanController::class, 'studentsGraduates'])->name('graduates');
    });

    // Guruhlar
    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get('/', [DekanController::class, 'groupsIndex'])->name('index');
        Route::get('/curators', [DekanController::class, 'groupsCurators'])->name('curators');
        Route::post('/{group}/curator', [DekanController::class, 'updateCurator'])->name('update-curator');
    });

    // O'qituvchilar
    Route::prefix('teachers')->name('teachers.')->group(function () {
        Route::get('/', [DekanController::class, 'teachersIndex'])->name('index');
    });

    // Kafedralar
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DekanController::class, 'departmentsIndex'])->name('index');
    });

    // Dars jadvali
    Route::prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/', [DekanController::class, 'scheduleIndex'])->name('index');
        Route::get('/create', [DekanController::class, 'scheduleCreate'])->name('create');
        Route::post('/', [DekanController::class, 'scheduleStore'])->name('store');
        Route::get('/{schedule}', [DekanController::class, 'scheduleShow'])->name('show');
        Route::get('/{schedule}/edit', [DekanController::class, 'scheduleEdit'])->name('edit');
        Route::put('/{schedule}', [DekanController::class, 'scheduleUpdate'])->name('update');
        Route::delete('/{schedule}', [DekanController::class, 'scheduleDestroy'])->name('destroy');
        Route::post('/{schedule}/slots', [DekanController::class, 'scheduleSlotStore'])->name('slots.store');
        Route::delete('/slots/{slot}', [DekanController::class, 'scheduleSlotDestroy'])->name('slots.destroy');

        // Imtihonlar
        Route::get('/exams/list', [DekanController::class, 'scheduleExams'])->name('exams');
        Route::get('/exams/create', [DekanController::class, 'examCreate'])->name('exams.create');
        Route::post('/exams', [DekanController::class, 'examStore'])->name('exams.store');
        Route::get('/exams/{exam}', [DekanController::class, 'examShow'])->name('exams.show');
        Route::get('/exams/{exam}/edit', [DekanController::class, 'examEdit'])->name('exams.edit');
        Route::put('/exams/{exam}', [DekanController::class, 'examUpdate'])->name('exams.update');
        Route::delete('/exams/{exam}', [DekanController::class, 'examDestroy'])->name('exams.destroy');
    });

    // O'zlashtirish / Baholar
    Route::prefix('grades')->name('grades.')->group(function () {
        Route::get('/', [DekanController::class, 'gradesIndex'])->name('index');
        Route::get('/gpa', [DekanController::class, 'gradesGpa'])->name('gpa');
        Route::get('/retakes', [DekanController::class, 'gradesRetakes'])->name('retakes');
    });

    // Davomat
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [DekanController::class, 'attendanceIndex'])->name('index');
    });

    // Stipendiya
    Route::prefix('scholarship')->name('scholarship.')->group(function () {
        Route::get('/', [DekanController::class, 'scholarshipIndex'])->name('index');
        Route::get('/applications', [DekanController::class, 'scholarshipApplications'])->name('applications');
    });

    // Hisobotlar
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/students', [DekanController::class, 'reportsStudents'])->name('students');
        Route::get('/grades', [DekanController::class, 'reportsGrades'])->name('grades');
        Route::get('/attendance', [DekanController::class, 'reportsAttendance'])->name('attendance');
    });

    // E'lonlar
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [DekanController::class, 'announcementsIndex'])->name('index');
    });

    // Sozlamalar
    Route::get('/settings', [DekanController::class, 'settings'])->name('settings');
});
