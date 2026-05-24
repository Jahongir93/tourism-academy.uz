<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Helpers\TemplateHelper;
use App\Models\CmsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TemplateController extends Controller
{
    /**
     * Available templates with their info
     */
    protected $templates = [
        'classic' => [
            'name' => 'Classic (Neon)',
            'description' => 'Neon ranglar va zamonaviy ko\'rinish',
            'preview' => '/images/templates/classic-preview.png',
            'colors' => ['#655CFF', '#D9FF5F', '#1a1a2e']
        ],
        'academic' => [
            'name' => 'Modern Academic',
            'description' => 'Professional akademik dizayn - xalqaro ta\'lim platformasi uslubi',
            'preview' => '/images/templates/academic-preview.png',
            'colors' => ['#0A1F44', '#2F80ED', '#FFFFFF']
        ]
    ];

    /**
     * Show template management page
     */
    public function index()
    {
        $activeTemplate = CmsSetting::where('key', 'active_template')->first();
        $currentTemplate = $activeTemplate ? $activeTemplate->value : 'classic';

        return view('cms.templates.index', [
            'templates' => $this->templates,
            'currentTemplate' => $currentTemplate
        ]);
    }

    /**
     * Activate a template
     */
    public function activate(Request $request)
    {
        $request->validate([
            'template' => 'required|string|in:' . implode(',', array_keys($this->templates))
        ]);

        CmsSetting::updateOrCreate(
            ['key' => 'active_template'],
            [
                'value' => $request->template,
                'type' => 'text',
                'group' => 'design',
                'label' => 'Faol shablon',
                'description' => 'Sayt dizayni uchun faol shablon'
            ]
        );

        // Clear template cache and view cache
        TemplateHelper::clearCache();
        \Artisan::call('view:clear');

        return redirect()->route('cms.templates.index')
            ->with('success', $this->templates[$request->template]['name'] . ' shabloni faollashtirildi!');
    }

    /**
     * Preview a template
     */
    public function preview($template)
    {
        if (!array_key_exists($template, $this->templates)) {
            abort(404);
        }

        return view('cms.templates.preview', [
            'template' => $template,
            'info' => $this->templates[$template]
        ]);
    }
}
