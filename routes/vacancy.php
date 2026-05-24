<?php

use App\Http\Controllers\VacancyFrontendController;
use App\Http\Controllers\Admin\VacancyController;
use App\Http\Controllers\Admin\VacancyApplicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vacancy Routes
|--------------------------------------------------------------------------
|
| Vakansiyalar moduli uchun routelar
|
*/

// Frontend routes (public)
Route::prefix('vacancies')->name('vacancies.')->group(function () {
    Route::get('/', [VacancyFrontendController::class, 'index'])->name('index');
    Route::get('/{vacancy}', [VacancyFrontendController::class, 'show'])->name('show');
    Route::get('/{vacancy}/apply', [VacancyFrontendController::class, 'apply'])->name('apply');
    Route::post('/{vacancy}/apply', [VacancyFrontendController::class, 'storeApplication'])->name('storeApplication');
    Route::get('/application/{application}/success', [VacancyFrontendController::class, 'success'])->name('success');
});

// Admin routes (protected)
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Vacancies CRUD
    Route::resource('vacancies', VacancyController::class)->names('admin.vacancies');
    Route::patch('vacancies/{vacancy}/toggle-status', [VacancyController::class, 'toggleStatus'])->name('admin.vacancies.toggle-status');

    // Applications
    Route::get('vacancy-applications', [VacancyApplicationController::class, 'index'])->name('admin.vacancy-applications.index');
    Route::get('vacancy-applications/export', [VacancyApplicationController::class, 'export'])->name('admin.vacancy-applications.export');
    Route::get('vacancy-applications/{application}', [VacancyApplicationController::class, 'show'])->name('admin.vacancy-applications.show');
    Route::patch('vacancy-applications/{application}/status', [VacancyApplicationController::class, 'updateStatus'])->name('admin.vacancy-applications.update-status');
    Route::post('vacancy-applications/{application}/response', [VacancyApplicationController::class, 'sendResponse'])->name('admin.vacancy-applications.send-response');
    Route::delete('vacancy-applications/{application}', [VacancyApplicationController::class, 'destroy'])->name('admin.vacancy-applications.destroy');
    Route::post('vacancy-applications/bulk-update-status', [VacancyApplicationController::class, 'bulkUpdateStatus'])->name('admin.vacancy-applications.bulk-update-status');
});
