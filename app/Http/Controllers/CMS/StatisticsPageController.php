<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsContent;

class StatisticsPageController extends Controller
{
    public function index()
    {
        // Default statistics page content structure
        $defaultStatistics = [
            // Hero Section
            ['key' => 'stats_hero_badge', 'type' => 'text', 'value_uz' => 'AKADEMIYA STATISTIKASI', 'value_en' => 'ACADEMY STATISTICS', 'value_ru' => 'СТАТИСТИКА АКАДЕМИИ', 'order' => 1],
            ['key' => 'stats_hero_title', 'type' => 'text', 'value_uz' => 'Talabalar statistikasi', 'value_en' => 'Student Statistics', 'value_ru' => 'Статистика студентов', 'order' => 2],
            ['key' => 'stats_hero_subtitle', 'type' => 'text', 'value_uz' => 'Bizning akademiyamiz haqida raqamlarda', 'value_en' => 'About our academy in numbers', 'value_ru' => 'О нашей академии в цифрах', 'order' => 3],

            // Age Statistics Section
            ['key' => 'stats_age_title', 'type' => 'text', 'value_uz' => "Yosh bo'yicha", 'value_en' => 'By Age', 'value_ru' => 'По возрасту', 'order' => 10],

            ['key' => 'stats_age1_icon', 'type' => 'text', 'value_uz' => 'fas fa-user-graduate', 'value_en' => 'fas fa-user-graduate', 'value_ru' => 'fas fa-user-graduate', 'order' => 11],
            ['key' => 'stats_age1_value', 'type' => 'text', 'value_uz' => '145', 'value_en' => '145', 'value_ru' => '145', 'order' => 12],
            ['key' => 'stats_age1_label', 'type' => 'text', 'value_uz' => '18-22 yosh', 'value_en' => '18-22 years', 'value_ru' => '18-22 лет', 'order' => 13],

            ['key' => 'stats_age2_icon', 'type' => 'text', 'value_uz' => 'fas fa-user-tie', 'value_en' => 'fas fa-user-tie', 'value_ru' => 'fas fa-user-tie', 'order' => 14],
            ['key' => 'stats_age2_value', 'type' => 'text', 'value_uz' => '89', 'value_en' => '89', 'value_ru' => '89', 'order' => 15],
            ['key' => 'stats_age2_label', 'type' => 'text', 'value_uz' => '23-27 yosh', 'value_en' => '23-27 years', 'value_ru' => '23-27 лет', 'order' => 16],

            ['key' => 'stats_age3_icon', 'type' => 'text', 'value_uz' => 'fas fa-user-friends', 'value_en' => 'fas fa-user-friends', 'value_ru' => 'fas fa-user-friends', 'order' => 17],
            ['key' => 'stats_age3_value', 'type' => 'text', 'value_uz' => '52', 'value_en' => '52', 'value_ru' => '52', 'order' => 18],
            ['key' => 'stats_age3_label', 'type' => 'text', 'value_uz' => '28-35 yosh', 'value_en' => '28-35 years', 'value_ru' => '28-35 лет', 'order' => 19],

            ['key' => 'stats_age4_icon', 'type' => 'text', 'value_uz' => 'fas fa-users', 'value_en' => 'fas fa-users', 'value_ru' => 'fas fa-users', 'order' => 20],
            ['key' => 'stats_age4_value', 'type' => 'text', 'value_uz' => '34', 'value_en' => '34', 'value_ru' => '34', 'order' => 21],
            ['key' => 'stats_age4_label', 'type' => 'text', 'value_uz' => '36+ yosh', 'value_en' => '36+ years', 'value_ru' => '36+ лет', 'order' => 22],

            // Region Statistics Section
            ['key' => 'stats_region_title', 'type' => 'text', 'value_uz' => "Mintaqa bo'yicha", 'value_en' => 'By Region', 'value_ru' => 'По регионам', 'order' => 30],

            ['key' => 'stats_region1_value', 'type' => 'text', 'value_uz' => '125', 'value_en' => '125', 'value_ru' => '125', 'order' => 31],
            ['key' => 'stats_region1_label', 'type' => 'text', 'value_uz' => 'Samarqand', 'value_en' => 'Samarkand', 'value_ru' => 'Самарканд', 'order' => 32],

            ['key' => 'stats_region2_value', 'type' => 'text', 'value_uz' => '85', 'value_en' => '85', 'value_ru' => '85', 'order' => 33],
            ['key' => 'stats_region2_label', 'type' => 'text', 'value_uz' => 'Toshkent', 'value_en' => 'Tashkent', 'value_ru' => 'Ташкент', 'order' => 34],

            ['key' => 'stats_region3_value', 'type' => 'text', 'value_uz' => '65', 'value_en' => '65', 'value_ru' => '65', 'order' => 35],
            ['key' => 'stats_region3_label', 'type' => 'text', 'value_uz' => 'Buxoro', 'value_en' => 'Bukhara', 'value_ru' => 'Бухара', 'order' => 36],

            ['key' => 'stats_region4_value', 'type' => 'text', 'value_uz' => '45', 'value_en' => '45', 'value_ru' => '45', 'order' => 37],
            ['key' => 'stats_region4_label', 'type' => 'text', 'value_uz' => 'Xorazm', 'value_en' => 'Khorezm', 'value_ru' => 'Хорезм', 'order' => 38],

            ['key' => 'stats_region5_value', 'type' => 'text', 'value_uz' => '38', 'value_en' => '38', 'value_ru' => '38', 'order' => 39],
            ['key' => 'stats_region5_label', 'type' => 'text', 'value_uz' => 'Qashqadaryo', 'value_en' => 'Kashkadarya', 'value_ru' => 'Кашкадарья', 'order' => 40],

            ['key' => 'stats_region6_value', 'type' => 'text', 'value_uz' => '62', 'value_en' => '62', 'value_ru' => '62', 'order' => 41],
            ['key' => 'stats_region6_label', 'type' => 'text', 'value_uz' => 'Boshqa hududlar', 'value_en' => 'Other regions', 'value_ru' => 'Другие регионы', 'order' => 42],

            // Education Level Statistics Section
            ['key' => 'stats_edu_title', 'type' => 'text', 'value_uz' => "Ta'lim darajasi bo'yicha", 'value_en' => 'By Education Level', 'value_ru' => 'По уровню образования', 'order' => 50],

            ['key' => 'stats_edu1_icon', 'type' => 'text', 'value_uz' => 'fas fa-graduation-cap', 'value_en' => 'fas fa-graduation-cap', 'value_ru' => 'fas fa-graduation-cap', 'order' => 51],
            ['key' => 'stats_edu1_value', 'type' => 'text', 'value_uz' => '180', 'value_en' => '180', 'value_ru' => '180', 'order' => 52],
            ['key' => 'stats_edu1_title', 'type' => 'text', 'value_uz' => 'Bakalavr', 'value_en' => 'Bachelor', 'value_ru' => 'Бакалавр', 'order' => 53],
            ['key' => 'stats_edu1_label', 'type' => 'text', 'value_uz' => '4 yillik dastur', 'value_en' => '4-year program', 'value_ru' => '4-летняя программа', 'order' => 54],

            ['key' => 'stats_edu2_icon', 'type' => 'text', 'value_uz' => 'fas fa-user-graduate', 'value_en' => 'fas fa-user-graduate', 'value_ru' => 'fas fa-user-graduate', 'order' => 55],
            ['key' => 'stats_edu2_value', 'type' => 'text', 'value_uz' => '95', 'value_en' => '95', 'value_ru' => '95', 'order' => 56],
            ['key' => 'stats_edu2_title', 'type' => 'text', 'value_uz' => 'Magistratura', 'value_en' => 'Master', 'value_ru' => 'Магистратура', 'order' => 57],
            ['key' => 'stats_edu2_label', 'type' => 'text', 'value_uz' => '2 yillik dastur', 'value_en' => '2-year program', 'value_ru' => '2-летняя программа', 'order' => 58],

            ['key' => 'stats_edu3_icon', 'type' => 'text', 'value_uz' => 'fas fa-certificate', 'value_en' => 'fas fa-certificate', 'value_ru' => 'fas fa-certificate', 'order' => 59],
            ['key' => 'stats_edu3_value', 'type' => 'text', 'value_uz' => '45', 'value_en' => '45', 'value_ru' => '45', 'order' => 60],
            ['key' => 'stats_edu3_title', 'type' => 'text', 'value_uz' => 'Qisqa muddatli', 'value_en' => 'Short-term', 'value_ru' => 'Краткосрочные', 'order' => 61],
            ['key' => 'stats_edu3_label', 'type' => 'text', 'value_uz' => 'Sertifikat dasturlari', 'value_en' => 'Certificate programs', 'value_ru' => 'Сертификатные программы', 'order' => 62],
        ];

        // Create default content if not exists
        foreach ($defaultStatistics as $item) {
            CmsContent::firstOrCreate(
                ['section' => 'statistics', 'key' => $item['key']],
                [
                    'type' => $item['type'],
                    'value_uz' => $item['value_uz'],
                    'value_en' => $item['value_en'],
                    'value_ru' => $item['value_ru'],
                    'order' => $item['order']
                ]
            );
        }

        $contents = CmsContent::where('section', 'statistics')->orderBy('order')->get();

        return view('cms.statistics.index', compact('contents'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $values) {
            if (is_array($values)) {
                CmsContent::updateOrCreate(
                    ['section' => 'statistics', 'key' => $key],
                    [
                        'value_uz' => $values['uz'] ?? '',
                        'value_en' => $values['en'] ?? '',
                        'value_ru' => $values['ru'] ?? '',
                    ]
                );
            }
        }

        return redirect()->route('cms.statistics.index')->with('success', "Statistika sahifasi muvaffaqiyatli yangilandi!");
    }
}
