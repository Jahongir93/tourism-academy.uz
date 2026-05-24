<?php

namespace App\Http\Controllers\CampusTour;

use App\Http\Controllers\Controller;
use App\Http\Requests\CampusTour\MapSettingsRequest;
use App\Models\CampusTour\MapSettings;
use App\Models\CampusTour\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MapSettingsController extends Controller
{
    public function index()
    {
        $settings = MapSettings::getOrCreate();
        $buildings = Building::active()->ordered()->get();

        return view('campus-tour.admin.map.index', compact('settings', 'buildings'));
    }

    public function update(MapSettingsRequest $request)
    {
        $settings = MapSettings::getOrCreate();
        $data = $request->validated();

        if ($request->hasFile('base_image')) {
            if ($settings->base_image) {
                Storage::disk('public')->delete($settings->base_image);
            }

            $file = $request->file('base_image');
            $filename = 'campus-map-' . time() . '.' . $file->getClientOriginalExtension();
            $data['base_image'] = $file->storeAs('campus-tour/map', $filename, 'public');

            // Get image dimensions
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo) {
                $data['image_width'] = $imageInfo[0];
                $data['image_height'] = $imageInfo[1];
            }
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $settings->update($data);

        return redirect()->route('campus-tour.map.index')
            ->with('success', 'Xarita sozlamalari saqlandi!');
    }

    public function editor()
    {
        $settings = MapSettings::getOrCreate();
        $buildings = Building::active()->ordered()->get();

        return view('campus-tour.admin.map.editor', compact('settings', 'buildings'));
    }

    public function saveMarkers(Request $request)
    {
        $request->validate([
            'markers' => 'required|array',
            'markers.*.id' => 'required|exists:campus_tour_buildings,id',
            'markers.*.marker_x' => 'required|numeric|min:0|max:100',
            'markers.*.marker_y' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($request->markers as $marker) {
            Building::where('id', $marker['id'])->update([
                'marker_x' => $marker['marker_x'],
                'marker_y' => $marker['marker_y'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Markerlar saqlandi!']);
    }
}
