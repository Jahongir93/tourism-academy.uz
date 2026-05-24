@extends('layouts.dashboard-new')

@section('title', 'Davlat Ramzlari - CMS')
@section('page-title', 'Davlat Ramzlarini Tahrirlash')

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
        padding: 16px 20px;
        border-radius: 12px 12px 0 0;
        margin: -1px -1px 0 -1px;
        color: white;
    }
    .section-header.flag { background: linear-gradient(135deg, #0099B5 0%, #1EB53A 100%); }
    .section-header.emblem { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); }
    .section-header.anthem { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
    .section-header.footer { background: linear-gradient(135deg, #374151 0%, #4b5563 100%); }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #0099B5 0%, #1EB53A 100%);
    }
    .item-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }
    .color-preview {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        cursor: pointer;
    }
    .verse-card {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-left: 4px solid #0099B5;
        padding: 20px;
        border-radius: 0 12px 12px 0;
        margin-bottom: 16px;
    }
    .chorus-card {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-left: 4px solid #1EB53A;
        padding: 20px;
        border-radius: 0 12px 12px 0;
        margin-bottom: 16px;
    }
</style>
@endsection

@section('content')

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="fas fa-flag text-primary me-2"></i>Davlat Ramzlarini Tahrirlash
                    </h1>
                    <p class="text-muted mb-0">Bayroq, Gerb va Madhiya ma'lumotlarini uchta tilda tahrirlang</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cms.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Orqaga
                    </a>
                    <a href="{{ route('state-symbols') }}" target="_blank" class="btn btn-info">
                        <i class="fas fa-external-link-alt me-1"></i> Ko'rish
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Navigation Tabs --}}
    <ul class="nav nav-pills mb-4" id="sectionTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="pill" href="#flag"><i class="fas fa-flag me-1"></i> Bayroq</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#emblem"><i class="fas fa-shield-alt me-1"></i> Gerb</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#anthem"><i class="fas fa-music me-1"></i> Madhiya</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#footer"><i class="fas fa-align-left me-1"></i> Xulosa</a>
        </li>
    </ul>

    <form action="{{ route('cms.state-symbols.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="tab-content">
            {{-- Flag Section --}}
            <div class="tab-pane fade show active" id="flag">
                <div class="content-card">
                    <div class="section-header flag">
                        <h5 class="mb-0"><i class="fas fa-flag me-2"></i>Davlat Bayrog'i</h5>
                    </div>
                    <div class="p-4">
                        {{-- Flag Image --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                @php
                                    $flagImage   = $contents->firstWhere('key', 'symbols_flag_image');
                                    $flagImgPath = $flagImage->value_uz ?? '';
                                    $flagHasImg  = !empty($flagImgPath);
                                    $flagImgUrl  = $flagHasImg
                                        ? (str_starts_with($flagImgPath, 'http') ? $flagImgPath : asset($flagImgPath))
                                        : '';
                                    $flagIsUpload = str_starts_with($flagImgPath, 'uploads/');
                                @endphp
                                <label class="form-label fw-semibold">Bayroq rasmi</label>
                                <div class="image-upload-wrapper" data-image-key="symbols_flag_image">
                                    <div class="image-preview-box mb-2" data-preview-target style="min-height:80px;">
                                        @if($flagHasImg)
                                            <img src="{{ $flagImgUrl }}" data-current-image class="img-thumbnail" style="max-height:120px;">
                                        @else
                                            <div class="image-placeholder text-muted small p-3 border rounded text-center">Rasm tanlanmagan</div>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        <input type="file" class="form-control form-control-sm flex-grow-1" accept="image/*" data-image-input>
                                        <button type="button" class="btn btn-sm btn-danger" data-image-delete style="{{ $flagIsUpload ? '' : 'display:none' }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="image-status small mt-1 text-muted"></div>
                                    <div class="form-text">PNG, JPG yoki SVG formatda</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                {{-- Adoption Date --}}
                                @php $adopted = $contents->firstWhere('key', 'symbols_flag_adopted'); @endphp
                                <label class="form-label fw-semibold">Qabul qilingan sana</label>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <div class="input-group input-group-sm mb-1">
                                            <span class="input-group-text">UZ</span>
                                            <input type="text" name="symbols_flag_adopted[uz]" class="form-control" value="{{ $adopted->value_uz ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group input-group-sm mb-1">
                                            <span class="input-group-text">EN</span>
                                            <input type="text" name="symbols_flag_adopted[en]" class="form-control" value="{{ $adopted->value_en ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">RU</span>
                                            <input type="text" name="symbols_flag_adopted[ru]" class="form-control" value="{{ $adopted->value_ru ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3"><i class="fas fa-palette me-2"></i>Bayroq ranglari</h6>

                        {{-- 4 Colors --}}
                        <div class="row">
                            @for($i = 1; $i <= 4; $i++)
                                <div class="col-md-6">
                                    <div class="item-card">
                                        <h6 class="fw-bold mb-3">{{ $i }}-rang</h6>

                                        @php $colorName = $contents->firstWhere('key', "symbols_flag_color{$i}_name"); @endphp
                                        <div class="mb-3">
                                            <label class="form-label small">Rang nomi</label>
                                            <div class="row g-1">
                                                <div class="col-4"><input type="text" name="symbols_flag_color{{ $i }}_name[uz]" class="form-control form-control-sm" value="{{ $colorName->value_uz ?? '' }}" placeholder="O'zbekcha"></div>
                                                <div class="col-4"><input type="text" name="symbols_flag_color{{ $i }}_name[en]" class="form-control form-control-sm" value="{{ $colorName->value_en ?? '' }}" placeholder="English"></div>
                                                <div class="col-4"><input type="text" name="symbols_flag_color{{ $i }}_name[ru]" class="form-control form-control-sm" value="{{ $colorName->value_ru ?? '' }}" placeholder="Русский"></div>
                                            </div>
                                        </div>

                                        @php $colorMeaning = $contents->firstWhere('key', "symbols_flag_color{$i}_meaning"); @endphp
                                        <div class="mb-3">
                                            <label class="form-label small">Ma'nosi</label>
                                            <div class="row g-1">
                                                <div class="col-4"><input type="text" name="symbols_flag_color{{ $i }}_meaning[uz]" class="form-control form-control-sm" value="{{ $colorMeaning->value_uz ?? '' }}"></div>
                                                <div class="col-4"><input type="text" name="symbols_flag_color{{ $i }}_meaning[en]" class="form-control form-control-sm" value="{{ $colorMeaning->value_en ?? '' }}"></div>
                                                <div class="col-4"><input type="text" name="symbols_flag_color{{ $i }}_meaning[ru]" class="form-control form-control-sm" value="{{ $colorMeaning->value_ru ?? '' }}"></div>
                                            </div>
                                        </div>

                                        @php $colorHex = $contents->firstWhere('key', "symbols_flag_color{$i}_hex"); @endphp
                                        <div class="mb-0">
                                            <label class="form-label small">Rang kodi (HEX)</label>
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="color" class="color-preview" value="{{ $colorHex->value_uz ?? '#000000' }}"
                                                       onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" name="symbols_flag_color{{ $i }}_hex[uz]" class="form-control form-control-sm"
                                                       value="{{ $colorHex->value_uz ?? '' }}" style="max-width: 120px;"
                                                       onchange="this.previousElementSibling.value = this.value">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3"><i class="fas fa-star me-2"></i>Bayroq elementlari</h6>

                        @for($i = 1; $i <= 2; $i++)
                            @php $element = $contents->firstWhere('key', "symbols_flag_element{$i}"); @endphp
                            <div class="mb-3">
                                <label class="form-label small">{{ $i }}-element</label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">UZ</span>
                                            <input type="text" name="symbols_flag_element{{ $i }}[uz]" class="form-control" value="{{ $element->value_uz ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">EN</span>
                                            <input type="text" name="symbols_flag_element{{ $i }}[en]" class="form-control" value="{{ $element->value_en ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">RU</span>
                                            <input type="text" name="symbols_flag_element{{ $i }}[ru]" class="form-control" value="{{ $element->value_ru ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- Emblem Section --}}
            <div class="tab-pane fade" id="emblem">
                <div class="content-card">
                    <div class="section-header emblem">
                        <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Davlat Gerbi</h5>
                    </div>
                    <div class="p-4">
                        {{-- Adoption Date --}}
                        @php $adopted = $contents->firstWhere('key', 'symbols_emblem_adopted'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Qabul qilingan sana</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">UZ</span>
                                        <input type="text" name="symbols_emblem_adopted[uz]" class="form-control" value="{{ $adopted->value_uz ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">EN</span>
                                        <input type="text" name="symbols_emblem_adopted[en]" class="form-control" value="{{ $adopted->value_en ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">RU</span>
                                        <input type="text" name="symbols_emblem_adopted[ru]" class="form-control" value="{{ $adopted->value_ru ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Emblem Image --}}
                        @php
                            $emblemImage   = $contents->firstWhere('key', 'symbols_emblem_image');
                            $emblemImgPath = $emblemImage->value_uz ?? '';
                            $emblemHasImg  = !empty($emblemImgPath);
                            $emblemImgUrl  = $emblemHasImg
                                ? (str_starts_with($emblemImgPath, 'http') ? $emblemImgPath : asset($emblemImgPath))
                                : '';
                            $emblemIsUpload = str_starts_with($emblemImgPath, 'uploads/');
                        @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Gerb rasmi</label>
                            <div class="image-upload-wrapper" data-image-key="symbols_emblem_image">
                                <div class="image-preview-box mb-2" data-preview-target style="min-height:80px;">
                                    @if($emblemHasImg)
                                        <img src="{{ $emblemImgUrl }}" data-current-image class="img-thumbnail" style="max-height:150px;">
                                    @else
                                        <div class="image-placeholder text-muted small p-3 border rounded text-center">Rasm tanlanmagan</div>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    <input type="file" class="form-control form-control-sm flex-grow-1" accept="image/*" data-image-input>
                                    <button type="button" class="btn btn-sm btn-danger" data-image-delete style="{{ $emblemIsUpload ? '' : 'display:none' }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="image-status small mt-1 text-muted"></div>
                                <div class="form-text">PNG yoki JPG formatda, orqa foni shaffof bo'lgan rasm tavsiya etiladi</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3"><i class="fas fa-shapes me-2"></i>Gerb elementlari</h6>

                        {{-- 6 Elements --}}
                        <div class="row">
                            @for($i = 1; $i <= 6; $i++)
                                <div class="col-md-6">
                                    <div class="item-card">
                                        <h6 class="fw-bold mb-3">{{ $i }}-element</h6>

                                        @php $elementName = $contents->firstWhere('key', "symbols_emblem_element{$i}_name"); @endphp
                                        <div class="mb-3">
                                            <label class="form-label small">Element nomi</label>
                                            <div class="row g-1">
                                                <div class="col-4"><input type="text" name="symbols_emblem_element{{ $i }}_name[uz]" class="form-control form-control-sm" value="{{ $elementName->value_uz ?? '' }}" placeholder="O'zbekcha"></div>
                                                <div class="col-4"><input type="text" name="symbols_emblem_element{{ $i }}_name[en]" class="form-control form-control-sm" value="{{ $elementName->value_en ?? '' }}" placeholder="English"></div>
                                                <div class="col-4"><input type="text" name="symbols_emblem_element{{ $i }}_name[ru]" class="form-control form-control-sm" value="{{ $elementName->value_ru ?? '' }}" placeholder="Русский"></div>
                                            </div>
                                        </div>

                                        @php $elementMeaning = $contents->firstWhere('key', "symbols_emblem_element{$i}_meaning"); @endphp
                                        <div class="mb-0">
                                            <label class="form-label small">Ma'nosi</label>
                                            <div class="row g-1">
                                                <div class="col-4"><textarea name="symbols_emblem_element{{ $i }}_meaning[uz]" class="form-control form-control-sm" rows="2">{{ $elementMeaning->value_uz ?? '' }}</textarea></div>
                                                <div class="col-4"><textarea name="symbols_emblem_element{{ $i }}_meaning[en]" class="form-control form-control-sm" rows="2">{{ $elementMeaning->value_en ?? '' }}</textarea></div>
                                                <div class="col-4"><textarea name="symbols_emblem_element{{ $i }}_meaning[ru]" class="form-control form-control-sm" rows="2">{{ $elementMeaning->value_ru ?? '' }}</textarea></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- Anthem Section --}}
            <div class="tab-pane fade" id="anthem">
                <div class="content-card">
                    <div class="section-header anthem">
                        <h5 class="mb-0"><i class="fas fa-music me-2"></i>Davlat Madhiyasi</h5>
                    </div>
                    <div class="p-4">
                        {{-- Anthem Title --}}
                        @php $title = $contents->firstWhere('key', 'symbols_anthem_title'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Madhiya sarlavhasi</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">UZ</span>
                                        <input type="text" name="symbols_anthem_title[uz]" class="form-control" value="{{ $title->value_uz ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">EN</span>
                                        <input type="text" name="symbols_anthem_title[en]" class="form-control" value="{{ $title->value_en ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">RU</span>
                                        <input type="text" name="symbols_anthem_title[ru]" class="form-control" value="{{ $title->value_ru ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            {{-- Adoption Date --}}
                            <div class="col-md-4">
                                @php $adopted = $contents->firstWhere('key', 'symbols_anthem_adopted'); @endphp
                                <label class="form-label fw-semibold">Qabul qilingan sana</label>
                                <input type="text" name="symbols_anthem_adopted[uz]" class="form-control" value="{{ $adopted->value_uz ?? '' }}">
                            </div>

                            {{-- Author Lyrics --}}
                            <div class="col-md-4">
                                @php $authorLyrics = $contents->firstWhere('key', 'symbols_anthem_author_lyrics'); @endphp
                                <label class="form-label fw-semibold">So'z muallifi</label>
                                <input type="text" name="symbols_anthem_author_lyrics[uz]" class="form-control" value="{{ $authorLyrics->value_uz ?? '' }}">
                            </div>

                            {{-- Author Music --}}
                            <div class="col-md-4">
                                @php $authorMusic = $contents->firstWhere('key', 'symbols_anthem_author_music'); @endphp
                                <label class="form-label fw-semibold">Musiqa muallifi</label>
                                <input type="text" name="symbols_anthem_author_music[uz]" class="form-control" value="{{ $authorMusic->value_uz ?? '' }}">
                            </div>
                        </div>

                        {{-- Audio File --}}
                        @php
                            $audio     = $contents->firstWhere('key', 'symbols_anthem_audio');
                            $audioPath = $audio->value_uz ?? '';
                            $audioUrl  = !empty($audioPath)
                                ? (str_starts_with($audioPath, 'http') ? $audioPath : asset($audioPath))
                                : '';
                        @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Madhiya audio fayli</label>
                            @if($audioUrl)
                                <div class="mb-2">
                                    <audio controls class="w-100">
                                        <source src="{{ $audioUrl }}">
                                        Brauzeringiz audio fayllarni qo'llab-quvvatlamaydi.
                                    </audio>
                                </div>
                            @endif
                            <input type="file" name="symbols_anthem_audio" class="form-control" accept="audio/*">
                            <div class="form-text">MP3 yoki WAV formatda</div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3"><i class="fas fa-align-left me-2"></i>Madhiya matni</h6>

                        {{-- Verse 1 --}}
                        @php $verse1 = $contents->firstWhere('key', 'symbols_anthem_verse1'); @endphp
                        <div class="verse-card">
                            <label class="form-label fw-semibold">1-band</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small">O'zbekcha</label>
                                    <textarea name="symbols_anthem_verse1[uz]" class="form-control" rows="5">{{ $verse1->value_uz ?? '' }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">English</label>
                                    <textarea name="symbols_anthem_verse1[en]" class="form-control" rows="5">{{ $verse1->value_en ?? '' }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Русский</label>
                                    <textarea name="symbols_anthem_verse1[ru]" class="form-control" rows="5">{{ $verse1->value_ru ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Chorus --}}
                        @php $chorus = $contents->firstWhere('key', 'symbols_anthem_chorus'); @endphp
                        <div class="chorus-card">
                            <label class="form-label fw-semibold">Naqorat</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small">O'zbekcha</label>
                                    <textarea name="symbols_anthem_chorus[uz]" class="form-control" rows="5">{{ $chorus->value_uz ?? '' }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">English</label>
                                    <textarea name="symbols_anthem_chorus[en]" class="form-control" rows="5">{{ $chorus->value_en ?? '' }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Русский</label>
                                    <textarea name="symbols_anthem_chorus[ru]" class="form-control" rows="5">{{ $chorus->value_ru ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Verse 2 --}}
                        @php $verse2 = $contents->firstWhere('key', 'symbols_anthem_verse2'); @endphp
                        <div class="verse-card">
                            <label class="form-label fw-semibold">2-band</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small">O'zbekcha</label>
                                    <textarea name="symbols_anthem_verse2[uz]" class="form-control" rows="5">{{ $verse2->value_uz ?? '' }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">English</label>
                                    <textarea name="symbols_anthem_verse2[en]" class="form-control" rows="5">{{ $verse2->value_en ?? '' }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Русский</label>
                                    <textarea name="symbols_anthem_verse2[ru]" class="form-control" rows="5">{{ $verse2->value_ru ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Section --}}
            <div class="tab-pane fade" id="footer">
                <div class="content-card">
                    <div class="section-header footer">
                        <h5 class="mb-0"><i class="fas fa-align-left me-2"></i>Xulosa Bo'limi</h5>
                    </div>
                    <div class="p-4">
                        {{-- Footer Title --}}
                        @php $footerTitle = $contents->firstWhere('key', 'symbols_footer_title'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Sarlavha</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">UZ</span>
                                        <input type="text" name="symbols_footer_title[uz]" class="form-control" value="{{ $footerTitle->value_uz ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">EN</span>
                                        <input type="text" name="symbols_footer_title[en]" class="form-control" value="{{ $footerTitle->value_en ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">RU</span>
                                        <input type="text" name="symbols_footer_title[ru]" class="form-control" value="{{ $footerTitle->value_ru ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Text --}}
                        @php $footerText = $contents->firstWhere('key', 'symbols_footer_text'); @endphp
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Matn</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small">O'zbekcha</label>
                                    <textarea name="symbols_footer_text[uz]" class="form-control" rows="4">{{ $footerText->value_uz ?? '' }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">English</label>
                                    <textarea name="symbols_footer_text[en]" class="form-control" rows="4">{{ $footerText->value_en ?? '' }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Русский</label>
                                    <textarea name="symbols_footer_text[ru]" class="form-control" rows="4">{{ $footerText->value_ru ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="card mt-4">
            <div class="card-body d-flex justify-content-end gap-2">
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo me-1"></i> Bekor qilish
                </button>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-1"></i> Saqlash
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
(function () {
    const UPLOAD_URL = '{{ route('cms.state-symbols.upload-image') }}';
    const DELETE_URL = '{{ route('cms.state-symbols.delete-image') }}';
    const CSRF      = '{{ csrf_token() }}';

    document.querySelectorAll('.image-upload-wrapper').forEach(function (wrapper) {
        const key        = wrapper.dataset.imageKey;
        const input      = wrapper.querySelector('[data-image-input]');
        const preview    = wrapper.querySelector('[data-preview-target]');
        const deleteBtn  = wrapper.querySelector('[data-image-delete]');
        const statusEl   = wrapper.querySelector('.image-status');

        function setStatus(msg, color) {
            statusEl.textContent = msg;
            statusEl.style.color = color || '#6b7280';
        }

        function setPreview(url) {
            preview.innerHTML = '<img src="' + url + '" class="img-thumbnail" style="max-height:150px;" data-current-image>';
        }

        function clearPreview() {
            preview.innerHTML = '<div class="image-placeholder text-muted small p-3 border rounded text-center">Rasm tanlanmagan</div>';
        }

        if (input) {
            input.addEventListener('change', function () {
                const file = input.files[0];
                if (!file) return;

                setStatus('Yuklanmoqda...', '#2563eb');

                const formData = new FormData();
                formData.append('key', key);
                formData.append('image', file);
                formData.append('_token', CSRF);

                fetch(UPLOAD_URL, { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(function (data) {
                        if (data.success) {
                            setPreview(data.url);
                            if (deleteBtn) deleteBtn.style.display = '';
                            setStatus('✓ Muvaffaqiyatli yuklandi', '#16a34a');
                        } else {
                            setStatus('✗ Xato: ' + (data.message || 'Yuklashda xato'), '#dc2626');
                        }
                        input.value = '';
                    })
                    .catch(function () {
                        setStatus('✗ Server xatosi', '#dc2626');
                        input.value = '';
                    });
            });
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', function () {
                if (!confirm('Rasmni o\'chirmoqchimisiz?')) return;

                setStatus('O\'chirilmoqda...', '#dc2626');

                fetch(DELETE_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: '_token=' + encodeURIComponent(CSRF) + '&key=' + encodeURIComponent(key),
                })
                    .then(r => r.json())
                    .then(function (data) {
                        if (data.success) {
                            clearPreview();
                            deleteBtn.style.display = 'none';
                            setStatus('Rasm o\'chirildi', '#6b7280');
                        } else {
                            setStatus('✗ ' + (data.message || 'Xato'), '#dc2626');
                        }
                    })
                    .catch(function () {
                        setStatus('✗ Server xatosi', '#dc2626');
                    });
            });
        }
    });
})();
</script>
@endpush
