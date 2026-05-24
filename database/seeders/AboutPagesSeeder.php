<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\CmsContent;
use Illuminate\Database\Seeder;

class AboutPagesSeeder extends Seeder
{
    /**
     * about.txt dan sahifalar yaratish
     *
     * php artisan db:seed --class=AboutPagesSeeder
     */
    public function run(): void
    {
        $this->createPages();
        $this->createContent();

        $this->command->info('✓ Barcha sahifalar muvaffaqiyatli yaratildi!');
    }

    protected function createPages(): void
    {
        $pages = [
            // ========== AKADEMIYA HAQIDA ==========
            [
                'title_uz' => 'Akademiya haqida',
                'title_ru' => 'Об академии',
                'title_en' => 'About Academy',
                'slug' => 'akademiya-haqida',
                'content_uz' => $this->getAboutContentUz(),
                'content_ru' => '<h2>Об академии</h2><p>Международная Академия Туризма — Самаркандский кампус</p>',
                'content_en' => '<h2>About Academy</h2><p>International Tourism Academy — Samarkand Campus</p>',
                'meta_description_uz' => 'Xalqaro Turizm Akademiyasi Samarqand kampusi haqida ma\'lumot',
                'status' => 'published',
                'show_in_menu' => true,
                'show_in_footer' => true,
                'order_position' => 1,
            ],

            // ========== TARKIBIY TUZILMA ==========
            [
                'title_uz' => 'Tarkibiy tuzilma',
                'title_ru' => 'Структура',
                'title_en' => 'Structure',
                'slug' => 'tarkibiy-tuzilma',
                'content_uz' => $this->getStructureContentUz(),
                'content_ru' => '<h2>Структура академии</h2>',
                'content_en' => '<h2>Academy Structure</h2>',
                'meta_description_uz' => 'Akademiya tarkibiy tuzilmasi - rahbariyat, bo\'limlar',
                'status' => 'published',
                'show_in_menu' => true,
                'order_position' => 2,
            ],

            // ========== RAHBARIYAT ==========
            [
                'title_uz' => 'Rahbariyat',
                'title_ru' => 'Руководство',
                'title_en' => 'Leadership',
                'slug' => 'rahbariyat',
                'content_uz' => $this->getLeadershipContentUz(),
                'content_ru' => '<h2>Руководство академии</h2>',
                'content_en' => '<h2>Academy Leadership</h2>',
                'meta_description_uz' => 'Akademiya rahbariyati - direktor, o\'rinbosarlar',
                'status' => 'published',
                'show_in_menu' => true,
                'order_position' => 3,
            ],

            // ========== BO'LIMLAR ==========
            [
                'title_uz' => 'Bo\'limlar',
                'title_ru' => 'Отделы',
                'title_en' => 'Departments',
                'slug' => 'bolimlar',
                'content_uz' => $this->getDepartmentsContentUz(),
                'content_ru' => '<h2>Отделы академии</h2>',
                'content_en' => '<h2>Academy Departments</h2>',
                'meta_description_uz' => 'Akademiya bo\'limlari va kafedralar',
                'status' => 'published',
                'show_in_menu' => true,
                'order_position' => 4,
            ],

            // ========== O'QITUVCHILAR ==========
            [
                'title_uz' => 'O\'qituvchilar',
                'title_ru' => 'Преподаватели',
                'title_en' => 'Teachers',
                'slug' => 'oqituvchilar',
                'content_uz' => $this->getTeachersContentUz(),
                'content_ru' => '<h2>Преподаватели</h2>',
                'content_en' => '<h2>Teachers</h2>',
                'meta_description_uz' => 'Akademiya o\'qituvchilari',
                'status' => 'published',
                'show_in_menu' => true,
                'order_position' => 5,
            ],

            // ========== XALQARO HAMKORLIK ==========
            [
                'title_uz' => 'Xalqaro hamkorlik',
                'title_ru' => 'Международное сотрудничество',
                'title_en' => 'International Cooperation',
                'slug' => 'xalqaro-hamkorlik',
                'content_uz' => $this->getInternationalContentUz(),
                'content_ru' => '<h2>Международное сотрудничество</h2>',
                'content_en' => '<h2>International Cooperation</h2>',
                'meta_description_uz' => 'UN Tourism, Les Roches universiteti bilan hamkorlik',
                'status' => 'published',
                'show_in_menu' => true,
                'order_position' => 6,
            ],

            // ========== O'QUV DASTURLARI ==========
            [
                'title_uz' => 'O\'quv dasturlari',
                'title_ru' => 'Учебные программы',
                'title_en' => 'Programs',
                'slug' => 'oquv-dasturlari',
                'content_uz' => $this->getProgramsContentUz(),
                'content_ru' => '<h2>Учебные программы</h2>',
                'content_en' => '<h2>Academic Programs</h2>',
                'meta_description_uz' => 'Malaka oshirish va qayta tayyorlash kurslari',
                'status' => 'published',
                'show_in_menu' => true,
                'order_position' => 7,
            ],

            // ========== KUTUBXONA ==========
            [
                'title_uz' => 'Axborot resurs markazi',
                'title_ru' => 'Информационный ресурсный центр',
                'title_en' => 'Information Resource Center',
                'slug' => 'kutubxona',
                'content_uz' => '<h2>Axborot resurs markazi</h2><p>Elektron kitoblar va o\'quv materiallari</p>',
                'content_ru' => '<h2>Информационный ресурсный центр</h2>',
                'content_en' => '<h2>Information Resource Center</h2>',
                'meta_description_uz' => 'Elektron kutubxona va o\'quv qo\'llanmalar',
                'status' => 'published',
                'show_in_menu' => true,
                'order_position' => 8,
            ],

            // ========== ALOQA ==========
            [
                'title_uz' => 'Aloqa',
                'title_ru' => 'Контакты',
                'title_en' => 'Contact',
                'slug' => 'aloqa',
                'content_uz' => '<h2>Biz bilan bog\'laning</h2>
<div class="contact-info">
    <p><strong>Manzil:</strong> Samarqand shahar, Istiqlol ko\'chasi, 47</p>
    <p><strong>Telefon:</strong> +998 66 233 XX XX</p>
    <p><strong>Email:</strong> info@tourism-academy.uz</p>
    <p><strong>Ish vaqti:</strong> Dushanba - Juma: 9:00 - 18:00</p>
</div>',
                'content_ru' => '<h2>Свяжитесь с нами</h2>',
                'content_en' => '<h2>Contact Us</h2>',
                'meta_description_uz' => 'Akademiya bilan bog\'lanish',
                'status' => 'published',
                'show_in_menu' => true,
                'show_in_footer' => true,
                'order_position' => 9,
            ],
        ];

        foreach ($pages as $pageData) {
            CmsPage::updateOrCreate(
                ['slug' => $pageData['slug']],
                array_merge($pageData, ['created_by' => 1])
            );
            $this->command->info("Sahifa yaratildi: {$pageData['title_uz']}");
        }
    }

