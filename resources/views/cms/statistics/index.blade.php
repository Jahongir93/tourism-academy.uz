@extends('layouts.dashboard-new')

@section('title', 'Statistika - CMS')
@section('page-title', 'Statistika sahifasini tahrirlash')

@section('styles')
<style>
    .content-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        margin-bottom: 24px;
    }
    .section-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: white;
        padding: 16px 20px;
        border-radius: 12px 12px 0 0;
    }
    .section-header.hero { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    .section-header.age { background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%); }
    .section-header.region { background: linear-gradient(135deg, #065f46 0%, #047857 100%); }
    .section-header.edu { background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }
    .stat-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
    }
</style>
@endsection

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="fas fa-chart-bar text-primary me-2"></i>Statistika sahifasini tahrirlash
                    </h1>
                    <p class="text-muted mb-0">Sahifani uchta tilda tahrirlang</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cms.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Orqaga
                    </a>
                    <a href="{{ route('statistics') }}" target="_blank" class="btn btn-info">
                        <i class="fas fa-external-link-alt me-1"></i> Ko'rish
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-pills mb-4">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#hero"><i class="fas fa-star me-1"></i> Hero</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#age"><i class="fas fa-users me-1"></i> Yosh bo'yicha</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#region"><i class="fas fa-map me-1"></i> Mintaqa</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#education"><i class="fas fa-graduation-cap me-1"></i> Ta'lim darajasi</a></li>
    </ul>

    <form action="{{ route('cms.statistics.update') }}" method="POST">
        @csrf

        <div class="tab-content">
            {{-- Hero Section --}}
            <div class="tab-pane fade show active" id="hero">
                <div class="content-card">
                    <div class="section-header hero">
                        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Hero Bo'limi</h5>
                    </div>
                    <div class="p-4">
                        @foreach(['stats_hero_badge', 'stats_hero_title', 'stats_hero_subtitle'] as $field)
                            @php $content = $contents->firstWhere('key', $field); @endphp
                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ ucwords(str_replace(['stats_hero_', '_'], ['', ' '], $field)) }}</label>
                                <div class="row g-2">
                                    @foreach(['uz' => '🇺🇿', 'en' => '🇬🇧', 'ru' => '🇷🇺'] as $lang => $flag)
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">{{ $flag }}</span>
                                            <input type="text" name="{{ $field }}[{{ $lang }}]" class="form-control" value="{{ $content->{'value_'.$lang} ?? '' }}">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Age Statistics --}}
            <div class="tab-pane fade" id="age">
                <div class="content-card">
                    <div class="section-header age">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Yosh bo'yicha statistika</h5>
                    </div>
                    <div class="p-4">
                        @php $ageTitle = $contents->firstWhere('key', 'stats_age_title'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                            <div class="row g-2">
                                <div class="col-md-4"><input type="text" name="stats_age_title[uz]" class="form-control" value="{{ $ageTitle->value_uz ?? '' }}"></div>
                                <div class="col-md-4"><input type="text" name="stats_age_title[en]" class="form-control" value="{{ $ageTitle->value_en ?? '' }}"></div>
                                <div class="col-md-4"><input type="text" name="stats_age_title[ru]" class="form-control" value="{{ $ageTitle->value_ru ?? '' }}"></div>
                            </div>
                        </div>

                        <div class="row">
                            @for($i = 1; $i <= 4; $i++)
                                <div class="col-md-3 mb-3">
                                    <div class="stat-card">
                                        <h6 class="fw-bold mb-3">Yosh guruhi {{ $i }}</h6>
                                        @php $icon = $contents->firstWhere('key', "stats_age{$i}_icon"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Icon</label>
                                            <input type="text" name="stats_age{{ $i }}_icon[uz]" class="form-control form-control-sm" value="{{ $icon->value_uz ?? '' }}">
                                        </div>
                                        @php $value = $contents->firstWhere('key', "stats_age{$i}_value"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Qiymat</label>
                                            <input type="text" name="stats_age{{ $i }}_value[uz]" class="form-control form-control-sm" value="{{ $value->value_uz ?? '' }}">
                                        </div>
                                        @php $label = $contents->firstWhere('key', "stats_age{$i}_label"); @endphp
                                        <div class="mb-0">
                                            <label class="form-label small">Label</label>
                                            <input type="text" name="stats_age{{ $i }}_label[uz]" class="form-control form-control-sm mb-1" value="{{ $label->value_uz ?? '' }}">
                                            <input type="text" name="stats_age{{ $i }}_label[en]" class="form-control form-control-sm mb-1" value="{{ $label->value_en ?? '' }}">
                                            <input type="text" name="stats_age{{ $i }}_label[ru]" class="form-control form-control-sm" value="{{ $label->value_ru ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- Region Statistics --}}
            <div class="tab-pane fade" id="region">
                <div class="content-card">
                    <div class="section-header region">
                        <h5 class="mb-0"><i class="fas fa-map me-2"></i>Mintaqa bo'yicha statistika</h5>
                    </div>
                    <div class="p-4">
                        @php $regionTitle = $contents->firstWhere('key', 'stats_region_title'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                            <div class="row g-2">
                                <div class="col-md-4"><input type="text" name="stats_region_title[uz]" class="form-control" value="{{ $regionTitle->value_uz ?? '' }}"></div>
                                <div class="col-md-4"><input type="text" name="stats_region_title[en]" class="form-control" value="{{ $regionTitle->value_en ?? '' }}"></div>
                                <div class="col-md-4"><input type="text" name="stats_region_title[ru]" class="form-control" value="{{ $regionTitle->value_ru ?? '' }}"></div>
                            </div>
                        </div>

                        <div class="row">
                            @for($i = 1; $i <= 6; $i++)
                                <div class="col-md-4 mb-3">
                                    <div class="stat-card">
                                        <h6 class="fw-bold mb-3">Mintaqa {{ $i }}</h6>
                                        @php $value = $contents->firstWhere('key', "stats_region{$i}_value"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Qiymat</label>
                                            <input type="text" name="stats_region{{ $i }}_value[uz]" class="form-control form-control-sm" value="{{ $value->value_uz ?? '' }}">
                                        </div>
                                        @php $label = $contents->firstWhere('key', "stats_region{$i}_label"); @endphp
                                        <div class="mb-0">
                                            <label class="form-label small">Mintaqa nomi</label>
                                            <input type="text" name="stats_region{{ $i }}_label[uz]" class="form-control form-control-sm mb-1" value="{{ $label->value_uz ?? '' }}">
                                            <input type="text" name="stats_region{{ $i }}_label[en]" class="form-control form-control-sm mb-1" value="{{ $label->value_en ?? '' }}">
                                            <input type="text" name="stats_region{{ $i }}_label[ru]" class="form-control form-control-sm" value="{{ $label->value_ru ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- Education Level Statistics --}}
            <div class="tab-pane fade" id="education">
                <div class="content-card">
                    <div class="section-header edu">
                        <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Ta'lim darajasi bo'yicha</h5>
                    </div>
                    <div class="p-4">
                        @php $eduTitle = $contents->firstWhere('key', 'stats_edu_title'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                            <div class="row g-2">
                                <div class="col-md-4"><input type="text" name="stats_edu_title[uz]" class="form-control" value="{{ $eduTitle->value_uz ?? '' }}"></div>
                                <div class="col-md-4"><input type="text" name="stats_edu_title[en]" class="form-control" value="{{ $eduTitle->value_en ?? '' }}"></div>
                                <div class="col-md-4"><input type="text" name="stats_edu_title[ru]" class="form-control" value="{{ $eduTitle->value_ru ?? '' }}"></div>
                            </div>
                        </div>

                        <div class="row">
                            @for($i = 1; $i <= 3; $i++)
                                <div class="col-md-4 mb-3">
                                    <div class="stat-card">
                                        <h6 class="fw-bold mb-3">Ta'lim darajasi {{ $i }}</h6>
                                        @php $icon = $contents->firstWhere('key', "stats_edu{$i}_icon"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Icon</label>
                                            <input type="text" name="stats_edu{{ $i }}_icon[uz]" class="form-control form-control-sm" value="{{ $icon->value_uz ?? '' }}">
                                        </div>
                                        @php $value = $contents->firstWhere('key', "stats_edu{$i}_value"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Qiymat</label>
                                            <input type="text" name="stats_edu{{ $i }}_value[uz]" class="form-control form-control-sm" value="{{ $value->value_uz ?? '' }}">
                                        </div>
                                        @php $title = $contents->firstWhere('key', "stats_edu{$i}_title"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Sarlavha</label>
                                            <input type="text" name="stats_edu{{ $i }}_title[uz]" class="form-control form-control-sm mb-1" value="{{ $title->value_uz ?? '' }}">
                                            <input type="text" name="stats_edu{{ $i }}_title[en]" class="form-control form-control-sm mb-1" value="{{ $title->value_en ?? '' }}">
                                            <input type="text" name="stats_edu{{ $i }}_title[ru]" class="form-control form-control-sm" value="{{ $title->value_ru ?? '' }}">
                                        </div>
                                        @php $label = $contents->firstWhere('key', "stats_edu{$i}_label"); @endphp
                                        <div class="mb-0">
                                            <label class="form-label small">Label</label>
                                            <input type="text" name="stats_edu{{ $i }}_label[uz]" class="form-control form-control-sm mb-1" value="{{ $label->value_uz ?? '' }}">
                                            <input type="text" name="stats_edu{{ $i }}_label[en]" class="form-control form-control-sm mb-1" value="{{ $label->value_en ?? '' }}">
                                            <input type="text" name="stats_edu{{ $i }}_label[ru]" class="form-control form-control-sm" value="{{ $label->value_ru ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body d-flex justify-content-end gap-2">
                <button type="reset" class="btn btn-secondary"><i class="fas fa-undo me-1"></i> Bekor qilish</button>
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-1"></i> Saqlash</button>
            </div>
        </div>
    </form>
@endsection
