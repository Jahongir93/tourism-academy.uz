<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::whereNull('parent_id')
            ->with('children')
            ->orderBy('order')
            ->get();

        return view('admin.menu.index', compact('menuItems'));
    }

    public function create()
    {
        $parentMenuItems = MenuItem::whereNull('parent_id')->orderBy('order')->get();
        return view('admin.menu.create', compact('parentMenuItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label_uz' => 'required|string|max:255',
            'label_en' => 'nullable|string|max:255',
            'label_ru' => 'nullable|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menu_items,id',
            'order' => 'required|integer',
            'is_active' => 'boolean',
            'open_in_new_tab' => 'boolean'
        ]);

        MenuItem::create($request->all());

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menyu elementi muvaffaqiyatli qo\'shildi!');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(MenuItem $menu)
    {
        $parentMenuItems = MenuItem::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('order')
            ->get();

        return view('admin.menu.edit', compact('menu', 'parentMenuItems'));
    }

    public function update(Request $request, MenuItem $menu)
    {
        $request->validate([
            'label_uz' => 'required|string|max:255',
            'label_en' => 'nullable|string|max:255',
            'label_ru' => 'nullable|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menu_items,id',
            'order' => 'required|integer',
            'is_active' => 'boolean',
            'open_in_new_tab' => 'boolean'
        ]);

        $menu->update($request->all());

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menyu elementi muvaffaqiyatli yangilandi!');
    }

    public function destroy(MenuItem $menu)
    {
        $menu->delete();

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menyu elementi muvaffaqiyatli o\'chirildi!');
    }
}