    protected function getAboutContentUz(): string
    {
        return <<<HTML
<section class="about-section">
    <h2>Xalqaro Turizm Akademiyasi — Samarqand kampusi</h2>
    <p>Xalqaro Turizm Akademiyasi Samarqand kampusi O'zbekiston Respublikasida turizm sohasini rivojlantirish, xalqaro standartlarga mos kadrlar tayyorlash va soha mutaxassislarining malakasini oshirish maqsadida tashkil etilgan davlat ta'lim muassasasidir.</p>

    <h3>Akademiyaning tashkil etilishining huquqiy asoslari</h3>
    <p>Xalqaro Turizm Akademiyasi Samarqand kampusi O'zbekiston Respublikasi Prezidentining <strong>"Butunjahon turizm tashkiloti shafeligida Xalqaro turizm akademiyasi Samarqand kampusi faoliyatini tashkil etish to'g'risida"gi PQ–350-son qarori</strong> asosida tashkil etilgan.</p>

    <h3>Akademiyaning huquqiy maqomi</h3>
    <ul>
        <li>Akademiya davlat muassasasi shaklidagi yuridik shaxs hisoblanadi.</li>
        <li>Akademiyaning muassisi — O'zbekiston Respublikasi Turizm qo'mitasi huzuridagi "Ipak yo'li" turizm va madaniy meros xalqaro universiteti.</li>
    </ul>

    <h3>Missiya va qadriyatlar</h3>
    <p>Xalqaro turizm akademiyasi Samarqand kampusining missiyasi — turizm va mehmondo'stlik industriyasini rivojlantirishga xizmat qiladigan, xalqaro standartlarga mos, amaliy bilim va ko'nikmalarga ega malakali mutaxassislar avlodini shakllantirishdir.</p>

    <p>Akademiya ta'lim jarayonida UN Tourism (sobiq UNWTO) tavsiyalari, ilg'or xalqaro tajriba hamda real biznes amaliyotini uyg'unlashtirgan holda, turizm sohasida ishlayotgan va faoliyatini yangi bosqichga olib chiqmoqchi bo'lgan mutaxassislar uchun qayta tayyorlash va malaka oshirishning zamonaviy modelini joriy etadi.</p>

    <h3>Bizning qadriyatlarimiz</h3>
    <div class="values-list">
        <div class="value-item">
            <strong>Amaliy natija</strong> — har bir kurs real ish ko'nikmasini beradi
        </div>
        <div class="value-item">
            <strong>Sifat va standartlar</strong> — davlat va xalqaro talablar asosida ta'lim
        </div>
        <div class="value-item">
            <strong>Mas'uliyat</strong> — ish beruvchi va tinglovchi oldidagi javobgarlik
        </div>
        <div class="value-item">
            <strong>Hamkorlik</strong> — ta'lim va real sektor integratsiyasi
        </div>
        <div class="value-item">
            <strong>Rivojlanish</strong> — doimiy yangilanish va zamonaviy yondashuv
        </div>
    </div>
</section>
HTML;
    }

