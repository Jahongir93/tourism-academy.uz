<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CmsContent;
use App\Models\CmsMenu;
use App\Models\CmsMenuItem;
use App\Support\CmsHeaderFooter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeaderFooterController extends Controller
{
    /**
     * Display the header editing page
     */
    public function header()
    {
        CmsHeaderFooter::syncDefaults('header');
        $contents = CmsHeaderFooter::contents('header');

        return view('cms.header-footer.header', compact('contents'));

        // Default header structure
        $defaultHeader = [
            ['key' => 'logo_url', 'type' => 'image', 'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 1],
            ['key' => 'site_name', 'type' => 'text', 'value_uz' => 'Tourism Academy', 'value_en' => 'Tourism Academy', 'value_ru' => 'Tourism Academy', 'order' => 2],
            ['key' => 'menu_home', 'type' => 'text', 'value_uz' => 'Bosh sahifa', 'value_en' => 'Home', 'value_ru' => 'Главная', 'order' => 3],
            ['key' => 'menu_about', 'type' => 'text', 'value_uz' => 'Biz haqimizda', 'value_en' => 'About Us', 'value_ru' => 'О нас', 'order' => 4],
            ['key' => 'menu_programs', 'type' => 'text', 'value_uz' => "Yo'nalishlar", 'value_en' => 'Programs', 'value_ru' => 'Программы', 'order' => 5],
            ['key' => 'menu_teachers', 'type' => 'text', 'value_uz' => "O'qituvchilar", 'value_en' => 'Teachers', 'value_ru' => 'Преподаватели', 'order' => 6],
            ['key' => 'menu_statistics', 'type' => 'text', 'value_uz' => 'Statistika', 'value_en' => 'Statistics', 'value_ru' => 'Статистика', 'order' => 7],
            ['key' => 'menu_blog', 'type' => 'text', 'value_uz' => 'Blog', 'value_en' => 'Blog', 'value_ru' => 'Блог', 'order' => 8],
            ['key' => 'menu_contact', 'type' => 'text', 'value_uz' => "Bog'lanish", 'value_en' => 'Contact', 'value_ru' => 'Контакты', 'order' => 9],
            ['key' => 'login_button', 'type' => 'text', 'value_uz' => 'Kirish', 'value_en' => 'Login', 'value_ru' => 'Вход', 'order' => 10],
            ['key' => 'dashboard_button', 'type' => 'text', 'value_uz' => 'Dashboard', 'value_en' => 'Dashboard', 'value_ru' => 'Панель', 'order' => 11],
        ];

        // Create missing default items using updateOrCreate
        foreach ($defaultHeader as $item) {
            CmsContent::firstOrCreate(
                ['section' => 'header', 'key' => $item['key']],
                [
                    'value_uz' => $item['value_uz'],
                    'value_en' => $item['value_en'],
                    'value_ru' => $item['value_ru'],
                    'type' => $item['type'],
                    'order' => $item['order']
                ]
            );
        }

        $contents = CmsContent::where('section', 'header')->orderBy('order')->get();

        return view('cms.header-footer.header', compact('contents'));
    }

    /**
     * Update the header content
     */
    public function updateHeader(Request $request)
    {
        // DEBUG removed

        try {
            $request->validate([
                'contents' => 'required|array',
                'contents.*.key' => 'nullable|string',
                'contents.*.type' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('cms/logos', 'public');

                CmsContent::updateOrCreate(
                    ['section' => 'header', 'key' => 'logo_url'],
                    [
                        'value_uz' => $logoPath,
                        'value_en' => $logoPath,
                        'value_ru' => $logoPath,
                        'type' => 'image',
                        'order' => 1
                    ]
                );
            }

            // Update other content
            foreach ($request->contents as $contentData) {
                if (!isset($contentData['key']) || $contentData['key'] === 'logo_url') continue;
                $valueUz = $contentData['value_uz'] ?? null;
                $valueEn = $contentData['value_en'] ?? null;
                $valueRu = $contentData['value_ru'] ?? null;

                if (($contentData['type'] ?? null) === 'url') {
                    $valueUz = CmsHeaderFooter::normalizeInputUrl($valueUz);
                    $valueEn = CmsHeaderFooter::normalizeInputUrl($valueEn ?: $valueUz);
                    $valueRu = CmsHeaderFooter::normalizeInputUrl($valueRu ?: $valueUz);
                }

                CmsContent::updateOrCreate(
                    ['section' => 'header', 'key' => $contentData['key']],
                    [
                        'value_uz' => $valueUz,
                        'value_en' => $valueEn,
                        'value_ru' => $valueRu,
                        'type' => $contentData['type'] ?? 'text',
                        'order' => $contentData['order'] ?? 0
                    ]
                );
            }

            return redirect()->route('cms.header-footer.header')
                ->with('success', 'Header muvaffaqiyatli yangilandi!');

        } catch (\Exception $e) {
            \Log::error('Header update error: ' . $e->getMessage());
            return redirect()->route('cms.header-footer.header')
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Display the footer editing page
     */
    public function footer()
    {
        CmsHeaderFooter::syncDefaults('footer');
        $contents = CmsHeaderFooter::contents('footer');

        return view('cms.header-footer.footer', compact('contents'));

        // Default footer structure
        $defaultFooter = [
            // Logo & Description
            ['key' => 'footer_logo', 'type' => 'image', 'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 1],
            ['key' => 'footer_title', 'type' => 'text', 'value_uz' => 'Tourism Academy', 'value_en' => 'Tourism Academy', 'value_ru' => 'Tourism Academy', 'order' => 2],
            ['key' => 'footer_description', 'type' => 'textarea', 'value_uz' => "UN Tourism bilan hamkorlikdagi Samarqand Xalqaro Turizm va Mehmondo'stlik Akademiyasi – zamonaviy ta'lim va yuqori malakali kadrlar tayyorlash markazi.", 'value_en' => 'Samarkand International Tourism and Hospitality Academy in partnership with UN Tourism - a center for modern education and training of highly qualified specialists.', 'value_ru' => 'Самаркандская международная академия туризма и гостеприимства в партнерстве с UN Tourism - центр современного образования и подготовки высококвалифицированных специалистов.', 'order' => 3],

            // Column 2: Information
            ['key' => 'col2_title', 'type' => 'text', 'value_uz' => "Ma'lumot", 'value_en' => 'Information', 'value_ru' => 'Информация', 'order' => 10],
            ['key' => 'col2_link1_text', 'type' => 'text', 'value_uz' => 'Biz haqimizda', 'value_en' => 'About Us', 'value_ru' => 'О нас', 'order' => 11],
            ['key' => 'col2_link1_url', 'type' => 'text', 'value_uz' => '/about', 'value_en' => '/about', 'value_ru' => '/about', 'order' => 12],
            ['key' => 'col2_link2_text', 'type' => 'text', 'value_uz' => "Ta'lim yo'nalishlari", 'value_en' => 'Programs', 'value_ru' => 'Программы', 'order' => 13],
            ['key' => 'col2_link2_url', 'type' => 'text', 'value_uz' => '/programs', 'value_en' => '/programs', 'value_ru' => '/programs', 'order' => 14],
            ['key' => 'col2_link3_text', 'type' => 'text', 'value_uz' => "O'qituvchilar", 'value_en' => 'Teachers', 'value_ru' => 'Преподаватели', 'order' => 15],
            ['key' => 'col2_link3_url', 'type' => 'text', 'value_uz' => '/teachers', 'value_en' => '/teachers', 'value_ru' => '/teachers', 'order' => 16],
            ['key' => 'col2_link4_text', 'type' => 'text', 'value_uz' => 'Statistika', 'value_en' => 'Statistics', 'value_ru' => 'Статистика', 'order' => 17],
            ['key' => 'col2_link4_url', 'type' => 'text', 'value_uz' => '/statistics', 'value_en' => '/statistics', 'value_ru' => '/statistics', 'order' => 18],
            ['key' => 'col2_link5_text', 'type' => 'text', 'value_uz' => 'Blog', 'value_en' => 'Blog', 'value_ru' => 'Блог', 'order' => 19],
            ['key' => 'col2_link5_url', 'type' => 'text', 'value_uz' => '/blog', 'value_en' => '/blog', 'value_ru' => '/blog', 'order' => 20],

            // Column 3: Services
            ['key' => 'col3_title', 'type' => 'text', 'value_uz' => 'Bizning xizmatlar', 'value_en' => 'Our Services', 'value_ru' => 'Наши услуги', 'order' => 30],
            ['key' => 'col3_link1_text', 'type' => 'text', 'value_uz' => "Bakalavr ta'limi", 'value_en' => 'Bachelor Program', 'value_ru' => 'Бакалавриат', 'order' => 31],
            ['key' => 'col3_link1_url', 'type' => 'text', 'value_uz' => '#', 'value_en' => '#', 'value_ru' => '#', 'order' => 32],
            ['key' => 'col3_link2_text', 'type' => 'text', 'value_uz' => 'Magistratura', 'value_en' => 'Master Program', 'value_ru' => 'Магистратура', 'order' => 33],
            ['key' => 'col3_link2_url', 'type' => 'text', 'value_uz' => '#', 'value_en' => '#', 'value_ru' => '#', 'order' => 34],
            ['key' => 'col3_link3_text', 'type' => 'text', 'value_uz' => 'Qisqa muddatli kurslar', 'value_en' => 'Short Courses', 'value_ru' => 'Краткосрочные курсы', 'order' => 35],
            ['key' => 'col3_link3_url', 'type' => 'text', 'value_uz' => '#', 'value_en' => '#', 'value_ru' => '#', 'order' => 36],
            ['key' => 'col3_link4_text', 'type' => 'text', 'value_uz' => "Onlayn ta'lim", 'value_en' => 'Online Education', 'value_ru' => 'Онлайн обучение', 'order' => 37],
            ['key' => 'col3_link4_url', 'type' => 'text', 'value_uz' => '#', 'value_en' => '#', 'value_ru' => '#', 'order' => 38],
            ['key' => 'col3_link5_text', 'type' => 'text', 'value_uz' => 'Sertifikat dasturlari', 'value_en' => 'Certificate Programs', 'value_ru' => 'Сертификатные программы', 'order' => 39],
            ['key' => 'col3_link5_url', 'type' => 'text', 'value_uz' => '#', 'value_en' => '#', 'value_ru' => '#', 'order' => 40],

            // Column 4: Contact
            ['key' => 'col4_title', 'type' => 'text', 'value_uz' => "Bog'lanish", 'value_en' => 'Contact', 'value_ru' => 'Контакты', 'order' => 50],
            ['key' => 'contact_address', 'type' => 'text', 'value_uz' => "Samarqand shahar, Istiqlol ko'chasi, 47", 'value_en' => '47 Istiqlol Street, Samarkand', 'value_ru' => 'г. Самарканд, ул. Истиклол, 47', 'order' => 51],
            ['key' => 'contact_phone', 'type' => 'text', 'value_uz' => '+998 66 233 XX XX', 'value_en' => '+998 66 233 XX XX', 'value_ru' => '+998 66 233 XX XX', 'order' => 52],
            ['key' => 'contact_email', 'type' => 'text', 'value_uz' => 'info@tourism.uz', 'value_en' => 'info@tourism.uz', 'value_ru' => 'info@tourism.uz', 'order' => 53],
            ['key' => 'newsletter_placeholder', 'type' => 'text', 'value_uz' => 'Email manzilingiz', 'value_en' => 'Your email address', 'value_ru' => 'Ваш email', 'order' => 54],

            // Social Media
            ['key' => 'social_facebook', 'type' => 'text', 'value_uz' => '#', 'value_en' => '#', 'value_ru' => '#', 'order' => 60],
            ['key' => 'social_twitter', 'type' => 'text', 'value_uz' => '#', 'value_en' => '#', 'value_ru' => '#', 'order' => 61],
            ['key' => 'social_instagram', 'type' => 'text', 'value_uz' => '#', 'value_en' => '#', 'value_ru' => '#', 'order' => 62],
            ['key' => 'social_youtube', 'type' => 'text', 'value_uz' => '#', 'value_en' => '#', 'value_ru' => '#', 'order' => 63],
            ['key' => 'social_linkedin', 'type' => 'text', 'value_uz' => '#', 'value_en' => '#', 'value_ru' => '#', 'order' => 64],
            ['key' => 'social_telegram', 'type' => 'text', 'value_uz' => '#', 'value_en' => '#', 'value_ru' => '#', 'order' => 65],

            // Copyright
            ['key' => 'copyright_text', 'type' => 'text', 'value_uz' => '© 2025 Tourism Academy. Barcha huquqlar himoyalangan.', 'value_en' => '© 2025 Tourism Academy. All rights reserved.', 'value_ru' => '© 2025 Tourism Academy. Все права защищены.', 'order' => 70],
        ];

        // Create missing default items using firstOrCreate
        foreach ($defaultFooter as $item) {
            CmsContent::firstOrCreate(
                ['section' => 'footer', 'key' => $item['key']],
                [
                    'value_uz' => $item['value_uz'],
                    'value_en' => $item['value_en'],
                    'value_ru' => $item['value_ru'],
                    'type' => $item['type'],
                    'order' => $item['order']
                ]
            );
        }

        $contents = CmsContent::where('section', 'footer')->orderBy('order')->get();

        return view('cms.header-footer.footer', compact('contents'));
    }

    /**
     * Update the footer content
     */
    public function updateFooter(Request $request)
    {
        try {
            $request->validate([
                'contents' => 'required|array',
                'contents.*.key' => 'nullable|string',
                'contents.*.type' => 'nullable|string',
                'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            // Handle logo upload — WAF-safe AJAX path first, then multipart
            $logoPath = null;
            if ($request->filled('footer_logo_path')) {
                $logoPath = ltrim($request->input('footer_logo_path'), '/');
            } elseif ($request->hasFile('footer_logo')) {
                $logoPath = $request->file('footer_logo')->store('cms/logos', 'public');
            }
            if ($logoPath) {
                CmsContent::updateOrCreate(
                    ['section' => 'footer', 'key' => 'footer_logo'],
                    [
                        'value_uz' => $logoPath,
                        'value_en' => $logoPath,
                        'value_ru' => $logoPath,
                        'type' => 'image',
                        'order' => 1
                    ]
                );
            }

            // Update other content
            foreach ($request->contents as $contentData) {
                if (!isset($contentData['key']) || $contentData['key'] === 'footer_logo') continue;
                $valueUz = $contentData['value_uz'] ?? null;
                $valueEn = $contentData['value_en'] ?? null;
                $valueRu = $contentData['value_ru'] ?? null;

                if (($contentData['type'] ?? null) === 'url' || str_ends_with($contentData['key'], '_url')) {
                    $valueUz = CmsHeaderFooter::normalizeInputUrl($valueUz);
                    $valueEn = CmsHeaderFooter::normalizeInputUrl($valueEn ?: $valueUz);
                    $valueRu = CmsHeaderFooter::normalizeInputUrl($valueRu ?: $valueUz);
                }

                CmsContent::updateOrCreate(
                    ['section' => 'footer', 'key' => $contentData['key']],
                    [
                        'value_uz' => $valueUz,
                        'value_en' => $valueEn,
                        'value_ru' => $valueRu,
                        'type' => $contentData['type'] ?? 'text',
                        'order' => $contentData['order'] ?? 0
                    ]
                );
            }

            return redirect()->route('cms.header-footer.footer')
                ->with('success', 'Footer muvaffaqiyatli yangilandi!');

        } catch (\Exception $e) {
            \Log::error('Footer update error: ' . $e->getMessage());
            return redirect()->route('cms.header-footer.footer')
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Add a new custom link to footer
     */
    public function addFooterLink(Request $request)
    {
        $request->validate([
            'column' => 'required|in:col2,col3',
            'link_text_uz' => 'required|string|max:255',
            'link_text_en' => 'nullable|string|max:255',
            'link_text_ru' => 'nullable|string|max:255',
            'link_url' => 'required|string|max:255',
        ]);

        $column = $request->column;
        $linkUrl = CmsHeaderFooter::normalizeInputUrl($request->link_url);
        $lastLink = CmsContent::where('section', 'footer')
            ->where('key', 'like', "{$column}_link%_text")
            ->orderBy('key', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastLink) {
            preg_match('/_link(\d+)_/', $lastLink->key, $matches);
            $nextNumber = ($matches[1] ?? 0) + 1;
        }

        $baseOrder = $column === 'col2' ? 10 : 30;
        $order = $baseOrder + ($nextNumber * 2);

        // Create link text
        CmsContent::create([
            'section' => 'footer',
            'key' => "{$column}_link{$nextNumber}_text",
            'value_uz' => $request->link_text_uz,
            'value_en' => $request->link_text_en ?? $request->link_text_uz,
            'value_ru' => $request->link_text_ru ?? $request->link_text_uz,
            'type' => 'text',
            'order' => $order
        ]);

        // Create link URL
        CmsContent::create([
            'section' => 'footer',
            'key' => "{$column}_link{$nextNumber}_url",
            'value_uz' => $linkUrl,
            'value_en' => $linkUrl,
            'value_ru' => $linkUrl,
            'type' => 'url',
            'order' => $order + 1
        ]);

        return redirect()->route('cms.header-footer.footer')
            ->with('success', "Yangi havola qo'shildi!");
    }

    /**
     * Delete a footer link
     */
    public function deleteFooterLink(Request $request)
    {
        $request->validate([
            'link_key' => 'required|string',
        ]);

        $linkKey = $request->link_key;
        $urlKey = str_replace('_text', '_url', $linkKey);

        CmsContent::where('section', 'footer')
            ->whereIn('key', [$linkKey, $urlKey])
            ->delete();

        return redirect()->route('cms.header-footer.footer')
            ->with('success', "Havola o'chirildi!");
    }

    /**
     * Update a footer link from the edit modal.
     */
    public function updateFooterLink(Request $request)
    {
        $validated = $request->validate([
            'link_key' => 'required|string',
            'link_text_uz' => 'required|string|max:255',
            'link_text_en' => 'nullable|string|max:255',
            'link_text_ru' => 'nullable|string|max:255',
            'link_url' => 'required|string|max:255',
        ]);

        $linkKey = $validated['link_key'];
        $urlKey = str_replace('_text', '_url', $linkKey);
        $link = CmsContent::where('section', 'footer')->where('key', $linkKey)->first();
        $url = CmsHeaderFooter::normalizeInputUrl($validated['link_url']);

        CmsContent::updateOrCreate(
            ['section' => 'footer', 'key' => $linkKey],
            [
                'value_uz' => $validated['link_text_uz'],
                'value_en' => $validated['link_text_en'] ?? $validated['link_text_uz'],
                'value_ru' => $validated['link_text_ru'] ?? $validated['link_text_uz'],
                'type' => 'text',
                'order' => $link?->order ?? 0,
                'is_active' => true,
            ]
        );

        CmsContent::updateOrCreate(
            ['section' => 'footer', 'key' => $urlKey],
            [
                'value_uz' => $url,
                'value_en' => $url,
                'value_ru' => $url,
                'type' => 'url',
                'order' => ($link?->order ?? 0) + 1,
                'is_active' => true,
            ]
        );

        return redirect()->route('cms.header-footer.footer')
            ->with('success', 'Havola muvaffaqiyatli yangilandi!');
    }

    /**
     * Add a new menu item to header
     */
    public function addHeaderMenu(Request $request)
    {
        $request->validate([
            'menu_label_uz' => 'required|string|max:255',
            'menu_label_en' => 'nullable|string|max:255',
            'menu_label_ru' => 'nullable|string|max:255',
            'menu_url' => 'required|string|max:255',
        ]);

        $menuUrl = CmsHeaderFooter::normalizeInputUrl($request->menu_url);

        // Find the next menu number
        $lastMenu = CmsContent::where('section', 'header')
            ->where('key', 'like', 'menu_custom_%')
            ->orderBy('key', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastMenu) {
            preg_match('/menu_custom_(\d+)/', $lastMenu->key, $matches);
            $nextNumber = ($matches[1] ?? 0) + 1;
        }

        $order = 20 + $nextNumber;

        // Create menu label
        CmsContent::create([
            'section' => 'header',
            'key' => "menu_custom_{$nextNumber}",
            'value_uz' => $request->menu_label_uz,
            'value_en' => $request->menu_label_en ?? $request->menu_label_uz,
            'value_ru' => $request->menu_label_ru ?? $request->menu_label_uz,
            'type' => 'text',
            'order' => $order
        ]);

        // Create menu URL
        CmsContent::create([
            'section' => 'header',
            'key' => "menu_custom_{$nextNumber}_url",
            'value_uz' => $menuUrl,
            'value_en' => $menuUrl,
            'value_ru' => $menuUrl,
            'type' => 'url',
            'order' => $order
        ]);

        return redirect()->route('cms.header-footer.header')
            ->with('success', "Yangi menyu qo'shildi!");
    }

    /**
     * Update a custom header menu item.
     */
    public function updateHeaderMenu(Request $request)
    {
        $validated = $request->validate([
            'menu_key' => 'required|string',
            'menu_label_uz' => 'required|string|max:255',
            'menu_label_en' => 'nullable|string|max:255',
            'menu_label_ru' => 'nullable|string|max:255',
            'menu_url' => 'required|string|max:255',
        ]);

        $menuKey = $validated['menu_key'];
        $urlKey = $menuKey . '_url';
        $menu = CmsContent::where('section', 'header')->where('key', $menuKey)->first();
        $url = CmsHeaderFooter::normalizeInputUrl($validated['menu_url']);

        CmsContent::updateOrCreate(
            ['section' => 'header', 'key' => $menuKey],
            [
                'value_uz' => $validated['menu_label_uz'],
                'value_en' => $validated['menu_label_en'] ?? $validated['menu_label_uz'],
                'value_ru' => $validated['menu_label_ru'] ?? $validated['menu_label_uz'],
                'type' => 'text',
                'order' => $menu?->order ?? 20,
                'is_active' => $menu?->is_active ?? true,
            ]
        );

        CmsContent::updateOrCreate(
            ['section' => 'header', 'key' => $urlKey],
            [
                'value_uz' => $url,
                'value_en' => $url,
                'value_ru' => $url,
                'type' => 'url',
                'order' => $menu?->order ?? 20,
                'is_active' => $menu?->is_active ?? true,
            ]
        );

        return redirect()->route('cms.header-footer.header')
            ->with('success', 'Menyu muvaffaqiyatli yangilandi!');
    }

    /**
     * Delete a header menu item
     */
    public function deleteHeaderMenu(Request $request)
    {
        $request->validate([
            'menu_key' => 'required|string',
        ]);

        $menuKey = $request->menu_key;
        $urlKey = $menuKey . '_url';

        CmsContent::where('section', 'header')
            ->whereIn('key', [$menuKey, $urlKey])
            ->delete();

        return redirect()->route('cms.header-footer.header')
            ->with('success', "Menyu o'chirildi!");
    }

    /**
     * Toggle menu status (active/inactive)
     */
    public function toggleMenuStatus(Request $request)
    {
        $request->validate([
            'menu_key' => 'required|string',
        ]);

        $menuKey = $request->menu_key;
        $urlKey = $menuKey . '_url';

        // Toggle both menu label and URL
        $menuItems = CmsContent::where('section', 'header')
            ->whereIn('key', [$menuKey, $urlKey])
            ->get();

        foreach ($menuItems as $item) {
            $item->is_active = !$item->is_active;
            $item->save();
        }

        $status = $menuItems->first()->is_active ? 'faollashtirildi' : 'yashirildi';

        return redirect()->route('cms.header-footer.header')
            ->with('success', "Menyu {$status}!");
    }

    /**
     * Add a submenu item to a menu
     */
    public function addSubmenu(Request $request)
    {
        $request->validate([
            'parent_menu_key' => 'required|string',
            'submenu_label_uz' => 'required|string|max:255',
            'submenu_label_en' => 'nullable|string|max:255',
            'submenu_label_ru' => 'nullable|string|max:255',
            'submenu_url' => 'required|string|max:255',
        ]);

        $parentKey = $request->parent_menu_key;
        $url = CmsHeaderFooter::normalizeInputUrl($request->submenu_url);

        // Find the next submenu number for this parent
        $lastSubmenu = CmsContent::where('section', 'header')
            ->where('key', 'like', "{$parentKey}_sub_%")
            ->where('key', 'not like', '%_url')
            ->orderBy('key', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastSubmenu) {
            preg_match('/_sub_(\d+)$/', $lastSubmenu->key, $matches);
            $nextNumber = ($matches[1] ?? 0) + 1;
        }

        // Get parent order to calculate submenu order
        $parentContent = CmsContent::where('section', 'header')
            ->where('key', $parentKey)
            ->first();
        $baseOrder = $parentContent ? ($parentContent->order * 10) : 100;
        $order = $baseOrder + $nextNumber;

        // Create submenu label
        CmsContent::create([
            'section' => 'header',
            'key' => "{$parentKey}_sub_{$nextNumber}",
            'value_uz' => $request->submenu_label_uz,
            'value_en' => $request->submenu_label_en ?? $request->submenu_label_uz,
            'value_ru' => $request->submenu_label_ru ?? $request->submenu_label_uz,
            'type' => 'text',
            'order' => $order,
            'is_active' => true
        ]);

        // Create submenu URL
        CmsContent::create([
            'section' => 'header',
            'key' => "{$parentKey}_sub_{$nextNumber}_url",
            'value_uz' => $url,
            'value_en' => $url,
            'value_ru' => $url,
            'type' => 'url',
            'order' => $order,
            'is_active' => true
        ]);

        // CmsMenuItem jadvaliga ham qo'shish (navbarda ko'rinishi uchun)
        $this->syncSubmenuToCmsMenuItem(
            $parentKey,
            $request->submenu_label_uz,
            $request->submenu_label_en ?? $request->submenu_label_uz,
            $request->submenu_label_ru ?? $request->submenu_label_uz,
            $url,
            $order
        );

        return redirect()->route('cms.header-footer.header')
            ->with('success', "Submenyu muvaffaqiyatli qo'shildi!");
    }

    /**
     * Submenyuni CmsMenuItem jadvaliga sinxronlashtirish
     */
    private function syncSubmenuToCmsMenuItem($parentKey, $titleUz, $titleEn, $titleRu, $url, $order)
    {
        // Parent menu kaliti bo'yicha CmsMenuItem dagi parentni topish
        $locationMap = [
            'menu_about' => 'header',
            'menu_home' => 'header',
            'menu_programs' => 'header',
            'menu_teachers' => 'header',
            'menu_blog' => 'header',
            'menu_contact' => 'header',
            'menu_statistics' => 'header',
        ];

        $location = $locationMap[$parentKey] ?? 'header';
        $headerMenu = CmsMenu::where('location', $location)->where('is_active', true)->first();

        if (!$headerMenu) {
            $headerMenu = CmsMenu::create([
                'name' => 'Asosiy menyu',
                'location' => 'header',
                'items' => [],
                'is_active' => true,
            ]);
        }

        // Parent CmsMenuItem ni topish yoki yaratish
        $parentTitleMap = [
            'menu_about' => ['uz' => 'Biz haqimizda', 'en' => 'About Us', 'ru' => 'О нас'],
            'menu_home' => ['uz' => 'Bosh sahifa', 'en' => 'Home', 'ru' => 'Главная'],
            'menu_programs' => ['uz' => "Yo'nalishlar", 'en' => 'Programs', 'ru' => 'Программы'],
            'menu_teachers' => ['uz' => "O'qituvchilar", 'en' => 'Teachers', 'ru' => 'Преподаватели'],
            'menu_blog' => ['uz' => 'Blog', 'en' => 'Blog', 'ru' => 'Блог'],
            'menu_contact' => ['uz' => "Bog'lanish", 'en' => 'Contact', 'ru' => 'Контакты'],
            'menu_statistics' => ['uz' => 'Statistika', 'en' => 'Statistics', 'ru' => 'Статистика'],
        ];

        $parentItem = null;
        if (isset($parentTitleMap[$parentKey])) {
            $titles = array_values($parentTitleMap[$parentKey]);
            $parentItem = CmsMenuItem::where('menu_id', $headerMenu->id)
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->where(function ($query) use ($titles) {
                    $query->whereIn('title_uz', $titles)
                        ->orWhereIn('title_en', $titles)
                        ->orWhereIn('title_ru', $titles);
                })
                ->first();
        }

        if (!$parentItem && isset($parentTitleMap[$parentKey])) {
            $titles = $parentTitleMap[$parentKey];
            $parentItem = CmsMenuItem::create([
                'menu_id' => $headerMenu->id,
                'title_uz' => $titles['uz'],
                'title_en' => $titles['en'],
                'title_ru' => $titles['ru'],
                'type' => 'custom',
                'url' => '#',
                'parent_id' => null,
                'order_position' => 1,
                'is_active' => true,
            ]);
        }

        if ($parentItem) {
            // Submenyuni dublikat qilmaslik uchun tekshirish
            $exists = CmsMenuItem::where('menu_id', $headerMenu->id)
                ->where('parent_id', $parentItem->id)
                ->where('url', $url)
                ->first();

            if (!$exists) {
                CmsMenuItem::create([
                    'menu_id' => $headerMenu->id,
                    'title_uz' => $titleUz,
                    'title_en' => $titleEn,
                    'title_ru' => $titleRu,
                    'type' => 'custom',
                    'url' => $url,
                    'parent_id' => $parentItem->id,
                    'order_position' => $order,
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Delete a submenu item
     */
    public function deleteSubmenu(Request $request)
    {
        $request->validate([
            'submenu_key' => 'required|string',
        ]);

        $submenuKey = $request->submenu_key;
        $urlKey = $submenuKey . '_url';

        // CmsContent dan URL ni olish (CmsMenuItem ni topish uchun)
        $urlContent = CmsContent::where('section', 'header')->where('key', $urlKey)->first();
        $submenuUrl = $urlContent ? $urlContent->value_uz : null;

        CmsContent::where('section', 'header')
            ->whereIn('key', [$submenuKey, $urlKey])
            ->delete();

        // CmsMenuItem dan ham o'chirish
        if ($submenuUrl) {
            $headerMenu = CmsMenu::where('location', 'header')->where('is_active', true)->first();
            if ($headerMenu) {
                CmsMenuItem::where('menu_id', $headerMenu->id)
                    ->where('url', $submenuUrl)
                    ->whereNotNull('parent_id')
                    ->delete();
            }
        }

        return redirect()->route('cms.header-footer.header')
            ->with('success', "Submenyu o'chirildi!");
    }

    /**
     * Update a submenu item
     */
    public function updateSubmenu(Request $request)
    {
        $request->validate([
            'submenu_key' => 'required|string',
            'submenu_label_uz' => 'required|string|max:255',
            'submenu_label_en' => 'nullable|string|max:255',
            'submenu_label_ru' => 'nullable|string|max:255',
            'submenu_url' => 'required|string|max:255',
        ]);

        $submenuKey = $request->submenu_key;
        $urlKey = $submenuKey . '_url';
        $newUrl = CmsHeaderFooter::normalizeInputUrl($request->submenu_url);

        // Eski URL ni olish (CmsMenuItem ni topish uchun)
        $oldUrlContent = CmsContent::where('section', 'header')->where('key', $urlKey)->first();
        $oldUrl = $oldUrlContent ? $oldUrlContent->value_uz : null;

        // Update submenu label
        CmsContent::where('section', 'header')
            ->where('key', $submenuKey)
            ->update([
                'value_uz' => $request->submenu_label_uz,
                'value_en' => $request->submenu_label_en ?? $request->submenu_label_uz,
                'value_ru' => $request->submenu_label_ru ?? $request->submenu_label_uz,
            ]);

        // Update submenu URL
        CmsContent::where('section', 'header')
            ->where('key', $urlKey)
            ->update([
                'value_uz' => $newUrl,
                'value_en' => $newUrl,
                'value_ru' => $newUrl,
            ]);

        // CmsMenuItem ni ham yangilash
        if ($oldUrl) {
            $headerMenu = CmsMenu::where('location', 'header')->where('is_active', true)->first();
            if ($headerMenu) {
                $menuItem = CmsMenuItem::where('menu_id', $headerMenu->id)
                    ->where('url', $oldUrl)
                    ->whereNotNull('parent_id')
                    ->first();
                if ($menuItem) {
                    $menuItem->update([
                        'title_uz' => $request->submenu_label_uz,
                        'title_en' => $request->submenu_label_en ?? $request->submenu_label_uz,
                        'title_ru' => $request->submenu_label_ru ?? $request->submenu_label_uz,
                        'url' => $newUrl,
                    ]);
                }
            }
        }

        return redirect()->route('cms.header-footer.header')
            ->with('success', "Submenyu muvaffaqiyatli yangilandi!");
    }
}
