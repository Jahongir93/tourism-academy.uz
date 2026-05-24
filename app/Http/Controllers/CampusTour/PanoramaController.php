<?php

namespace App\Http\Controllers\CampusTour;

use App\Http\Controllers\Controller;
use App\Http\Requests\CampusTour\PanoramaRequest;
use App\Models\CampusTour\Panorama;
use App\Models\CampusTour\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PanoramaController extends Controller
{
    public function index(Request $request)
    {
        $query = Panorama::with('building')->ordered();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        $panoramas = $query->paginate(12);
        $buildings = Building::active()->ordered()->get();

        return view('campus-tour.admin.panoramas.index', compact('panoramas', 'buildings'));
    }

    public function create()
    {
        $buildings = Building::active()->ordered()->get();
        return view('campus-tour.admin.panoramas.create', compact('buildings'));
    }

    public function store(PanoramaRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $data['image_path'] = $file->storeAs('campus-tour/panoramas', $filename, 'public');

            // Create thumbnail
            $this->createThumbnail($file, $filename, $data);
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured', false);

        Panorama::create($data);

        return redirect()->route('campus-tour.panoramas.index')
            ->with('success', 'Panorama muvaffaqiyatli yaratildi!');
    }

    public function show(Panorama $panorama)
    {
        $panorama->load('building');
        return view('campus-tour.admin.panoramas.show', compact('panorama'));
    }

    public function edit(Panorama $panorama)
    {
        $buildings = Building::active()->ordered()->get();
        return view('campus-tour.admin.panoramas.edit', compact('panorama', 'buildings'));
    }

    public function update(PanoramaRequest $request, Panorama $panorama)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($panorama->image_path) {
                Storage::disk('public')->delete($panorama->image_path);
            }
            if ($panorama->thumbnail_path) {
                Storage::disk('public')->delete($panorama->thumbnail_path);
            }

            $file = $request->file('image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $data['image_path'] = $file->storeAs('campus-tour/panoramas', $filename, 'public');

            $this->createThumbnail($file, $filename, $data);
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured', false);

        $panorama->update($data);

        return redirect()->route('campus-tour.panoramas.index')
            ->with('success', 'Panorama muvaffaqiyatli yangilandi!');
    }

    public function destroy(Panorama $panorama)
    {
        if ($panorama->image_path) {
            Storage::disk('public')->delete($panorama->image_path);
        }
        if ($panorama->thumbnail_path) {
            Storage::disk('public')->delete($panorama->thumbnail_path);
        }

        $panorama->delete();

        return redirect()->route('campus-tour.panoramas.index')
            ->with('success', 'Panorama o\'chirildi!');
    }

    public function preview(Panorama $panorama)
    {
        return view('campus-tour.admin.panoramas.preview', compact('panorama'));
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:campus_tour_panoramas,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Panorama::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }

    private function createThumbnail($file, $filename, &$data)
    {
        try {
            $thumbnailPath = 'campus-tour/panoramas/thumbnails/' . $filename;

            // Simple copy for now - you can add image manipulation later
            $data['thumbnail_path'] = $file->storeAs(
                'campus-tour/panoramas/thumbnails',
                $filename,
                'public'
            );
        } catch (\Exception $e) {
            // Thumbnail creation failed, continue without it
            \Log::warning('Thumbnail creation failed: ' . $e->getMessage());
        }
    }
}
