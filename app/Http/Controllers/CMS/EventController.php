<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CmsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = CmsEvent::orderBy('start_date', 'desc');
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_uz', 'like', "%{$search}%")
                  ->orWhere('title_ru', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $events = $query->paginate(20);
        
        return view('cms.events.index', compact('events'));
    }

    public function create()
    {
        return view('cms.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_uz' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_events',
            'description_uz' => 'required|string',
            'description_ru' => 'nullable|string',
            'description_en' => 'nullable|string',
            'content_uz' => 'nullable|string',
            'content_ru' => 'nullable|string',
            'content_en' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'type' => 'required|in:conference,seminar,workshop,meeting,ceremony,other',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'is_featured' => 'boolean',
            'is_online' => 'boolean',
            'online_link' => 'nullable|string|max:255',
            'requires_registration' => 'boolean',
            'max_participants' => 'nullable|integer|min:1'
        ]);
        
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title_uz']);
        }
        
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $this->storeEventImage($request->file('featured_image'));
        }

        $validated['created_by'] = Auth::id();
        
        $event = CmsEvent::create($validated);
        
        return redirect()->route('cms.events.edit', $event)
            ->with('success', 'Tadbir muvaffaqiyatli yaratildi!');
    }

    public function edit(CmsEvent $event)
    {
        return view('cms.events.edit', compact('event'));
    }

    public function update(Request $request, CmsEvent $event)
    {
        $validated = $request->validate([
            'title_uz' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_events,slug,' . $event->id,
            'description_uz' => 'required|string',
            'description_ru' => 'nullable|string',
            'description_en' => 'nullable|string',
            'content_uz' => 'nullable|string',
            'content_ru' => 'nullable|string',
            'content_en' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'type' => 'required|in:conference,seminar,workshop,meeting,ceremony,other',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'is_featured' => 'boolean',
            'is_online' => 'boolean',
            'online_link' => 'nullable|string|max:255',
            'requires_registration' => 'boolean',
            'max_participants' => 'nullable|integer|min:1'
        ]);
        
        if ($request->hasFile('featured_image')) {
            // Delete old image if it exists in public dir
            if ($event->featured_image && str_starts_with($event->featured_image, 'images/events/')) {
                $oldPath = public_path($event->featured_image);
                if (file_exists($oldPath)) @unlink($oldPath);
            }
            $validated['featured_image'] = $this->storeEventImage($request->file('featured_image'));
        }

        $event->update($validated);
        
        return redirect()->route('cms.events.edit', $event)
            ->with('success', 'Tadbir muvaffaqiyatli yangilandi!');
    }

    private function storeEventImage($file): string
    {
        $dir = public_path('images/events');
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $filename = time() . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($dir, $filename);
        return 'images/events/' . $filename;
    }

    public function destroy(CmsEvent $event)
    {
        $event->delete();
        return redirect()->route('cms.events.index')
            ->with('success', 'Tadbir muvaffaqiyatli o\'chirildi!');
    }
}