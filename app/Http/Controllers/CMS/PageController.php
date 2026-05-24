<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = CmsPage::with(['parent', 'creator'])
            ->orderBy('order_position')
            ->orderBy('created_at', 'desc');
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_uz', 'like', "%{$search}%")
                  ->orWhere('title_ru', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }
        
        $pages = $query->paginate(20);
        
        return view('cms.pages.index', compact('pages'));
    }

    public function create()
    {
        $templates = CmsTemplate::where('type', 'page')
            ->where('is_active', true)
            ->get();
        
        $pages = CmsPage::root()
            ->orderBy('order_position')
            ->get();
        
        return view('cms.pages.create', compact('templates', 'pages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_uz' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages',
            'meta_description_uz' => 'nullable|string',
            'meta_description_ru' => 'nullable|string',
            'meta_description_en' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'content_uz' => 'nullable|string',
            'content_ru' => 'nullable|string',
            'content_en' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'template' => 'nullable|string',
            'parent_id' => 'nullable|exists:cms_pages,id',
            'order_position' => 'nullable|integer',
            'status' => 'required|in:draft,published,archived',
            'is_homepage' => 'boolean',
            'show_in_menu' => 'boolean',
            'show_in_footer' => 'boolean',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'page_builder_data' => 'nullable|array',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date'
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title_uz']);
        }
        
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('cms/pages', 'public');
        }
        
        if ($request->hasFile('og_image')) {
            $validated['og_image'] = $request->file('og_image')
                ->store('cms/pages/og', 'public');
        }
        
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $page = CmsPage::create($validated);

        // Check if user wants to go to builder
        if ($request->input('action') === 'builder') {
            return redirect()->route('cms.pages.builder', $page)
                ->with('success', 'Sahifa muvaffaqiyatli yaratildi! Endi vizual tahrirlash mumkin.');
        }

        return redirect()->route('cms.pages.edit', $page)
            ->with('success', 'Sahifa muvaffaqiyatli yaratildi!');
    }

    public function edit(CmsPage $page)
    {
        $templates = CmsTemplate::where('type', 'page')
            ->where('is_active', true)
            ->get();
        
        $pages = CmsPage::where('id', '!=', $page->id)
            ->root()
            ->orderBy('order_position')
            ->get();
        
        return view('cms.pages.edit', compact('page', 'templates', 'pages'));
    }

    public function update(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'title_uz' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug,' . $page->id,
            'meta_description_uz' => 'nullable|string',
            'meta_description_ru' => 'nullable|string',
            'meta_description_en' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'content_uz' => 'nullable|string',
            'content_ru' => 'nullable|string',
            'content_en' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'template' => 'nullable|string',
            'parent_id' => 'nullable|exists:cms_pages,id',
            'order_position' => 'nullable|integer',
            'status' => 'required|in:draft,published,archived',
            'is_homepage' => 'boolean',
            'show_in_menu' => 'boolean',
            'show_in_footer' => 'boolean',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'page_builder_data' => 'nullable|array',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date'
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('cms/pages', 'public');
        }

        if ($request->hasFile('og_image')) {
            $validated['og_image'] = $request->file('og_image')
                ->store('cms/pages/og', 'public');
        }

        $validated['updated_by'] = Auth::id();

        $page->update($validated);

        return redirect()->route('cms.pages.edit', $page)
            ->with('success', 'Sahifa muvaffaqiyatli yangilandi!');
    }

    public function destroy(CmsPage $page)
    {
        if ($page->is_homepage) {
            return back()->with('error', 'Bosh sahifani o\'chirish mumkin emas!');
        }
        
        if ($page->children()->exists()) {
            return back()->with('error', 'Ichki sahifalari bor sahifani o\'chirish mumkin emas!');
        }
        
        $page->delete();
        
        return redirect()->route('cms.pages.index')
            ->with('success', 'Sahifa muvaffaqiyatli o\'chirildi!');
    }

    public function builder(CmsPage $page)
    {
        return view('cms.pages.builder', compact('page'));
    }

    public function updateBuilder(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'page_builder_data' => 'required|array'
        ]);
        
        $page->update([
            'page_builder_data' => $validated['page_builder_data'],
            'updated_by' => Auth::id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Sahifa muvaffaqiyatli saqlandi!'
        ]);
    }

    public function preview(CmsPage $page)
    {
        return view('cms.pages.preview', compact('page'));
    }

    public function duplicate(CmsPage $page)
    {
        $newPage = $page->replicate();
        $newPage->title_uz = $page->title_uz . ' (Nusxa)';
        $newPage->slug = Str::slug($newPage->title_uz);
        $newPage->status = 'draft';
        $newPage->is_homepage = false;
        $newPage->created_by = Auth::id();
        $newPage->updated_by = Auth::id();
        $newPage->views_count = 0;
        $newPage->save();
        
        return redirect()->route('cms.pages.edit', $newPage)
            ->with('success', 'Sahifa muvaffaqiyatli nusxalandi!');
    }
}