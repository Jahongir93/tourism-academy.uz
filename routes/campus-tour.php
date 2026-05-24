<?php

use App\Http\Controllers\CampusTour\PanoramaController;
use App\Http\Controllers\CampusTour\BuildingController;
use App\Http\Controllers\CampusTour\TransportRouteController;
use App\Http\Controllers\CampusTour\MapSettingsController;
use App\Http\Controllers\CampusTour\CampusTourFrontendController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Campus Tour Routes
|--------------------------------------------------------------------------
|
| Interaktiv xaritalar va virtual tur moduli uchun routelar
|
*/

// Frontend routes (public)
Route::prefix('campus-tour')->name('campus-tour.public.')->group(function () {
    Route::get('/', [CampusTourFrontendController::class, 'index'])->name('index');
    Route::get('/virtual-tour', [CampusTourFrontendController::class, 'virtualTour'])->name('virtual-tour');
    Route::get('/panorama/{panorama}', [CampusTourFrontendController::class, 'panorama'])->name('panorama');
    Route::get('/map', [CampusTourFrontendController::class, 'map'])->name('map');
    Route::get('/building/{building}', [CampusTourFrontendController::class, 'building'])->name('building');
    Route::get('/directions', [CampusTourFrontendController::class, 'directions'])->name('directions');
    Route::get('/route/{route}', [CampusTourFrontendController::class, 'route'])->name('route');

    // API endpoints
    Route::get('/api/buildings', [CampusTourFrontendController::class, 'apiBuildings'])->name('api.buildings');
    Route::get('/api/panoramas', [CampusTourFrontendController::class, 'apiPanoramas'])->name('api.panoramas');
});

// Admin routes (protected)
Route::prefix('admin/campus-tour')->name('campus-tour.')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', function () {
        $panoramasCount = \App\Models\CampusTour\Panorama::count();
        $buildingsCount = \App\Models\CampusTour\Building::count();
        $routesCount = \App\Models\CampusTour\TransportRoute::count();

        return view('campus-tour.admin.dashboard', compact('panoramasCount', 'buildingsCount', 'routesCount'));
    })->name('dashboard');

    // Panoramas CRUD
    Route::resource('panoramas', PanoramaController::class);
    Route::get('panoramas/{panorama}/preview', [PanoramaController::class, 'preview'])->name('panoramas.preview');
    Route::post('panoramas/update-order', [PanoramaController::class, 'updateOrder'])->name('panoramas.update-order');

    // Buildings CRUD
    Route::resource('buildings', BuildingController::class);
    Route::post('buildings/update-order', [BuildingController::class, 'updateOrder'])->name('buildings.update-order');
    Route::patch('buildings/{building}/marker', [BuildingController::class, 'updateMarker'])->name('buildings.update-marker');

    // Transport Routes CRUD
    Route::resource('routes', TransportRouteController::class);
    Route::post('routes/update-order', [TransportRouteController::class, 'updateOrder'])->name('routes.update-order');

    // Map Settings
    Route::get('map', [MapSettingsController::class, 'index'])->name('map.index');
    Route::put('map', [MapSettingsController::class, 'update'])->name('map.update');
    Route::get('map/editor', [MapSettingsController::class, 'editor'])->name('map.editor');
    Route::post('map/markers', [MapSettingsController::class, 'saveMarkers'])->name('map.save-markers');
});
