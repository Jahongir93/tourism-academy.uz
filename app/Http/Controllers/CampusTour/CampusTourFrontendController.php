<?php

namespace App\Http\Controllers\CampusTour;

use App\Http\Controllers\Controller;
use App\Models\CampusTour\Panorama;
use App\Models\CampusTour\Building;
use App\Models\CampusTour\TransportRoute;
use App\Models\CampusTour\MapSettings;
use Illuminate\Http\Request;

class CampusTourFrontendController extends Controller
{
    public function index()
    {
        $featuredPanoramas = Panorama::active()->featured()->ordered()->limit(6)->get();
        $buildings = Building::active()->ordered()->limit(8)->get();
        $routes = TransportRoute::active()->ordered()->limit(6)->get();

        return view('campus-tour.frontend.index', compact('featuredPanoramas', 'buildings', 'routes'));
    }

    public function virtualTour()
    {
        $panoramas = Panorama::active()->ordered()->with('building')->get();
        $buildings = Building::active()->ordered()->get();

        return view('campus-tour.frontend.virtual-tour', compact('panoramas', 'buildings'));
    }

    public function panorama(Panorama $panorama)
    {
        if (!$panorama->is_active) {
            abort(404);
        }

        $panorama->load('building');
        $relatedPanoramas = Panorama::active()
            ->where('id', '!=', $panorama->id)
            ->when($panorama->building_id, function ($q) use ($panorama) {
                $q->where('building_id', $panorama->building_id);
            })
            ->ordered()
            ->limit(4)
            ->get();

        return view('campus-tour.frontend.panorama', compact('panorama', 'relatedPanoramas'));
    }

    public function map()
    {
        $settings = MapSettings::getActive() ?? MapSettings::getOrCreate();
        $buildings = Building::active()->withCoordinates()->ordered()->with('panorama')->get();

        return view('campus-tour.frontend.map', compact('settings', 'buildings'));
    }

    public function building(Building $building)
    {
        if (!$building->is_active) {
            abort(404);
        }

        $building->load(['panorama', 'panoramas']);

        return view('campus-tour.frontend.building', compact('building'));
    }

    public function directions()
    {
        $routes = TransportRoute::active()->ordered()->get();
        $types = TransportRoute::TYPES;

        $groupedRoutes = $routes->groupBy('type');

        return view('campus-tour.frontend.directions', compact('routes', 'types', 'groupedRoutes'));
    }

    public function route(TransportRoute $route)
    {
        if (!$route->is_active) {
            abort(404);
        }

        return view('campus-tour.frontend.route', compact('route'));
    }

    // API endpoints for AJAX
    public function apiBuildings()
    {
        $buildings = Building::active()
            ->withCoordinates()
            ->ordered()
            ->with('panorama:id,title,thumbnail_path')
            ->get(['id', 'title', 'short_description', 'icon', 'color', 'marker_x', 'marker_y', 'latitude', 'longitude', 'panorama_id', 'image']);

        return response()->json([
            'success' => true,
            'buildings' => $buildings->map(function ($building) {
                return [
                    'id' => $building->id,
                    'title' => $building->getLocalizedTitle(),
                    'description' => $building->short_description,
                    'icon' => $building->marker_icon,
                    'color' => $building->color,
                    'marker_x' => $building->marker_x,
                    'marker_y' => $building->marker_y,
                    'lat' => $building->latitude,
                    'lng' => $building->longitude,
                    'image' => $building->image_url,
                    'has_panorama' => $building->panorama_id !== null,
                    'panorama_id' => $building->panorama_id,
                ];
            }),
        ]);
    }

    public function apiPanoramas()
    {
        $panoramas = Panorama::active()
            ->ordered()
            ->get(['id', 'title', 'description', 'image_path', 'thumbnail_path', 'building_id']);

        return response()->json([
            'success' => true,
            'panoramas' => $panoramas->map(function ($panorama) {
                return [
                    'id' => $panorama->id,
                    'title' => $panorama->getLocalizedTitle(),
                    'description' => $panorama->getLocalizedDescription(),
                    'image' => $panorama->image_url,
                    'thumbnail' => $panorama->thumbnail_url,
                    'building_id' => $panorama->building_id,
                ];
            }),
        ]);
    }
}
