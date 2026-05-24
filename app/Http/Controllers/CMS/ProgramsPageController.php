<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsContent;

class ProgramsPageController extends Controller
{
    public function index()
    {
        // Default programs page content structure
        $defaultPrograms = [
            // Hero Section
            ['key' => 'programs_hero_badge', 'type' => 'text', 'value_uz' => "O'QUV DASTURLARI", 'value_en' => 'PROGRAMS', 'value_ru' => 'ПРОГРАММЫ', 'order' => 1],
            ['key' => 'programs_hero_title_highlight', 'type' => 'text', 'value_uz' => "Mehmondo'stlik Boshqaruvi", 'value_en' => 'Hospitality Management', 'value_ru' => 'Управление гостеприимством', 'order' => 2],
            ['key' => 'programs_hero_title_2', 'type' => 'text', 'value_uz' => 'Dasturlari', 'value_en' => 'Programs', 'value_ru' => 'Программы', 'order' => 3],
            ['key' => 'programs_hero_description', 'type' => 'textarea', 'value_uz' => "UN Tourism bilan hamkorlikda turizm va mehmondo'stlik sohasida professional kadrlar tayyorlash dasturlari. Xalqaro sertifikatlarga ega bo'ling va global karyera imkoniyatlariga ega bo'ling.", 'value_en' => 'Professional training programs in tourism and hospitality in partnership with UN Tourism. Get international certificates and global career opportunities.', 'value_ru' => 'Программы профессиональной подготовки в сфере туризма и гостеприимства в партнерстве с UN Tourism. Получите международные сертификаты и глобальные карьерные возможности.', 'order' => 4],

            // Stats Section
            ['key' => 'programs_stat1_icon', 'type' => 'text', 'value_uz' => 'fas fa-book-open', 'value_en' => 'fas fa-book-open', 'value_ru' => 'fas fa-book-open', 'order' => 10],
            ['key' => 'programs_stat1_value', 'type' => 'text', 'value_uz' => '8', 'value_en' => '8', 'value_ru' => '8', 'order' => 11],
            ['key' => 'programs_stat1_label', 'type' => 'text', 'value_uz' => "Ta'lim dasturlari", 'value_en' => 'Training Programs', 'value_ru' => 'Учебные программы', 'order' => 12],

            ['key' => 'programs_stat2_icon', 'type' => 'text', 'value_uz' => 'fas fa-certificate', 'value_en' => 'fas fa-certificate', 'value_ru' => 'fas fa-certificate', 'order' => 13],
            ['key' => 'programs_stat2_value', 'type' => 'text', 'value_uz' => 'UN Tourism', 'value_en' => 'UN Tourism', 'value_ru' => 'UN Tourism', 'order' => 14],
            ['key' => 'programs_stat2_label', 'type' => 'text', 'value_uz' => 'Sertifikat', 'value_en' => 'Certificate', 'value_ru' => 'Сертификат', 'order' => 15],

            ['key' => 'programs_stat3_icon', 'type' => 'text', 'value_uz' => 'fas fa-globe', 'value_en' => 'fas fa-globe', 'value_ru' => 'fas fa-globe', 'order' => 16],
            ['key' => 'programs_stat3_value', 'type' => 'text', 'value_uz' => 'Xalqaro', 'value_en' => 'International', 'value_ru' => 'Международное', 'order' => 17],
            ['key' => 'programs_stat3_label', 'type' => 'text', 'value_uz' => 'Tan olinish', 'value_en' => 'Recognition', 'value_ru' => 'Признание', 'order' => 18],

            // Programs Section Title
            ['key' => 'programs_section_title', 'type' => 'text', 'value_uz' => 'Dasturlar', 'value_en' => 'Programs', 'value_ru' => 'Программы', 'order' => 20],
            ['key' => 'programs_section_subtitle', 'type' => 'text', 'value_uz' => "Mehmondo'stlik sohasida professional bilim va ko'nikmalarni rivojlantirish uchun maxsus ishlab chiqilgan dasturlar", 'value_en' => 'Programs specially designed to develop professional knowledge and skills in the hospitality sector', 'value_ru' => 'Программы, специально разработанные для развития профессиональных знаний и навыков в сфере гостеприимства', 'order' => 21],

            // 8 Programs
            ['key' => 'program1_title', 'type' => 'text', 'value_uz' => "Turizmda innovatsion, alternativ va barqaror yo'nalishlar", 'value_en' => 'Innovative, alternative and sustainable directions in tourism', 'value_ru' => 'Инновационные, альтернативные и устойчивые направления в туризме', 'order' => 30],
            ['key' => 'program1_dates', 'type' => 'text', 'value_uz' => '21-aprel - 23-may', 'value_en' => 'April 21 - May 23', 'value_ru' => '21 апреля - 23 мая', 'order' => 31],
            ['key' => 'program1_description', 'type' => 'textarea', 'value_uz' => "Mehmondo'stlik industriyasida barqarorlik tamoyillari va innovatsion amaliyotlar, ularning operatsion samaradorlikka, marketing natijalariga va mijozlar tajribasiga ta'siri haqida chuqur bilim.", 'value_en' => 'In-depth knowledge of sustainability principles and innovative practices in the hospitality industry, their impact on operational efficiency, marketing results, and customer experience.', 'value_ru' => 'Глубокие знания о принципах устойчивости и инновационных практиках в индустрии гостеприимства, их влиянии на операционную эффективность, маркетинговые результаты и клиентский опыт.', 'order' => 32],
            ['key' => 'program1_topics', 'type' => 'text', 'value_uz' => '5', 'value_en' => '5', 'value_ru' => '5', 'order' => 33],

            ['key' => 'program2_title', 'type' => 'text', 'value_uz' => 'Tadbirkorlik, biznes modellashtirish va inqirozni boshqarish', 'value_en' => 'Entrepreneurship, business modeling and crisis management', 'value_ru' => 'Предпринимательство, бизнес-моделирование и кризисное управление', 'order' => 40],
            ['key' => 'program2_dates', 'type' => 'text', 'value_uz' => '26-may - 27-iyun', 'value_en' => 'May 26 - June 27', 'value_ru' => '26 мая - 27 июня', 'order' => 41],
            ['key' => 'program2_description', 'type' => 'textarea', 'value_uz' => "Mehmondo'stlik sohasida muvaffaqiyatli kichik va o'rta bizneslarni yaratish va boshqarish, biznesning uzluksizligini ta'minlash va inqirozni boshqarish strategiyalari.", 'value_en' => 'Creating and managing successful small and medium businesses in the hospitality sector, ensuring business continuity and crisis management strategies.', 'value_ru' => 'Создание и управление успешными малыми и средними предприятиями в сфере гостеприимства, обеспечение непрерывности бизнеса и стратегии кризисного управления.', 'order' => 42],
            ['key' => 'program2_topics', 'type' => 'text', 'value_uz' => '6', 'value_en' => '6', 'value_ru' => '6', 'order' => 43],

            ['key' => 'program3_title', 'type' => 'text', 'value_uz' => 'Loyihalarni boshqarish va xalqaro inson resurslari', 'value_en' => 'Project management and international human resources', 'value_ru' => 'Управление проектами и международные человеческие ресурсы', 'order' => 50],
            ['key' => 'program3_dates', 'type' => 'text', 'value_uz' => '30-iyun - 31-iyul', 'value_en' => 'June 30 - July 31', 'value_ru' => '30 июня - 31 июля', 'order' => 51],
            ['key' => 'program3_description', 'type' => 'textarea', 'value_uz' => "Mehmondo'stlik sohasida loyihalarni va inson resurslarini samarali boshqarish, loyiha boshqaruvi tamoyillari va xalqaro HR amaliyotlari.", 'value_en' => 'Effective management of projects and human resources in hospitality, project management principles and international HR practices.', 'value_ru' => 'Эффективное управление проектами и человеческими ресурсами в сфере гостеприимства, принципы управления проектами и международные HR практики.', 'order' => 52],
            ['key' => 'program3_topics', 'type' => 'text', 'value_uz' => '5', 'value_en' => '5', 'value_ru' => '5', 'order' => 53],

            ['key' => 'program4_title', 'type' => 'text', 'value_uz' => 'Rivojlangan moliya va byudjetlashtirish', 'value_en' => 'Advanced finance and budgeting', 'value_ru' => 'Продвинутые финансы и бюджетирование', 'order' => 60],
            ['key' => 'program4_dates', 'type' => 'text', 'value_uz' => '4-avgust - 5-sentabr', 'value_en' => 'August 4 - September 5', 'value_ru' => '4 августа - 5 сентября', 'order' => 61],
            ['key' => 'program4_description', 'type' => 'textarea', 'value_uz' => "Mehmondo'stlik sohasida rivojlangan moliyaviy rejalashtirish va byudjetlashtirish texnikalari, daromad va xarajatlarni prognozlash, moliyaviy ko'rsatkichlar orqali boshqaruv qarorlarini qabul qilish.", 'value_en' => 'Advanced financial planning and budgeting techniques in hospitality, revenue and expense forecasting, management decision-making through financial indicators.', 'value_ru' => 'Продвинутые техники финансового планирования и бюджетирования в сфере гостеприимства, прогнозирование доходов и расходов, принятие управленческих решений через финансовые показатели.', 'order' => 62],
            ['key' => 'program4_topics', 'type' => 'text', 'value_uz' => '5', 'value_en' => '5', 'value_ru' => '5', 'order' => 63],

            ['key' => 'program5_title', 'type' => 'text', 'value_uz' => "Marketing strategiyalari va ma'lumotlar tahlili", 'value_en' => 'Marketing strategies and data analysis', 'value_ru' => 'Маркетинговые стратегии и анализ данных', 'order' => 70],
            ['key' => 'program5_dates', 'type' => 'text', 'value_uz' => '8-sentabr - 10-oktabr', 'value_en' => 'September 8 - October 10', 'value_ru' => '8 сентября - 10 октября', 'order' => 71],
            ['key' => 'program5_description', 'type' => 'textarea', 'value_uz' => "Mehmonxona biznesida marketing strategiyalari, raqamli marketing, ma'lumotlarni tahlil qilish va biznes optimizatsiyasi.", 'value_en' => 'Marketing strategies in hotel business, digital marketing, data analysis and business optimization.', 'value_ru' => 'Маркетинговые стратегии в гостиничном бизнесе, цифровой маркетинг, анализ данных и оптимизация бизнеса.', 'order' => 72],
            ['key' => 'program5_topics', 'type' => 'text', 'value_uz' => '6', 'value_en' => '6', 'value_ru' => '6', 'order' => 73],

            ['key' => 'program6_title', 'type' => 'text', 'value_uz' => 'Daromad boshqaruvi', 'value_en' => 'Revenue management', 'value_ru' => 'Управление доходами', 'order' => 80],
            ['key' => 'program6_dates', 'type' => 'text', 'value_uz' => '13-oktabr - 14-noyabr', 'value_en' => 'October 13 - November 14', 'value_ru' => '13 октября - 14 ноября', 'order' => 81],
            ['key' => 'program6_description', 'type' => 'textarea', 'value_uz' => "Mehmondo'stlik sohasida daromad va narx siyosatini samarali boshqarish, inventarizatsiyani boshqarish, talabni prognozlash va taqsimot kanallarini tahlil qilish.", 'value_en' => 'Effective revenue and pricing policy management in hospitality, inventory management, demand forecasting and distribution channel analysis.', 'value_ru' => 'Эффективное управление доходами и ценовой политикой в сфере гостеприимства, управление запасами, прогнозирование спроса и анализ каналов распределения.', 'order' => 82],
            ['key' => 'program6_topics', 'type' => 'text', 'value_uz' => '12', 'value_en' => '12', 'value_ru' => '12', 'order' => 83],

            ['key' => 'program7_title', 'type' => 'text', 'value_uz' => "Mehmondo'stlik ko'chmas mulki va investitsiyalar", 'value_en' => 'Hospitality real estate and investments', 'value_ru' => 'Недвижимость гостеприимства и инвестиции', 'order' => 90],
            ['key' => 'program7_dates', 'type' => 'text', 'value_uz' => '17-noyabr - 19-dekabr', 'value_en' => 'November 17 - December 19', 'value_ru' => '17 ноября - 19 декабря', 'order' => 91],
            ['key' => 'program7_description', 'type' => 'textarea', 'value_uz' => "Xalqaro mehmondo'stlik va ko'chmas mulk industriyalarida muvaffaqiyatli ishlash, moliyaviy tahlil, investitsiya strategiyalari va aktivlarni boshqarish.", 'value_en' => 'Successful work in international hospitality and real estate industries, financial analysis, investment strategies and asset management.', 'value_ru' => 'Успешная работа в международной индустрии гостеприимства и недвижимости, финансовый анализ, инвестиционные стратегии и управление активами.', 'order' => 92],
            ['key' => 'program7_topics', 'type' => 'text', 'value_uz' => '4', 'value_en' => '4', 'value_ru' => '4', 'order' => 93],

            ['key' => 'program8_title', 'type' => 'text', 'value_uz' => 'Tadbirlarni boshqarish', 'value_en' => 'Event management', 'value_ru' => 'Управление мероприятиями', 'order' => 100],
            ['key' => 'program8_dates', 'type' => 'text', 'value_uz' => '5-yanvar - 30-yanvar 2026', 'value_en' => 'January 5 - January 30, 2026', 'value_ru' => '5 января - 30 января 2026', 'order' => 101],
            ['key' => 'program8_description', 'type' => 'textarea', 'value_uz' => "Mehmondo'stlik sohasida tadbirlarni rejalashtirish, boshqarish va muvaffaqiyatli amalga oshirish, tadbir dizayni, logistika, marketing va protokol.", 'value_en' => 'Planning, managing and successfully executing events in hospitality, event design, logistics, marketing and protocol.', 'value_ru' => 'Планирование, управление и успешное проведение мероприятий в сфере гостеприимства, дизайн мероприятий, логистика, маркетинг и протокол.', 'order' => 102],
            ['key' => 'program8_topics', 'type' => 'text', 'value_uz' => '13', 'value_en' => '13', 'value_ru' => '13', 'order' => 103],

            // Benefits Section
            ['key' => 'benefits_title', 'type' => 'text', 'value_uz' => "Siz nimalarga ega bo'lasiz?", 'value_en' => 'What will you gain?', 'value_ru' => 'Что вы получите?', 'order' => 110],
            ['key' => 'benefits_subtitle', 'type' => 'text', 'value_uz' => "Dasturlarni tugatganingizdan so'ng siz quyidagi ko'nikma va imkoniyatlarga ega bo'lasiz", 'value_en' => 'After completing the programs, you will have the following skills and opportunities', 'value_ru' => 'После завершения программ вы получите следующие навыки и возможности', 'order' => 111],

            // 6 Benefits
            ['key' => 'benefit1_icon', 'type' => 'text', 'value_uz' => 'fas fa-award', 'value_en' => 'fas fa-award', 'value_ru' => 'fas fa-award', 'order' => 120],
            ['key' => 'benefit1_title', 'type' => 'text', 'value_uz' => 'Xalqaro sertifikat', 'value_en' => 'International Certificate', 'value_ru' => 'Международный сертификат', 'order' => 121],
            ['key' => 'benefit1_text', 'type' => 'text', 'value_uz' => "UN Tourism tomonidan tan olingan sertifikatlar barcha a'zo mamlakatlarida amal qiladi", 'value_en' => 'Certificates recognized by UN Tourism are valid in all member countries', 'value_ru' => 'Сертификаты, признанные UN Tourism, действительны во всех странах-членах', 'order' => 122],

            ['key' => 'benefit2_icon', 'type' => 'text', 'value_uz' => 'fas fa-users', 'value_en' => 'fas fa-users', 'value_ru' => 'fas fa-users', 'order' => 123],
            ['key' => 'benefit2_title', 'type' => 'text', 'value_uz' => 'Professional tarmoq', 'value_en' => 'Professional Network', 'value_ru' => 'Профессиональная сеть', 'order' => 124],
            ['key' => 'benefit2_text', 'type' => 'text', 'value_uz' => 'Global turizm industriyasi mutaxassislari va tengdoshlar bilan aloqalar', 'value_en' => 'Connections with global tourism industry experts and peers', 'value_ru' => 'Связи с экспертами мировой туристической индустрии и коллегами', 'order' => 125],

            ['key' => 'benefit3_icon', 'type' => 'text', 'value_uz' => 'fas fa-briefcase', 'value_en' => 'fas fa-briefcase', 'value_ru' => 'fas fa-briefcase', 'order' => 126],
            ['key' => 'benefit3_title', 'type' => 'text', 'value_uz' => 'Karyera imkoniyatlari', 'value_en' => 'Career Opportunities', 'value_ru' => 'Карьерные возможности', 'order' => 127],
            ['key' => 'benefit3_text', 'type' => 'text', 'value_uz' => "Yirik mehmonxonalar va turizm korxonalarida rahbarlik lavozimlariga yo'l", 'value_en' => 'Path to leadership positions in major hotels and tourism enterprises', 'value_ru' => 'Путь к руководящим должностям в крупных отелях и туристических предприятиях', 'order' => 128],

            ['key' => 'benefit4_icon', 'type' => 'text', 'value_uz' => 'fas fa-chart-line', 'value_en' => 'fas fa-chart-line', 'value_ru' => 'fas fa-chart-line', 'order' => 129],
            ['key' => 'benefit4_title', 'type' => 'text', 'value_uz' => "Amaliy ko'nikmalar", 'value_en' => 'Practical Skills', 'value_ru' => 'Практические навыки', 'order' => 130],
            ['key' => 'benefit4_text', 'type' => 'text', 'value_uz' => "Real biznes vaziyatlarida qo'llash mumkin bo'lgan amaliy bilimlar", 'value_en' => 'Practical knowledge applicable to real business situations', 'value_ru' => 'Практические знания, применимые в реальных бизнес-ситуациях', 'order' => 131],

            ['key' => 'benefit5_icon', 'type' => 'text', 'value_uz' => 'fas fa-laptop-code', 'value_en' => 'fas fa-laptop-code', 'value_ru' => 'fas fa-laptop-code', 'order' => 132],
            ['key' => 'benefit5_title', 'type' => 'text', 'value_uz' => 'Zamonaviy texnologiyalar', 'value_en' => 'Modern Technologies', 'value_ru' => 'Современные технологии', 'order' => 133],
            ['key' => 'benefit5_text', 'type' => 'text', 'value_uz' => "Raqamli marketing, ma'lumotlar tahlili va zamonaviy boshqaruv tizimlari", 'value_en' => 'Digital marketing, data analysis and modern management systems', 'value_ru' => 'Цифровой маркетинг, анализ данных и современные системы управления', 'order' => 134],

            ['key' => 'benefit6_icon', 'type' => 'text', 'value_uz' => 'fas fa-globe-americas', 'value_en' => 'fas fa-globe-americas', 'value_ru' => 'fas fa-globe-americas', 'order' => 135],
            ['key' => 'benefit6_title', 'type' => 'text', 'value_uz' => "Global ko'nikmalar", 'value_en' => 'Global Skills', 'value_ru' => 'Глобальные навыки', 'order' => 136],
            ['key' => 'benefit6_text', 'type' => 'text', 'value_uz' => "Xalqaro standartlar va eng yaxshi amaliyotlar bo'yicha chuqur bilim", 'value_en' => 'In-depth knowledge of international standards and best practices', 'value_ru' => 'Глубокие знания международных стандартов и лучших практик', 'order' => 137],
        ];

        // Create default content if not exists
        foreach ($defaultPrograms as $item) {
            CmsContent::firstOrCreate(
                ['section' => 'programs', 'key' => $item['key']],
                [
                    'type' => $item['type'],
                    'value_uz' => $item['value_uz'],
                    'value_en' => $item['value_en'],
                    'value_ru' => $item['value_ru'],
                    'order' => $item['order']
                ]
            );
        }

        $contents = CmsContent::where('section', 'programs')->orderBy('order')->get();

        return view('cms.programs.index', compact('contents'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $values) {
            if (is_array($values)) {
                CmsContent::updateOrCreate(
                    ['section' => 'programs', 'key' => $key],
                    [
                        'value_uz' => $values['uz'] ?? '',
                        'value_en' => $values['en'] ?? '',
                        'value_ru' => $values['ru'] ?? '',
                    ]
                );
            }
        }

        return redirect()->route('cms.programs.index')->with('success', "Dasturlar sahifasi muvaffaqiyatli yangilandi!");
    }
}
