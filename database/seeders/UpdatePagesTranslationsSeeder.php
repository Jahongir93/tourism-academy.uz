<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class UpdatePagesTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            // 1. Akademiya haqida
            'akademiya-haqida' => [
                'content_ru' => '<section class="about-section">
    <h2>Международная Академия Туризма — Самаркандский кампус</h2>
    <p>Самаркандский кампус Международной Академии Туризма — это государственное образовательное учреждение, созданное для развития туристической отрасли в Республике Узбекистан, подготовки кадров в соответствии с международными стандартами и повышения квалификации специалистов отрасли.</p>

    <h3>Правовые основы создания Академии</h3>
    <p>Самаркандский кампус Международной Академии Туризма создан на основании <strong>Постановления Президента Республики Узбекистан № ПП-350 "Об организации деятельности Самаркандского кампуса Международной академии туризма под эгидой Всемирной туристской организации"</strong>.</p>

    <h3>Правовой статус Академии</h3>
    <ul>
        <li>Академия является юридическим лицом в форме государственного учреждения.</li>
        <li>Учредителем Академии является Международный университет туризма и культурного наследия «Шёлковый путь» при Комитете по туризму Республики Узбекистан.</li>
    </ul>

    <h3>Миссия и ценности</h3>
    <p>Миссия Самаркандского кампуса Международной Академии Туризма — формирование поколения квалифицированных специалистов с практическими знаниями и навыками, соответствующими международным стандартам, для развития индустрии туризма и гостеприимства.</p>

    <p>В образовательном процессе Академия внедряет современную модель переподготовки и повышения квалификации для специалистов туристической отрасли, объединяя рекомендации UN Tourism (бывшая UNWTO), передовой международный опыт и реальную бизнес-практику.</p>

    <h3>Наши ценности</h3>
    <div class="values-list">
        <div class="value-item">
            <strong>Практический результат</strong> — каждый курс даёт реальные рабочие навыки
        </div>
        <div class="value-item">
            <strong>Качество и стандарты</strong> — обучение на основе государственных и международных требований
        </div>
        <div class="value-item">
            <strong>Ответственность</strong> — обязательства перед работодателем и слушателем
        </div>
        <div class="value-item">
            <strong>Партнёрство</strong> — интеграция образования и реального сектора
        </div>
        <div class="value-item">
            <strong>Развитие</strong> — постоянное обновление и современный подход
        </div>
    </div>
</section>',
                'content_en' => '<section class="about-section">
    <h2>International Tourism Academy — Samarkand Campus</h2>
    <p>The Samarkand Campus of the International Tourism Academy is a state educational institution established to develop the tourism industry in the Republic of Uzbekistan, train personnel in accordance with international standards, and enhance the qualifications of industry specialists.</p>

    <h3>Legal Basis for Establishing the Academy</h3>
    <p>The Samarkand Campus of the International Tourism Academy was established based on the <strong>Decree of the President of the Republic of Uzbekistan No. PP-350 "On organizing the activities of the Samarkand Campus of the International Tourism Academy under the auspices of the World Tourism Organization"</strong>.</p>

    <h3>Legal Status of the Academy</h3>
    <ul>
        <li>The Academy is a legal entity in the form of a state institution.</li>
        <li>The founder of the Academy is the "Silk Road" International University of Tourism and Cultural Heritage under the Tourism Committee of the Republic of Uzbekistan.</li>
    </ul>

    <h3>Mission and Values</h3>
    <p>The mission of the Samarkand Campus of the International Tourism Academy is to develop a generation of qualified specialists with practical knowledge and skills meeting international standards to serve the tourism and hospitality industry.</p>

    <p>The Academy implements a modern model of retraining and professional development for tourism industry specialists, combining UN Tourism (formerly UNWTO) recommendations, advanced international experience, and real business practice in its educational process.</p>

    <h3>Our Values</h3>
    <div class="values-list">
        <div class="value-item">
            <strong>Practical Results</strong> — every course provides real working skills
        </div>
        <div class="value-item">
            <strong>Quality and Standards</strong> — education based on state and international requirements
        </div>
        <div class="value-item">
            <strong>Responsibility</strong> — accountability to employers and students
        </div>
        <div class="value-item">
            <strong>Partnership</strong> — integration of education and the real sector
        </div>
        <div class="value-item">
            <strong>Development</strong> — continuous improvement and modern approach
        </div>
    </div>
</section>',
            ],

            // 2. Tarkibiy tuzilma
            'tarkibiy-tuzilma' => [
                'content_ru' => '<section class="structure-section">
    <h2>Организационная структура Академии</h2>
    <p>Международная Академия Туризма имеет современную организационную структуру, обеспечивающую эффективную работу учебного процесса и административного управления.</p>

    <h3>Основные подразделения</h3>
    <ul>
        <li><strong>Ректорат</strong> — общее руководство деятельностью Академии</li>
        <li><strong>Учебный отдел</strong> — организация и координация образовательного процесса</li>
        <li><strong>Отдел международного сотрудничества</strong> — развитие международных связей и партнёрств</li>
        <li><strong>Отдел информационных технологий</strong> — техническое обеспечение и цифровизация</li>
        <li><strong>Финансово-экономический отдел</strong> — финансовое планирование и учёт</li>
        <li><strong>Отдел кадров</strong> — работа с персоналом и документооборот</li>
        <li><strong>Библиотека и информационный центр</strong> — обеспечение учебными материалами</li>
    </ul>

    <h3>Учебные направления</h3>
    <ul>
        <li>Гостиничный бизнес и гостеприимство</li>
        <li>Туроператорская деятельность</li>
        <li>Ресторанный сервис</li>
        <li>Управление туризмом</li>
    </ul>
</section>',
                'content_en' => '<section class="structure-section">
    <h2>Academy Organizational Structure</h2>
    <p>The International Tourism Academy has a modern organizational structure that ensures effective operation of the educational process and administrative management.</p>

    <h3>Main Departments</h3>
    <ul>
        <li><strong>Rectorate</strong> — general management of Academy activities</li>
        <li><strong>Academic Department</strong> — organization and coordination of the educational process</li>
        <li><strong>International Cooperation Department</strong> — development of international relations and partnerships</li>
        <li><strong>IT Department</strong> — technical support and digitalization</li>
        <li><strong>Finance and Economics Department</strong> — financial planning and accounting</li>
        <li><strong>Human Resources Department</strong> — personnel management and documentation</li>
        <li><strong>Library and Information Center</strong> — provision of educational materials</li>
    </ul>

    <h3>Educational Directions</h3>
    <ul>
        <li>Hotel Business and Hospitality</li>
        <li>Tour Operator Activities</li>
        <li>Restaurant Service</li>
        <li>Tourism Management</li>
    </ul>
</section>',
            ],

            // 3. Rahbariyat
            'rahbariyat' => [
                'content_ru' => '<section class="leadership-section">
    <h2>Руководство Академии</h2>
    <p>Академией руководит опытная команда специалистов в области образования и туризма.</p>

    <h3>Ректор</h3>
    <p>Ректор Академии осуществляет общее руководство деятельностью учреждения, определяет стратегическое направление развития и представляет Академию в государственных органах и международных организациях.</p>

    <h3>Проректоры</h3>
    <ul>
        <li><strong>Проректор по учебной работе</strong> — организация и контроль качества образовательного процесса</li>
        <li><strong>Проректор по международному сотрудничеству</strong> — развитие международных связей и партнёрств</li>
        <li><strong>Проректор по научной работе</strong> — координация научно-исследовательской деятельности</li>
    </ul>

    <h3>Учёный совет</h3>
    <p>Учёный совет — коллегиальный орган управления, рассматривающий важные вопросы учебной, научной и воспитательной деятельности Академии.</p>
</section>',
                'content_en' => '<section class="leadership-section">
    <h2>Academy Leadership</h2>
    <p>The Academy is led by an experienced team of specialists in education and tourism.</p>

    <h3>Rector</h3>
    <p>The Rector of the Academy provides overall leadership of the institution\'s activities, determines the strategic direction of development, and represents the Academy in government bodies and international organizations.</p>

    <h3>Vice-Rectors</h3>
    <ul>
        <li><strong>Vice-Rector for Academic Affairs</strong> — organization and quality control of the educational process</li>
        <li><strong>Vice-Rector for International Cooperation</strong> — development of international relations and partnerships</li>
        <li><strong>Vice-Rector for Research</strong> — coordination of scientific research activities</li>
    </ul>

    <h3>Academic Council</h3>
    <p>The Academic Council is a collegial governing body that considers important issues of the Academy\'s educational, scientific, and instructional activities.</p>
</section>',
            ],

            // 4. Bo'limlar
            'bolimlar' => [
                'content_ru' => '<section class="departments-section">
    <h2>Отделы и подразделения</h2>

    <h3>1. Учебный отдел</h3>
    <p>Организация учебного процесса, составление расписания, контроль успеваемости слушателей, выдача сертификатов и дипломов.</p>

    <h3>2. Отдел международного сотрудничества</h3>
    <p>Развитие международных связей, организация стажировок за рубежом, привлечение иностранных экспертов и преподавателей.</p>

    <h3>3. Отдел информационных технологий</h3>
    <p>Техническое обеспечение учебного процесса, администрирование информационных систем, поддержка онлайн-обучения.</p>

    <h3>4. Финансово-экономический отдел</h3>
    <p>Финансовое планирование, бухгалтерский учёт, работа с договорами и оплатой обучения.</p>

    <h3>5. Отдел кадров</h3>
    <p>Подбор персонала, ведение кадрового делопроизводства, организация повышения квалификации сотрудников.</p>

    <h3>6. Научно-методический отдел</h3>
    <p>Разработка учебных программ, методическое обеспечение, организация научных исследований.</p>

    <h3>7. Маркетинг и коммуникации</h3>
    <p>Продвижение образовательных программ, работа со СМИ, организация мероприятий.</p>

    <h3>8. Административно-хозяйственная часть</h3>
    <p>Обеспечение материально-технической базы, содержание зданий и территории.</p>
</section>',
                'content_en' => '<section class="departments-section">
    <h2>Departments and Divisions</h2>

    <h3>1. Academic Department</h3>
    <p>Organization of the educational process, scheduling, monitoring student progress, issuing certificates and diplomas.</p>

    <h3>2. International Cooperation Department</h3>
    <p>Development of international relations, organization of internships abroad, attracting foreign experts and teachers.</p>

    <h3>3. IT Department</h3>
    <p>Technical support of the educational process, information systems administration, online learning support.</p>

    <h3>4. Finance and Economics Department</h3>
    <p>Financial planning, accounting, working with contracts and tuition payments.</p>

    <h3>5. Human Resources Department</h3>
    <p>Staff recruitment, personnel record keeping, organization of employee professional development.</p>

    <h3>6. Scientific and Methodological Department</h3>
    <p>Development of curricula, methodological support, organization of scientific research.</p>

    <h3>7. Marketing and Communications</h3>
    <p>Promotion of educational programs, media relations, event organization.</p>

    <h3>8. Administrative and Facilities Department</h3>
    <p>Provision of material and technical resources, maintenance of buildings and grounds.</p>
</section>',
            ],

            // 5. O'qituvchilar
            'oqituvchilar' => [
                'content_ru' => '<section class="teachers-section">
    <h2>Преподавательский состав</h2>
    <p>В Академии работают высококвалифицированные преподаватели с богатым опытом работы в сфере туризма и гостеприимства.</p>

    <h3>Квалификация преподавателей</h3>
    <ul>
        <li>Кандидаты и доктора наук в области туризма и экономики</li>
        <li>Практики с многолетним опытом работы в индустрии</li>
        <li>Международные эксперты и консультанты</li>
        <li>Сертифицированные тренеры по программам UN Tourism</li>
    </ul>

    <h3>Области экспертизы</h3>
    <ul>
        <li>Гостиничный менеджмент</li>
        <li>Туроператорская деятельность</li>
        <li>Ресторанный бизнес</li>
        <li>Маркетинг в туризме</li>
        <li>Цифровые технологии в туризме</li>
        <li>Иностранные языки</li>
    </ul>

    <h3>Постоянное развитие</h3>
    <p>Наши преподаватели регулярно проходят повышение квалификации, участвуют в международных конференциях и семинарах.</p>
</section>',
                'content_en' => '<section class="teachers-section">
    <h2>Teaching Staff</h2>
    <p>The Academy employs highly qualified teachers with extensive experience in tourism and hospitality.</p>

    <h3>Teacher Qualifications</h3>
    <ul>
        <li>PhD holders and Doctors of Science in tourism and economics</li>
        <li>Practitioners with many years of industry experience</li>
        <li>International experts and consultants</li>
        <li>Certified trainers in UN Tourism programs</li>
    </ul>

    <h3>Areas of Expertise</h3>
    <ul>
        <li>Hotel Management</li>
        <li>Tour Operator Activities</li>
        <li>Restaurant Business</li>
        <li>Tourism Marketing</li>
        <li>Digital Technologies in Tourism</li>
        <li>Foreign Languages</li>
    </ul>

    <h3>Continuous Development</h3>
    <p>Our teachers regularly undergo professional development, participate in international conferences and seminars.</p>
</section>',
            ],

            // 6. Xalqaro hamkorlik
            'xalqaro-hamkorlik' => [
                'content_ru' => '<section class="international-section">
    <h2>Международное сотрудничество</h2>
    <p>Академия активно развивает международные связи с ведущими образовательными учреждениями и организациями в сфере туризма.</p>

    <h3>Партнёры</h3>
    <ul>
        <li><strong>UN Tourism (UNWTO)</strong> — Всемирная туристская организация ООН</li>
        <li>Ведущие университеты Европы, Азии и Америки</li>
        <li>Международные гостиничные сети</li>
        <li>Туристические ассоциации</li>
    </ul>

    <h3>Направления сотрудничества</h3>
    <ul>
        <li>Совместные образовательные программы</li>
        <li>Обмен студентами и преподавателями</li>
        <li>Стажировки за рубежом</li>
        <li>Совместные научные исследования</li>
        <li>Международные конференции и семинары</li>
    </ul>

    <h3>Международная аккредитация</h3>
    <p>Академия работает над получением международной аккредитации своих образовательных программ, что обеспечит признание дипломов за рубежом.</p>

    <h3>Программы обмена</h3>
    <p>Студенты и преподаватели имеют возможность участвовать в программах академического обмена с зарубежными вузами-партнёрами.</p>
</section>',
                'content_en' => '<section class="international-section">
    <h2>International Cooperation</h2>
    <p>The Academy actively develops international relations with leading educational institutions and organizations in the tourism sector.</p>

    <h3>Partners</h3>
    <ul>
        <li><strong>UN Tourism (UNWTO)</strong> — United Nations World Tourism Organization</li>
        <li>Leading universities in Europe, Asia, and America</li>
        <li>International hotel chains</li>
        <li>Tourism associations</li>
    </ul>

    <h3>Areas of Cooperation</h3>
    <ul>
        <li>Joint educational programs</li>
        <li>Student and faculty exchange</li>
        <li>Internships abroad</li>
        <li>Joint research projects</li>
        <li>International conferences and seminars</li>
    </ul>

    <h3>International Accreditation</h3>
    <p>The Academy is working towards international accreditation of its educational programs, which will ensure recognition of diplomas abroad.</p>

    <h3>Exchange Programs</h3>
    <p>Students and teachers have the opportunity to participate in academic exchange programs with partner universities abroad.</p>
</section>',
            ],

            // 7. O'quv dasturlari
            'oquv-dasturlari' => [
                'content_ru' => '<section class="programs-section">
    <h2>Учебные программы</h2>
    <p>Академия предлагает разнообразные образовательные программы для специалистов туристической отрасли.</p>

    <h3>Программы переподготовки</h3>
    <ul>
        <li>Менеджмент гостиничного бизнеса (6 месяцев)</li>
        <li>Туроператорская деятельность (4 месяца)</li>
        <li>Ресторанный сервис (4 месяца)</li>
        <li>Экскурсионная деятельность (3 месяца)</li>
    </ul>

    <h3>Курсы повышения квалификации</h3>
    <ul>
        <li>Управление качеством в гостиничном бизнесе (72 часа)</li>
        <li>Цифровой маркетинг в туризме (40 часов)</li>
        <li>Стандарты обслуживания гостей (36 часов)</li>
        <li>Иностранный язык для туризма (120 часов)</li>
    </ul>

    <h3>Краткосрочные курсы</h3>
    <ul>
        <li>Искусство гостеприимства</li>
        <li>Работа с жалобами гостей</li>
        <li>Этикет и деловой протокол</li>
        <li>Основы сомелье</li>
    </ul>

    <h3>Форма обучения</h3>
    <p>Очная, заочная и дистанционная формы обучения. Гибкий график для работающих специалистов.</p>
</section>',
                'content_en' => '<section class="programs-section">
    <h2>Educational Programs</h2>
    <p>The Academy offers various educational programs for tourism industry specialists.</p>

    <h3>Retraining Programs</h3>
    <ul>
        <li>Hotel Business Management (6 months)</li>
        <li>Tour Operator Activities (4 months)</li>
        <li>Restaurant Service (4 months)</li>
        <li>Tour Guide Activities (3 months)</li>
    </ul>

    <h3>Professional Development Courses</h3>
    <ul>
        <li>Quality Management in Hotel Business (72 hours)</li>
        <li>Digital Marketing in Tourism (40 hours)</li>
        <li>Guest Service Standards (36 hours)</li>
        <li>Foreign Language for Tourism (120 hours)</li>
    </ul>

    <h3>Short-term Courses</h3>
    <ul>
        <li>The Art of Hospitality</li>
        <li>Handling Guest Complaints</li>
        <li>Etiquette and Business Protocol</li>
        <li>Sommelier Basics</li>
    </ul>

    <h3>Study Format</h3>
    <p>Full-time, part-time, and distance learning options. Flexible schedule for working professionals.</p>
</section>',
            ],

            // 8. Axborot resurs markazi
            'kutubxona' => [
                'content_ru' => '<section class="library-section">
    <h2>Информационный ресурсный центр</h2>
    <p>Библиотека и информационный центр Академии обеспечивает слушателей и преподавателей современными учебными и научными ресурсами.</p>

    <h3>Ресурсы</h3>
    <ul>
        <li>Учебники и учебные пособия по туризму и гостеприимству</li>
        <li>Научные журналы и периодические издания</li>
        <li>Электронные базы данных</li>
        <li>Видеоматериалы и мультимедийные ресурсы</li>
        <li>Доступ к международным электронным библиотекам</li>
    </ul>

    <h3>Услуги</h3>
    <ul>
        <li>Выдача книг и учебных материалов</li>
        <li>Читальный зал с компьютерами</li>
        <li>Консультации библиографа</li>
        <li>Межбиблиотечный абонемент</li>
        <li>Копирование и сканирование документов</li>
    </ul>

    <h3>Режим работы</h3>
    <p>Понедельник — Пятница: 9:00 — 18:00<br>Суббота: 9:00 — 14:00</p>
</section>',
                'content_en' => '<section class="library-section">
    <h2>Information Resource Center</h2>
    <p>The Academy\'s Library and Information Center provides students and teachers with modern educational and scientific resources.</p>

    <h3>Resources</h3>
    <ul>
        <li>Textbooks and manuals on tourism and hospitality</li>
        <li>Scientific journals and periodicals</li>
        <li>Electronic databases</li>
        <li>Video materials and multimedia resources</li>
        <li>Access to international electronic libraries</li>
    </ul>

    <h3>Services</h3>
    <ul>
        <li>Book and study material lending</li>
        <li>Reading room with computers</li>
        <li>Bibliographer consultations</li>
        <li>Interlibrary loan</li>
        <li>Document copying and scanning</li>
    </ul>

    <h3>Working Hours</h3>
    <p>Monday — Friday: 9:00 AM — 6:00 PM<br>Saturday: 9:00 AM — 2:00 PM</p>
</section>',
            ],

            // 9. Aloqa
            'aloqa' => [
                'content_ru' => '<section class="contact-section">
    <h2>Контактная информация</h2>

    <h3>Адрес</h3>
    <p>Республика Узбекистан, г. Самарканд<br>
    ул. Университетская, 17</p>

    <h3>Телефоны</h3>
    <p>Приёмная: +998 (66) 233-00-00<br>
    Учебный отдел: +998 (66) 233-00-01<br>
    Приёмная комиссия: +998 (66) 233-00-02</p>

    <h3>Электронная почта</h3>
    <p>Общие вопросы: info@ita.uz<br>
    Приём документов: admission@ita.uz</p>

    <h3>Социальные сети</h3>
    <ul>
        <li>Telegram: @ita_samarkand</li>
        <li>Instagram: @ita_samarkand</li>
        <li>Facebook: International Tourism Academy Samarkand</li>
    </ul>

    <h3>Режим работы</h3>
    <p>Понедельник — Пятница: 9:00 — 18:00<br>
    Суббота: 9:00 — 13:00<br>
    Воскресенье: выходной</p>
</section>',
                'content_en' => '<section class="contact-section">
    <h2>Contact Information</h2>

    <h3>Address</h3>
    <p>Republic of Uzbekistan, Samarkand city<br>
    17 University Street</p>

    <h3>Phone Numbers</h3>
    <p>Reception: +998 (66) 233-00-00<br>
    Academic Department: +998 (66) 233-00-01<br>
    Admissions Office: +998 (66) 233-00-02</p>

    <h3>Email</h3>
    <p>General inquiries: info@ita.uz<br>
    Admissions: admission@ita.uz</p>

    <h3>Social Media</h3>
    <ul>
        <li>Telegram: @ita_samarkand</li>
        <li>Instagram: @ita_samarkand</li>
        <li>Facebook: International Tourism Academy Samarkand</li>
    </ul>

    <h3>Working Hours</h3>
    <p>Monday — Friday: 9:00 AM — 6:00 PM<br>
    Saturday: 9:00 AM — 1:00 PM<br>
    Sunday: Closed</p>
</section>',
            ],
        ];

        foreach ($translations as $slug => $data) {
            $page = CmsPage::where('slug', $slug)->first();
            if ($page) {
                $page->update($data);
                $this->command->info("✓ {$page->title_uz} sahifasi yangilandi");
            }
        }

        $this->command->info("\n✅ Barcha sahifalar 3 tilda yangilandi!");
    }
}
