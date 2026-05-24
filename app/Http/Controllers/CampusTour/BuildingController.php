<?php

namespace App\Http\Controllers\CampusTour;

use App\Http\Controllers\Controller;
use App\Http\Requests\CampusTour\BuildingRequest;
use App\Models\CampusTour\Building;
use App\Models\CampusTour\Panorama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BuildingController extends Controller
{
    public function index(Request $request)
    {
        $query = Building::with('panorama')->ordered();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $buildings = $query->paginate(12);

        return view('campus-tour.admin.buildings.index', compact('buildings'));
    }

    public function create()
    {
        $panoramas = Panorama::active()->ordered()->get();
        return view('campus-tour.admin.buildings.create', compact('panoramas'));
    }

    public function store(BuildingRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $data['image'] = $file->storeAs('campus-tour/buildings', $filename, 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        Building::create($data);

        return redirect()->route('campus-tour.buildings.index')
            ->with('success', 'Bino muvaffaqiyatli qo\'shildi!');
    }

    public function show(Building $building)
    {
        $building->load(['panorama', 'panoramas']);
        return view('campus-tour.admin.buildings.show', compact('building'));
    }

    public function edit(Building $building)
    {
        $panoramas = Panorama::active()->ordered()->get();
        return view('campus-tour.admin.buildings.edit', compact('building', 'panoramas'));
    }

    public function update(BuildingRequest $request, Building $building)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($building->image) {
                Storage::disk('public')->delete($building->image);
            }

            $file = $request->file('image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $data['image'] = $file->storeAs('campus-tour/buildings', $filename, 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $building->update($data);

        return redirect()->route('campus-tour.buildings.index')
            ->with('success', 'Bino muvaffaqiyatli yangilandi!');
    }

    public function destroy(Building $building)
    {
        if ($building->image) {
            Storage::disk('public')->delete($building->image);
        }

        $building->delete();

        return redirect()->route('campus-tour.buildings.index')
            ->with('success', 'Bino o\'chirildi!');
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:campus_tour_buildings,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Building::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }

    public function updateMarker(Request $request, Building $building)
    {
        $request->validate([
            'marker_x' => 'required|numeric|min:0|max:100',
            'marker_y' => 'required|numeric|min:0|max:100',
        ]);

        $building->update([
            'marker_x' => $request->marker_x,
            'marker_y' => $request->marker_y,
        ]);

        return response()->json(['success' => true, 'building' => $building]);
    }
}
