<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsContent;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function index()
    {
        return view('admin.cms.index');
    }

    public function editSection($section)
    {
        $contents = CmsContent::where('section', $section)->orderBy('order')->get();

        return view('admin.cms.edit', compact('section', 'contents'));
    }

    public function updateSection(Request $request, $section)
    {
        $request->validate([
            'contents' => 'required|array',
            'contents.*.key' => 'required|string',
            'contents.*.value_uz' => 'nullable|string',
            'contents.*.value_en' => 'nullable|string',
            'contents.*.value_ru' => 'nullable|string',
            'contents.*.type' => 'required|string',
            'contents.*.order' => 'required|integer'
        ]);

        foreach ($request->contents as $contentData) {
            CmsContent::updateOrCreate(
                [
                    'section' => $section,
                    'key' => $contentData['key']
                ],
                [
                    'value_uz' => $contentData['value_uz'] ?? null,
                    'value_en' => $contentData['value_en'] ?? null,
                    'value_ru' => $contentData['value_ru'] ?? null,
                    'type' => $contentData['type'],
                    'order' => $contentData['order']
                ]
            );
        }

        return redirect()->route('admin.cms.edit', $section)
            ->with('success', 'Kontent muvaffaqiyatli yangilandi!');
    }

    public function header()
    {
        return $this->editSection('header');
    }

    public function footer()
    {
        return $this->editSection('footer');
    }

    public function homeQuickAccess()
    {
        return $this->editSection('home_quick_access');
    }

    public function aboutUnTourism()
    {
        return $this->editSection('about_un_tourism');
    }

    public function programsStats()
    {
        return $this->editSection('programs_stats');
    }

    public function teachersSection()
    {
        return $this->editSection('teachers_section');
    }

    public function statisticsAge()
    {
        return $this->editSection('statistics_age');
    }

    public function contacts()
    {
        return $this->editSection('contacts');
    }
}
