<?php

namespace Database\Seeders;

use App\Models\CmsContent;
use App\Models\CmsSetting;
use Illuminate\Database\Seeder;

class CmsContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set default theme
        CmsSetting::updateOrCreate(
            ['key' => 'site_theme'],
            [
                'value' => 'theme1',
                'type' => 'select',
                'group' => 'appearance',
                'label' => 'Site Theme',
                'description' => 'Select the visual theme for the public website',
                'options' => json_encode(['theme1', 'theme2', 'theme3', 'theme4', 'theme5']),
                'is_public' => true,
            ]
        );

        $content = [
            // Hero Slides
            [
                'section' => 'homepage',
                'key' => 'slide1_badge',
                'value_uz' => 'HOSPITALITY MANAGEMENT',
                'value_en' => 'HOSPITALITY MANAGEMENT',
                'value_ru' => 'ГОСТИНИЧНЫЙ МЕНЕДЖМЕНТ',
            ],
            [
                'section' => 'homepage',
                'key' => 'slide1_title',
                'value_uz' => "Xalqaro Turizm va Mehmondo'stlik Akademiyasi",
                'value_en' => 'International Academy of Tourism and Hospitality',
                'value_ru' => 'Международная Академия Туризма и Гостеприимства',
            ],
            [
                'section' => 'homepage',
                'key' => 'slide1_subtitle',
                'value_uz' => "Kelajak kadrlarini bugun tayyorlaymiz",
                'value_en' => 'Preparing future professionals today',
                'value_ru' => 'Готовим будущих профессионалов сегодня',
            ],
            [
                'section' => 'homepage',
                'key' => 'slide2_badge',
                'value_uz' => "TA'LIM SIFATI",
                'value_en' => 'QUALITY EDUCATION',
                'value_ru' => 'КАЧЕСТВЕННОЕ ОБРАЗОВАНИЕ',
            ],
            [
                'section' => 'homepage',
                'key' => 'slide2_title',
                'value_uz' => "Zamonaviy ta'lim - Yorqin kelajak",
                'value_en' => 'Modern Education - Bright Future',
                'value_ru' => 'Современное образование - Светлое будущее',
            ],
            [
                'section' => 'homepage',
                'key' => 'slide2_subtitle',
                'value_uz' => "Xalqaro standartlar asosida ta'lim",
                'value_en' => 'Education based on international standards',
                'value_ru' => 'Образование на основе международных стандартов',
            ],
            [
                'section' => 'homepage',
                'key' => 'slide3_badge',
                'value_uz' => 'KARYERA',
                'value_en' => 'CAREER',
                'value_ru' => 'КАРЬЕРА',
            ],
            [
                'section' => 'homepage',
                'key' => 'slide3_title',
                'value_uz' => "Global imkoniyatlar - Cheksiz salohiyat",
                'value_en' => 'Global Opportunities - Unlimited Potential',
                'value_ru' => 'Глобальные возможности - Безграничный потенциал',
            ],
            [
                'section' => 'homepage',
                'key' => 'slide3_subtitle',
                'value_uz' => "Dunyo mehmonxonalarida ishlash imkoniyati",
                'value_en' => 'Opportunity to work in world hotels',
                'value_ru' => 'Возможность работы в мировых отелях',
            ],

            // Buttons
            [
                'section' => 'homepage',
                'key' => 'btn_learn_more',
                'value_uz' => "Batafsil ma'lumot",
                'value_en' => 'Learn More',
                'value_ru' => 'Подробнее',
            ],
            [
                'section' => 'homepage',
                'key' => 'btn_apply',
                'value_uz' => "Hujjat topshirish",
                'value_en' => 'Apply Now',
                'value_ru' => 'Подать заявку',
            ],

            // Quick Links
            [
                'section' => 'homepage',
                'key' => 'quick_link1_title',
                'value_uz' => 'HEMIS tizimiga kirish',
                'value_en' => 'HEMIS System Login',
                'value_ru' => 'Вход в систему HEMIS',
            ],
            [
                'section' => 'homepage',
                'key' => 'quick_link1_desc',
                'value_uz' => "Talabalar uchun HEMIS tizimi orqali o'quv jarayonini kuzatish",
                'value_en' => 'Track academic progress through HEMIS for students',
                'value_ru' => 'Отслеживание учебного процесса через HEMIS для студентов',
            ],
            [
                'section' => 'homepage',
                'key' => 'quick_link2_title',
                'value_uz' => "Kutubxona resurslari",
                'value_en' => 'Library Resources',
                'value_ru' => 'Ресурсы библиотеки',
            ],
            [
                'section' => 'homepage',
                'key' => 'quick_link2_desc',
                'value_uz' => "Elektron kutubxona va o'quv materiallari bazasi",
                'value_en' => 'Digital library and educational materials database',
                'value_ru' => 'Электронная библиотека и база учебных материалов',
            ],
            [
                'section' => 'homepage',
                'key' => 'quick_link3_title',
                'value_uz' => "Qabulxona",
                'value_en' => 'Admission Office',
                'value_ru' => 'Приёмная комиссия',
            ],
            [
                'section' => 'homepage',
                'key' => 'quick_link3_desc',
                'value_uz' => "Hujjat topshirish va qabul jarayoni haqida",
                'value_en' => 'About document submission and admission process',
                'value_ru' => 'О подаче документов и процессе поступления',
            ],
            [
                'section' => 'homepage',
                'key' => 'quick_link4_title',
                'value_uz' => "Amaliyot",
                'value_en' => 'Internship',
                'value_ru' => 'Практика',
            ],
            [
                'section' => 'homepage',
                'key' => 'quick_link4_desc',
                'value_uz' => "Amaliyot o'rni va ish joylari haqida ma'lumot",
                'value_en' => 'Information about internship and job opportunities',
                'value_ru' => 'Информация о местах практики и трудоустройстве',
            ],

            // News Section
            [
                'section' => 'homepage',
                'key' => 'news_title',
                'value_uz' => "So'nggi yangiliklar",
                'value_en' => 'Latest News',
                'value_ru' => 'Последние новости',
            ],
            [
                'section' => 'homepage',
                'key' => 'news_view_all',
                'value_uz' => "Barcha yangiliklar",
                'value_en' => 'All News',
                'value_ru' => 'Все новости',
            ],
            [
                'section' => 'homepage',
                'key' => 'news_read_more',
                'value_uz' => "Batafsil o'qish",
                'value_en' => 'Read More',
                'value_ru' => 'Читать далее',
            ],

            // About Page
            [
                'section' => 'about',
                'key' => 'page_title',
                'value_uz' => 'Biz haqimizda',
                'value_en' => 'About Us',
                'value_ru' => 'О нас',
            ],
            [
                'section' => 'about',
                'key' => 'page_description',
                'value_uz' => "Akademiyamiz tarixi, missiyasi va qadriyatlari haqida batafsil ma'lumot",
                'value_en' => 'Detailed information about our academy history, mission and values',
                'value_ru' => 'Подробная информация об истории, миссии и ценностях нашей академии',
            ],
            [
                'section' => 'about',
                'key' => 'hero_badge',
                'value_uz' => 'BIZ HAQIMIZDA',
                'value_en' => 'ABOUT US',
                'value_ru' => 'О НАС',
            ],

            // Teachers Page
            [
                'section' => 'teachers',
                'key' => 'page_title',
                'value_uz' => "O'qituvchilar",
                'value_en' => 'Teachers',
                'value_ru' => 'Преподаватели',
            ],
            [
                'section' => 'teachers',
                'key' => 'page_description',
                'value_uz' => "Akademiyamizning malakali professor-o'qituvchilari",
                'value_en' => 'Qualified professors and teachers of our academy',
                'value_ru' => 'Квалифицированные профессора и преподаватели нашей академии',
            ],
            [
                'section' => 'teachers',
                'key' => 'hero_badge',
                'value_uz' => "BIZNING JAMOA",
                'value_en' => 'OUR TEAM',
                'value_ru' => 'НАША КОМАНДА',
            ],

            // Programs Page
            [
                'section' => 'programs',
                'key' => 'page_title',
                'value_uz' => "Ta'lim yo'nalishlari",
                'value_en' => 'Academic Programs',
                'value_ru' => 'Учебные программы',
            ],
            [
                'section' => 'programs',
                'key' => 'page_description',
                'value_uz' => "Bakalavriat va magistratura dasturlari",
                'value_en' => 'Bachelor and Master programs',
                'value_ru' => 'Программы бакалавриата и магистратуры',
            ],
            [
                'section' => 'programs',
                'key' => 'hero_badge',
                'value_uz' => "TA'LIM DASTURLARI",
                'value_en' => 'ACADEMIC PROGRAMS',
                'value_ru' => 'УЧЕБНЫЕ ПРОГРАММЫ',
            ],

            // Contact Page
            [
                'section' => 'contact',
                'key' => 'page_title',
                'value_uz' => 'Aloqa',
                'value_en' => 'Contact',
                'value_ru' => 'Контакты',
            ],
            [
                'section' => 'contact',
                'key' => 'page_description',
                'value_uz' => "Biz bilan bog'laning",
                'value_en' => 'Get in touch with us',
                'value_ru' => 'Свяжитесь с нами',
            ],
            [
                'section' => 'contact',
                'key' => 'hero_badge',
                'value_uz' => 'ALOQA',
                'value_en' => 'CONTACT US',
                'value_ru' => 'СВЯЗАТЬСЯ С НАМИ',
            ],

            // Statistics Page
            [
                'section' => 'statistics',
                'key' => 'page_title',
                'value_uz' => 'Statistika',
                'value_en' => 'Statistics',
                'value_ru' => 'Статистика',
            ],
            [
                'section' => 'statistics',
                'key' => 'page_description',
                'value_uz' => "Akademiya haqida raqamlarda",
                'value_en' => 'Academy in numbers',
                'value_ru' => 'Академия в цифрах',
            ],
            [
                'section' => 'statistics',
                'key' => 'hero_badge',
                'value_uz' => 'STATISTIKA',
                'value_en' => 'STATISTICS',
                'value_ru' => 'СТАТИСТИКА',
            ],

            // Blog Page
            [
                'section' => 'blog',
                'key' => 'page_title',
                'value_uz' => 'Blog',
                'value_en' => 'Blog',
                'value_ru' => 'Блог',
            ],
            [
                'section' => 'blog',
                'key' => 'page_description',
                'value_uz' => "Akademiya hayoti va yangiliklari",
                'value_en' => 'Academy life and news',
                'value_ru' => 'Жизнь и новости академии',
            ],
            [
                'section' => 'blog',
                'key' => 'hero_badge',
                'value_uz' => 'BLOG',
                'value_en' => 'BLOG',
                'value_ru' => 'БЛОГ',
            ],

            // ========== HEADER MENU ITEMS ==========
            [
                'section' => 'header',
                'key' => 'menu_home',
                'value_uz' => 'Bosh sahifa',
                'value_en' => 'Home',
                'value_ru' => 'Главная',
            ],
            [
                'section' => 'header',
                'key' => 'menu_about',
                'value_uz' => 'Biz haqimizda',
                'value_en' => 'About Us',
                'value_ru' => 'О нас',
            ],
            [
                'section' => 'header',
                'key' => 'menu_programs',
                'value_uz' => "Yo'nalishlar",
                'value_en' => 'Programs',
                'value_ru' => 'Программы',
            ],
            [
                'section' => 'header',
                'key' => 'menu_teachers',
                'value_uz' => "O'qituvchilar",
                'value_en' => 'Teachers',
                'value_ru' => 'Преподаватели',
            ],
            [
                'section' => 'header',
                'key' => 'menu_statistics',
                'value_uz' => 'Statistika',
                'value_en' => 'Statistics',
                'value_ru' => 'Статистика',
            ],
            [
                'section' => 'header',
                'key' => 'menu_blog',
                'value_uz' => 'Blog',
                'value_en' => 'Blog',
                'value_ru' => 'Блог',
            ],
            [
                'section' => 'header',
                'key' => 'menu_contact',
                'value_uz' => "Bog'lanish",
                'value_en' => 'Contact',
                'value_ru' => 'Контакты',
            ],
            [
                'section' => 'header',
                'key' => 'login_button',
                'value_uz' => 'Kirish',
                'value_en' => 'Login',
                'value_ru' => 'Вход',
            ],
            [
                'section' => 'header',
                'key' => 'dashboard_button',
                'value_uz' => 'Dashboard',
                'value_en' => 'Dashboard',
                'value_ru' => 'Панель',
            ],

            // ========== FOOTER CONTENT ==========
            [
                'section' => 'footer',
                'key' => 'footer_title',
                'value_uz' => "Xalqaro Turizm va Mehmondo'stlik Akademiyasi",
                'value_en' => 'International Academy of Tourism and Hospitality',
                'value_ru' => 'Международная Академия Туризма и Гостеприимства',
            ],
            [
                'section' => 'footer',
                'key' => 'footer_description',
                'value_uz' => "Turizm va mehmondo'stlik sohasida yuqori malakali mutaxassislarni tayyorlash",
                'value_en' => 'Training highly qualified specialists in tourism and hospitality',
                'value_ru' => 'Подготовка высококвалифицированных специалистов в сфере туризма и гостеприимства',
            ],
            [
                'section' => 'footer',
                'key' => 'col2_title',
                'value_uz' => 'Tezkor havolalar',
                'value_en' => 'Quick Links',
                'value_ru' => 'Быстрые ссылки',
            ],
            [
                'section' => 'footer',
                'key' => 'col3_title',
                'value_uz' => 'Resurslar',
                'value_en' => 'Resources',
                'value_ru' => 'Ресурсы',
            ],
            [
                'section' => 'footer',
                'key' => 'col4_title',
                'value_uz' => "Bog'lanish",
                'value_en' => 'Contact',
                'value_ru' => 'Контакты',
            ],
            [
                'section' => 'footer',
                'key' => 'contact_address',
                'value_uz' => "Samarqand shahar, Istiqlol ko'chasi, 47",
                'value_en' => '47 Istiqlol Street, Samarkand city',
                'value_ru' => 'г. Самарканд, ул. Истиклол, 47',
            ],
            [
                'section' => 'footer',
                'key' => 'contact_phone',
                'value_uz' => '+998 66 233 XX XX',
                'value_en' => '+998 66 233 XX XX',
                'value_ru' => '+998 66 233 XX XX',
            ],
            [
                'section' => 'footer',
                'key' => 'contact_email',
                'value_uz' => 'info@tourism.uz',
                'value_en' => 'info@tourism.uz',
                'value_ru' => 'info@tourism.uz',
            ],
            [
                'section' => 'footer',
                'key' => 'social_title',
                'value_uz' => 'Ijtimoiy tarmoqlar',
                'value_en' => 'Social Media',
                'value_ru' => 'Социальные сети',
            ],
            [
                'section' => 'footer',
                'key' => 'copyright_text',
                'value_uz' => '© 2025 Tourism Academy. Barcha huquqlar himoyalangan.',
                'value_en' => '© 2025 Tourism Academy. All rights reserved.',
                'value_ru' => '© 2025 Tourism Academy. Все права защищены.',
            ],
            [
                'section' => 'footer',
                'key' => 'privacy_policy',
                'value_uz' => 'Maxfiylik siyosati',
                'value_en' => 'Privacy Policy',
                'value_ru' => 'Политика конфиденциальности',
            ],
            [
                'section' => 'footer',
                'key' => 'terms_of_use',
                'value_uz' => 'Foydalanish shartlari',
                'value_en' => 'Terms of Use',
                'value_ru' => 'Условия использования',
            ],
            [
                'section' => 'footer',
                'key' => 'cookie_settings',
                'value_uz' => 'Cookie sozlamalari',
                'value_en' => 'Cookie Settings',
                'value_ru' => 'Настройки Cookie',
            ],

            // Footer Column 2 Links
            [
                'section' => 'footer',
                'key' => 'col2_link1_text',
                'value_uz' => 'Bosh sahifa',
                'value_en' => 'Home',
                'value_ru' => 'Главная',
            ],
            [
                'section' => 'footer',
                'key' => 'col2_link2_text',
                'value_uz' => 'Biz haqimizda',
                'value_en' => 'About Us',
                'value_ru' => 'О нас',
            ],
            [
                'section' => 'footer',
                'key' => 'col2_link3_text',
                'value_uz' => "Yo'nalishlar",
                'value_en' => 'Programs',
                'value_ru' => 'Программы',
            ],
            [
                'section' => 'footer',
                'key' => 'col2_link4_text',
                'value_uz' => "Bog'lanish",
                'value_en' => 'Contact',
                'value_ru' => 'Контакты',
            ],

            // Footer Column 3 Links
            [
                'section' => 'footer',
                'key' => 'col3_link1_text',
                'value_uz' => 'HEMIS tizimi',
                'value_en' => 'HEMIS System',
                'value_ru' => 'Система HEMIS',
            ],
            [
                'section' => 'footer',
                'key' => 'col3_link2_text',
                'value_uz' => 'Kutubxona',
                'value_en' => 'Library',
                'value_ru' => 'Библиотека',
            ],
            [
                'section' => 'footer',
                'key' => 'col3_link3_text',
                'value_uz' => 'Yangiliklar',
                'value_en' => 'News',
                'value_ru' => 'Новости',
            ],
        ];

        foreach ($content as $item) {
            CmsContent::updateOrCreate(
                ['section' => $item['section'], 'key' => $item['key']],
                $item
            );
        }

        $this->command->info('CMS content seeded successfully with 3 languages!');
    }
}