    protected function getStructureContentUz(): string
    {
        return <<<HTML
<section class="structure-section">
    <h2>Tarkibiy tuzilma</h2>
    <p>Akademiyaning tarkibiy tuzilmasi quyidagi bo'limlardan iborat:</p>

    <div class="structure-list">
        <div class="structure-item">
            <h4>Rahbariyat</h4>
            <p>Direktor, direktor o'rinbosarlari</p>
        </div>
        <div class="structure-item">
            <h4>O'quv jarayonini rejalashtirish bo'limi</h4>
            <p>Academic planning department</p>
        </div>
        <div class="structure-item">
            <h4>Turizm va mehmondo'stlik kafedrasi</h4>
            <p>Tourism and Hospitality Faculty</p>
        </div>
        <div class="structure-item">
            <h4>Raqamli ta'lim texnologiyalari bo'limi</h4>
            <p>IT and Infrastructure Department</p>
        </div>
        <div class="structure-item">
            <h4>Marketing va PR bo'limi</h4>
            <p>Marketing and PR department</p>
        </div>
    </div>
</section>
HTML;
    }

    protected function getLeadershipContentUz(): string
    {
        return <<<HTML
<section class="leadership-section">
    <h2>Rahbariyat</h2>

    <div class="leader-card">
        <div class="leader-info">
            <h3>Direktor</h3>
            <p class="leader-name">Narzikulov Dilshod Rustamovich</p>
            <p><strong>Telefon:</strong> <a href="tel:"></a></p>
            <p><strong>Email:</strong> <a href="mailto:"></a></p>
        </div>
    </div>

    <div class="leader-card">
        <div class="leader-info">
            <h3>Direktor o'rinbosari</h3>
            <p class="leader-name">Raxmanov Jasur Ubaydulloevich</p>
            <p><strong>Telefon:</strong> <a href="tel:"></a></p>
            <p><strong>Email:</strong> <a href="mailto:"></a></p>
        </div>
    </div>

    <div class="leader-card">
        <div class="leader-info">
            <h3>Akademik masalalar bo'yicha direktor o'rinbosari</h3>
            <p class="leader-name">—</p>
        </div>
    </div>
</section>
HTML;
    }

