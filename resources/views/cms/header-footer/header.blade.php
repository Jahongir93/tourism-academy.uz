@extends('layouts.dashboard-new')

@section('title', 'Header tahrirlash - CMS')
@section('page-title', 'Header tahrirlash')

@section('styles')
<style>
    .language-tabs .nav-link {
        border-radius: 8px 8px 0 0;
        font-weight: 500;
    }
    .language-tabs .nav-link.active {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border-color: #4f46e5;
    }
    .content-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        transition: all 0.3s;
    }
    .content-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border-color: #4f46e5;
    }
    .section-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: white;
        padding: 20px;
        border-radius: 12px 12px 0 0;
        margin: -1px -1px 0 -1px;
    }
    .preview-header {
        background: #0D0D0D;
        border-bottom: 3px solid #D7FF37;
        padding: 15px 30px;
        border-radius: 8px;
    }
    .preview-logo {
        height: 35px;
        filter: brightness(0) invert(1);
    }
    .preview-menu-item {
        color: rgba(255,255,255,0.8);
        margin: 0 12px;
        font-size: 14px;
    }
    .preview-btn {
        background: #D7FF37;
        color: #000;
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
    }
    .flag-icon {
        width: 24px;
        height: 16px;
        object-fit: cover;
        border-radius: 2px;
    }
    .menu-accordion-header {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.2s;
    }
    .menu-accordion-header:hover {
        background-color: #f8f9fa !important;
    }
    .menu-accordion-body {
        border-top: 1px solid #e5e7eb;
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
                        <i class="fas fa-heading text-primary me-2"></i>Header tahrirlash
                    </h1>
                    <p class="text-muted mb-0">Sayt headerini uchta tilda tahrirlang</p>
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

    <form action="{{ route('cms.header-footer.header.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            {{-- Logo Section --}}
            <div class="col-lg-4 mb-4">
                <div class="content-card h-100">
                    <div class="section-header">
                        <h5 class="mb-0"><i class="fas fa-image me-2"></i>Logo</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $logoContent = $contents->where('key', 'logo_url')->first();
                            $logoPath = $logoContent ? $logoContent->value_uz : '';
                        @endphp

                        <div class="text-center mb-4">
                            @if($logoPath)
                                <img src="{{ asset('storage/' . $logoPath) }}" alt="Current Logo" class="img-fluid mb-3" style="max-height: 80px; background: #1a1a2e; padding: 15px; border-radius: 8px;">
                            @else
                                <div class="bg-light p-4 rounded mb-3">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="text-muted mt-2 mb-0">Logo yuklanmagan</p>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Yangi logo yuklash</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <small class="text-muted">Tavsiya: PNG, SVG (shaffof fon bilan)</small>
                        </div>

                        <input type="hidden" name="contents[logo][key]" value="logo_url">
                        <input type="hidden" name="contents[logo][type]" value="image">
                        <input type="hidden" name="contents[logo][order]" value="1">
                        <input type="hidden" name="contents[logo][value_uz]" value="{{ $logoPath }}">
                        <input type="hidden" name="contents[logo][value_en]" value="{{ $logoPath }}">
                        <input type="hidden" name="contents[logo][value_ru]" value="{{ $logoPath }}">
                    </div>
                </div>
            </div>

            {{-- Site Name --}}
            <div class="col-lg-8 mb-4">
                <div class="content-card h-100">
                    <div class="section-header">
                        <h5 class="mb-0"><i class="fas fa-heading me-2"></i>Sayt nomi</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $siteNameContent = $contents->where('key', 'site_name')->first();
                        @endphp

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <img src="{{ asset('assets/flags/uz.png') }}" class="flag-icon me-1" alt="UZ"> O'zbekcha
                                </label>
                                <input type="text" name="contents[site_name][value_uz]"
                                       value="{{ $siteNameContent->value_uz ?? 'Tourism Academy' }}"
                                       class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <img src="{{ asset('assets/flags/en.png') }}" class="flag-icon me-1" alt="EN"> English
                                </label>
                                <input type="text" name="contents[site_name][value_en]"
                                       value="{{ $siteNameContent->value_en ?? 'Tourism Academy' }}"
                                       class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <img src="{{ asset('assets/flags/ru.png') }}" class="flag-icon me-1" alt="RU"> Русский
                                </label>
                                <input type="text" name="contents[site_name][value_ru]"
                                       value="{{ $siteNameContent->value_ru ?? 'Tourism Academy' }}"
                                       class="form-control">
                            </div>
                        </div>

                        <input type="hidden" name="contents[site_name][key]" value="site_name">
                        <input type="hidden" name="contents[site_name][type]" value="text">
                        <input type="hidden" name="contents[site_name][order]" value="2">
                    </div>
                </div>
            </div>
        </div>

        {{-- Menu Items with Links and Submenus --}}
        <div class="content-card mb-4">
            <div class="section-header">
                <h5 class="mb-0"><i class="fas fa-bars me-2"></i>Menyu elementlari</h5>
            </div>
            <div class="p-4">
                @php
                    $menuItems = [
                        'menu_home' => ['label' => 'Bosh sahifa', 'default_url' => '/'],
                        'menu_about' => ['label' => 'Biz haqimizda', 'default_url' => '/about'],
                        'menu_programs' => ['label' => "Yo'nalishlar", 'default_url' => '/programs'],
                        'menu_teachers' => ['label' => "O'qituvchilar", 'default_url' => '/teachers'],
                        'menu_statistics' => ['label' => 'Statistika', 'default_url' => '/statistics'],
                        'menu_blog' => ['label' => 'Blog', 'default_url' => '/blog'],
                        'menu_contact' => ['label' => "Bog'lanish", 'default_url' => '/aloqa'],
                    ];
                    $order = 3;
                @endphp

                <div id="menuAccordion">
                    @foreach($menuItems as $key => $defaults)
                        @php
                            $content = $contents->where('key', $key)->first();
                            $urlContent = $contents->where('key', $key . '_url')->first();
                            $menuName = ucfirst(str_replace('menu_', '', $key));
                            // Get submenus for this menu item
                            $submenus = $contents->filter(function($item) use ($key) {
                                return str_starts_with($item->key, $key . '_sub_') && !str_ends_with($item->key, '_url');
                            })->sortBy('order');
                        @endphp
                        <div class="menu-accordion-item border mb-2 rounded-3 overflow-hidden">
                            <div class="menu-accordion-header" onclick="toggleMenuAccordion('{{ $key }}')" style="cursor: pointer; padding: 15px 20px; background: #fff; display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <span class="badge bg-primary me-2">{{ $menuName }}</span>
                                    <span class="text-muted small">{{ $content->value_uz ?? $defaults['label'] }}</span>
                                    @if($submenus->count() > 0)
                                        <span class="badge bg-info ms-2">{{ $submenus->count() }} submenyu</span>
                                    @endif
                                </div>
                                <i class="fas fa-chevron-down menu-accordion-icon" id="icon_{{ $key }}" style="transition: transform 0.3s;"></i>
                            </div>
                            <div id="collapse_{{ $key }}" class="menu-accordion-body" style="display: none;">
                                <div class="accordion-body bg-light">
                                    {{-- Menu Label --}}
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold"><i class="fas fa-tag me-1"></i>Menyu nomi</label>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><img src="{{ asset('assets/flags/uz.png') }}" class="flag-icon" alt="UZ"></span>
                                                <input type="text" name="contents[{{ $key }}][value_uz]"
                                                       value="{{ $content->value_uz ?? $defaults['label'] }}"
                                                       class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><img src="{{ asset('assets/flags/en.png') }}" class="flag-icon" alt="EN"></span>
                                                <input type="text" name="contents[{{ $key }}][value_en]"
                                                       value="{{ $content->value_en ?? '' }}"
                                                       class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><img src="{{ asset('assets/flags/ru.png') }}" class="flag-icon" alt="RU"></span>
                                                <input type="text" name="contents[{{ $key }}][value_ru]"
                                                       value="{{ $content->value_ru ?? '' }}"
                                                       class="form-control">
                                            </div>
                                        </div>
                                        <input type="hidden" name="contents[{{ $key }}][key]" value="{{ $key }}">
                                        <input type="hidden" name="contents[{{ $key }}][type]" value="text">
                                        <input type="hidden" name="contents[{{ $key }}][order]" value="{{ $order++ }}">
                                    </div>

                                    {{-- Menu URL --}}
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold"><i class="fas fa-link me-1"></i>Havola (URL)</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="contents[{{ $key }}_url][value_uz]"
                                                   value="{{ $urlContent->value_uz ?? $defaults['default_url'] }}"
                                                   class="form-control form-control-sm"
                                                   placeholder="/sahifa-nomi yoki https://...">
                                            <input type="hidden" name="contents[{{ $key }}_url][key]" value="{{ $key }}_url">
                                            <input type="hidden" name="contents[{{ $key }}_url][type]" value="url">
                                            <input type="hidden" name="contents[{{ $key }}_url][order]" value="{{ $order }}">
                                            <input type="hidden" name="contents[{{ $key }}_url][value_en]" value="{{ $urlContent->value_en ?? $defaults['default_url'] }}">
                                            <input type="hidden" name="contents[{{ $key }}_url][value_ru]" value="{{ $urlContent->value_ru ?? $defaults['default_url'] }}">
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Ichki: /about | Tashqi: https://example.com | Bo'sh: # (submenyu uchun)
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Submenus Section --}}
                                    <div class="border-top pt-3 mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <label class="form-label fw-semibold mb-0">
                                                <i class="fas fa-sitemap me-1"></i>Submenyular
                                            </label>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="openAddSubmenuModal('{{ $key }}', '{{ $menuName }}')">
                                                <i class="fas fa-plus me-1"></i>Submenyu qo'shish
                                            </button>
                                        </div>

                                        @php
                                            // Biz haqimizda uchun CmsMenuItem larni ham olish
                                            $cmsSubmenus = collect();
                                            if ($key === 'menu_about') {
                                                $headerMenu = \App\Models\CmsMenu::where('location', 'header')->where('is_active', true)->first();
                                                if ($headerMenu) {
                                                    // "Akademiya haqida" parent menyusini topish
                                                    $aboutParent = $headerMenu->menuItems()->whereNull('parent_id')->where('is_active', true)->first();
                                                    if ($aboutParent) {
                                                        $cmsSubmenus = $aboutParent->children()->where('is_active', true)->orderBy('order_position')->get();
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if($submenus->count() > 0 || $cmsSubmenus->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered bg-white mb-0">
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th>Nomi</th>
                                                            <th width="200">URL</th>
                                                            <th width="80" class="text-center">Amallar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- CMS Content submenular --}}
                                                        @foreach($submenus as $submenu)
                                                            @php
                                                                $submenuUrl = $contents->where('key', $submenu->key . '_url')->first();
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <strong>{{ $submenu->value_uz }}</strong>
                                                                    @if($submenu->value_en)
                                                                        <small class="text-muted d-block">EN: {{ $submenu->value_en }}</small>
                                                                    @endif
                                                                </td>
                                                                <td><code class="small">{{ $submenuUrl->value_uz ?? '#' }}</code></td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                                                            onclick="openEditSubmenuModal('{{ $submenu->key }}', '{{ addslashes($submenu->value_uz) }}', '{{ addslashes($submenu->value_en ?? '') }}', '{{ addslashes($submenu->value_ru ?? '') }}', '{{ addslashes($submenuUrl->value_uz ?? '#') }}')"
                                                                            title="Tahrirlash">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-danger"
                                                                            onclick="deleteSubmenu('{{ $submenu->key }}')"
                                                                            title="O'chirish">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach

                                                        {{-- CmsMenuItem submenular (faqat menu_about uchun) --}}
                                                        @foreach($cmsSubmenus as $cmsChild)
                                                            <tr class="table-info">
                                                                <td>
                                                                    <strong>{{ $cmsChild->title_uz }}</strong>
                                                                    @if($cmsChild->title_en)
                                                                        <small class="text-muted d-block">EN: {{ $cmsChild->title_en }}</small>
                                                                    @endif
                                                                    @if($cmsChild->title_ru)
                                                                        <small class="text-muted d-block">RU: {{ $cmsChild->title_ru }}</small>
                                                                    @endif
                                                                    <span class="badge bg-success mt-1">CMS Sahifa</span>
                                                                </td>
                                                                <td><code class="small">{{ $cmsChild->url }}</code></td>
                                                                <td class="text-center">
                                                                    <a href="{{ route('cms.menus.index') }}" class="btn btn-sm btn-outline-info" title="Menyularda tahrirlash">
                                                                        <i class="fas fa-external-link-alt"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-3 text-muted bg-white rounded border">
                                                <i class="fas fa-folder-open me-1"></i>
                                                Submenyu yo'q. Qo'shish uchun yuqoridagi tugmani bosing.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Custom Menu Items --}}
        <div class="content-card mb-4">
            <div class="section-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Qo'shimcha menyular</h5>
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addMenuModal">
                    <i class="fas fa-plus me-1"></i> Yangi menyu qo'shish
                </button>
            </div>
            <div class="p-4">
                @php
                    $customMenus = $contents->filter(function($item) {
                        return str_starts_with($item->key, 'menu_custom_') && !str_ends_with($item->key, '_url');
                    });
                @endphp

                @if($customMenus->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Menyu nomi (O'zbekcha)</th>
                                    <th>URL</th>
                                    <th width="80" class="text-center">Holat</th>
                                    <th width="150" class="text-center">Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customMenus as $menu)
                                    @php
                                        $menuUrl = $contents->where('key', $menu->key . '_url')->first();
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $menu->value_uz }}</strong></td>
                                        <td><code>{{ $menuUrl->value_uz ?? '#' }}</code></td>
                                        <td class="text-center">
                                            @if($menu->is_active)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-eye me-1"></i>Ko'rinadi
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-eye-slash me-1"></i>Yashirin
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('cms.header-footer.header.toggle-menu') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="menu_key" value="{{ $menu->key }}">
                                                <button type="submit" class="btn btn-sm {{ $menu->is_active ? 'btn-warning' : 'btn-success' }}"
                                                        title="{{ $menu->is_active ? 'Yashirish' : 'Ko\'rsatish' }}">
                                                    <i class="fas {{ $menu->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('cms.header-footer.header.delete-menu') }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Ushbu menyuni o\'chirmoqchimisiz?');">
                                                @csrf
                                                <input type="hidden" name="menu_key" value="{{ $menu->key }}">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        <p class="mb-0">Hozircha qo'shimcha menyular yo'q. Yangi menyu qo'shish uchun yuqoridagi tugmani bosing.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Buttons --}}
        <div class="content-card mb-4">
            <div class="section-header">
                <h5 class="mb-0"><i class="fas fa-mouse-pointer me-2"></i>Tugmalar</h5>
            </div>
            <div class="p-4">
                <div class="row">
                    @php
                        $loginContent = $contents->where('key', 'login_button')->first();
                        $dashboardContent = $contents->where('key', 'dashboard_button')->first();
                    @endphp

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Kirish tugmasi</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <input type="text" name="contents[login_button][value_uz]"
                                       value="{{ $loginContent->value_uz ?? 'Kirish' }}"
                                       class="form-control" placeholder="O'zbekcha">
                            </div>
                            <div class="col-4">
                                <input type="text" name="contents[login_button][value_en]"
                                       value="{{ $loginContent->value_en ?? 'Login' }}"
                                       class="form-control" placeholder="English">
                            </div>
                            <div class="col-4">
                                <input type="text" name="contents[login_button][value_ru]"
                                       value="{{ $loginContent->value_ru ?? 'Вход' }}"
                                       class="form-control" placeholder="Русский">
                            </div>
                        </div>
                        <input type="hidden" name="contents[login_button][key]" value="login_button">
                        <input type="hidden" name="contents[login_button][type]" value="text">
                        <input type="hidden" name="contents[login_button][order]" value="10">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Dashboard tugmasi</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <input type="text" name="contents[dashboard_button][value_uz]"
                                       value="{{ $dashboardContent->value_uz ?? 'Dashboard' }}"
                                       class="form-control" placeholder="O'zbekcha">
                            </div>
                            <div class="col-4">
                                <input type="text" name="contents[dashboard_button][value_en]"
                                       value="{{ $dashboardContent->value_en ?? 'Dashboard' }}"
                                       class="form-control" placeholder="English">
                            </div>
                            <div class="col-4">
                                <input type="text" name="contents[dashboard_button][value_ru]"
                                       value="{{ $dashboardContent->value_ru ?? 'Панель' }}"
                                       class="form-control" placeholder="Русский">
                            </div>
                        </div>
                        <input type="hidden" name="contents[dashboard_button][key]" value="dashboard_button">
                        <input type="hidden" name="contents[dashboard_button][type]" value="text">
                        <input type="hidden" name="contents[dashboard_button][order]" value="11">
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('cms.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                <i class="fas fa-times me-1"></i> Bekor qilish
            </a>
            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-save me-1"></i> Saqlash
            </button>
        </div>
    </form>

{{-- Preview Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Header ko'rinishi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="preview-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        @if($logoPath)
                            <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="preview-logo me-3">
                        @else
                            <img src="{{ asset('oqlogo.png') }}" alt="Logo" class="preview-logo me-3">
                        @endif
                    </div>
                    <div class="d-flex align-items-center">
                        @foreach($menuItems as $key => $defaultValue)
                            @php $content = $contents->where('key', $key)->first(); @endphp
                            <span class="preview-menu-item">{{ $content->value_uz ?? $defaultValue }}</span>
                        @endforeach
                        <span class="preview-btn ms-3">
                            <i class="fas fa-sign-in-alt me-1"></i>{{ $loginContent->value_uz ?? 'Kirish' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Menu Modal --}}
<div class="modal fade" id="addMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cms.header-footer.header.add-menu') }}" method="POST">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: white;">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Yangi menyu qo'shish</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <img src="{{ asset('assets/flags/uz.png') }}" class="flag-icon me-1" alt="UZ">
                            Menyu nomi (O'zbekcha) <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="menu_label_uz" class="form-control" required placeholder="Masalan: Fakultetlar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <img src="{{ asset('assets/flags/en.png') }}" class="flag-icon me-1" alt="EN">
                            Menyu nomi (English)
                        </label>
                        <input type="text" name="menu_label_en" class="form-control" placeholder="Example: Faculties">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <img src="{{ asset('assets/flags/ru.png') }}" class="flag-icon me-1" alt="RU">
                            Menyu nomi (Русский)
                        </label>
                        <input type="text" name="menu_label_ru" class="form-control" placeholder="Например: Факультеты">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL <span class="text-danger">*</span></label>
                        <input type="text" name="menu_url" class="form-control" required placeholder="/faculties">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Ichki sahifa uchun: /about, tashqi havola uchun: https://example.com
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Bekor qilish
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Qo'shish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Submenu Modal --}}
<div class="modal fade" id="addSubmenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cms.header-footer.header.add-submenu') }}" method="POST">
                @csrf
                <input type="hidden" name="parent_menu_key" id="submenu_parent_key">
                <div class="modal-header" style="background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-sitemap me-2"></i>Submenyu qo'shish
                        <small class="d-block opacity-75 mt-1" id="submenu_parent_label"></small>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <img src="{{ asset('assets/flags/uz.png') }}" class="flag-icon me-1" alt="UZ">
                            Submenyu nomi (O'zbekcha) <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="submenu_label_uz" class="form-control" required placeholder="Masalan: Bakalavr">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <img src="{{ asset('assets/flags/en.png') }}" class="flag-icon me-1" alt="EN">
                            Submenyu nomi (English)
                        </label>
                        <input type="text" name="submenu_label_en" class="form-control" placeholder="Example: Bachelor">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <img src="{{ asset('assets/flags/ru.png') }}" class="flag-icon me-1" alt="RU">
                            Submenyu nomi (Русский)
                        </label>
                        <input type="text" name="submenu_label_ru" class="form-control" placeholder="Например: Бакалавр">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL <span class="text-danger">*</span></label>
                        <input type="text" name="submenu_url" class="form-control" required placeholder="/programs/bachelor">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Ichki sahifa: /programs/bachelor | Tashqi: https://example.com
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Bekor qilish
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Qo'shish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Submenu Form (hidden) --}}
<form id="deleteSubmenuForm" action="{{ route('cms.header-footer.header.delete-submenu') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="submenu_key" id="delete_submenu_key">
</form>

{{-- Edit Submenu Modal --}}
<div class="modal fade" id="editSubmenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cms.header-footer.header.update-submenu') }}" method="POST">
                @csrf
                <input type="hidden" name="submenu_key" id="edit_submenu_key">
                <div class="modal-header" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Submenyuni tahrirlash
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <img src="{{ asset('assets/flags/uz.png') }}" class="flag-icon me-1" alt="UZ">
                            Submenyu nomi (O'zbekcha) <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="submenu_label_uz" id="edit_submenu_label_uz" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <img src="{{ asset('assets/flags/en.png') }}" class="flag-icon me-1" alt="EN">
                            Submenyu nomi (English)
                        </label>
                        <input type="text" name="submenu_label_en" id="edit_submenu_label_en" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <img src="{{ asset('assets/flags/ru.png') }}" class="flag-icon me-1" alt="RU">
                            Submenyu nomi (Русский)
                        </label>
                        <input type="text" name="submenu_label_ru" id="edit_submenu_label_ru" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL <span class="text-danger">*</span></label>
                        <input type="text" name="submenu_url" id="edit_submenu_url" class="form-control" required>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Ichki sahifa: /programs/bachelor | Tashqi: https://example.com
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Bekor qilish
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Toggle Menu Accordion - Custom implementation
    function toggleMenuAccordion(key) {
        var body = document.getElementById('collapse_' + key);
        var icon = document.getElementById('icon_' + key);

        if (body.style.display === 'none' || body.style.display === '') {
            body.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
        } else {
            body.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }

    // Open Add Submenu Modal
    function openAddSubmenuModal(parentKey, parentLabel) {
        document.getElementById('submenu_parent_key').value = parentKey;
        document.getElementById('submenu_parent_label').textContent = parentLabel + ' uchun';

        // Reset form
        document.querySelector('#addSubmenuModal form').reset();
        document.getElementById('submenu_parent_key').value = parentKey;

        // Open modal
        new bootstrap.Modal(document.getElementById('addSubmenuModal')).show();
    }

    // Delete Submenu
    function deleteSubmenu(submenuKey) {
        if (confirm("Ushbu submenyuni o'chirmoqchimisiz?")) {
            document.getElementById('delete_submenu_key').value = submenuKey;
            document.getElementById('deleteSubmenuForm').submit();
        }
    }

    // Open Edit Submenu Modal
    function openEditSubmenuModal(submenuKey, labelUz, labelEn, labelRu, url) {
        document.getElementById('edit_submenu_key').value = submenuKey;
        document.getElementById('edit_submenu_label_uz').value = labelUz;
        document.getElementById('edit_submenu_label_en').value = labelEn;
        document.getElementById('edit_submenu_label_ru').value = labelRu;
        document.getElementById('edit_submenu_url').value = url;

        new bootstrap.Modal(document.getElementById('editSubmenuModal')).show();
    }
</script>
@endsection
