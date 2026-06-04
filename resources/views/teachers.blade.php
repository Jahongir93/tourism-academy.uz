@extends(\App\Helpers\TemplateHelper::getLayout())

@php
    use App\Models\CmsContent;
    use App\Models\Employee;

    // Get teachers page content from CMS
    $teachersContents = CmsContent::where('section', 'teachers')->get()->keyBy('key');

    // Get current language
    $lang = app()->getLocale() ?? 'uz';
    $langField = 'value_' . $lang;

    // Helper function to get content
    $getContent = function($key, $default = '') use ($teachersContents, $langField) {
        $content = $teachersContents->get($key);
        return $content ? ($content->$langField ?? $content->value_uz ?? $default) : $default;
    };

    // Helper: normalize a stored photo path to a public URL (firewall-safe)
    $resolvePhoto = function($path) {
        if (!$path) return null;
        if (str_starts_with($path, 'http')) return $path;          // external
        if (str_starts_with($path, 'assets/') || str_starts_with($path, 'images/') || str_starts_with($path, 'storage/')) {
            return asset($path);                                    // already a public path
        }
        return asset('storage/' . ltrim($path, '/'));               // public-disk upload
    };

    // Bio in current language with uz fallback
    $bioField = 'bio_' . $lang;

    // Merge Employee + legacy CmsContent teachers (same source as /cms/teachers, /employees/teachers)
    $employeeTeachers = Employee::where('employee_type', 'teacher')
        ->where(function($q){ $q->where('show_on_site', true)->orWhereNull('show_on_site'); })
        ->with(['employmentDetail.position'])
        ->orderBy('public_order')->orderBy('last_name')->orderBy('first_name')
        ->get()
        ->map(function($emp) use ($resolvePhoto, $bioField) {
            $empDetail = $emp->employmentDetail;
            $positionName = $empDetail && $empDetail->position ? $empDetail->position->name : ($emp->position ?? '');
            return (object)[
                'id' => 'emp_' . $emp->id,
                'name' => $emp->full_name,
                'position' => $positionName,
                'bio' => $emp->{$bioField} ?: ($emp->bio_uz ?? ''),
                'image' => $resolvePhoto($emp->photo_url),
            ];
        });

    $legacyCmsTeachers = CmsContent::where('section', 'teachers_list')->orderBy('order')->get()
        ->map(function($c) use ($langField) {
            $data = json_decode($c->$langField ?? $c->value_uz, true) ?? [];
            return (object)[
                'id' => 'cms_' . $c->id,
                'name' => $data['name'] ?? '-',
                'position' => $data['position'] ?? '',
                'bio' => $data['bio'] ?? '',
                'image' => $data['image'] ?? null,
            ];
        });

    $cmsTeachers = $employeeTeachers->concat($legacyCmsTeachers);

    // Helper to get teacher data (already normalized)
    $getTeacherData = function($teacher) {
        return [
            'name' => $teacher->name,
            'position' => $teacher->position,
            'bio' => $teacher->bio,
            'image' => $teacher->image,
        ];
    };

    // Static teachers fallback
    $staticTeachers = [
        ['name' => 'Odilov Akmaljon', 'position' => 'Kafedra mudiri', 'image' => 'Odilov Akmaljon.JPG', 'bio_uz' => "Iqtisod fanlari bo'yicha falsafa doktori (PhD). London School of Business and Finance, Buyuk Britaniyada Business English o'qigan.", 'bio_en' => 'PhD in Economics. Studied Business English at London School of Business and Finance, UK.', 'bio_ru' => 'Кандидат экономических наук (PhD). Изучал деловой английский в London School of Business and Finance, Великобритания.'],
        ['name' => 'Klykov Artem', 'position' => 'PhD, Dotsent', 'image' => 'Klykov Artem.JPG', 'bio_uz' => "Turizm kafedrasi shtatlı o'qituvchisi (PhD). Shveytsariyaning Les Roches universitetida magistratura dasturini tugatgan.", 'bio_en' => 'Tourism department staff teacher (PhD). Completed masters program at Les Roches University, Switzerland.', 'bio_ru' => 'Штатный преподаватель кафедры туризма (PhD). Закончил магистратуру в университете Les Roches, Швейцария.'],
        ['name' => 'Xusen Ibragimov', 'position' => 'PhD, Dotsent', 'image' => 'Xusen Ibragimov.JPG', 'bio_uz' => "Turizm kafedrasi shtatlı o'qituvchisi (PhD). Ispaniyaning Alikante universitetida PhD dissertatsiyasini himoya qilgan.", 'bio_en' => 'Tourism department staff teacher (PhD). Defended PhD dissertation at University of Alicante, Spain.', 'bio_ru' => 'Штатный преподаватель кафедры туризма (PhD). Защитил диссертацию в университете Аликанте, Испания.'],
        ['name' => 'Nilufar Rakhimova', 'position' => 'PhD, Katta o\'qituvchi', 'image' => 'Nilufar Rakhimova.JPG', 'bio_uz' => "Westminster universitetining Toshkent kampusida PhD dissertatsiyasini muvaffaqiyatli himoya qilgan.", 'bio_en' => 'Successfully defended PhD dissertation at Westminster University Tashkent campus.', 'bio_ru' => 'Успешно защитила диссертацию PhD в Ташкентском кампусе Вестминстерского университета.'],
        ['name' => 'Abdurasul Akhmadjonov', 'position' => 'Katta o\'qituvchi', 'image' => 'Abdurasul Akhmadjonov.JPG', 'bio_uz' => "Turizm va mehmondo'stlik sohasida 10 yillik tajribaga ega mutaxassis.", 'bio_en' => 'Specialist with 10 years of experience in tourism and hospitality.', 'bio_ru' => 'Специалист с 10-летним опытом в сфере туризма и гостеприимства.'],
        ['name' => 'Botir Rakhmatullaev', 'position' => 'Dotsent', 'image' => 'Botir Rakhmatullaev.JPG', 'bio_uz' => "Iqtisodiyot fanlari nomzodi. Turizm iqtisodiyoti va marketing bo'yicha mutaxassis.", 'bio_en' => 'Candidate of Economic Sciences. Specialist in tourism economics and marketing.', 'bio_ru' => 'Кандидат экономических наук. Специалист по экономике туризма и маркетингу.'],
        ['name' => 'Charos Makhmadieva', 'position' => 'O\'qituvchi', 'image' => 'Charos Makhmadieva.JPG', 'bio_uz' => "Mehmondo'stlik menejment bo'yicha magistr. Xalqaro mehmonxonalarda amaliyot o'tagan.", 'bio_en' => 'Master in Hospitality Management. Completed internship at international hotels.', 'bio_ru' => 'Магистр по менеджменту гостеприимства. Проходила практику в международных отелях.'],
        ['name' => 'Dilnoza Muxidinova', 'position' => 'O\'qituvchi', 'image' => 'Dilnoza Muxidinova.JPG', 'bio_uz' => "Turizm va xalqaro munosabatlar bo'yicha mutaxassis. Bir nechta xalqaro konferensiyalarda ishtirok etgan.", 'bio_en' => 'Specialist in tourism and international relations. Participated in several international conferences.', 'bio_ru' => 'Специалист по туризму и международным отношениям. Участвовала в нескольких международных конференциях.'],
    ];
