@extends('layouts.dashboard-new')

@section('title', 'Footer tahrirlash - CMS')
@section('page-title', 'Footer tahrirlash')

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
    .section-header.success {
        background: linear-gradient(135deg, #065f46 0%, #047857 100%);
    }
    .section-header.warning {
        background: linear-gradient(135deg, #92400e 0%, #b45309 100%);
    }
    .section-header.info {
        background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
    }
    .section-header.danger {
        background: linear-gradient(135deg, #991b1b 0%, #b91c1c 100%);
    }
    .link-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
    }
    .link-item:hover {
        border-color: #4f46e5;
        background: #f1f5f9;
    }
    .social-icon-preview {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #1e1b4b;
        color: white;
        font-size: 18px;
    }
    .flag-icon {
        width: 24px;
        height: 16px;
        object-fit: cover;
        border-radius: 2px;
    }
    .preview-footer {
        background: #0D0D0D;
        color: white;
        padding: 30px;
        border-radius: 8px;
    }
    .preview-footer h6 {
        color: white;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .preview-footer a {
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
    }
    .preview-footer a:hover {
        color: #D7FF37;
    }
    .preview-footer-bottom {
        border-top: 3px solid #D7FF37;
        padding-top: 20px;
        margin-top: 30px;
    }
    .cms-config-modal {
        z-index: 20050;
    }
    body > .modal-backdrop {
        z-index: 20040;
    }
    .cms-config-modal .modal-dialog {
        max-width: min(640px, calc(100vw - 24px));
    }
    .cms-config-modal .modal-content {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.25);
        overflow: hidden;
    }
    .cms-config-modal .modal-body {
        max-height: calc(100vh - 190px);
        overflow-y: auto;
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
                        <i class="fas fa-shoe-prints text-primary me-2"></i>Footer tahrirlash
                    </h1>
                    <p class="text-muted mb-0">Sayt footerini uchta tilda tahrirlang</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cms.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Orqaga
                    </a>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#previewModal">
                        <i class="fas fa-eye me-1"></i> Ko'rish
                    </button>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><strong>Xatoliklar:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('cms.header-footer.footer.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            {{-- Column 1: Logo & Description --}}
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="section-header">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>1-ustun: Logo va tavsif</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $footerLogo = $contents->where('key', 'footer_logo')->first();
                            $footerTitle = $contents->where('key', 'footer_title')->first();
                            $footerDesc = $contents->where('key', 'footer_description')->first();
                        @endphp

                        {{-- Logo --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Footer logosi</label>
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <img id="footer_logo_preview"
                                         src="{{ $footerLogo && $footerLogo->value_uz ? \App\Support\CmsHeaderFooter::assetUrl($footerLogo->value_uz) : '' }}"
                                         alt="Footer Logo"
                                         style="height: 50px; background: #1a1a2e; padding: 10px; border-radius: 6px;{{ ($footerLogo && $footerLogo->value_uz) ? '' : 'display:none' }}">
                                </div>
                                <div class="col">
                                    {{-- name yo'q + data-no-waf: WAF-safe, AJAX orqali yuklanadi --}}
                                    <input type="file" id="footer_logo_input" class="form-control" accept="image/*" data-no-waf>
                                    <input type="hidden" name="footer_logo_path" id="footer_logo_path">
                                    <div id="footer_logo_status" style="font-size:12px;margin-top:4px"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Title --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Sarlavha</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><img src="{{ asset('assets/flags/uz.png') }}" class="flag-icon"></span>
                                        <input type="text" name="contents[footer_title][value_uz]"
                                               value="{{ $footerTitle->value_uz ?? 'Tourism Academy' }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><img src="{{ asset('assets/flags/en.png') }}" class="flag-icon"></span>
                                        <input type="text" name="contents[footer_title][value_en]"
                                               value="{{ $footerTitle->value_en ?? '' }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><img src="{{ asset('assets/flags/ru.png') }}" class="flag-icon"></span>
                                        <input type="text" name="contents[footer_title][value_ru]"
                                               value="{{ $footerTitle->value_ru ?? '' }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="contents[footer_title][key]" value="footer_title">
                            <input type="hidden" name="contents[footer_title][type]" value="text">
                            <input type="hidden" name="contents[footer_title][order]" value="2">
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tavsif</label>
                            <ul class="nav nav-tabs nav-tabs-sm mb-2" role="tablist">
                                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#desc_uz">UZ</a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#desc_en">EN</a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#desc_ru">RU</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="desc_uz">
                                    <textarea name="contents[footer_description][value_uz]" class="form-control" rows="3">{{ $footerDesc->value_uz ?? '' }}</textarea>
                                </div>
                                <div class="tab-pane fade" id="desc_en">
                                    <textarea name="contents[footer_description][value_en]" class="form-control" rows="3">{{ $footerDesc->value_en ?? '' }}</textarea>
                                </div>
                                <div class="tab-pane fade" id="desc_ru">
                                    <textarea name="contents[footer_description][value_ru]" class="form-control" rows="3">{{ $footerDesc->value_ru ?? '' }}</textarea>
                                </div>
                            </div>
                            <input type="hidden" name="contents[footer_description][key]" value="footer_description">
                            <input type="hidden" name="contents[footer_description][type]" value="textarea">
                            <input type="hidden" name="contents[footer_description][order]" value="3">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Column 4: Contact --}}
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="section-header info">
                        <h5 class="mb-0"><i class="fas fa-address-card me-2"></i>4-ustun: Bog'lanish</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $col4Title = $contents->where('key', 'col4_title')->first();
                            $address = $contents->where('key', 'contact_address')->first();
                            $phone = $contents->where('key', 'contact_phone')->first();
                            $email = $contents->where('key', 'contact_email')->first();
                            $newsletter = $contents->where('key', 'newsletter_placeholder')->first();
                        @endphp

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ustun sarlavhasi</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="text" name="contents[col4_title][value_uz]" value="{{ $col4Title->value_uz ?? "Bog'lanish" }}" class="form-control form-control-sm" placeholder="UZ">
                                </div>
                                <div class="col-4">
                                    <input type="text" name="contents[col4_title][value_en]" value="{{ $col4Title->value_en ?? 'Contact' }}" class="form-control form-control-sm" placeholder="EN">
                                </div>
                                <div class="col-4">
                                    <input type="text" name="contents[col4_title][value_ru]" value="{{ $col4Title->value_ru ?? 'Контакты' }}" class="form-control form-control-sm" placeholder="RU">
                                </div>
                            </div>
                            <input type="hidden" name="contents[col4_title][key]" value="col4_title">
                            <input type="hidden" name="contents[col4_title][type]" value="text">
                            <input type="hidden" name="contents[col4_title][order]" value="50">
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt text-danger me-1"></i> Manzil</label>
                            <div class="row g-2">
                                <div class="col-4"><input type="text" name="contents[contact_address][value_uz]" value="{{ $address->value_uz ?? '' }}" class="form-control form-control-sm"></div>
                                <div class="col-4"><input type="text" name="contents[contact_address][value_en]" value="{{ $address->value_en ?? '' }}" class="form-control form-control-sm"></div>
                                <div class="col-4"><input type="text" name="contents[contact_address][value_ru]" value="{{ $address->value_ru ?? '' }}" class="form-control form-control-sm"></div>
                            </div>
                            <input type="hidden" name="contents[contact_address][key]" value="contact_address">
                            <input type="hidden" name="contents[contact_address][type]" value="text">
                            <input type="hidden" name="contents[contact_address][order]" value="51">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-phone text-success me-1"></i> Telefon</label>
                                <input type="text" name="contents[contact_phone][value_uz]" value="{{ $phone->value_uz ?? '' }}" class="form-control form-control-sm">
                                <input type="hidden" name="contents[contact_phone][key]" value="contact_phone">
                                <input type="hidden" name="contents[contact_phone][type]" value="text">
                                <input type="hidden" name="contents[contact_phone][order]" value="52">
                                <input type="hidden" name="contents[contact_phone][value_en]" value="{{ $phone->value_en ?? '' }}">
                                <input type="hidden" name="contents[contact_phone][value_ru]" value="{{ $phone->value_ru ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-envelope text-primary me-1"></i> Email</label>
                                <input type="text" name="contents[contact_email][value_uz]" value="{{ $email->value_uz ?? '' }}" class="form-control form-control-sm">
                                <input type="hidden" name="contents[contact_email][key]" value="contact_email">
                                <input type="hidden" name="contents[contact_email][type]" value="text">
                                <input type="hidden" name="contents[contact_email][order]" value="53">
                                <input type="hidden" name="contents[contact_email][value_en]" value="{{ $email->value_en ?? '' }}">
                                <input type="hidden" name="contents[contact_email][value_ru]" value="{{ $email->value_ru ?? '' }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-paper-plane text-warning me-1"></i> Newsletter placeholder</label>
                            <div class="row g-2">
                                <div class="col-4"><input type="text" name="contents[newsletter_placeholder][value_uz]" value="{{ $newsletter->value_uz ?? 'Email manzilingiz' }}" class="form-control form-control-sm"></div>
                                <div class="col-4"><input type="text" name="contents[newsletter_placeholder][value_en]" value="{{ $newsletter->value_en ?? 'Your email' }}" class="form-control form-control-sm"></div>
                                <div class="col-4"><input type="text" name="contents[newsletter_placeholder][value_ru]" value="{{ $newsletter->value_ru ?? 'Ваш email' }}" class="form-control form-control-sm"></div>
                            </div>
                            <input type="hidden" name="contents[newsletter_placeholder][key]" value="newsletter_placeholder">
                            <input type="hidden" name="contents[newsletter_placeholder][type]" value="text">
                            <input type="hidden" name="contents[newsletter_placeholder][order]" value="54">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Column 2: Information --}}
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="section-header success">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>2-ustun: Ma'lumot</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $col2Title = $contents->where('key', 'col2_title')->first();
                            $col2Links = $contents->filter(function($item) {
                                return str_starts_with($item->key, 'col2_link') && str_ends_with($item->key, '_text');
                            })->sortBy('order');
                        @endphp

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ustun sarlavhasi</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="text" name="contents[col2_title][value_uz]" value="{{ $col2Title->value_uz ?? "Ma'lumot" }}" class="form-control form-control-sm" placeholder="UZ">
                                </div>
                                <div class="col-4">
                                    <input type="text" name="contents[col2_title][value_en]" value="{{ $col2Title->value_en ?? 'Information' }}" class="form-control form-control-sm" placeholder="EN">
                                </div>
                                <div class="col-4">
                                    <input type="text" name="contents[col2_title][value_ru]" value="{{ $col2Title->value_ru ?? 'Информация' }}" class="form-control form-control-sm" placeholder="RU">
                                </div>
                            </div>
                            <input type="hidden" name="contents[col2_title][key]" value="col2_title">
                            <input type="hidden" name="contents[col2_title][type]" value="text">
                            <input type="hidden" name="contents[col2_title][order]" value="10">
                        </div>

                        <label class="form-label fw-semibold">Havolalar</label>
                        <div class="link-list" id="col2LinkList">
                        @foreach($col2Links as $link)
                            @php
                                $urlKey = str_replace('_text', '_url', $link->key);
                                $urlContent = $contents->where('key', $urlKey)->first();
                            @endphp
                            <div class="link-item d-flex align-items-center justify-content-between p-2 mb-2 bg-light rounded">
                                <div class="link-info flex-grow-1">
                                    <strong>{{ $link->value_uz }}</strong>
                                    <small class="text-muted d-block">{{ $urlContent->value_uz ?? '#' }}</small>
                                </div>
                                <div class="link-actions">
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-link-btn me-1"
                                        data-link-key="{{ $link->key }}"
                                        data-url-key="{{ $urlKey }}"
                                        data-text-uz="{{ $link->value_uz }}"
                                        data-text-en="{{ $link->value_en }}"
                                        data-text-ru="{{ $link->value_ru }}"
                                        data-url="{{ $urlContent->value_uz ?? '#' }}"
                                        data-order="{{ $link->order }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-link-btn" data-link-key="{{ $link->key }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <!-- Hidden form fields -->
                                <input type="hidden" name="contents[{{ $link->key }}][key]" value="{{ $link->key }}">
                                <input type="hidden" name="contents[{{ $link->key }}][type]" value="text">
                                <input type="hidden" name="contents[{{ $link->key }}][order]" value="{{ $link->order }}">
                                <input type="hidden" name="contents[{{ $link->key }}][value_uz]" value="{{ $link->value_uz }}" class="link-text-uz">
                                <input type="hidden" name="contents[{{ $link->key }}][value_en]" value="{{ $link->value_en }}" class="link-text-en">
                                <input type="hidden" name="contents[{{ $link->key }}][value_ru]" value="{{ $link->value_ru }}" class="link-text-ru">
                                <input type="hidden" name="contents[{{ $urlKey }}][key]" value="{{ $urlKey }}">
                                <input type="hidden" name="contents[{{ $urlKey }}][type]" value="text">
                                <input type="hidden" name="contents[{{ $urlKey }}][order]" value="{{ ($urlContent->order ?? $link->order) + 1 }}">
                                <input type="hidden" name="contents[{{ $urlKey }}][value_uz]" value="{{ $urlContent->value_uz ?? '#' }}" class="link-url">
                                <input type="hidden" name="contents[{{ $urlKey }}][value_en]" value="{{ $urlContent->value_uz ?? '#' }}">
                                <input type="hidden" name="contents[{{ $urlKey }}][value_ru]" value="{{ $urlContent->value_uz ?? '#' }}">
                            </div>
                        @endforeach
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#addLinkModal" data-column="col2">
                            <i class="fas fa-plus me-1"></i> Havola qo'shish
                        </button>
                    </div>
                </div>
            </div>

            {{-- Column 3: Services --}}
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="section-header warning">
                        <h5 class="mb-0"><i class="fas fa-concierge-bell me-2"></i>3-ustun: Resurslar</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $col3Title = $contents->where('key', 'col3_title')->first();
                            $col3Links = $contents->filter(function($item) {
                                return str_starts_with($item->key, 'col3_link') && str_ends_with($item->key, '_text');
                            })->sortBy('order');
                        @endphp

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ustun sarlavhasi</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="text" name="contents[col3_title][value_uz]" value="{{ $col3Title->value_uz ?? 'Bizning xizmatlar' }}" class="form-control form-control-sm" placeholder="UZ">
                                </div>
                                <div class="col-4">
                                    <input type="text" name="contents[col3_title][value_en]" value="{{ $col3Title->value_en ?? 'Our Services' }}" class="form-control form-control-sm" placeholder="EN">
                                </div>
                                <div class="col-4">
                                    <input type="text" name="contents[col3_title][value_ru]" value="{{ $col3Title->value_ru ?? 'Наши услуги' }}" class="form-control form-control-sm" placeholder="RU">
                                </div>
                            </div>
                            <input type="hidden" name="contents[col3_title][key]" value="col3_title">
                            <input type="hidden" name="contents[col3_title][type]" value="text">
                            <input type="hidden" name="contents[col3_title][order]" value="30">
                        </div>

                        <label class="form-label fw-semibold">Havolalar</label>
                        <div class="link-list" id="col3LinkList">
                        @foreach($col3Links as $link)
                            @php
                                $urlKey = str_replace('_text', '_url', $link->key);
                                $urlContent = $contents->where('key', $urlKey)->first();
                            @endphp
                            <div class="link-item d-flex align-items-center justify-content-between p-2 mb-2 bg-light rounded">
                                <div class="link-info flex-grow-1">
                                    <strong>{{ $link->value_uz }}</strong>
                                    <small class="text-muted d-block">{{ $urlContent->value_uz ?? '#' }}</small>
                                </div>
                                <div class="link-actions">
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-link-btn me-1"
                                        data-link-key="{{ $link->key }}"
                                        data-url-key="{{ $urlKey }}"
                                        data-text-uz="{{ $link->value_uz }}"
                                        data-text-en="{{ $link->value_en }}"
                                        data-text-ru="{{ $link->value_ru }}"
                                        data-url="{{ $urlContent->value_uz ?? '#' }}"
                                        data-order="{{ $link->order }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-link-btn" data-link-key="{{ $link->key }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <!-- Hidden form fields -->
                                <input type="hidden" name="contents[{{ $link->key }}][key]" value="{{ $link->key }}">
                                <input type="hidden" name="contents[{{ $link->key }}][type]" value="text">
                                <input type="hidden" name="contents[{{ $link->key }}][order]" value="{{ $link->order }}">
                                <input type="hidden" name="contents[{{ $link->key }}][value_uz]" value="{{ $link->value_uz }}" class="link-text-uz">
                                <input type="hidden" name="contents[{{ $link->key }}][value_en]" value="{{ $link->value_en }}" class="link-text-en">
                                <input type="hidden" name="contents[{{ $link->key }}][value_ru]" value="{{ $link->value_ru }}" class="link-text-ru">
                                <input type="hidden" name="contents[{{ $urlKey }}][key]" value="{{ $urlKey }}">
                                <input type="hidden" name="contents[{{ $urlKey }}][type]" value="text">
                                <input type="hidden" name="contents[{{ $urlKey }}][order]" value="{{ ($urlContent->order ?? $link->order) + 1 }}">
                                <input type="hidden" name="contents[{{ $urlKey }}][value_uz]" value="{{ $urlContent->value_uz ?? '#' }}" class="link-url">
                                <input type="hidden" name="contents[{{ $urlKey }}][value_en]" value="{{ $urlContent->value_uz ?? '#' }}">
                                <input type="hidden" name="contents[{{ $urlKey }}][value_ru]" value="{{ $urlContent->value_uz ?? '#' }}">
                            </div>
                        @endforeach
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#addLinkModal" data-column="col3">
                            <i class="fas fa-plus me-1"></i> Havola qo'shish
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Social Media & Copyright --}}
        <div class="row">
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="section-header danger">
                        <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Ijtimoiy tarmoqlar</h5>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            @php
                                $socials = [
                                    'social_facebook' => ['icon' => 'fab fa-facebook-f', 'color' => '#1877f2', 'name' => 'Facebook'],
                                    'social_twitter' => ['icon' => 'fab fa-twitter', 'color' => '#1da1f2', 'name' => 'Twitter'],
                                    'social_instagram' => ['icon' => 'fab fa-instagram', 'color' => '#e4405f', 'name' => 'Instagram'],
                                    'social_youtube' => ['icon' => 'fab fa-youtube', 'color' => '#ff0000', 'name' => 'YouTube'],
                                    'social_linkedin' => ['icon' => 'fab fa-linkedin-in', 'color' => '#0077b5', 'name' => 'LinkedIn'],
                                    'social_telegram' => ['icon' => 'fab fa-telegram-plane', 'color' => '#0088cc', 'name' => 'Telegram'],
                                ];
                                $socialOrder = 60;
                            @endphp

                            @foreach($socials as $key => $social)
                                @php
                                    $content = $contents->where('key', $key)->first();
                                @endphp
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text" style="background: {{ $social['color'] }}; color: white;">
                                            <i class="{{ $social['icon'] }}"></i>
                                        </span>
                                        <input type="text" name="contents[{{ $key }}][value_uz]"
                                               value="{{ $content->value_uz ?? '#' }}"
                                               class="form-control" placeholder="{{ $social['name'] }} URL">
                                    </div>
                                    <input type="hidden" name="contents[{{ $key }}][key]" value="{{ $key }}">
                                    <input type="hidden" name="contents[{{ $key }}][type]" value="text">
                                    <input type="hidden" name="contents[{{ $key }}][order]" value="{{ $socialOrder++ }}">
                                    <input type="hidden" name="contents[{{ $key }}][value_en]" value="{{ $content->value_en ?? '#' }}">
                                    <input type="hidden" name="contents[{{ $key }}][value_ru]" value="{{ $content->value_ru ?? '#' }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="content-card">
                    <div class="section-header" style="background: linear-gradient(135deg, #374151 0%, #4b5563 100%);">
                        <h5 class="mb-0"><i class="fas fa-copyright me-2"></i>Copyright</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $copyright = $contents->where('key', 'copyright_text')->first();
                        @endphp

                        <div class="mb-2">
                            <label class="form-label small text-muted">O'zbekcha</label>
                            <input type="text" name="contents[copyright_text][value_uz]"
                                   value="{{ $copyright->value_uz ?? '© 2025 Tourism Academy. Barcha huquqlar himoyalangan.' }}"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small text-muted">English</label>
                            <input type="text" name="contents[copyright_text][value_en]"
                                   value="{{ $copyright->value_en ?? '© 2025 Tourism Academy. All rights reserved.' }}"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small text-muted">Русский</label>
                            <input type="text" name="contents[copyright_text][value_ru]"
                                   value="{{ $copyright->value_ru ?? '© 2025 Tourism Academy. Все права защищены.' }}"
                                   class="form-control form-control-sm">
                        </div>
                        <input type="hidden" name="contents[copyright_text][key]" value="copyright_text">
                        <input type="hidden" name="contents[copyright_text][type]" value="text">
                        <input type="hidden" name="contents[copyright_text][order]" value="70">
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex gap-2 justify-content-end mt-4">
            <a href="{{ route('cms.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                <i class="fas fa-times me-1"></i> Bekor qilish
            </a>
            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-save me-1"></i> Saqlash
            </button>
        </div>
    </form>

{{-- Add Link Modal --}}
<div class="modal fade cms-config-modal" id="addLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cms.header-footer.footer.add-link') }}" method="POST">
                @csrf
                <input type="hidden" name="column" id="addLinkColumn" value="col2">

                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Yangi havola qo'shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Havola matni (O'zbekcha) <span class="text-danger">*</span></label>
                        <input type="text" name="link_text_uz" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Havola matni (English)</label>
                        <input type="text" name="link_text_en" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Havola matni (Русский)</label>
                        <input type="text" name="link_text_ru" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="text" name="link_url" class="form-control" placeholder="/page-url yoki https://..." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Qo'shish</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Link Form --}}
<form id="deleteLinkForm" action="{{ route('cms.header-footer.footer.delete-link') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="link_key" id="deleteLinkKey">
</form>

{{-- Edit Link Modal --}}
<div class="modal fade cms-config-modal" id="editLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cms.header-footer.footer.update-link') }}" method="POST">
                @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Havolani tahrirlash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="link_key" id="editLinkKey">
                <input type="hidden" id="editUrlKey">
                <div class="mb-3">
                    <label class="form-label">Havola matni (O'zbekcha) <span class="text-danger">*</span></label>
                    <input type="text" name="link_text_uz" id="editLinkTextUz" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Havola matni (English)</label>
                    <input type="text" name="link_text_en" id="editLinkTextEn" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Havola matni (Русский)</label>
                    <input type="text" name="link_text_ru" id="editLinkTextRu" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">URL <span class="text-danger">*</span></label>
                    <input type="text" name="link_url" id="editLinkUrl" class="form-control" placeholder="/page-url yoki https://..." required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="submit" class="btn btn-primary" id="saveEditLink">Saqlash</button>
            </div>
            </form>
        </div>
    </div>
</div>

{{-- Preview Modal --}}
<div class="modal fade cms-config-modal" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Footer ko'rinishi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="preview-footer">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <h6>{{ $footerTitle->value_uz ?? 'Tourism Academy' }}</h6>
                            <p class="small" style="color: rgba(255,255,255,0.6);">{{ Str::limit($footerDesc->value_uz ?? '', 100) }}</p>
                        </div>
                        <div class="col-md-3">
                            <h6>{{ $col2Title->value_uz ?? "Ma'lumot" }}</h6>
                            @foreach($col2Links as $link)
                                <a href="#">{{ $link->value_uz }}</a>
                            @endforeach
                        </div>
                        <div class="col-md-3">
                            <h6>{{ $col3Title->value_uz ?? 'Bizning xizmatlar' }}</h6>
                            @foreach($col3Links as $link)
                                <a href="#">{{ $link->value_uz }}</a>
                            @endforeach
                        </div>
                        <div class="col-md-3">
                            <h6>{{ $col4Title->value_uz ?? "Bog'lanish" }}</h6>
                            <p class="small" style="color: rgba(255,255,255,0.6);"><i class="fas fa-map-marker-alt me-2"></i>{{ $address->value_uz ?? '' }}</p>
                            <p class="small" style="color: rgba(255,255,255,0.6);"><i class="fas fa-phone me-2"></i>{{ $phone->value_uz ?? '' }}</p>
                            <p class="small" style="color: rgba(255,255,255,0.6);"><i class="fas fa-envelope me-2"></i>{{ $email->value_uz ?? '' }}</p>
                        </div>
                    </div>
                    <div class="preview-footer-bottom d-flex justify-content-between align-items-center">
                        <span class="small" style="color: rgba(255,255,255,0.6);">{{ $copyright->value_uz ?? '' }}</span>
                        <div>
                            @foreach($socials as $key => $social)
                                <a href="#" class="me-3" style="color: white;"><i class="{{ $social['icon'] }}"></i></a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentEditLinkItem = null;

    function prepareCmsConfigModals() {
        document.querySelectorAll('.cms-config-modal').forEach(function(modal) {
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
        });
    }

    prepareCmsConfigModals();

    // Set column when opening add link modal
    document.querySelectorAll('[data-bs-target="#addLinkModal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            prepareCmsConfigModals();
            document.getElementById('addLinkColumn').value = this.dataset.column;
        });
    });

    // Edit link - open modal
    document.querySelectorAll('.edit-link-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentEditLinkItem = this.closest('.link-item');

            document.getElementById('editLinkKey').value = this.dataset.linkKey;
            document.getElementById('editUrlKey').value = this.dataset.urlKey;
            document.getElementById('editLinkTextUz').value = this.dataset.textUz || '';
            document.getElementById('editLinkTextEn').value = this.dataset.textEn || '';
            document.getElementById('editLinkTextRu').value = this.dataset.textRu || '';
            document.getElementById('editLinkUrl').value = this.dataset.url || '#';

            prepareCmsConfigModals();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editLinkModal')).show();
        });
    });

    const editLinkForm = document.querySelector('#editLinkModal form');
    if (editLinkForm) {
        editLinkForm.addEventListener('submit', function(e) {
            const textUz = document.getElementById('editLinkTextUz').value.trim();
            const url = document.getElementById('editLinkUrl').value.trim();

            if (textUz && url) {
                return;
            }

            e.preventDefault();
            alert('Matn va URL kiritilishi shart!');
        });
    }

    // Show save reminder toast
    function showSaveReminder() {
        // Remove existing reminder if any
        const existing = document.querySelector('.save-reminder-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'save-reminder-toast alert alert-warning alert-dismissible fade show position-fixed shadow-lg';
        toast.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 320px;';
        toast.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>O'zgarishlar saqlangan!</strong><br>
            <small>Iltimos, sahifa pastidagi <b>"Saqlash"</b> tugmasini bosing.</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast.parentNode) toast.remove();
        }, 8000);
    }

    // Delete link
    document.querySelectorAll('.delete-link-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (confirm("Ushbu havolani o'chirishni xohlaysizmi?")) {
                const linkKey = this.dataset.linkKey;
                console.log('Deleting link:', linkKey);
                document.getElementById('deleteLinkKey').value = linkKey;
                document.getElementById('deleteLinkForm').submit();
            }
        });
    });

    // Footer logo: WAF-safe AJAX base64 upload
    document.getElementById('footer_logo_input')?.addEventListener('change', function(e) {
        const file = e.target.files[0]; if (!file) return;
        const status = document.getElementById('footer_logo_status');
        const reader = new FileReader();
        reader.onload = function(ev) {
            const prev = document.getElementById('footer_logo_preview');
            if (prev) { prev.src = ev.target.result; prev.style.display = 'inline-block'; }
            status.textContent = 'Yuklanmoqda...'; status.style.color = '#f59e0b';
            fetch('{{ route("cms.upload.image.b64") }}', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                body: JSON.stringify({ name: file.name, type: file.type, data: ev.target.result.split(',')[1] })
            })
            .then(r => r.ok ? r.json() : Promise.reject('HTTP ' + r.status))
            .then(res => {
                if (res.path) { document.getElementById('footer_logo_path').value = res.path; status.textContent = '✓ Logo yuklandi — saqlashni bosing'; status.style.color = '#10b981'; }
                else { throw new Error(res.error || 'xato'); }
            })
            .catch(err => { status.textContent = '✗ Xato: ' + err; status.style.color = '#ef4444'; });
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection
