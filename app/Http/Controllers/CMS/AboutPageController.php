<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsContent;
use Illuminate\Support\Str;

class AboutPageController extends Controller
{
    private const UPLOAD_DIR = 'uploads/about';
    private const ALLOWED_IMAGE_KEYS = [
        'about_mgmt1_image', 'about_mgmt2_image', 'about_mgmt3_image',
    ];

    /**
     * AJAX: rasm public/uploads/about/ ga to'g'ridan-to'g'ri yuklanadi
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'key' => 'required|string',
                'image' => 'required|file|max:20480',
            ]);

            if (!in_array($request->key, self::ALLOWED_IMAGE_KEYS)) {
                return response()->json(['success' => false, 'error' => 'Noto\'g\'ri kalit'], 400);
            }

            $file = $request->file('image');
            if (!$file->isValid()) {
                return response()->json(['success' => false, 'error' => $file->getErrorMessage()], 400);
            }
            if (!str_starts_with($file->getMimeType(), 'image/')) {
                return response()->json(['success' => false, 'error' => 'Faqat rasm fayllari qabul qilinadi'], 400);
            }

            $uploadDir = public_path(self::UPLOAD_DIR);
            if (!is_dir($uploadDir)) {
                if (!@mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                    return response()->json([
                        'success' => false,
                        'error' => "Yuklash papkasini yaratib bo'lmadi: {$uploadDir}. Server permissions tekshiring.",
                    ], 500);
                }
            }
            if (!is_writable($uploadDir)) {
                return response()->json([
                    'success' => false,
                    'error' => "Papka yozish uchun ochilmagan: {$uploadDir}. chmod 775 bering.",
                ], 500);
            }

            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'png';
            $filename = $request->key . '_' . time() . '_' . Str::random(8) . '.' . $ext;

            try {
                $file->move($uploadDir, $filename);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => "Faylni saqlab bo'lmadi: " . $e->getMessage(),
                ], 500);
            }

            $relPath = self::UPLOAD_DIR . '/' . $filename;
            $fullPath = public_path($relPath);

            if (!file_exists($fullPath)) {
                return response()->json(['success' => false, 'error' => 'Fayl ko\'chirilmadi'], 500);
            }

            // Eski rasmni o'chirish (faqat yangi format)
            $existing = CmsContent::where('section', 'about')->where('key', $request->key)->first();
            if ($existing && $existing->value_uz && Str::startsWith($existing->value_uz, self::UPLOAD_DIR)) {
                $oldFile = public_path($existing->value_uz);
                if (file_exists($oldFile) && $oldFile !== $fullPath) {
                    @unlink($oldFile);
                }
            }

            CmsContent::updateOrCreate(
                ['section' => 'about', 'key' => $request->key],
                [
                    'value_uz' => $relPath,
                    'value_en' => $relPath,
                    'value_ru' => $relPath,
                    'type' => 'image',
                ]
            );

            return response()->json([
                'success' => true,
                'path' => $relPath,
                'url' => asset($relPath) . '?t=' . time(),
                'message' => 'Rasm yuklandi va saqlandi',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => collect($e->errors())->flatten()->first() ?? 'Validatsiya xatosi',
            ], 422);
        } catch (\Exception $e) {
            \Log::error('about uploadImage failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Server xatosi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX: rasmni o'chirish
     */
    public function deleteImage(Request $request)
    {
        try {
            $request->validate(['key' => 'required|string']);

            if (!in_array($request->key, self::ALLOWED_IMAGE_KEYS)) {
                return response()->json(['success' => false, 'error' => 'Noto\'g\'ri kalit'], 400);
            }

            $content = CmsContent::where('section', 'about')->where('key', $request->key)->first();
            if (!$content) {
                return response()->json(['success' => false, 'error' => 'Yozuv topilmadi'], 404);
            }

            $oldPath = $content->value_uz;
            if ($oldPath && Str::startsWith($oldPath, self::UPLOAD_DIR)) {
                $oldFile = public_path($oldPath);
                if (file_exists($oldFile)) @unlink($oldFile);
            }

            $content->update([
                'value_uz' => '',
                'value_en' => '',
                'value_ru' => '',
            ]);

            return response()->json(['success' => true, 'message' => 'Rasm o\'chirildi']);
        } catch (\Exception $e) {
            \Log::error('about deleteImage failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Server xatosi: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function index()
    {
        // Default about page content structure
        $defaultAbout = [
            // Hero Section
            ['key' => 'about_hero_badge', 'type' => 'text', 'value_uz' => 'BIZ HAQIMIZDA', 'value_en' => 'ABOUT US', 'value_ru' => 'О НАС', 'order' => 1],
            ['key' => 'about_hero_title_1', 'type' => 'text', 'value_uz' => 'UN Tourism bilan hamkorlikdagi Samarqand', 'value_en' => 'Samarkand International Tourism in partnership with UN Tourism', 'value_ru' => 'Самаркандская международная академия туризма в партнерстве с UN Tourism', 'order' => 2],
            ['key' => 'about_hero_title_highlight', 'type' => 'text', 'value_uz' => "Xalqaro Turizm va Mehmondo'stlik", 'value_en' => 'and Hospitality', 'value_ru' => 'и Гостеприимства', 'order' => 3],
            ['key' => 'about_hero_title_2', 'type' => 'text', 'value_uz' => 'Akademiyasiga xush kelibsiz', 'value_en' => 'Academy welcome', 'value_ru' => 'Академия приветствует вас', 'order' => 4],
            ['key' => 'about_hero_description', 'type' => 'textarea', 'value_uz' => "O'zbekiston Respublikasi Prezidenti Shavkat Mirziyoyev tashabbusi bilan va UN Tourism bilan hamkorlikda tashkil etilgan Samarqand Xalqaro Turizm va Mehmondo'stlik Akademiyasi – bu turizm sohasi uchun yuqori malakali inson kapitalini tayyorlashga qaratilgan zamonaviy ta'lim maskani.", 'value_en' => "Samarkand International Tourism and Hospitality Academy, established on the initiative of President Shavkat Mirziyoyev in partnership with UN Tourism, is a modern educational institution focused on training highly qualified human capital for the tourism industry.", 'value_ru' => 'Самаркандская международная академия туризма и гостеприимства, созданная по инициативе Президента Шавката Мирзиёева в партнерстве с UN Tourism, является современным образовательным учреждением, направленным на подготовку высококвалифицированных кадров для туристической отрасли.', 'order' => 5],
            ['key' => 'about_hero_date', 'type' => 'text', 'value_uz' => '16-oktabr, 2023', 'value_en' => 'October 16, 2023', 'value_ru' => '16 октября 2023', 'order' => 6],

            // UN Tourism Certificate Section
            ['key' => 'about_cert_title', 'type' => 'text', 'value_uz' => 'UN Tourism sertifikatining xalqaro tan olinishi', 'value_en' => 'International recognition of UN Tourism certificate', 'value_ru' => 'Международное признание сертификата UN Tourism', 'order' => 10],
            ['key' => 'about_cert_subtitle', 'type' => 'text', 'value_uz' => "Bizning sertifikatlarimiz butun dunyo bo'ylab tan olinadi", 'value_en' => 'Our certificates are recognized worldwide', 'value_ru' => 'Наши сертификаты признаются во всем мире', 'order' => 11],

            // 4 Certificate features
            ['key' => 'about_cert1_icon', 'type' => 'text', 'value_uz' => 'fas fa-globe', 'value_en' => 'fas fa-globe', 'value_ru' => 'fas fa-globe', 'order' => 12],
            ['key' => 'about_cert1_title', 'type' => 'text', 'value_uz' => 'Xalqaro tan olinish', 'value_en' => 'International Recognition', 'value_ru' => 'Международное признание', 'order' => 13],
            ['key' => 'about_cert1_text', 'type' => 'text', 'value_uz' => "Sertifikatlar barcha UN Tourism a'zo mamlakatlarida tan olinadi", 'value_en' => 'Certificates are recognized in all UN Tourism member countries', 'value_ru' => 'Сертификаты признаются во всех странах-членах UN Tourism', 'order' => 14],

            ['key' => 'about_cert2_icon', 'type' => 'text', 'value_uz' => 'fas fa-certificate', 'value_en' => 'fas fa-certificate', 'value_ru' => 'fas fa-certificate', 'order' => 15],
            ['key' => 'about_cert2_title', 'type' => 'text', 'value_uz' => 'Xalqaro standartlar', 'value_en' => 'International Standards', 'value_ru' => 'Международные стандарты', 'order' => 16],
            ['key' => 'about_cert2_text', 'type' => 'text', 'value_uz' => "Xalqaro standartlarga mos o'quv dasturlari", 'value_en' => 'Curriculum aligned with international standards', 'value_ru' => 'Учебные программы соответствуют международным стандартам', 'order' => 17],

            ['key' => 'about_cert3_icon', 'type' => 'text', 'value_uz' => 'fas fa-handshake', 'value_en' => 'fas fa-handshake', 'value_ru' => 'fas fa-handshake', 'order' => 18],
            ['key' => 'about_cert3_title', 'type' => 'text', 'value_uz' => 'Hamkorlik', 'value_en' => 'Partnership', 'value_ru' => 'Партнерство', 'order' => 19],
            ['key' => 'about_cert3_text', 'type' => 'text', 'value_uz' => 'Yetakchi turizm tashkilotlari bilan hamkorlik', 'value_en' => 'Partnership with leading tourism organizations', 'value_ru' => 'Партнерство с ведущими туристическими организациями', 'order' => 20],

            ['key' => 'about_cert4_icon', 'type' => 'text', 'value_uz' => 'fas fa-briefcase', 'value_en' => 'fas fa-briefcase', 'value_ru' => 'fas fa-briefcase', 'order' => 21],
            ['key' => 'about_cert4_title', 'type' => 'text', 'value_uz' => 'Amaliyot imkoniyatlari', 'value_en' => 'Internship Opportunities', 'value_ru' => 'Возможности стажировки', 'order' => 22],
            ['key' => 'about_cert4_text', 'type' => 'text', 'value_uz' => 'Amaliyot va stajirovkalar imkoniyati', 'value_en' => 'Internship and training opportunities', 'value_ru' => 'Возможности практики и стажировки', 'order' => 23],

            // Management Section
            ['key' => 'about_mgmt_title', 'type' => 'text', 'value_uz' => 'Akademiya rahbariyati', 'value_en' => 'Academy Management', 'value_ru' => 'Руководство академии', 'order' => 30],
            ['key' => 'about_mgmt_subtitle', 'type' => 'text', 'value_uz' => 'Professional va tajribali rahbarlar jamoasi', 'value_en' => 'Professional and experienced leadership team', 'value_ru' => 'Профессиональная и опытная команда руководителей', 'order' => 31],

            // 3 Management positions
            ['key' => 'about_mgmt1_icon', 'type' => 'text', 'value_uz' => 'fas fa-user-tie', 'value_en' => 'fas fa-user-tie', 'value_ru' => 'fas fa-user-tie', 'order' => 32],
            ['key' => 'about_mgmt1_title', 'type' => 'text', 'value_uz' => 'Direktor', 'value_en' => 'Director', 'value_ru' => 'Директор', 'order' => 33],
            ['key' => 'about_mgmt1_role', 'type' => 'text', 'value_uz' => 'Akademiya rahbari', 'value_en' => 'Academy Head', 'value_ru' => 'Руководитель академии', 'order' => 34],
            ['key' => 'about_mgmt1_text', 'type' => 'text', 'value_uz' => "Ta'lim jarayoni va akademiya faoliyatini umumiy boshqarish", 'value_en' => 'General management of educational process and academy activities', 'value_ru' => 'Общее руководство учебным процессом и деятельностью академии', 'order' => 35],
            ['key' => 'about_mgmt1_image', 'type' => 'image', 'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 36],

            ['key' => 'about_mgmt2_icon', 'type' => 'text', 'value_uz' => 'fas fa-book-reader', 'value_en' => 'fas fa-book-reader', 'value_ru' => 'fas fa-book-reader', 'order' => 37],
            ['key' => 'about_mgmt2_title', 'type' => 'text', 'value_uz' => "O'quv ishlari bo'yicha o'rinbosar", 'value_en' => 'Deputy Director for Academic Affairs', 'value_ru' => 'Заместитель по учебной работе', 'order' => 38],
            ['key' => 'about_mgmt2_role', 'type' => 'text', 'value_uz' => "O'quv jarayoni rahbari", 'value_en' => 'Academic Process Manager', 'value_ru' => 'Руководитель учебного процесса', 'order' => 39],
            ['key' => 'about_mgmt2_text', 'type' => 'text', 'value_uz' => "O'quv jarayonini boshqarish va nazorat qilish", 'value_en' => 'Managing and supervising the educational process', 'value_ru' => 'Управление и контроль учебного процесса', 'order' => 40],
            ['key' => 'about_mgmt2_image', 'type' => 'image', 'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 41],

            ['key' => 'about_mgmt3_icon', 'type' => 'text', 'value_uz' => 'fas fa-cogs', 'value_en' => 'fas fa-cogs', 'value_ru' => 'fas fa-cogs', 'order' => 42],
            ['key' => 'about_mgmt3_title', 'type' => 'text', 'value_uz' => 'Operatsion faoliyat o\'rinbosari', 'value_en' => 'Deputy Director for Operations', 'value_ru' => 'Заместитель по операционной деятельности', 'order' => 43],
            ['key' => 'about_mgmt3_role', 'type' => 'text', 'value_uz' => 'Operatsion jarayonlar rahbari', 'value_en' => 'Operations Manager', 'value_ru' => 'Руководитель операционных процессов', 'order' => 44],
            ['key' => 'about_mgmt3_text', 'type' => 'text', 'value_uz' => 'Akademiya operatsion faoliyatini boshqarish', 'value_en' => 'Managing academy operational activities', 'value_ru' => 'Управление операционной деятельностью академии', 'order' => 45],
            ['key' => 'about_mgmt3_image', 'type' => 'image', 'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 46],
        ];

        // Create default content if not exists
        foreach ($defaultAbout as $item) {
            CmsContent::firstOrCreate(
                ['section' => 'about', 'key' => $item['key']],
                [
                    'type' => $item['type'],
                    'value_uz' => $item['value_uz'],
                    'value_en' => $item['value_en'],
                    'value_ru' => $item['value_ru'],
                    'order' => $item['order']
                ]
            );
        }

        $contents = CmsContent::where('section', 'about')->orderBy('order')->get();

        return view('cms.about.index', compact('contents'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $values) {
            if (is_array($values)) {
                // Text/textarea fields with language support
                CmsContent::updateOrCreate(
                    ['section' => 'about', 'key' => $key],
                    [
                        'value_uz' => $values['uz'] ?? '',
                        'value_en' => $values['en'] ?? '',
                        'value_ru' => $values['ru'] ?? '',
                    ]
                );
            }
        }

        // Handle image uploads
        foreach ($request->allFiles() as $key => $file) {
            $path = $file->store('cms/about', 'public');
            CmsContent::updateOrCreate(
                ['section' => 'about', 'key' => $key],
                [
                    'value_uz' => $path,
                    'value_en' => $path,
                    'value_ru' => $path,
                    'type' => 'image'
                ]
            );
        }

        return redirect()->route('cms.about.index')->with('success', "Biz haqimizda sahifasi muvaffaqiyatli yangilandi!");
    }
}