@endphp

@section('title', $getContent('teachers_page_title', "O'qituvchilar") . ' - Tourism Academy')
@section('description', $getContent('teachers_page_description', "Xalqaro tajribaga ega malakali o'qituvchilar jamoasi"))

@section('content')
<style>
    /* Hero Section */
    .teachers-hero {
        min-height: 55vh;
        padding-top: 140px;
        padding-bottom: 80px;
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 50%, #1b263b 100%);
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .teachers-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(ellipse at 20% 30%, rgba(59, 130, 246, 0.2) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 70%, rgba(147, 51, 234, 0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 50% 50%, rgba(16, 185, 129, 0.1) 0%, transparent 60%);
        pointer-events: none;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        padding: 12px 28px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.35);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .teachers-hero h1 {
        color: #ffffff;
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 2;
        line-height: 1.2;
    }

    .teachers-hero p {
        color: #cbd5e1;
        font-size: 1.2rem;
        max-width: 700px;
        position: relative;
        z-index: 2;
        line-height: 1.7;
    }

    .hero-breadcrumb {
        margin-top: 30px;
        position: relative;
        z-index: 2;
    }

    .hero-breadcrumb a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: color 0.3s;
    }

    .hero-breadcrumb a:hover {
        color: #3b82f6;
    }

    .hero-breadcrumb span {
        color: rgba(255, 255, 255, 0.5);
        margin: 0 12px;
    }

    .hero-breadcrumb .current {
        color: #3b82f6;
        font-weight: 600;
    }

    /* Stats Section */
    .stats-section {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        padding: 60px 0;
        position: relative;
    }

    .stat-card-mini {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        backdrop-filter: blur(10px);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #cbd5e1;
        font-size: 0.95rem;
    }

    /* Teachers Section */
    .teachers-section {
        padding: 80px 0;
        background: #f8fafc;
    }

    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .section-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1e3a5f;
        margin-bottom: 15px;
    }

    .section-subtitle {
        color: #64748b;
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* Teacher Cards */
    .teacher-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        height: 100%;
    }

    .teacher-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.15);
    }

    .teacher-image {
        height: 280px;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .teacher-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
        transition: transform 0.5s ease;
    }

    .teacher-card:hover .teacher-image img {
        transform: scale(1.1);
    }

    .teacher-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
    }

    .teacher-info {
        padding: 25px;
        text-align: center;
    }

    .teacher-info h5 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 8px;
    }

    .teacher-info .position {
        color: #3b82f6;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .teacher-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 10px 24px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .teacher-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.35);
        color: white;
    }

    /* Modal Styles */
    .teacher-modal .modal-content {
        border-radius: 20px;
        border: none;
        overflow: hidden;
    }

    .teacher-modal .modal-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        color: white;
        padding: 25px 30px;
        border: none;
    }

    .teacher-modal .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
    }

    .teacher-modal .modal-position {
        color: #3b82f6;
        font-size: 0.95rem;
        margin-top: 5px;
    }

    .teacher-modal .modal-body {
        padding: 30px;
    }

    .teacher-modal .modal-body p {
        color: #64748b;
        line-height: 1.8;
        font-size: 1rem;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(ellipse at 30% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 70% 50%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
    }

    .cta-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .cta-title {
        color: white;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 20px;
    }

    .cta-text {
        color: #cbd5e1;
        font-size: 1.15rem;
        max-width: 600px;
        margin: 0 auto 35px;
    }

    .cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 18px 40px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        text-decoration: none;
        box-shadow: 0 15px 40px rgba(59, 130, 246, 0.4);
        transition: all 0.3s ease;
    }

    .cta-btn:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.5);
        color: white;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .teachers-hero h1 {
            font-size: 2.3rem;
        }
        .section-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 768px) {
        .teachers-hero {
            padding-top: 120px;
            min-height: 45vh;
        }
        .teachers-hero h1 {
            font-size: 1.8rem;
        }
        .teachers-hero p {
            font-size: 1rem;
        }
        .stat-value {
            font-size: 2rem;
        }
        .cta-title {
            font-size: 1.8rem;
        }
        .teacher-image {
            height: 220px;
        }
    }
