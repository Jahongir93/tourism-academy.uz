@extends('layouts.dashboard-new')

@section('title', "Yo'nalishlar - CMS")
@section('page-title', "Yo'nalishlar sahifasini tahrirlash")

@section('styles')
<style>
    .content-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        transition: all 0.3s;
        margin-bottom: 24px;
    }
    .section-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: white;
        padding: 16px 20px;
        border-radius: 12px 12px 0 0;
    }
    .section-header.hero { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    .section-header.stats { background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%); }
    .section-header.programs { background: linear-gradient(135deg, #065f46 0%, #047857 100%); }
    .section-header.benefits { background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }
    .program-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }
</style>
@endsection

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="fas fa-graduation-cap text-primary me-2"></i>Yo'nalishlar sahifasini tahrirlash
                    </h1>
                    <p class="text-muted mb-0">Sahifani uchta tilda tahrirlang</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cms.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Orqaga
                    </a>
                    <a href="{{ route('programs') }}" target="_blank" class="btn btn-info">
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
        <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#stats"><i class="fas fa-chart-bar me-1"></i> Statistika</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#programs"><i class="fas fa-book me-1"></i> Dasturlar</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#benefits"><i class="fas fa-gift me-1"></i> Afzalliklar</a></li>
    </ul>

    <form action="{{ route('cms.programs.update') }}" method="POST">
        @csrf

        <div class="tab-content">
            {{-- Hero Section --}}
            <div class="tab-pane fade show active" id="hero">
                <div class="content-card">
                    <div class="section-header hero">
                        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Hero Bo'limi</h5>
                    </div>
                    <div class="p-4">
                        @foreach(['programs_hero_badge', 'programs_hero_title_highlight', 'programs_hero_title_2', 'programs_hero_description'] as $field)
                            @php $content = $contents->firstWhere('key', $field); @endphp
                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ ucwords(str_replace(['programs_hero_', '_'], ['', ' '], $field)) }}</label>
                                <div class="row g-2">
                                    @foreach(['uz' => '🇺🇿', 'en' => '🇬🇧', 'ru' => '🇷🇺'] as $lang => $flag)
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">{{ $flag }}</span>
                                            @if($field == 'programs_hero_description')
                                                <textarea name="{{ $field }}[{{ $lang }}]" class="form-control" rows="3">{{ $content->{'value_'.$lang} ?? '' }}</textarea>
                                            @else
                                                <input type="text" name="{{ $field }}[{{ $lang }}]" class="form-control" value="{{ $content->{'value_'.$lang} ?? '' }}">
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Stats Section --}}
            <div class="tab-pane fade" id="stats">
                <div class="content-card">
                    <div class="section-header stats">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistika kartochkalari</h5>
                    </div>
                    <div class="p-4">
                        <div class="row">
                            @for($i = 1; $i <= 3; $i++)
                                <div class="col-md-4">
                                    <div class="program-card">
                                        <h6 class="fw-bold mb-3">Statistika {{ $i }}</h6>
                                        @php $icon = $contents->firstWhere('key', "programs_stat{$i}_icon"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Icon</label>
                                            <input type="text" name="programs_stat{{ $i }}_icon[uz]" class="form-control form-control-sm" value="{{ $icon->value_uz ?? '' }}">
                                        </div>
                                        @php $value = $contents->firstWhere('key', "programs_stat{$i}_value"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Qiymat</label>
                                            <input type="text" name="programs_stat{{ $i }}_value[uz]" class="form-control form-control-sm" value="{{ $value->value_uz ?? '' }}">
                                        </div>
                                        @php $label = $contents->firstWhere('key', "programs_stat{$i}_label"); @endphp
                                        <div class="mb-0">
                                            <label class="form-label small">Label</label>
                                            <input type="text" name="programs_stat{{ $i }}_label[uz]" class="form-control form-control-sm mb-1" value="{{ $label->value_uz ?? '' }}" placeholder="O'zbekcha">
                                            <input type="text" name="programs_stat{{ $i }}_label[en]" class="form-control form-control-sm mb-1" value="{{ $label->value_en ?? '' }}" placeholder="English">
                                            <input type="text" name="programs_stat{{ $i }}_label[ru]" class="form-control form-control-sm" value="{{ $label->value_ru ?? '' }}" placeholder="Русский">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- Programs Section --}}
            <div class="tab-pane fade" id="programs">
                <div class="content-card">
                    <div class="section-header programs">
                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Ta'lim dasturlari (8 ta)</h5>
                    </div>
                    <div class="p-4">
                        {{-- Section titles --}}
                        @php $secTitle = $contents->firstWhere('key', 'programs_section_title'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                            <div class="row g-2">
                                <div class="col-md-4"><input type="text" name="programs_section_title[uz]" class="form-control" value="{{ $secTitle->value_uz ?? '' }}"></div>
                                <div class="col-md-4"><input type="text" name="programs_section_title[en]" class="form-control" value="{{ $secTitle->value_en ?? '' }}"></div>
                                <div class="col-md-4"><input type="text" name="programs_section_title[ru]" class="form-control" value="{{ $secTitle->value_ru ?? '' }}"></div>
                            </div>
                        </div>

                        @php $secSubtitle = $contents->firstWhere('key', 'programs_section_subtitle'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bo'lim tavsifi</label>
                            <div class="row g-2">
                                <div class="col-md-4"><textarea name="programs_section_subtitle[uz]" class="form-control" rows="2">{{ $secSubtitle->value_uz ?? '' }}</textarea></div>
                                <div class="col-md-4"><textarea name="programs_section_subtitle[en]" class="form-control" rows="2">{{ $secSubtitle->value_en ?? '' }}</textarea></div>
                                <div class="col-md-4"><textarea name="programs_section_subtitle[ru]" class="form-control" rows="2">{{ $secSubtitle->value_ru ?? '' }}</textarea></div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            @for($i = 1; $i <= 8; $i++)
                                <div class="col-md-6 mb-3">
                                    <div class="program-card">
                                        <h6 class="fw-bold mb-3 text-primary">Dastur {{ $i }}</h6>

                                        @php $title = $contents->firstWhere('key', "program{$i}_title"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Sarlavha</label>
                                            <input type="text" name="program{{ $i }}_title[uz]" class="form-control form-control-sm mb-1" value="{{ $title->value_uz ?? '' }}" placeholder="O'zbekcha">
                                            <input type="text" name="program{{ $i }}_title[en]" class="form-control form-control-sm mb-1" value="{{ $title->value_en ?? '' }}" placeholder="English">
                                            <input type="text" name="program{{ $i }}_title[ru]" class="form-control form-control-sm" value="{{ $title->value_ru ?? '' }}" placeholder="Русский">
                                        </div>

                                        @php $dates = $contents->firstWhere('key', "program{$i}_dates"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Sanalar</label>
                                            <input type="text" name="program{{ $i }}_dates[uz]" class="form-control form-control-sm mb-1" value="{{ $dates->value_uz ?? '' }}">
                                            <input type="text" name="program{{ $i }}_dates[en]" class="form-control form-control-sm mb-1" value="{{ $dates->value_en ?? '' }}">
                                            <input type="text" name="program{{ $i }}_dates[ru]" class="form-control form-control-sm" value="{{ $dates->value_ru ?? '' }}">
                                        </div>

                                        @php $desc = $contents->firstWhere('key', "program{$i}_description"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Tavsif</label>
                                            <textarea name="program{{ $i }}_description[uz]" class="form-control form-control-sm mb-1" rows="2">{{ $desc->value_uz ?? '' }}</textarea>
                                            <textarea name="program{{ $i }}_description[en]" class="form-control form-control-sm mb-1" rows="2">{{ $desc->value_en ?? '' }}</textarea>
                                            <textarea name="program{{ $i }}_description[ru]" class="form-control form-control-sm" rows="2">{{ $desc->value_ru ?? '' }}</textarea>
                                        </div>

                                        @php $topics = $contents->firstWhere('key', "program{$i}_topics"); @endphp
                                        <div class="mb-0">
                                            <label class="form-label small">Mavzular soni</label>
                                            <input type="text" name="program{{ $i }}_topics[uz]" class="form-control form-control-sm" value="{{ $topics->value_uz ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- Benefits Section --}}
            <div class="tab-pane fade" id="benefits">
                <div class="content-card">
                    <div class="section-header benefits">
                        <h5 class="mb-0"><i class="fas fa-gift me-2"></i>Afzalliklar (6 ta)</h5>
                    </div>
                    <div class="p-4">
                        @php $benTitle = $contents->firstWhere('key', 'benefits_title'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                            <div class="row g-2">
                                <div class="col-md-4"><input type="text" name="benefits_title[uz]" class="form-control" value="{{ $benTitle->value_uz ?? '' }}"></div>
                                <div class="col-md-4"><input type="text" name="benefits_title[en]" class="form-control" value="{{ $benTitle->value_en ?? '' }}"></div>
                                <div class="col-md-4"><input type="text" name="benefits_title[ru]" class="form-control" value="{{ $benTitle->value_ru ?? '' }}"></div>
                            </div>
                        </div>

                        @php $benSubtitle = $contents->firstWhere('key', 'benefits_subtitle'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bo'lim tavsifi</label>
                            <div class="row g-2">
                                <div class="col-md-4"><textarea name="benefits_subtitle[uz]" class="form-control" rows="2">{{ $benSubtitle->value_uz ?? '' }}</textarea></div>
                                <div class="col-md-4"><textarea name="benefits_subtitle[en]" class="form-control" rows="2">{{ $benSubtitle->value_en ?? '' }}</textarea></div>
                                <div class="col-md-4"><textarea name="benefits_subtitle[ru]" class="form-control" rows="2">{{ $benSubtitle->value_ru ?? '' }}</textarea></div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            @for($i = 1; $i <= 6; $i++)
                                <div class="col-md-4 mb-3">
                                    <div class="program-card">
                                        <h6 class="fw-bold mb-3">Afzallik {{ $i }}</h6>
                                        @php $icon = $contents->firstWhere('key', "benefit{$i}_icon"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Icon</label>
                                            <input type="text" name="benefit{{ $i }}_icon[uz]" class="form-control form-control-sm" value="{{ $icon->value_uz ?? '' }}">
                                        </div>
                                        @php $title = $contents->firstWhere('key', "benefit{$i}_title"); @endphp
                                        <div class="mb-2">
                                            <label class="form-label small">Sarlavha</label>
                                            <input type="text" name="benefit{{ $i }}_title[uz]" class="form-control form-control-sm mb-1" value="{{ $title->value_uz ?? '' }}">
                                            <input type="text" name="benefit{{ $i }}_title[en]" class="form-control form-control-sm mb-1" value="{{ $title->value_en ?? '' }}">
                                            <input type="text" name="benefit{{ $i }}_title[ru]" class="form-control form-control-sm" value="{{ $title->value_ru ?? '' }}">
                                        </div>
                                        @php $text = $contents->firstWhere('key', "benefit{$i}_text"); @endphp
                                        <div class="mb-0">
                                            <label class="form-label small">Matn</label>
                                            <textarea name="benefit{{ $i }}_text[uz]" class="form-control form-control-sm mb-1" rows="2">{{ $text->value_uz ?? '' }}</textarea>
                                            <textarea name="benefit{{ $i }}_text[en]" class="form-control form-control-sm mb-1" rows="2">{{ $text->value_en ?? '' }}</textarea>
                                            <textarea name="benefit{{ $i }}_text[ru]" class="form-control form-control-sm" rows="2">{{ $text->value_ru ?? '' }}</textarea>
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
