@extends('layouts.dashboard-new')

@section('title', "O'qituvchilar sahifasi boshqaruvi - CMS")
@section('page-title', "O'qituvchilar sahifasi")

@section('styles')
<style>
    .content-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        transition: all 0.3s;
        margin-bottom: 24px;
    }
    .content-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .section-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: white;
        padding: 16px 20px;
        border-radius: 12px 12px 0 0;
        margin: -1px -1px 0 -1px;
    }
    .section-header.hero { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    .section-header.teachers { background: linear-gradient(135deg, #065f46 0%, #047857 100%); }
    .section-header.stats { background: linear-gradient(135deg, #7c2d12 0%, #c2410c 100%); }
    .stat-row {
        background: #f9fafb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        border-left: 3px solid #c2410c;
    }
    .stat-row.auto {
        border-left-color: #10b981;
        background: #ecfdf5;
    }
    .stat-row-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .stat-auto-badge {
        background: #10b981;
        color: white;
        font-size: 0.7rem;
        padding: 3px 10px;
        border-radius: 50px;
        font-weight: 600;
    }
    .section-body {
        padding: 24px;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #6b7280;
        padding: 12px 20px;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #4f46e5;
        border-bottom: 2px solid #4f46e5;
        background: transparent;
    }
    .teacher-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s;
    }
    .teacher-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .teacher-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background: linear-gradient(135deg, #c4f3d5 0%, #a7f3d0 100%);
    }
    .teacher-placeholder {
        width: 100%;
        height: 180px;
        background: linear-gradient(135deg, #c4f3d5 0%, #a7f3d0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        color: #065f46;
    }
</style>
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">O'qituvchilar sahifasi kontentini boshqaring</p>
        </div>
        <a href="{{ route('teachers') }}" target="_blank" class="btn btn-outline-primary">
            <i class="fas fa-external-link-alt me-2"></i>Sahifani ko'rish
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Hero Section -->
    <div class="content-card">
        <div class="section-header hero">
            <h5 class="mb-0"><i class="fas fa-heading me-2"></i>Hero bo'limi</h5>
        </div>
        <div class="section-body">
            <form action="{{ route('cms.teachers.update') }}" method="POST">
                @csrf

                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#hero-uz">O'zbekcha</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#hero-en">English</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#hero-ru">Русский</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Uzbek -->
                    <div class="tab-pane fade show active" id="hero-uz">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Badge matni (UZ)</label>
                                <input type="text" name="teachers_hero_badge[uz]" class="form-control"
                                       value="{{ $contents->where('key', 'teachers_hero_badge')->first()->value_uz ?? 'BIZNING JAMOA' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sarlavha (UZ)</label>
                                <input type="text" name="teachers_hero_title[uz]" class="form-control"
                                       value="{{ $contents->where('key', 'teachers_hero_title')->first()->value_uz ?? "Bizning O'qituvchilar" }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tavsif (UZ)</label>
                                <textarea name="teachers_hero_subtitle[uz]" class="form-control" rows="2">{{ $contents->where('key', 'teachers_hero_subtitle')->first()->value_uz ?? 'Xalqaro tajribaga ega malakali mutaxassislar jamoasi' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- English -->
                    <div class="tab-pane fade" id="hero-en">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Badge text (EN)</label>
                                <input type="text" name="teachers_hero_badge[en]" class="form-control"
                                       value="{{ $contents->where('key', 'teachers_hero_badge')->first()->value_en ?? 'OUR TEAM' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Title (EN)</label>
                                <input type="text" name="teachers_hero_title[en]" class="form-control"
                                       value="{{ $contents->where('key', 'teachers_hero_title')->first()->value_en ?? 'Our Teachers' }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description (EN)</label>
                                <textarea name="teachers_hero_subtitle[en]" class="form-control" rows="2">{{ $contents->where('key', 'teachers_hero_subtitle')->first()->value_en ?? 'A team of qualified specialists with international experience' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Russian -->
                    <div class="tab-pane fade" id="hero-ru">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Текст бейджа (RU)</label>
                                <input type="text" name="teachers_hero_badge[ru]" class="form-control"
                                       value="{{ $contents->where('key', 'teachers_hero_badge')->first()->value_ru ?? 'НАША КОМАНДА' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Заголовок (RU)</label>
                                <input type="text" name="teachers_hero_title[ru]" class="form-control"
                                       value="{{ $contents->where('key', 'teachers_hero_title')->first()->value_ru ?? 'Наши Преподаватели' }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Описание (RU)</label>
                                <textarea name="teachers_hero_subtitle[ru]" class="form-control" rows="2">{{ $contents->where('key', 'teachers_hero_subtitle')->first()->value_ru ?? 'Команда квалифицированных специалистов с международным опытом' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="content-card">
        <div class="section-header stats">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistika bo'limi (4 ta karta)</h5>
        </div>
        <div class="section-body">
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="fas fa-info-circle me-2 fs-5"></i>
                <div>
                    Bu yerda 4 ta statistika kartasining qiymatlari (50+, 15+, 10+, 100+) va yorliqlarini qo'lda kiritasiz.
                    Saqlaganingizdan keyin <a href="{{ route('teachers') }}" target="_blank" class="alert-link">o'qituvchilar sahifasida</a> darhol ko'rinadi.
                </div>
            </div>

            <form action="{{ route('cms.teachers.update') }}" method="POST">
                @csrf

                @php
                    $stat1ValueUz = optional($contents->where('key', 'teachers_stat1_value')->first())->value_uz ?? '50+';
                    $stat1ValueEn = optional($contents->where('key', 'teachers_stat1_value')->first())->value_en ?? '50+';
                    $stat1ValueRu = optional($contents->where('key', 'teachers_stat1_value')->first())->value_ru ?? '50+';
                    $stat1LabelUz = optional($contents->where('key', 'teachers_stat1_label')->first())->value_uz ?? "O'qituvchilar";
                    $stat1LabelEn = optional($contents->where('key', 'teachers_stat1_label')->first())->value_en ?? 'Teachers';
                    $stat1LabelRu = optional($contents->where('key', 'teachers_stat1_label')->first())->value_ru ?? 'Преподаватели';

                    $stat2ValueUz = optional($contents->where('key', 'teachers_stat2_value')->first())->value_uz ?? '15+';
                    $stat2ValueEn = optional($contents->where('key', 'teachers_stat2_value')->first())->value_en ?? '15+';
                    $stat2ValueRu = optional($contents->where('key', 'teachers_stat2_value')->first())->value_ru ?? '15+';
                    $stat2LabelUz = optional($contents->where('key', 'teachers_stat2_label')->first())->value_uz ?? 'PhD darajali';
                    $stat2LabelEn = optional($contents->where('key', 'teachers_stat2_label')->first())->value_en ?? 'PhD level';
                    $stat2LabelRu = optional($contents->where('key', 'teachers_stat2_label')->first())->value_ru ?? 'Уровня PhD';

                    $stat3ValueUz = optional($contents->where('key', 'teachers_stat3_value')->first())->value_uz ?? '10+';
                    $stat3ValueEn = optional($contents->where('key', 'teachers_stat3_value')->first())->value_en ?? '10+';
                    $stat3ValueRu = optional($contents->where('key', 'teachers_stat3_value')->first())->value_ru ?? '10+';
                    $stat3LabelUz = optional($contents->where('key', 'teachers_stat3_label')->first())->value_uz ?? 'Xalqaro tajriba';
                    $stat3LabelEn = optional($contents->where('key', 'teachers_stat3_label')->first())->value_en ?? 'International experience';
                    $stat3LabelRu = optional($contents->where('key', 'teachers_stat3_label')->first())->value_ru ?? 'Международный опыт';

                    $stat4ValueUz = optional($contents->where('key', 'teachers_stat4_value')->first())->value_uz ?? '100+';
                    $stat4ValueEn = optional($contents->where('key', 'teachers_stat4_value')->first())->value_en ?? '100+';
                    $stat4ValueRu = optional($contents->where('key', 'teachers_stat4_value')->first())->value_ru ?? '100+';
                    $stat4LabelUz = optional($contents->where('key', 'teachers_stat4_label')->first())->value_uz ?? 'Ilmiy maqolalar';
                    $stat4LabelEn = optional($contents->where('key', 'teachers_stat4_label')->first())->value_en ?? 'Scientific articles';
                    $stat4LabelRu = optional($contents->where('key', 'teachers_stat4_label')->first())->value_ru ?? 'Научные статьи';
                @endphp

                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#stats-uz">O'zbekcha</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#stats-en">English</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#stats-ru">Русский</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- UZBEK TAB --}}
                    <div class="tab-pane fade show active" id="stats-uz">
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> 1-statistika</div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-muted">Qiymat (UZ)</label>
                                    <input type="text" name="teachers_stat1_value[uz]" class="form-control" value="{{ $stat1ValueUz }}" placeholder="50+">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-semibold text-muted">Yorliq (UZ)</label>
                                    <input type="text" name="teachers_stat1_label[uz]" class="form-control" value="{{ $stat1LabelUz }}" placeholder="O'qituvchilar">
                                </div>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> 2-statistika</div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-muted">Qiymat (UZ)</label>
                                    <input type="text" name="teachers_stat2_value[uz]" class="form-control" value="{{ $stat2ValueUz }}" placeholder="15+">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-semibold text-muted">Yorliq (UZ)</label>
                                    <input type="text" name="teachers_stat2_label[uz]" class="form-control" value="{{ $stat2LabelUz }}" placeholder="PhD darajali">
                                </div>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> 3-statistika</div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-muted">Qiymat (UZ)</label>
                                    <input type="text" name="teachers_stat3_value[uz]" class="form-control" value="{{ $stat3ValueUz }}" placeholder="10+">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-semibold text-muted">Yorliq (UZ)</label>
                                    <input type="text" name="teachers_stat3_label[uz]" class="form-control" value="{{ $stat3LabelUz }}" placeholder="Xalqaro tajriba">
                                </div>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> 4-statistika</div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-muted">Qiymat (UZ)</label>
                                    <input type="text" name="teachers_stat4_value[uz]" class="form-control" value="{{ $stat4ValueUz }}" placeholder="100+">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-semibold text-muted">Yorliq (UZ)</label>
                                    <input type="text" name="teachers_stat4_label[uz]" class="form-control" value="{{ $stat4LabelUz }}" placeholder="Ilmiy maqolalar">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ENGLISH TAB --}}
                    <div class="tab-pane fade" id="stats-en">
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> Stat #1</div>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label small fw-semibold text-muted">Value (EN)</label>
                                    <input type="text" name="teachers_stat1_value[en]" class="form-control" value="{{ $stat1ValueEn }}" placeholder="50+"></div>
                                <div class="col-md-8"><label class="form-label small fw-semibold text-muted">Label (EN)</label>
                                    <input type="text" name="teachers_stat1_label[en]" class="form-control" value="{{ $stat1LabelEn }}" placeholder="Teachers"></div>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> Stat #2</div>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label small fw-semibold text-muted">Value (EN)</label>
                                    <input type="text" name="teachers_stat2_value[en]" class="form-control" value="{{ $stat2ValueEn }}" placeholder="15+"></div>
                                <div class="col-md-8"><label class="form-label small fw-semibold text-muted">Label (EN)</label>
                                    <input type="text" name="teachers_stat2_label[en]" class="form-control" value="{{ $stat2LabelEn }}" placeholder="PhD level"></div>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> Stat #3</div>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label small fw-semibold text-muted">Value (EN)</label>
                                    <input type="text" name="teachers_stat3_value[en]" class="form-control" value="{{ $stat3ValueEn }}" placeholder="10+"></div>
                                <div class="col-md-8"><label class="form-label small fw-semibold text-muted">Label (EN)</label>
                                    <input type="text" name="teachers_stat3_label[en]" class="form-control" value="{{ $stat3LabelEn }}" placeholder="International experience"></div>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> Stat #4</div>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label small fw-semibold text-muted">Value (EN)</label>
                                    <input type="text" name="teachers_stat4_value[en]" class="form-control" value="{{ $stat4ValueEn }}" placeholder="100+"></div>
                                <div class="col-md-8"><label class="form-label small fw-semibold text-muted">Label (EN)</label>
                                    <input type="text" name="teachers_stat4_label[en]" class="form-control" value="{{ $stat4LabelEn }}" placeholder="Scientific articles"></div>
                            </div>
                        </div>
                    </div>

                    {{-- RUSSIAN TAB --}}
                    <div class="tab-pane fade" id="stats-ru">
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> Статистика #1</div>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label small fw-semibold text-muted">Значение (RU)</label>
                                    <input type="text" name="teachers_stat1_value[ru]" class="form-control" value="{{ $stat1ValueRu }}" placeholder="50+"></div>
                                <div class="col-md-8"><label class="form-label small fw-semibold text-muted">Подпись (RU)</label>
                                    <input type="text" name="teachers_stat1_label[ru]" class="form-control" value="{{ $stat1LabelRu }}" placeholder="Преподаватели"></div>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> Статистика #2</div>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label small fw-semibold text-muted">Значение (RU)</label>
                                    <input type="text" name="teachers_stat2_value[ru]" class="form-control" value="{{ $stat2ValueRu }}" placeholder="15+"></div>
                                <div class="col-md-8"><label class="form-label small fw-semibold text-muted">Подпись (RU)</label>
                                    <input type="text" name="teachers_stat2_label[ru]" class="form-control" value="{{ $stat2LabelRu }}" placeholder="Уровня PhD"></div>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> Статистика #3</div>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label small fw-semibold text-muted">Значение (RU)</label>
                                    <input type="text" name="teachers_stat3_value[ru]" class="form-control" value="{{ $stat3ValueRu }}" placeholder="10+"></div>
                                <div class="col-md-8"><label class="form-label small fw-semibold text-muted">Подпись (RU)</label>
                                    <input type="text" name="teachers_stat3_label[ru]" class="form-control" value="{{ $stat3LabelRu }}" placeholder="Международный опыт"></div>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-title"><i class="fas fa-pen"></i> Статистика #4</div>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label small fw-semibold text-muted">Значение (RU)</label>
                                    <input type="text" name="teachers_stat4_value[ru]" class="form-control" value="{{ $stat4ValueRu }}" placeholder="100+"></div>
                                <div class="col-md-8"><label class="form-label small fw-semibold text-muted">Подпись (RU)</label>
                                    <input type="text" name="teachers_stat4_label[ru]" class="form-control" value="{{ $stat4LabelRu }}" placeholder="Научные статьи"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Statistikani saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Teachers List - Merged from Employee table + legacy CMS -->
    <div class="alert alert-info d-flex align-items-center mb-3">
        <i class="fas fa-info-circle me-2 fs-4"></i>
        <div class="flex-grow-1">
            <strong>Ma'lumot:</strong> Sariq <span class="badge" style="background:rgba(245,158,11,.15);color:var(--c-amber)">CMS</span> belgili o'qituvchilar eski ma'lumotlar — ular <a href="{{ route('employees.teachers') }}" class="alert-link">/employees/teachers</a> da ko'rinmaydi.
            <br><small>Tugmani bosing — eski CMS yozuvlari Employee jadvaliga ko'chiriladi va ikkala sahifada ham ko'rinadi.</small>
        </div>
        <form action="{{ route('cms.teachers.migrate-to-employees') }}" method="POST" class="ms-2">
            @csrf
            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Eski CMS o\'qituvchilarini Employee jadvaliga ko\'chirishni tasdiqlaysizmi?')">
                <i class="fas fa-exchange-alt me-1"></i> CMS → Employees
            </button>
        </form>
    </div>

    <div class="content-card">
        <div class="section-header teachers d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>O'qituvchilar ro'yxati ({{ $teachers->count() }} ta)</h5>
            <div>
                <a href="{{ route('employees.create') }}?type=teacher" class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-2"></i>Yangi o'qituvchi qo'shish
                </a>
            </div>
        </div>
        <div class="section-body">
            @if($teachers->count() > 0)
                <div class="row g-4">
                    @foreach($teachers as $teacher)
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="teacher-card position-relative">
                                @if($teacher->source === 'cms')
                                    <span class="badge position-absolute" style="top:8px;right:8px;z-index:2;background:rgba(245,158,11,.15);color:var(--c-amber)" title="Eski CMS ma'lumot">CMS</span>
                                @else
                                    <span class="badge bg-success position-absolute" style="top:8px; right:8px; z-index:2;" title="Xodimlar bazasi">HR</span>
                                @endif
                                @if(!empty($teacher->image))
                                    @php
                                        $img = $teacher->image;
                                        if (str_starts_with($img, 'http')) {
                                            $imgSrc = $img;
                                        } elseif (str_starts_with($img, 'assets/')) {
                                            $imgSrc = asset($img);
                                        } else {
                                            $imgSrc = asset('storage/' . ltrim($img, '/'));
                                        }
                                    @endphp
                                    <img src="{{ $imgSrc }}" alt="{{ $teacher->name }}" class="teacher-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="teacher-placeholder d-none align-items-center justify-content-center">
                                        <i class="fas fa-user-tie fa-3x text-white"></i>
                                    </div>
                                @else
                                    <div class="teacher-placeholder d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user-tie fa-3x text-white"></i>
                                    </div>
                                @endif
                                <div class="p-3">
                                    <h6 class="fw-bold mb-1">{{ $teacher->name }}</h6>
                                    @if($teacher->position)
                                        <p class="small text-primary mb-1"><i class="fas fa-briefcase me-1"></i>{{ $teacher->position }}</p>
                                    @endif
                                    @if($teacher->email)
                                        <p class="small text-muted mb-2"><i class="fas fa-envelope me-1"></i>{{ $teacher->email }}</p>
                                    @endif
                                    @if($teacher->bio)
                                        <p class="text-muted small mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $teacher->bio }}</p>
                                    @endif
                                    <div class="d-flex gap-2 mt-3">
                                        @if($teacher->source === 'employee')
                                            <a href="{{ $teacher->view_url }}" class="btn btn-sm btn-outline-info flex-grow-1">
                                                <i class="fas fa-eye me-1"></i>Ko'rish
                                            </a>
                                            <a href="{{ $teacher->edit_url }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                                <i class="fas fa-edit me-1"></i>Tahrirlash
                                            </a>
                                        @else
                                            <form action="{{ route('cms.teachers.destroy', $teacher->id) }}" method="POST" class="d-inline flex-grow-1" onsubmit="return confirm('Bu eski CMS yozuvini o\'chirmoqchimisiz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                    <i class="fas fa-trash me-1"></i>O'chirish
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-4"></i>
                    <h5 class="text-muted">Hozircha o'qituvchilar yo'q</h5>
                    <p class="text-muted mb-4">Xodimlar bo'limidan yangi o'qituvchi qo'shing</p>
                    <a href="{{ route('employees.create') }}?type=teacher" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Yangi o'qituvchi qo'shish
                    </a>
                </div>
            @endif
        </div>
    </div>

@endsection
