<?php

use Illuminate\Support\Facades\Route;
use App\Models\LmsCourse;
use App\Models\LmsMaterial;
use App\Models\LmsVideo;
use App\Models\LmsPracticeTest;
use App\Models\LmsLibraryBook;
use App\Models\LmsCertificate;
use App\Models\LmsCourseEnrollment;
use App\Models\LmsForumPost;

Route::get('/test-lms-models', function () {
    try {
        $results = [];

        $results['LmsCourse'] = LmsCourse::count();
        $results['LmsMaterial'] = LmsMaterial::count();
        $results['LmsVideo'] = LmsVideo::count();
        $results['LmsPracticeTest'] = LmsPracticeTest::count();
        $results['LmsLibraryBook'] = LmsLibraryBook::count();
        $results['LmsCertificate'] = LmsCertificate::count();
        $results['LmsCourseEnrollment'] = LmsCourseEnrollment::count();
        $results['LmsForumPost'] = LmsForumPost::count();

        return response()->json([
            'status' => 'success',
            'counts' => $results
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// LMS Test Import Routes
Route::middleware(['auth'])->prefix('lms/tests')->name('lms.tests.')->group(function () {
    Route::get('/import', [App\Http\Controllers\LMS\LmsTestController::class, 'showImportForm'])->name('import');
    Route::post('/import', [App\Http\Controllers\LMS\LmsTestController::class, 'import'])->name('import.store');
    Route::get('/template/download', [App\Http\Controllers\LMS\LmsTestController::class, 'downloadTemplate'])->name('template.download');
});
