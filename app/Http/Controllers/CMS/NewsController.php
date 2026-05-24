<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CmsNews;
use App\Models\CmsNewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = CmsNews::with(['category', 'author'])
            ->orderBy('published_at', 'desc');
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_uz', 'like', "%{$search}%")
                  ->orWhere('title_ru', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('content_uz', 'like', "%{$search}%");
            });
        }
        
        $news = $query->paginate(20);
        $categories = CmsNewsCategory::active()->get();
        
        return view('cms.news.index', compact('news', 'categories'));
    }

    public function create()
    {
        $categories = CmsNewsCategory::active()->get();
        return view('cms.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_uz' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_news',
            'excerpt_uz' => 'nullable|string',
            'excerpt_ru' => 'nullable|string',
            'excerpt_en' => 'nullable|string',
            'content_uz' => 'required|string',
            'content_ru' => 'nullable|string',
            'content_en' => 'nullable|string',
            'featured_image' => 'nullable|image|max:5120', // 5MB
            'gallery.*' => 'nullable|image|max:5120', // 5MB har bir rasm
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB PDF/Word
            'category_id' => 'nullable|exists:cms_news_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'tags' => 'nullable|string',
            'published_at' => 'nullable|date'
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title_uz']);
        }

        // Upload featured image
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $dir = public_path('images/news');
            if (!is_dir($dir)) mkdir($dir, 0775, true);
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $validated['featured_image'] = 'images/news/' . $filename;
        }

        // Upload gallery images
        $galleryPaths = [];
        if ($request->hasFile('gallery')) {
            $galleryDir = public_path('images/news/gallery');
            if (!is_dir($galleryDir)) mkdir($galleryDir, 0775, true);
            foreach ($request->file('gallery') as $file) {
                $filename = time() . '_' . uniqid() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $file->move($galleryDir, $filename);
                $galleryPaths[] = 'images/news/gallery/' . $filename;
            }
        }
        $validated['gallery'] = $galleryPaths;

        // Upload attachments (PDF/Word)
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            $filesDir = public_path('files/news');
            if (!is_dir($filesDir)) mkdir($filesDir, 0775, true);
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move($filesDir, $filename);
                $attachmentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => 'files/news/' . $filename,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType()
                ];
            }
        }
        $validated['attachments'] = $attachmentPaths;

        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        $validated['author_id'] = Auth::id();

        $news = CmsNews::create($validated);

        return redirect()->route('cms.news.edit', $news)
            ->with('success', 'Yangilik muvaffaqiyatli yaratildi!');
    }

    public function edit(CmsNews $news)
    {
        $categories = CmsNewsCategory::active()->get();
        return view('cms.news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, CmsNews $news)
    {
        $validated = $request->validate([
            'title_uz' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_news,slug,' . $news->id,
            'excerpt_uz' => 'nullable|string',
            'excerpt_ru' => 'nullable|string',
            'excerpt_en' => 'nullable|string',
            'content_uz' => 'required|string',
            'content_ru' => 'nullable|string',
            'content_en' => 'nullable|string',
            'featured_image' => 'nullable|image|max:5120', // 5MB
            'gallery.*' => 'nullable|image|max:5120', // 5MB har bir rasm
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB PDF/Word
            'category_id' => 'nullable|exists:cms_news_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'tags' => 'nullable|string',
            'published_at' => 'nullable|date'
        ]);

        // Upload featured image
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $dir = public_path('images/news');
            if (!is_dir($dir)) mkdir($dir, 0775, true);
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $validated['featured_image'] = 'images/news/' . $filename;
        }

        // Upload gallery images (append to existing)
        if ($request->hasFile('gallery')) {
            $galleryDir = public_path('images/news/gallery');
            if (!is_dir($galleryDir)) mkdir($galleryDir, 0775, true);
            $existingGallery = $news->gallery ?? [];
            foreach ($request->file('gallery') as $file) {
                $filename = time() . '_' . uniqid() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $file->move($galleryDir, $filename);
                $existingGallery[] = 'images/news/gallery/' . $filename;
            }
            $validated['gallery'] = $existingGallery;
        }

        // Upload attachments (append to existing)
        if ($request->hasFile('attachments')) {
            $filesDir = public_path('files/news');
            if (!is_dir($filesDir)) mkdir($filesDir, 0775, true);
            $existingAttachments = $news->attachments ?? [];
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move($filesDir, $filename);
                $existingAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => 'files/news/' . $filename,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType()
                ];
            }
            $validated['attachments'] = $existingAttachments;
        }

        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        $news->update($validated);

        return redirect()->route('cms.news.edit', $news)
            ->with('success', 'Yangilik muvaffaqiyatli yangilandi!');
    }

    public function destroy(CmsNews $news)
    {
        $news->delete();
        return redirect()->route('cms.news.index')
            ->with('success', 'Yangilik muvaffaqiyatli o\'chirildi!');
    }
}