    protected function getDepartmentsContentUz(): string
    {
        return <<<HTML
<section class="departments-section">
    <h2>Bo'limlar</h2>

    <div class="department-card">
        <h4>O'quv jarayonini rejalashtirish bo'limi</h4>
        <p class="en-name">Academic planning department</p>
        <p><strong>Bo'lim boshlig'i:</strong> —</p>
    </div>

    <div class="department-card">
        <h4>Oziq ovqat va ichimliklar bo'limi</h4>
        <p class="en-name">Food and Beverage department</p>
        <p><strong>Bo'lim boshlig'i:</strong> —</p>
    </div>

    <div class="department-card">
        <h4>Turizm va mehmondo'stlik kafedrasi</h4>
        <p class="en-name">Tourism and Hospitality Faculty</p>
        <p><strong>Kafedra mudiri:</strong> —</p>
    </div>

    <div class="department-card">
        <h4>Raqamli ta'lim texnologiyalari bo'limi</h4>
        <p class="en-name">IT and Infrastructure Department</p>
        <p><strong>Bo'lim boshlig'i:</strong> Xolmurodov Ozod Abdimannon o'g'li</p>
        <p><strong>Telefon:</strong> <a href="tel:+998932331833">+998 93 233 18 33</a></p>
    </div>

    <div class="department-card">
        <h4>Buxgalteriya bo'limi</h4>
        <p class="en-name">Accounting Department</p>
        <p><strong>Bo'lim boshlig'i:</strong> Axmedova Nilufar Raxmatovna</p>
        <p><strong>Telefon:</strong> <a href="tel:+998902125393">+998 90 212 53 93</a></p>
    </div>

    <div class="department-card">
        <h4>Xodimlar bilan ishlash bo'limi</h4>
        <p class="en-name">HR Department</p>
        <p><strong>Bo'lim boshlig'i:</strong> Aliyeva Madina Abdumansurovna</p>
        <p><strong>Telefon:</strong> <a href="tel:+998902700789">+998 90 270 07 89</a></p>
    </div>

    <div class="department-card">
        <h4>Texnik-eksplutatsiya va xo'jalik ishlari bo'limi</h4>
        <p class="en-name">Campus service</p>
        <p><strong>Bo'lim boshlig'i:</strong> Abdukasimov Bexzod Raxmanberdievich</p>
        <p><strong>Telefon:</strong> <a href="tel:+998939945400">+998 93 994 54 00</a></p>
    </div>

    <div class="department-card">
        <h4>Marketing va PR bo'limi</h4>
        <p class="en-name">Marketing and PR department</p>
        <p><strong>Bo'lim boshlig'i:</strong> Muradova Nigina Shukurovna</p>
        <p><strong>Telefon:</strong> <a href="tel:+998973982217">+998 97 398 22 17</a></p>
        <p><strong>Email:</strong> <a href="mailto:pr.tourismacademy@gmail.com">pr.tourismacademy@gmail.com</a></p>
    </div>
</section>
HTML;
    }

    protected function getTeachersContentUz(): string
    {
        return <<<HTML
<section class="teachers-section">
    <h2>O'qituvchilar tarkibi</h2>

    <div class="teachers-grid">
        <div class="teacher-card">
            <div class="teacher-photo"></div>
            <h4>Narkulova Shaxnoza Shakarbekovna</h4>
        </div>
        <div class="teacher-card">
            <div class="teacher-photo"></div>
            <h4>Mohammad Shahparan</h4>
        </div>
        <div class="teacher-card">
            <div class="teacher-photo"></div>
            <h4>Djurakulova Shamsiya Djamshedovna</h4>
        </div>
        <div class="teacher-card">
            <div class="teacher-photo"></div>
            <h4>Maxmudova Zarrina Zayniddin qizi</h4>
        </div>
        <div class="teacher-card">
            <div class="teacher-photo"></div>
            <h4>Yelena Salnikova - PhD</h4>
        </div>
    </div>
</section>
HTML;
    }

