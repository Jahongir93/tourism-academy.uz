<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ThemeController extends Controller
{
    /**
     * Show theme settings page
     */
    public function index()
    {
        $currentTheme = CmsSetting::getCurrentTheme();
        $themes = CmsSetting::getAvailableThemes();
        $lang = app()->getLocale();

        return view('cms.themes.index', compact('currentTheme', 'themes', 'lang'));
    }

    /**
     * Update theme setting
     */
    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:theme1,theme2,theme3,theme4,theme5',
        ]);

        CmsSetting::updateOrCreate(
            ['key' => 'site_theme'],
            [
                'value' => $request->theme,
                'type' => 'select',
                'group' => 'appearance',
                'label' => 'Site Theme',
                'description' => 'Select the visual theme for the public website',
                'options' => json_encode(array_keys(CmsSetting::getAvailableThemes())),
                'is_public' => true,
            ]
        );

        // Clear cache
        Cache::forget('cms_setting_site_theme');

        return redirect()->back()->with('success', 'Tema muvaffaqiyatli o\'zgartirildi!');
    }

    /**
     * Preview theme (AJAX)
     */
    public function preview(Request $request)
    {
        $theme = $request->get('theme', 'theme1');
        $themes = CmsSetting::getAvailableThemes();

        if (!isset($themes[$theme])) {
            return response()->json(['error' => 'Invalid theme'], 400);
        }

        return response()->json([
            'success' => true,
            'theme' => $themes[$theme],
        ]);
    }
}
