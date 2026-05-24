<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CmsContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomePageController extends Controller
{
    private const UPLOAD_DIR = 'uploads/homepage';
    private const ALLOWED_IMAGE_KEYS = [
        'slide1_image', 'slide2_image', 'slide3_image',
        'gallery_image_1', 'gallery_image_2', 'gallery_image_3', 'gallery_image_4',
        'partner1_logo', 'partner2_logo', 'partner3_logo',
        'partner4_logo', 'partner5_logo', 'partner6_logo',
    ];

    /**
     * AJAX: yangi yondashuv - rasm public/uploads/homepage/ ga to'g'ridan-to'g'ri yuklanadi
     * Symlink kerak emas, har qanday Linux/Windows serverda ishlaydi
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

            // Eski rasmni o'chirish (faqat yangi format — uploads/ bilan boshlansa)
            $existing = CmsContent::where('section', 'homepage')->where('key', $request->key)->first();
            if ($existing && $existing->value_uz && Str::startsWith($existing->value_uz, self::UPLOAD_DIR)) {
                $oldFile = public_path($existing->value_uz);
                if (file_exists($oldFile) && $oldFile !== $fullPath) {
                    @unlink($oldFile);
                }
            }

            CmsContent::updateOrCreate(
                ['section' => 'homepage', 'key' => $request->key],
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
            \Log::error('uploadImage failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Server xatosi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX: rasmni o'chirish — DB qiymati bo'shatiladi va fayl o'chiriladi
     */
    public function deleteImage(Request $request)
    {
        try {
            $request->validate(['key' => 'required|string']);

            if (!in_array($request->key, self::ALLOWED_IMAGE_KEYS)) {
                return response()->json(['success' => false, 'error' => 'Noto\'g\'ri kalit'], 400);
            }

            $content = CmsContent::where('section', 'homepage')->where('key', $request->key)->first();
            if (!$content) {
                return response()->json(['success' => false, 'error' => 'Yozuv topilmadi'], 404);
            }

            // Faqat yangi format faylini o'chirish (uploads/ bilan boshlansa)
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
            \Log::error('deleteImage failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Server xatosi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get default homepage structure with all keys used by home.blade.php
     */
    private function getDefaultHomepage(): array
    {
        return [
            // ========== HERO SLIDER ==========
            // Slide 1
            ['key' => 'slide1_badge', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 1 - Badge',
                'value_uz' => 'HOSPITALITY MANAGEMENT', 'value_en' => 'HOSPITALITY MANAGEMENT', 'value_ru' => 'ГОСТИНИЧНЫЙ МЕНЕДЖМЕНТ', 'order' => 1],
            ['key' => 'slide1_title', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 1 - Sarlavha',
                'value_uz' => "Xalqaro Turizm va Mehmondo'stlik Akademiyasi", 'value_en' => 'International Academy of Tourism and Hospitality', 'value_ru' => 'Международная Академия Туризма и Гостеприимства', 'order' => 2],
            ['key' => 'slide1_subtitle', 'type' => 'textarea', 'group' => 'Hero Slider', 'label' => 'Slide 1 - Tavsif',
                'value_uz' => "UN Tourism bilan hamkorlikda zamonaviy ta'lim. Xalqaro sertifikatlar va jahon miqyosidagi karyera imkoniyatlari.", 'value_en' => 'Modern education in partnership with UN Tourism. International certificates and global career opportunities.', 'value_ru' => 'Современное образование в партнерстве с UN Tourism. Международные сертификаты и глобальные карьерные возможности.', 'order' => 3],
            ['key' => 'slide1_btn1', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 1 - Tugma 1',
                'value_uz' => "Ro'yxatdan o'tish", 'value_en' => 'Register', 'value_ru' => 'Регистрация', 'order' => 4],
            ['key' => 'slide1_btn2', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 1 - Tugma 2',
                'value_uz' => 'Batafsil', 'value_en' => 'Learn More', 'value_ru' => 'Подробнее', 'order' => 5],
            ['key' => 'slide1_image', 'type' => 'image', 'group' => 'Hero Slider', 'label' => 'Slide 1 - Fon rasmi',
                'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 6],

            // Slide 2
            ['key' => 'slide2_badge', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 2 - Badge',
                'value_uz' => 'MEHMONXONA BIZNESI', 'value_en' => 'HOTEL BUSINESS', 'value_ru' => 'ГОСТИНИЧНЫЙ БИЗНЕС', 'order' => 10],
            ['key' => 'slide2_title', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 2 - Sarlavha',
                'value_uz' => 'Mehmonxona va Restoran Menejment', 'value_en' => 'Hotel and Restaurant Management', 'value_ru' => 'Гостиничный и Ресторанный Менеджмент', 'order' => 11],
            ['key' => 'slide2_subtitle', 'type' => 'textarea', 'group' => 'Hero Slider', 'label' => 'Slide 2 - Tavsif',
                'value_uz' => "5 yulduzli mehmonxonalarda amaliyot. Front Office, Housekeeping va F&B bo'limlari bo'yicha chuqur bilimlar.", 'value_en' => 'Internship in 5-star hotels. In-depth knowledge of Front Office, Housekeeping and F&B departments.', 'value_ru' => 'Стажировка в 5-звездочных отелях. Глубокие знания отделов Front Office, Housekeeping и F&B.', 'order' => 12],
            ['key' => 'slide2_btn1', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 2 - Tugma 1',
                'value_uz' => 'Dasturlar', 'value_en' => 'Programs', 'value_ru' => 'Программы', 'order' => 13],
            ['key' => 'slide2_btn2', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 2 - Tugma 2',
                'value_uz' => "Bog'lanish", 'value_en' => 'Contact', 'value_ru' => 'Контакты', 'order' => 14],
            ['key' => 'slide2_image', 'type' => 'image', 'group' => 'Hero Slider', 'label' => 'Slide 2 - Fon rasmi',
                'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 15],

            // Slide 3
            ['key' => 'slide3_badge', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 3 - Badge',
                'value_uz' => 'TURIZM MENEJMENT', 'value_en' => 'TOURISM MANAGEMENT', 'value_ru' => 'ТУРИЗМ МЕНЕДЖМЕНТ', 'order' => 20],
            ['key' => 'slide3_title', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 3 - Sarlavha',
                'value_uz' => 'Turizm va Ekskursiya Xizmatlari', 'value_en' => 'Tourism and Excursion Services', 'value_ru' => 'Туризм и Экскурсионные Услуги', 'order' => 21],
            ['key' => 'slide3_subtitle', 'type' => 'textarea', 'group' => 'Hero Slider', 'label' => 'Slide 3 - Tavsif',
                'value_uz' => "O'zbekiston va jahon bo'ylab turistik marshrutlar. Gid tayyorlash, destinatsiya boshqaruvi va turizm marketingi.", 'value_en' => 'Tourist routes across Uzbekistan and the world. Guide training, destination management and tourism marketing.', 'value_ru' => 'Туристические маршруты по Узбекистану и миру. Подготовка гидов, управление дестинациями и маркетинг туризма.', 'order' => 22],
            ['key' => 'slide3_btn1', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 3 - Tugma 1',
                'value_uz' => "O'qituvchilar", 'value_en' => 'Teachers', 'value_ru' => 'Преподаватели', 'order' => 23],
            ['key' => 'slide3_btn2', 'type' => 'text', 'group' => 'Hero Slider', 'label' => 'Slide 3 - Tugma 2',
                'value_uz' => 'Yangiliklar', 'value_en' => 'News', 'value_ru' => 'Новости', 'order' => 24],
            ['key' => 'slide3_image', 'type' => 'image', 'group' => 'Hero Slider', 'label' => 'Slide 3 - Fon rasmi',
                'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 25],

            // ========== STATISTICS ==========
            ['key' => 'stats_label', 'type' => 'text', 'group' => 'Statistika', 'label' => 'Bo\'lim yorlig\'i',
                'value_uz' => 'Statistika', 'value_en' => 'Statistics', 'value_ru' => 'Статистика', 'order' => 30],
            ['key' => 'stats_title', 'type' => 'text', 'group' => 'Statistika', 'label' => 'Bo\'lim sarlavhasi',
                'value_uz' => 'Bizning yutuqlarimiz', 'value_en' => 'Our Achievements', 'value_ru' => 'Наши достижения', 'order' => 31],
            ['key' => 'stat1_number', 'type' => 'text', 'group' => 'Statistika', 'label' => 'Stat 1 - Raqam',
                'value_uz' => '1500', 'value_en' => '1500', 'value_ru' => '1500', 'order' => 32],
            ['key' => 'stat1_label', 'type' => 'text', 'group' => 'Statistika', 'label' => 'Stat 1 - Yorliq',
                'value_uz' => 'Bitiruvchilar', 'value_en' => 'Graduates', 'value_ru' => 'Выпускники', 'order' => 33],
            ['key' => 'stat2_number', 'type' => 'text', 'group' => 'Statistika', 'label' => 'Stat 2 - Raqam',
                'value_uz' => '95', 'value_en' => '95', 'value_ru' => '95', 'order' => 34],
            ['key' => 'stat2_label', 'type' => 'text', 'group' => 'Statistika', 'label' => 'Stat 2 - Yorliq',
                'value_uz' => 'Ish bilan ta\'minlanganlik %', 'value_en' => 'Employment Rate %', 'value_ru' => 'Уровень трудоустройства %', 'order' => 35],
            ['key' => 'stat3_number', 'type' => 'text', 'group' => 'Statistika', 'label' => 'Stat 3 - Raqam',
                'value_uz' => '25', 'value_en' => '25', 'value_ru' => '25', 'order' => 36],
            ['key' => 'stat3_label', 'type' => 'text', 'group' => 'Statistika', 'label' => 'Stat 3 - Yorliq',
                'value_uz' => 'Yillik tajriba', 'value_en' => 'Years of Experience', 'value_ru' => 'Лет опыта', 'order' => 37],
            ['key' => 'stat4_number', 'type' => 'text', 'group' => 'Statistika', 'label' => 'Stat 4 - Raqam',
                'value_uz' => '50', 'value_en' => '50', 'value_ru' => '50', 'order' => 38],
            ['key' => 'stat4_label', 'type' => 'text', 'group' => 'Statistika', 'label' => 'Stat 4 - Yorliq',
                'value_uz' => 'Xalqaro hamkorlar', 'value_en' => 'International Partners', 'value_ru' => 'Международные партнеры', 'order' => 39],

            // ========== QUICK LINKS ==========
            ['key' => 'quick_link1_title', 'type' => 'text', 'group' => 'Tezkor havolalar', 'label' => 'Havola 1 - Sarlavha',
                'value_uz' => 'HEMIS tizimiga kirish', 'value_en' => 'HEMIS System Login', 'value_ru' => 'Вход в систему HEMIS', 'order' => 40],
            ['key' => 'quick_link1_desc', 'type' => 'textarea', 'group' => 'Tezkor havolalar', 'label' => 'Havola 1 - Tavsif',
                'value_uz' => "Talabalar uchun HEMIS tizimi. Baholar, jadval va o'quv ma'lumotlari.", 'value_en' => 'HEMIS system for students. Grades, schedule and academic information.', 'value_ru' => 'Система HEMIS для студентов. Оценки, расписание и учебная информация.', 'order' => 41],
            ['key' => 'quick_link2_title', 'type' => 'text', 'group' => 'Tezkor havolalar', 'label' => 'Havola 2 - Sarlavha',
                'value_uz' => 'LMS tizimiga kirish', 'value_en' => 'LMS System Login', 'value_ru' => 'Вход в систему LMS', 'order' => 42],
            ['key' => 'quick_link2_desc', 'type' => 'textarea', 'group' => 'Tezkor havolalar', 'label' => 'Havola 2 - Tavsif',
                'value_uz' => "Onlayn ta'lim platformasi. Darslar, topshiriqlar va test sinovlari.", 'value_en' => 'Online learning platform. Lessons, assignments and tests.', 'value_ru' => 'Онлайн платформа обучения. Уроки, задания и тесты.', 'order' => 43],
            ['key' => 'quick_link3_title', 'type' => 'text', 'group' => 'Tezkor havolalar', 'label' => 'Havola 3 - Sarlavha',
                'value_uz' => 'Onlayn ariza qoldirish', 'value_en' => 'Online Application', 'value_ru' => 'Онлайн заявка', 'order' => 44],
            ['key' => 'quick_link3_desc', 'type' => 'textarea', 'group' => 'Tezkor havolalar', 'label' => 'Havola 3 - Tavsif',
                'value_uz' => "Qabul uchun ariza topshirish. Tez va qulay ro'yxatdan o'tish.", 'value_en' => 'Submit application for admission. Quick and easy registration.', 'value_ru' => 'Подать заявку на поступление. Быстрая и удобная регистрация.', 'order' => 45],
            ['key' => 'quick_link4_title', 'type' => 'text', 'group' => 'Tezkor havolalar', 'label' => 'Havola 4 - Sarlavha',
                'value_uz' => 'Virtual tur', 'value_en' => 'Virtual Tour', 'value_ru' => 'Виртуальный тур', 'order' => 46],
            ['key' => 'quick_link4_desc', 'type' => 'textarea', 'group' => 'Tezkor havolalar', 'label' => 'Havola 4 - Tavsif',
                'value_uz' => "Akademiyamiz bilan 360° virtual sayohat. Bino va auditoriyalar.", 'value_en' => '360° virtual tour of our academy. Buildings and auditoriums.', 'value_ru' => 'Виртуальный тур 360° по нашей академии. Здания и аудитории.', 'order' => 47],

            // ========== NEWS SECTION ==========
            ['key' => 'news_label', 'type' => 'text', 'group' => 'Yangiliklar', 'label' => 'Bo\'lim yorlig\'i',
                'value_uz' => 'Yangiliklar', 'value_en' => 'News', 'value_ru' => 'Новости', 'order' => 50],
            ['key' => 'news_title', 'type' => 'text', 'group' => 'Yangiliklar', 'label' => 'Bo\'lim sarlavhasi',
                'value_uz' => 'Akademiyamiz yangiliklari', 'value_en' => 'Academy News', 'value_ru' => 'Новости академии', 'order' => 51],
            ['key' => 'news_subtitle', 'type' => 'text', 'group' => 'Yangiliklar', 'label' => 'Bo\'lim tavsifi',
                'value_uz' => "So'nggi tadbirlar va yangiliklar", 'value_en' => 'Latest events and news', 'value_ru' => 'Последние события и новости', 'order' => 52],

            // ========== EVENTS SECTION ==========
            ['key' => 'events_label', 'type' => 'text', 'group' => 'Tadbirlar', 'label' => 'Bo\'lim yorlig\'i',
                'value_uz' => 'Tadbirlar', 'value_en' => 'Events', 'value_ru' => 'Мероприятия', 'order' => 60],
            ['key' => 'events_title', 'type' => 'text', 'group' => 'Tadbirlar', 'label' => 'Bo\'lim sarlavhasi',
                'value_uz' => 'Kelgusi tadbirlar', 'value_en' => 'Upcoming Events', 'value_ru' => 'Предстоящие мероприятия', 'order' => 61],
            ['key' => 'events_subtitle', 'type' => 'text', 'group' => 'Tadbirlar', 'label' => 'Bo\'lim tavsifi',
                'value_uz' => "Akademiyamizda bo'lib o'tadigan tadbirlar", 'value_en' => 'Events happening at our academy', 'value_ru' => 'Мероприятия в нашей академии', 'order' => 62],

            // ========== ACADEMY LIFE (GALLERY) ==========
            ['key' => 'academy_label', 'type' => 'text', 'group' => 'Akademiya hayoti', 'label' => 'Bo\'lim yorlig\'i',
                'value_uz' => 'Akademiya hayoti', 'value_en' => 'Academy Life', 'value_ru' => 'Жизнь академии', 'order' => 70],
            ['key' => 'academy_title', 'type' => 'text', 'group' => 'Akademiya hayoti', 'label' => 'Bo\'lim sarlavhasi',
                'value_uz' => 'Bizning hayotimiz', 'value_en' => 'Our Life', 'value_ru' => 'Наша жизнь', 'order' => 71],
            ['key' => 'gallery_title_1', 'type' => 'text', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 1 - Sarlavha',
                'value_uz' => 'Zamonaviy kutubxona', 'value_en' => 'Modern Library', 'value_ru' => 'Современная библиотека', 'order' => 72],
            ['key' => 'gallery_text_1', 'type' => 'textarea', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 1 - Matn',
                'value_uz' => 'Eng yangi adabiyotlar va elektron resurslar', 'value_en' => 'Latest literature and digital resources', 'value_ru' => 'Новейшая литература и электронные ресурсы', 'order' => 73],
            ['key' => 'gallery_image_1', 'type' => 'image', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 1 - Rasm',
                'value_uz' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?w=600', 'value_en' => '', 'value_ru' => '', 'order' => 74],
            ['key' => 'gallery_title_2', 'type' => 'text', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 2 - Sarlavha',
                'value_uz' => 'Sport majmuasi', 'value_en' => 'Sports Complex', 'value_ru' => 'Спортивный комплекс', 'order' => 75],
            ['key' => 'gallery_text_2', 'type' => 'textarea', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 2 - Matn',
                'value_uz' => 'Zamonaviy sport zallari va maydonchalar', 'value_en' => 'Modern gyms and fields', 'value_ru' => 'Современные спортзалы и площадки', 'order' => 76],
            ['key' => 'gallery_image_2', 'type' => 'image', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 2 - Rasm',
                'value_uz' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=600', 'value_en' => '', 'value_ru' => '', 'order' => 77],
            ['key' => 'gallery_title_3', 'type' => 'text', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 3 - Sarlavha',
                'value_uz' => 'Laboratoriyalar', 'value_en' => 'Laboratories', 'value_ru' => 'Лаборатории', 'order' => 78],
            ['key' => 'gallery_text_3', 'type' => 'textarea', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 3 - Matn',
                'value_uz' => 'Yuqori texnologiyali o\'quv laboratoriyalari', 'value_en' => 'High-tech educational laboratories', 'value_ru' => 'Высокотехнологичные учебные лаборатории', 'order' => 79],
            ['key' => 'gallery_image_3', 'type' => 'image', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 3 - Rasm',
                'value_uz' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=600', 'value_en' => '', 'value_ru' => '', 'order' => 80],
            ['key' => 'gallery_title_4', 'type' => 'text', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 4 - Sarlavha',
                'value_uz' => 'O\'quv jarayoni', 'value_en' => 'Learning Process', 'value_ru' => 'Учебный процесс', 'order' => 81],
            ['key' => 'gallery_text_4', 'type' => 'textarea', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 4 - Matn',
                'value_uz' => 'Zamonaviy auditoriya va interaktiv darslar', 'value_en' => 'Modern classrooms and interactive lessons', 'value_ru' => 'Современные аудитории и интерактивные занятия', 'order' => 82],
            ['key' => 'gallery_image_4', 'type' => 'image', 'group' => 'Akademiya hayoti', 'label' => 'Galereya 4 - Rasm',
                'value_uz' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600', 'value_en' => '', 'value_ru' => '', 'order' => 83],

            // ========== FAQ SECTION ==========
            ['key' => 'faq_label', 'type' => 'text', 'group' => 'FAQ', 'label' => 'Bo\'lim yorlig\'i',
                'value_uz' => 'Ko\'p so\'raladigan savollar', 'value_en' => 'Frequently Asked Questions', 'value_ru' => 'Часто задаваемые вопросы', 'order' => 90],
            ['key' => 'faq_title', 'type' => 'text', 'group' => 'FAQ', 'label' => 'Bo\'lim sarlavhasi',
                'value_uz' => 'Savollaringizga javoblar', 'value_en' => 'Your Questions Answered', 'value_ru' => 'Ответы на ваши вопросы', 'order' => 91],
            ['key' => 'faq_subtitle', 'type' => 'text', 'group' => 'FAQ', 'label' => 'Bo\'lim tavsifi',
                'value_uz' => 'Eng ko\'p beriladigan savollarga javoblar', 'value_en' => 'Answers to the most frequently asked questions', 'value_ru' => 'Ответы на самые частые вопросы', 'order' => 92],
            ['key' => 'faq1_question', 'type' => 'text', 'group' => 'FAQ', 'label' => 'FAQ 1 - Savol',
                'value_uz' => 'Qanday yo\'nalishlar mavjud?', 'value_en' => 'What programs are available?', 'value_ru' => 'Какие программы доступны?', 'order' => 93],
            ['key' => 'faq1_answer', 'type' => 'textarea', 'group' => 'FAQ', 'label' => 'FAQ 1 - Javob',
                'value_uz' => 'Bizda mehmonxona menejment, turizm menejment, restoran biznesi va boshqa yo\'nalishlar mavjud.', 'value_en' => 'We have hotel management, tourism management, restaurant business and other programs.', 'value_ru' => 'У нас есть гостиничный менеджмент, туризм, ресторанный бизнес и другие программы.', 'order' => 94],
            ['key' => 'faq2_question', 'type' => 'text', 'group' => 'FAQ', 'label' => 'FAQ 2 - Savol',
                'value_uz' => 'O\'qish muddati qancha?', 'value_en' => 'How long is the study period?', 'value_ru' => 'Какова продолжительность обучения?', 'order' => 95],
            ['key' => 'faq2_answer', 'type' => 'textarea', 'group' => 'FAQ', 'label' => 'FAQ 2 - Javob',
                'value_uz' => 'Bakalavriat 4 yil, magistratura 2 yil davom etadi.', 'value_en' => 'Bachelor\'s degree is 4 years, Master\'s degree is 2 years.', 'value_ru' => 'Бакалавриат 4 года, магистратура 2 года.', 'order' => 96],
            ['key' => 'faq3_question', 'type' => 'text', 'group' => 'FAQ', 'label' => 'FAQ 3 - Savol',
                'value_uz' => 'Amaliyot imkoniyatlari bormi?', 'value_en' => 'Are there internship opportunities?', 'value_ru' => 'Есть ли возможности стажировки?', 'order' => 97],
            ['key' => 'faq3_answer', 'type' => 'textarea', 'group' => 'FAQ', 'label' => 'FAQ 3 - Javob',
                'value_uz' => 'Ha, talabalar 5 yulduzli mehmonxonalar va xalqaro kompaniyalarda amaliyot o\'taydilar.', 'value_en' => 'Yes, students do internships at 5-star hotels and international companies.', 'value_ru' => 'Да, студенты проходят стажировку в 5-звездочных отелях и международных компаниях.', 'order' => 98],

            // ========== PARTNERS SECTION ==========
            ['key' => 'partners_label', 'type' => 'text', 'group' => 'Hamkorlar', 'label' => 'Bo\'lim yorlig\'i',
                'value_uz' => 'Hamkorlarimiz', 'value_en' => 'Our Partners', 'value_ru' => 'Наши партнеры', 'order' => 100],
            // Partner 1
            ['key' => 'partner1_logo', 'type' => 'image', 'group' => 'Hamkorlar', 'label' => 'Hamkor 1 - Logo',
                'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 101],
            ['key' => 'partner1_name', 'type' => 'text', 'group' => 'Hamkorlar', 'label' => 'Hamkor 1 - Nomi',
                'value_uz' => 'UN Tourism', 'value_en' => 'UN Tourism', 'value_ru' => 'UN Tourism', 'order' => 102],
            // Partner 2
            ['key' => 'partner2_logo', 'type' => 'image', 'group' => 'Hamkorlar', 'label' => 'Hamkor 2 - Logo',
                'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 103],
            ['key' => 'partner2_name', 'type' => 'text', 'group' => 'Hamkorlar', 'label' => 'Hamkor 2 - Nomi',
                'value_uz' => 'Hilton Hotels', 'value_en' => 'Hilton Hotels', 'value_ru' => 'Hilton Hotels', 'order' => 104],
            // Partner 3
            ['key' => 'partner3_logo', 'type' => 'image', 'group' => 'Hamkorlar', 'label' => 'Hamkor 3 - Logo',
                'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 105],
            ['key' => 'partner3_name', 'type' => 'text', 'group' => 'Hamkorlar', 'label' => 'Hamkor 3 - Nomi',
                'value_uz' => 'Marriott', 'value_en' => 'Marriott', 'value_ru' => 'Marriott', 'order' => 106],
            // Partner 4
            ['key' => 'partner4_logo', 'type' => 'image', 'group' => 'Hamkorlar', 'label' => 'Hamkor 4 - Logo',
                'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 107],
            ['key' => 'partner4_name', 'type' => 'text', 'group' => 'Hamkorlar', 'label' => 'Hamkor 4 - Nomi',
                'value_uz' => 'Hyatt', 'value_en' => 'Hyatt', 'value_ru' => 'Hyatt', 'order' => 108],
            // Partner 5
            ['key' => 'partner5_logo', 'type' => 'image', 'group' => 'Hamkorlar', 'label' => 'Hamkor 5 - Logo',
                'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 109],
            ['key' => 'partner5_name', 'type' => 'text', 'group' => 'Hamkorlar', 'label' => 'Hamkor 5 - Nomi',
                'value_uz' => 'Radisson', 'value_en' => 'Radisson', 'value_ru' => 'Radisson', 'order' => 110],
            // Partner 6
            ['key' => 'partner6_logo', 'type' => 'image', 'group' => 'Hamkorlar', 'label' => 'Hamkor 6 - Logo',
                'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 111],
            ['key' => 'partner6_name', 'type' => 'text', 'group' => 'Hamkorlar', 'label' => 'Hamkor 6 - Nomi',
                'value_uz' => 'Accor', 'value_en' => 'Accor', 'value_ru' => 'Accor', 'order' => 112],

            // ========== BUTTONS ==========
            ['key' => 'btn_learn_more', 'type' => 'text', 'group' => 'Tugmalar', 'label' => 'Batafsil tugmasi',
                'value_uz' => 'Batafsil', 'value_en' => 'Learn More', 'value_ru' => 'Подробнее', 'order' => 110],
            ['key' => 'btn_all_news', 'type' => 'text', 'group' => 'Tugmalar', 'label' => 'Barcha yangiliklar tugmasi',
                'value_uz' => 'Barcha yangiliklar', 'value_en' => 'All News', 'value_ru' => 'Все новости', 'order' => 111],
            ['key' => 'btn_read_more', 'type' => 'text', 'group' => 'Tugmalar', 'label' => 'Ko\'proq o\'qish tugmasi',
                'value_uz' => 'Ko\'proq o\'qish', 'value_en' => 'Read More', 'value_ru' => 'Читать далее', 'order' => 112],
        ];
    }

    /**
     * Display the homepage editing page
     */
    public function index()
    {
        $defaultHomepage = $this->getDefaultHomepage();

        // Create missing default items
        foreach ($defaultHomepage as $item) {
            CmsContent::firstOrCreate(
                ['section' => 'homepage', 'key' => $item['key']],
                [
                    'value_uz' => $item['value_uz'],
                    'value_en' => $item['value_en'],
                    'value_ru' => $item['value_ru'],
                    'type' => $item['type'] ?? 'text',
                    'order' => $item['order'] ?? 0
                ]
            );
        }

        $contents = CmsContent::where('section', 'homepage')->orderBy('order')->get();

        // Group contents for better UI
        $groupedContents = [];
        foreach ($defaultHomepage as $item) {
            $group = $item['group'] ?? 'Boshqa';
            if (!isset($groupedContents[$group])) {
                $groupedContents[$group] = [];
            }
            $content = $contents->where('key', $item['key'])->first();
            if ($content) {
                $content->label = $item['label'] ?? $item['key'];
                $groupedContents[$group][] = $content;
            }
        }

        return view('cms.homepage.index', compact('contents', 'groupedContents'));
    }

    /**
     * Update homepage content
     */
    public function update(Request $request)
    {
        try {
            // Handle file uploads
            $imageFields = [
                'slide1_image', 'slide2_image', 'slide3_image',
                'gallery_image_1', 'gallery_image_2', 'gallery_image_3', 'gallery_image_4',
                'partner1_logo', 'partner2_logo', 'partner3_logo',
                'partner4_logo', 'partner5_logo', 'partner6_logo',
            ];

            // Image fields are now handled via AJAX (uploadImage/deleteImage methods).
            // This block stays as a no-op safety net for legacy submissions.
            $uploadedCount = 0;
            $uploadErrors = [];

            // Update other content
            if ($request->has('contents')) {
                foreach ($request->contents as $key => $contentData) {
                    if (in_array($key, $imageFields)) continue;

                    CmsContent::updateOrCreate(
                        ['section' => 'homepage', 'key' => $key],
                        [
                            'value_uz' => $contentData['value_uz'] ?? null,
                            'value_en' => $contentData['value_en'] ?? null,
                            'value_ru' => $contentData['value_ru'] ?? null,
                            'type' => $contentData['type'] ?? 'text',
                        ]
                    );
                }
            }

            return redirect()->route('cms.homepage.index')
                ->with('success', 'Bosh sahifa muvaffaqiyatli yangilandi!');

        } catch (\Exception $e) {
            \Log::error('Homepage update error: ' . $e->getMessage());
            return redirect()->route('cms.homepage.index')
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }
}