    protected function getInternationalContentUz(): string
    {
        return <<<HTML
<section class="international-section">
    <h2>Xalqaro hamkorlik</h2>

    <h3>UN Tourism bilan hamkorlik</h3>
    <p>UN Tourism bilan kelishuv doirasida Akademiyada quyidagi asosiy yo'nalishlar bo'yicha modulli dasturlar amalga oshiriladi:</p>
    <ul>
        <li>Mehmondo'stlik menejmenti (8 ta modul)</li>
        <li>Mehmonxona faoliyati va innovatsiyalar (8 ta modul)</li>
        <li>Destinatsiyalarni barqaror boshqaruvi (7 ta modul)</li>
    </ul>

    <h3>Ta'lim dasturlari</h3>
    <p>Akademiyada ikki asosiy ta'lim dasturi yo'lga qo'yilgan:</p>
    <ul>
        <li><strong>Trenerlar malakasini oshirish kurslari</strong> – 6 oylik, onlayn va oflayn formatda</li>
        <li><strong>Qayta tayyorlash kurslari</strong> – qisqa va uzoq muddatli dasturlar</li>
    </ul>

    <h3>Les Roches universiteti bilan hamkorlik</h3>
    <p>Ta'lim jarayonlari dunyoning nufuzli mehmondo'stlik ta'lim muassasalaridan biri bo'lgan Shveysariyaning Les Roches universiteti bilan hamkorlikda tashkil etiladi (ma'ruzalar onlayn va oflayn formatlarda olib boriladi).</p>

    <h3>Rejalashtirilgan kurslar</h3>
    <ul>
        <li>Mehmondo'stlik va mehmonxona faoliyatini boshqarish bo'yicha amaliy kurslar (Hotel Operations, Front Office, Housekeeping, F&B, HACCP)</li>
        <li>Tur operatorlik va tur agentlik faoliyatini boshqarish kurslari</li>
        <li>Restoran, kafe va catering (Hospitality & F&B) menejmenti bo'yicha o'quv dasturlari</li>
        <li>Turizm yo'nalishida faoliyat yuritayotgan professor-o'qituvchilar uchun malaka oshirish kurslari</li>
        <li>Hunarmandlar uchun turizmga yo'naltirilgan mahsulot va xizmatlarni rivojlantirish bo'yicha maxsus kurslar</li>
    </ul>
</section>
HTML;
    }

    protected function getProgramsContentUz(): string
    {
        return <<<HTML
<section class="programs-section">
    <h2>O'quv dasturlari</h2>

    <p>Xalqaro Turizm Akademiyasi Samarqand kampusida ta'lim jarayoni amaliyotga yo'naltirilgan, modulli va kompetensiyaviy yondashuv asosida tashkil etiladi.</p>

    <h3>Qisqa muddatli malaka oshirish kurslari</h3>
    <p>Turizm, turagentlik va mehmonxona sohasida faoliyat yuritayotgan mutaxassislar uchun:</p>
    <ul>
        <li>Kurslar ro'yxati yangilanmoqda...</li>
    </ul>

    <h3>Uzoq muddatli kasbiy qayta tayyorlash kurslari</h3>
    <p>Turizm sohasida yangi faoliyat boshlamoqchi yoki kasbini chuqurlashtirmoqchi bo'lganlar uchun:</p>
    <ul>
        <li>Kurslar ro'yxati yangilanmoqda...</li>
    </ul>

    <h3>Sertifikat va kasbiy imkoniyatlar</h3>
    <p>Akademiya tomonidan beriladigan:</p>
    <ul>
        <li>Malaka oshirish sertifikatlari</li>
        <li>Qayta tayyorlash diplomlari</li>
    </ul>
    <p>Mazkur hujjatlar belgilangan tartibda rasmiylashtiriladi va davlat ta'lim standartlariga mos hisoblanadi.</p>
    <p>Bu hujjatlar turizm sohasida ishga joylashish, turagentlik faoliyatini boshlash, turizm yo'nalishida biznes ochish uchun huquqiy asos bo'lib xizmat qiladi.</p>
</section>
HTML;
    }

    protected function createContent(): void
    {
        // Bosh sahifa uchun kontentlar
        $content = [
            // Ustunliklar
            [
                'section' => 'advantages',
                'key' => 'item1_title',
                'value_uz' => 'Huquqiy asoslangan va xalqaro maqomga ega ta\'lim muassasasi',
                'value_ru' => 'Юридически основанное и международно признанное учебное заведение',
                'value_en' => 'Legally established and internationally recognized institution',
            ],
            [
                'section' => 'advantages',
                'key' => 'item1_desc',
                'value_uz' => 'Xalqaro Turizm Akademiyasi Samarqand kampusi O\'zbekiston Respublikasi Prezidentining qarori asosida tashkil etilgan bo\'lib, BMT Turizm Tashkiloti (UN Tourism) shafeligida faoliyat yuritadi.',
                'value_ru' => 'Международная академия туризма основана указом Президента Республики Узбекистан и действует под эгидой ЮНВТО.',
                'value_en' => 'International Tourism Academy is established by decree of the President and operates under UN Tourism patronage.',
            ],
            [
                'section' => 'advantages',
                'key' => 'item2_title',
                'value_uz' => 'Amaliyotga yo\'naltirilgan ta\'lim modeli',
                'value_ru' => 'Практико-ориентированная модель обучения',
                'value_en' => 'Practice-oriented education model',
            ],
            [
                'section' => 'advantages',
                'key' => 'item2_desc',
                'value_uz' => 'Ta\'lim jarayoni nazariy bilimlar bilan cheklanmaydi, balki real ish jarayonlariga mos amaliy mashg\'ulotlar, real keyslar orqali olib boriladi.',
                'value_ru' => 'Обучение не ограничивается теорией, а включает практические занятия и реальные кейсы.',
                'value_en' => 'Education is not limited to theory, but includes practical exercises and real cases.',
            ],
            [
                'section' => 'advantages',
                'key' => 'item3_title',
                'value_uz' => 'Turizm biznesi bilan to\'g\'ridan-to\'g\'ri hamkorlik',
                'value_ru' => 'Прямое сотрудничество с туристическим бизнесом',
                'value_en' => 'Direct cooperation with tourism business',
            ],
            [
                'section' => 'advantages',
                'key' => 'item3_desc',
                'value_uz' => 'Akademiya mehmonxonalar, turistik kompaniyalar va turagentliklar bilan hamkorlikda faoliyat yuritadi. Bitiruvchilar real ish joylariga tavsiya etiladi.',
                'value_ru' => 'Академия сотрудничает с гостиницами, турфирмами и турагентствами. Выпускники рекомендуются на реальные рабочие места.',
                'value_en' => 'Academy cooperates with hotels and travel companies. Graduates are recommended for real jobs.',
            ],
            [
                'section' => 'advantages',
                'key' => 'item4_title',
                'value_uz' => 'Xalqaro ekspertlar va zamonaviy metodologiyalar',
                'value_ru' => 'Международные эксперты и современные методологии',
                'value_en' => 'International experts and modern methodologies',
            ],
            [
                'section' => 'advantages',
                'key' => 'item4_desc',
                'value_uz' => 'Ta\'lim jarayoni xorijiy ekspertlar va amaliyotchi mutaxassislar ishtirokida olib boriladi.',
                'value_ru' => 'Обучение проводится с участием иностранных экспертов и практикующих специалистов.',
                'value_en' => 'Education is conducted with foreign experts and practicing specialists.',
            ],
            [
                'section' => 'advantages',
                'key' => 'item5_title',
                'value_uz' => 'Global munosabatlar',
                'value_ru' => 'Глобальные отношения',
                'value_en' => 'Global relations',
            ],
            [
                'section' => 'advantages',
                'key' => 'item5_desc',
                'value_uz' => 'Buyuk Ipak yo\'lining markazida joylashganimizni hisobga olib, biz Sharq va G\'arbning ilg\'or bilim va tajribalarini o\'zimizga moslashtirib foydalanamiz.',
                'value_ru' => 'Находясь в центре Великого Шелкового пути, мы адаптируем лучшие знания и опыт Востока и Запада.',
                'value_en' => 'Located at the center of the Great Silk Road, we adapt the best knowledge of East and West.',
            ],
            [
                'section' => 'advantages',
                'key' => 'item6_title',
                'value_uz' => 'Eng zamonaviy moddiy texnik baza',
                'value_ru' => 'Современная материально-техническая база',
                'value_en' => 'State-of-the-art facilities',
            ],
            [
                'section' => 'advantages',
                'key' => 'item6_desc',
                'value_uz' => 'Akademiya eng zamonaviy kompyuter texnologiyalari va kasbga oid simulyatsion xonalar bilan jihozlangan.',
                'value_ru' => 'Академия оснащена современными компьютерными технологиями и симуляционными кабинетами.',
                'value_en' => 'Academy is equipped with modern computer technologies and simulation rooms.',
            ],
        ];

        foreach ($content as $item) {
            CmsContent::updateOrCreate(
                ['section' => $item['section'], 'key' => $item['key']],
                $item
            );
        }

        $this->command->info("Ustunliklar kontenti yaratildi");
    }
}