</style>

<!-- Hero Section -->
<section class="teachers-hero">
    <div class="container">
        <div data-aos="fade-up">
            <div class="hero-badge">
                <i class="fas fa-users"></i>
                {{ $getContent('teachers_hero_badge', 'BIZNING JAMOA') }}
            </div>
            <h1>{{ $getContent('teachers_hero_title', "Bizning o'qituvchilar") }}</h1>
            <p>{{ $getContent('teachers_hero_subtitle', "Xalqaro tajribaga ega malakali mutaxassislar jamoasi. Har bir o'qituvchi o'z sohasining yetakchi mutaxassisi.") }}</p>
            <div class="hero-breadcrumb">
                <a href="{{ route('home') }}">{{ $getContent('teachers_breadcrumb_home', 'Bosh sahifa') }}</a>
                <span>/</span>
                <span class="current">{{ $getContent('teachers_breadcrumb_teachers', "O'qituvchilar") }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card-mini">
                    <div class="stat-value">{{ $getContent('teachers_stat1_value', '50+') }}</div>
                    <div class="stat-label">{{ $getContent('teachers_stat1_label', "O'qituvchilar") }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card-mini">
                    <div class="stat-value">{{ $getContent('teachers_stat2_value', '15+') }}</div>
                    <div class="stat-label">{{ $getContent('teachers_stat2_label', 'PhD darajali') }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card-mini">
                    <div class="stat-value">{{ $getContent('teachers_stat3_value', '10+') }}</div>
                    <div class="stat-label">{{ $getContent('teachers_stat3_label', 'Xalqaro tajriba') }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card-mini">
                    <div class="stat-value">{{ $getContent('teachers_stat4_value', '100+') }}</div>
                    <div class="stat-label">{{ $getContent('teachers_stat4_label', 'Ilmiy maqolalar') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Teachers Section -->
<section class="teachers-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-chalkboard-teacher"></i>
                {{ $getContent('teachers_section_badge', "O'qituvchilar") }}
            </div>
            <h2 class="section-title">{{ $getContent('teachers_section_title', 'Professional jamoa') }}</h2>
            <p class="section-subtitle">{{ $getContent('teachers_section_subtitle', "O'z sohalarida yetakchi mutaxassislar bilan tanishing") }}</p>
        </div>

        <div class="row g-4">
            @if($cmsTeachers->count() > 0)
                @foreach($cmsTeachers as $index => $teacher)
                    @php $data = $getTeacherData($teacher); @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 50 }}">
                        <div class="teacher-card">
                            <div class="teacher-image">
                                @if(!empty($data['image']))
                                    <img src="{{ str_starts_with($data['image'], 'http') ? $data['image'] : asset($data['image']) }}" alt="{{ $data['name'] ?? '' }}">
                                @else
                                    <div class="teacher-placeholder">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="teacher-info">
                                <h5>{{ $data['name'] ?? '-' }}</h5>
                                <div class="position">{{ $data['position'] ?? $getContent('default_position', "O'qituvchi") }}</div>
                                <button class="teacher-btn" data-bs-toggle="modal" data-bs-target="#teacherModal{{ $teacher->id }}">
                                    <i class="fas fa-info-circle"></i>{{ $getContent('btn_details', 'Batafsil') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Teacher Modal -->
                    <div class="modal fade teacher-modal" id="teacherModal{{ $teacher->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h5 class="modal-title">{{ $data['name'] ?? '-' }}</h5>
                                        <div class="modal-position">{{ $data['position'] ?? '' }}</div>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ $data['bio'] ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach($staticTeachers as $index => $teacher)
                    @php
                        $bioKey = 'bio_' . $lang;
                        $bio = $teacher[$bioKey] ?? $teacher['bio_uz'] ?? '';
                    @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 50 }}">
                        <div class="teacher-card">
                            <div class="teacher-image">
                                @php $imagePath = 'assets/images/teachers/' . $teacher['image']; @endphp
                                @if(file_exists(public_path($imagePath)))
                                    <img src="{{ asset($imagePath) }}" alt="{{ $teacher['name'] }}">
                                @else
                                    <div class="teacher-placeholder">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="teacher-info">
                                <h5>{{ $teacher['name'] }}</h5>
                                <div class="position">{{ $teacher['position'] ?? $getContent('default_position', "O'qituvchi") }}</div>
                                <button class="teacher-btn" data-bs-toggle="modal" data-bs-target="#staticTeacherModal{{ $index }}">
                                    <i class="fas fa-info-circle"></i>{{ $getContent('btn_details', 'Batafsil') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Static Teacher Modal -->
                    <div class="modal fade teacher-modal" id="staticTeacherModal{{ $index }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h5 class="modal-title">{{ $teacher['name'] }}</h5>
                                        <div class="modal-position">{{ $teacher['position'] ?? '' }}</div>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ $bio }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content" data-aos="fade-up">
            <h2 class="cta-title">{{ $getContent('teachers_cta_title', "Jamoamizga qo'shiling!") }}</h2>
            <p class="cta-text">{{ $getContent('teachers_cta_text', "Professional jamoa bilan birga ishlang va o'z bilimlaringizni ulashing.") }}</p>
            <a href="{{ route('contact') }}" class="cta-btn">
                <i class="fas fa-paper-plane"></i>
                {{ $getContent('teachers_cta_button', "Bog'lanish") }}
            </a>
        </div>
    </div>
</section>
@endsection
