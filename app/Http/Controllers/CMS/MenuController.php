<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CmsMenu;
use App\Models\CmsMenuItem;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = CmsMenu::with('menuItems')->get();
        return view('cms.menus.index', compact('menus'));
    }

    public function create()
    {
        $pages = CmsPage::published()->orderBy('title_uz')->get();
        return view('cms.menus.create', compact('pages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|in:header,footer,sidebar',
            'is_active' => 'boolean'
        ]);
        
        $menu = CmsMenu::create($validated);
        
        return redirect()->route('cms.menus.edit', $menu)
            ->with('success', 'Menyu muvaffaqiyatli yaratildi!');
    }

    public function edit(CmsMenu $menu)
    {
        $pages = CmsPage::published()->orderBy('title_uz')->get();
        $menu->load('menuItems');
        return view('cms.menus.edit', compact('menu', 'pages'));
    }

    public function update(Request $request, CmsMenu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|in:header,footer,sidebar',
            'is_active' => 'boolean'
        ]);
        
        $menu->update($validated);
        
        return redirect()->route('cms.menus.edit', $menu)
            ->with('success', 'Menyu muvaffaqiyatli yangilandi!');
    }

    public function destroy(CmsMenu $menu)
    {
        $menu->delete();
        return redirect()->route('cms.menus.index')
            ->with('success', 'Menyu muvaffaqiyatli o\'chirildi!');
    }

    public function storeItem(Request $request, CmsMenu $menu)
    {
        $validated = $request->validate([
            'title_uz' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'url' => 'required|string|max:500',
            'page_id' => 'nullable|exists:cms_pages,id',
            'parent_id' => 'nullable|exists:cms_menu_items,id',
            'order_position' => 'nullable|integer|min:1',
            'icon' => 'nullable|string|max:100',
        ]);

        $validated['menu_id'] = $menu->id;
        $validated['type'] = $request->page_id ? 'page' : 'custom';
        $validated['is_active'] = true;
        $validated['order_position'] = $validated['order_position'] ?? ($menu->menuItems->count() + 1);

        CmsMenuItem::create($validated);

        return redirect()->route('cms.menus.edit', $menu)
            ->with('success', 'Menyu elementi muvaffaqiyatli qo\'shildi!');
    }

    public function updateItem(Request $request, CmsMenu $menu, CmsMenuItem $item)
    {
        $validated = $request->validate([
            'title_uz' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'url' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:cms_menu_items,id',
            'order_position' => 'nullable|integer|min:1',
            'icon' => 'nullable|string|max:100',
        ]);

        $item->update($validated);

        return redirect()->route('cms.menus.edit', $menu)
            ->with('success', 'Menyu elementi muvaffaqiyatli yangilandi!');
    }

    public function destroyItem(CmsMenu $menu, CmsMenuItem $item)
    {
        $item->children()->delete();
        $item->delete();

        return redirect()->route('cms.menus.edit', $menu)
            ->with('success', 'Menyu elementi muvaffaqiyatli o\'chirildi!');
    }
}