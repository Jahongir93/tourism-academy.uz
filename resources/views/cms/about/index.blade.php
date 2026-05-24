@extends('layouts.dashboard-new')

@section('title', 'Biz haqimizda - CMS')
@section('page-title', 'Biz haqimizda sahifasini tahrirlash')

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
    .section-header.cert { background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%); }
    .section-header.mgmt { background: linear-gradient(135deg, #065f46 0%, #047857 100%); }
    .flag-icon {
        width: 24px;
        height: 16px;
        object-fit: cover;
        border-radius: 2px;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }
    .item-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
    }

    /* Image upload widget */
    .image-upload-wrapper { position: relative; }
    .image-preview-box {
        position: relative;
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        overflow: hidden;
        background: #f8fafc;
        margin-bottom: 8px;
        min-height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: border-color 0.2s, background 0.2s;
        cursor: pointer;
    }
    .image-preview-box:hover { border-color: #4f46e5; background: #f1f5ff; }
    .image-preview-box img {
        width: 100%;
        height: 100%;
        max-height: 200px;
        object-fit: cover;
        display: block;
    }
    .image-placeholder {
        color: #94a3b8;
        text-align: center;
        padding: 20px;
        font-size: 0.85rem;
    }
    .image-placeholder i { font-size: 1.8rem; margin-bottom: 6px; display: block; }
    .image-preview-box.loading::before {
        content: ''; position: absolute; inset: 0;
        background: rgba(255,255,255,0.7); z-index: 1;
    }
    .image-preview-box.loading::after {
        content: ''; position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 32px; height: 32px;
        border: 3px solid #cbd5e1; border-top-color: #4f46e5;
        border-radius: 50%; z-index: 2;
        animation: imgspin 0.7s linear infinite;
    }
    @keyframes imgspin { to { transform: translate(-50%, -50%) rotate(360deg); } }
</style>
@endsection

@section('content')

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="fas fa-info-circle text-primary me-2"></i>Biz haqimizda sahifasini tahrirlash
                    </h1>
                    <p class="text-muted mb-0">Sahifani uchta tilda tahrirlang</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cms.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Orqaga
                    </a>
                    <a href="{{ route('about') }}" target="_blank" class="btn btn-info">
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
            <a class="nav-link active" data-bs-toggle="pill" href="#hero"><i class="fas fa-star me-1"></i> Hero</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#certificate"><i class="fas fa-certificate me-1"></i> Sertifikat</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#management"><i class="fas fa-users me-1"></i> Rahbariyat</a>
        </li>
    </ul>

    <form action="{{ route('cms.about.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="tab-content">
            {{-- Hero Section --}}
            <div class="tab-pane fade show active" id="hero">
                <div class="content-card">
                    <div class="section-header hero">
                        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Hero Bo'limi</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $heroFields = ['about_hero_badge', 'about_hero_title_1', 'about_hero_title_highlight', 'about_hero_title_2', 'about_hero_description', 'about_hero_date'];
                        @endphp

                        @foreach($heroFields as $field)
                            @php $content = $contents->firstWhere('key', $field); @endphp
                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ ucwords(str_replace(['about_hero_', '_'], ['', ' '], $field)) }}</label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">🇺🇿</span>
                                            @if($field == 'about_hero_description')
                                                <textarea name="{{ $field }}[uz]" class="form-control" rows="3">{{ $content->value_uz ?? '' }}</textarea>
                                            @else
                                                <input type="text" name="{{ $field }}[uz]" class="form-control" value="{{ $content->value_uz ?? '' }}">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">🇬🇧</span>
                                            @if($field == 'about_hero_description')
                                                <textarea name="{{ $field }}[en]" class="form-control" rows="3">{{ $content->value_en ?? '' }}</textarea>
                                            @else
                                                <input type="text" name="{{ $field }}[en]" class="form-control" value="{{ $content->value_en ?? '' }}">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">🇷🇺</span>
                                            @if($field == 'about_hero_description')
                                                <textarea name="{{ $field }}[ru]" class="form-control" rows="3">{{ $content->value_ru ?? '' }}</textarea>
                                            @else
                                                <input type="text" name="{{ $field }}[ru]" class="form-control" value="{{ $content->value_ru ?? '' }}">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Certificate Section --}}
            <div class="tab-pane fade" id="certificate">
                <div class="content-card">
                    <div class="section-header cert">
                        <h5 class="mb-0"><i class="fas fa-certificate me-2"></i>UN Tourism Sertifikat Bo'limi</h5>
                    </div>
                    <div class="p-4">
                        {{-- Section titles --}}
                        @php $certTitle = $contents->firstWhere('key', 'about_cert_title'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇺🇿</span>
                                        <input type="text" name="about_cert_title[uz]" class="form-control" value="{{ $certTitle->value_uz ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇬🇧</span>
                                        <input type="text" name="about_cert_title[en]" class="form-control" value="{{ $certTitle->value_en ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇷🇺</span>
                                        <input type="text" name="about_cert_title[ru]" class="form-control" value="{{ $certTitle->value_ru ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php $certSubtitle = $contents->firstWhere('key', 'about_cert_subtitle'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bo'lim tavsifi</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇺🇿</span>
                                        <input type="text" name="about_cert_subtitle[uz]" class="form-control" value="{{ $certSubtitle->value_uz ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇬🇧</span>
                                        <input type="text" name="about_cert_subtitle[en]" class="form-control" value="{{ $certSubtitle->value_en ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇷🇺</span>
                                        <input type="text" name="about_cert_subtitle[ru]" class="form-control" value="{{ $certSubtitle->value_ru ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- 4 Certificate Items --}}
                        <div class="row">
                            @for($i = 1; $i <= 4; $i++)
                                <div class="col-md-6 mb-4">
                                    <div class="item-card">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-award text-primary me-2"></i>Sertifikat xususiyati {{ $i }}</h6>

                                        @php $icon = $contents->firstWhere('key', "about_cert{$i}_icon"); @endphp
                                        <div class="mb-3">
                                            <label class="form-label small">Icon (Font Awesome)</label>
                                            <input type="text" name="about_cert{{ $i }}_icon[uz]" class="form-control form-control-sm" value="{{ $icon->value_uz ?? '' }}" placeholder="fas fa-globe">
                                        </div>

                                        @php $title = $contents->firstWhere('key', "about_cert{$i}_title"); @endphp
                                        <div class="mb-3">
                                            <label class="form-label small">Sarlavha</label>
                                            <div class="row g-1">
                                                <div class="col-4"><input type="text" name="about_cert{{ $i }}_title[uz]" class="form-control form-control-sm" value="{{ $title->value_uz ?? '' }}" placeholder="O'zbekcha"></div>
                                                <div class="col-4"><input type="text" name="about_cert{{ $i }}_title[en]" class="form-control form-control-sm" value="{{ $title->value_en ?? '' }}" placeholder="English"></div>
                                                <div class="col-4"><input type="text" name="about_cert{{ $i }}_title[ru]" class="form-control form-control-sm" value="{{ $title->value_ru ?? '' }}" placeholder="Русский"></div>
                                            </div>
                                        </div>

                                        @php $text = $contents->firstWhere('key', "about_cert{$i}_text"); @endphp
                                        <div class="mb-0">
                                            <label class="form-label small">Matn</label>
                                            <div class="row g-1">
                                                <div class="col-4"><input type="text" name="about_cert{{ $i }}_text[uz]" class="form-control form-control-sm" value="{{ $text->value_uz ?? '' }}"></div>
                                                <div class="col-4"><input type="text" name="about_cert{{ $i }}_text[en]" class="form-control form-control-sm" value="{{ $text->value_en ?? '' }}"></div>
                                                <div class="col-4"><input type="text" name="about_cert{{ $i }}_text[ru]" class="form-control form-control-sm" value="{{ $text->value_ru ?? '' }}"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- Management Section --}}
            <div class="tab-pane fade" id="management">
                <div class="content-card">
                    <div class="section-header mgmt">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Rahbariyat Bo'limi</h5>
                    </div>
                    <div class="p-4">
                        {{-- Section titles --}}
                        @php $mgmtTitle = $contents->firstWhere('key', 'about_mgmt_title'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇺🇿</span>
                                        <input type="text" name="about_mgmt_title[uz]" class="form-control" value="{{ $mgmtTitle->value_uz ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇬🇧</span>
                                        <input type="text" name="about_mgmt_title[en]" class="form-control" value="{{ $mgmtTitle->value_en ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇷🇺</span>
                                        <input type="text" name="about_mgmt_title[ru]" class="form-control" value="{{ $mgmtTitle->value_ru ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php $mgmtSubtitle = $contents->firstWhere('key', 'about_mgmt_subtitle'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bo'lim tavsifi</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇺🇿</span>
                                        <input type="text" name="about_mgmt_subtitle[uz]" class="form-control" value="{{ $mgmtSubtitle->value_uz ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇬🇧</span>
                                        <input type="text" name="about_mgmt_subtitle[en]" class="form-control" value="{{ $mgmtSubtitle->value_en ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">🇷🇺</span>
                                        <input type="text" name="about_mgmt_subtitle[ru]" class="form-control" value="{{ $mgmtSubtitle->value_ru ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- 3 Management Items --}}
                        <div class="row">
                            @for($i = 1; $i <= 3; $i++)
                                <div class="col-md-4 mb-4">
                                    <div class="item-card">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-user-tie text-success me-2"></i>Rahbar {{ $i }}</h6>

                                        @php $icon = $contents->firstWhere('key', "about_mgmt{$i}_icon"); @endphp
                                        <div class="mb-3">
                                            <label class="form-label small">Icon</label>
                                            <input type="text" name="about_mgmt{{ $i }}_icon[uz]" class="form-control form-control-sm" value="{{ $icon->value_uz ?? '' }}">
                                        </div>

                                        @php $title = $contents->firstWhere('key', "about_mgmt{$i}_title"); @endphp
                                        <div class="mb-3">
                                            <label class="form-label small">Lavozim</label>
                                            <input type="text" name="about_mgmt{{ $i }}_title[uz]" class="form-control form-control-sm mb-1" value="{{ $title->value_uz ?? '' }}" placeholder="O'zbekcha">
                                            <input type="text" name="about_mgmt{{ $i }}_title[en]" class="form-control form-control-sm mb-1" value="{{ $title->value_en ?? '' }}" placeholder="English">
                                            <input type="text" name="about_mgmt{{ $i }}_title[ru]" class="form-control form-control-sm" value="{{ $title->value_ru ?? '' }}" placeholder="Русский">
                                        </div>

                                        @php $role = $contents->firstWhere('key', "about_mgmt{$i}_role"); @endphp
                                        <div class="mb-3">
                                            <label class="form-label small">Rol</label>
                                            <input type="text" name="about_mgmt{{ $i }}_role[uz]" class="form-control form-control-sm mb-1" value="{{ $role->value_uz ?? '' }}">
                                            <input type="text" name="about_mgmt{{ $i }}_role[en]" class="form-control form-control-sm mb-1" value="{{ $role->value_en ?? '' }}">
                                            <input type="text" name="about_mgmt{{ $i }}_role[ru]" class="form-control form-control-sm" value="{{ $role->value_ru ?? '' }}">
                                        </div>

                                        @php $text = $contents->firstWhere('key', "about_mgmt{$i}_text"); @endphp
                                        <div class="mb-3">
                                            <label class="form-label small">Tavsif</label>
                                            <textarea name="about_mgmt{{ $i }}_text[uz]" class="form-control form-control-sm mb-1" rows="2">{{ $text->value_uz ?? '' }}</textarea>
                                            <textarea name="about_mgmt{{ $i }}_text[en]" class="form-control form-control-sm mb-1" rows="2">{{ $text->value_en ?? '' }}</textarea>
                                            <textarea name="about_mgmt{{ $i }}_text[ru]" class="form-control form-control-sm" rows="2">{{ $text->value_ru ?? '' }}</textarea>
                                        </div>

                                        @php
                                            $image = $contents->firstWhere('key', "about_mgmt{$i}_image");
                                            $hasImg = $image && $image->value_uz;
                                            $isCustomUpload = $hasImg && !\Illuminate\Support\Str::startsWith($image->value_uz, 'http');
                                            if ($hasImg) {
                                                if (\Illuminate\Support\Str::startsWith($image->value_uz, 'http')) $imgSrc = $image->value_uz;
                                                elseif (\Illuminate\Support\Str::startsWith($image->value_uz, 'uploads/')) $imgSrc = asset($image->value_uz);
                                                else $imgSrc = asset('storage/' . $image->value_uz);
                                            }
                                        @endphp
                                        <div class="mb-0">
                                            <label class="form-label small">Rasm</label>
                                            <div class="image-upload-wrapper" data-image-key="about_mgmt{{ $i }}_image">
                                                <div class="image-preview-box" data-preview-target>
                                                    @if($hasImg)
                                                        <img src="{{ $imgSrc }}" alt="Rahbar {{ $i }}" data-current-image>
                                                    @else
                                                        <div class="image-placeholder">
                                                            <i class="fas fa-user-tie"></i>
                                                            <div>Rasm tanlang</div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex gap-2 mt-2">
                                                    <input type="file" class="form-control form-control-sm flex-grow-1" accept="image/*" data-image-input>
                                                    <button type="button" class="btn btn-sm btn-danger" data-image-delete style="{{ $isCustomUpload ? '' : 'display:none' }}" title="Rasmni o'chirish">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="image-status small mt-1"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
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

<script>
    (function initAboutImageUploads() {
        const UPLOAD_URL = @json(route('cms.about.upload-image'));
        const DELETE_URL = @json(route('cms.about.delete-image'));
        const CSRF = @json(csrf_token());

        function showStatus(wrapper, type, message) {
            const status = wrapper.querySelector('.image-status');
            if (!status) return;
            const colors = { success: 'text-success', error: 'text-danger', loading: 'text-primary' };
            const icons = { success: 'check-circle', error: 'exclamation-circle', loading: 'spinner fa-spin' };
            status.className = 'image-status small mt-1 ' + (colors[type] || '');
            status.innerHTML = '<i class="fas fa-' + (icons[type] || 'info-circle') + ' me-1"></i>' + message;
            if (type === 'success') setTimeout(() => { status.innerHTML = ''; }, 3000);
        }

        function setPreviewImage(previewBox, url) {
            previewBox.innerHTML = '<img src="' + url + '" alt="Rasm" data-current-image>';
        }

        function setPreviewPlaceholder(previewBox) {
            previewBox.innerHTML = '<div class="image-placeholder"><i class="fas fa-user-tie"></i><div>Rasm tanlang</div></div>';
        }

        async function uploadImage(wrapper, file) {
            const previewBox = wrapper.querySelector('[data-preview-target]');
            const deleteBtn = wrapper.querySelector('[data-image-delete]');
            const key = wrapper.dataset.imageKey;

            if (!file.type.startsWith('image/')) {
                showStatus(wrapper, 'error', 'Faqat rasm fayllari');
                return;
            }
            if (file.size > 20 * 1024 * 1024) {
                showStatus(wrapper, 'error', 'Rasm 20MB dan katta');
                return;
            }

            previewBox.classList.add('loading');
            showStatus(wrapper, 'loading', 'Yuklanmoqda...');

            const fd = new FormData();
            fd.append('image', file);
            fd.append('key', key);
            fd.append('_token', CSRF);

            try {
                const res = await fetch(UPLOAD_URL, {
                    method: 'POST', body: fd,
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                let data;
                try { data = await res.json(); }
                catch (e) { showStatus(wrapper, 'error', 'Server javobi noto\'g\'ri (HTTP ' + res.status + ')'); return; }
                if (!res.ok || !data.success) {
                    showStatus(wrapper, 'error', data.error || ('Xato (HTTP ' + res.status + ')'));
                    return;
                }
                setPreviewImage(previewBox, data.url);
                if (deleteBtn) deleteBtn.style.display = '';
                showStatus(wrapper, 'success', data.message || 'Saqlandi');
            } catch (err) {
                showStatus(wrapper, 'error', 'Tarmoq xatosi: ' + err.message);
            } finally {
                previewBox.classList.remove('loading');
            }
        }

        async function deleteImage(wrapper) {
            const previewBox = wrapper.querySelector('[data-preview-target]');
            const deleteBtn = wrapper.querySelector('[data-image-delete]');
            const fileInput = wrapper.querySelector('[data-image-input]');
            const key = wrapper.dataset.imageKey;

            if (!confirm('Rasmni o\'chirishni tasdiqlaysizmi?')) return;

            previewBox.classList.add('loading');
            showStatus(wrapper, 'loading', 'O\'chirilmoqda...');

            try {
                const fd = new URLSearchParams();
                fd.append('key', key);
                fd.append('_token', CSRF);
                const res = await fetch(DELETE_URL, {
                    method: 'POST', body: fd,
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const data = await res.json();
                if (!res.ok || !data.success) {
                    showStatus(wrapper, 'error', data.error || 'O\'chirib bo\'lmadi');
                    return;
                }
                setPreviewPlaceholder(previewBox);
                if (deleteBtn) deleteBtn.style.display = 'none';
                if (fileInput) fileInput.value = '';
                showStatus(wrapper, 'success', 'O\'chirildi');
            } catch (err) {
                showStatus(wrapper, 'error', 'Tarmoq xatosi: ' + err.message);
            } finally {
                previewBox.classList.remove('loading');
            }
        }

        function init() {
            document.querySelectorAll('.image-upload-wrapper[data-image-key]').forEach(wrapper => {
                if (wrapper.dataset.bound === '1') return;
                wrapper.dataset.bound = '1';
                const fileInput = wrapper.querySelector('[data-image-input]');
                const deleteBtn = wrapper.querySelector('[data-image-delete]');
                const previewBox = wrapper.querySelector('[data-preview-target]');
                if (previewBox && fileInput) {
                    previewBox.addEventListener('click', e => {
                        if (e.target.closest('button')) return;
                        fileInput.click();
                    });
                }
                if (fileInput) {
                    fileInput.addEventListener('change', () => {
                        const file = fileInput.files && fileInput.files[0];
                        if (file) uploadImage(wrapper, file);
                        fileInput.value = '';
                    });
                }
                if (deleteBtn) deleteBtn.addEventListener('click', () => deleteImage(wrapper));
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else { init(); }
    })();
</script>
@endsection
