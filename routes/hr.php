<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HR\HRDashboardController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\StudentController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\ReportController;

// HR routes - faqat HR Manager va SuperAdmin uchun
Route::prefix('hr')->middleware(['auth', 'role:hr_manager|superadmin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [HRDashboardController::class, 'index'])->name('hr.dashboard');
    Route::get('/api/dashboard-stats', [HRDashboardController::class, 'getDashboardStats'])->name('hr.api.dashboard-stats');

    // Pending registrations moduli
    Route::prefix('pending-registrations')->name('hr.pending-registrations.')->group(function () {
        Route::get('/', [App\Http\Controllers\HR\PendingRegistrationController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\HR\PendingRegistrationController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [App\Http\Controllers\HR\PendingRegistrationController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [App\Http\Controllers\HR\PendingRegistrationController::class, 'reject'])->name('reject');
        Route::delete('/{id}', [App\Http\Controllers\HR\PendingRegistrationController::class, 'destroy'])->name('destroy');
    });

    // Xodimlar moduli
    Route::prefix('employees')->name('hr.employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::post('/quick-store', [EmployeeController::class, 'quickStore'])->name('quick-store');
        Route::get('/{id}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [EmployeeController::class, 'exportExcel'])->name('export');
        Route::post('/import', [EmployeeController::class, 'import'])->name('import');
    });

    // Talabalar moduli
    Route::prefix('students')->name('hr.students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/create', [StudentController::class, 'create'])->name('create');
        Route::post('/', [StudentController::class, 'store'])->name('store');
        Route::post('/quick-store', [StudentController::class, 'quickStore'])->name('quick-store');
        Route::get('/{id}', [StudentController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [StudentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{id}', [StudentController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [StudentController::class, 'exportExcel'])->name('export');
        Route::post('/import', [StudentController::class, 'import'])->name('import');
    });

    // Davomat moduli
    Route::prefix('attendance')->name('hr.attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/mark', [AttendanceController::class, 'mark'])->name('mark');
        Route::post('/mark', [AttendanceController::class, 'storeMark'])->name('store-mark');
        Route::get('/today', [AttendanceController::class, 'today'])->name('today');
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
        Route::get('/report/export', [AttendanceController::class, 'exportReport'])->name('report.export');
        Route::get('/user/{userId}', [AttendanceController::class, 'userAttendance'])->name('user');
        Route::post('/bulk-mark', [AttendanceController::class, 'bulkMark'])->name('bulk-mark');
        Route::get('/calendar', [AttendanceController::class, 'calendar'])->name('calendar');
        Route::get('/api/calendar-data', [AttendanceController::class, 'calendarData'])->name('api.calendar-data');
    });

    // Hisobotlar
    Route::prefix('reports')->name('hr.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/generate', [ReportController::class, 'generate'])->name('generate');
        Route::post('/generate', [ReportController::class, 'processGenerate'])->name('process-generate');
        Route::get('/download/{type}', [ReportController::class, 'download'])->name('download');
        Route::get('/employee-summary', [ReportController::class, 'employeeSummary'])->name('employee-summary');
        Route::get('/student-summary', [ReportController::class, 'studentSummary'])->name('student-summary');
        Route::get('/attendance-summary', [ReportController::class, 'attendanceSummary'])->name('attendance-summary');
        Route::get('/monthly-report', [ReportController::class, 'monthlyReport'])->name('monthly-report');
    });
});