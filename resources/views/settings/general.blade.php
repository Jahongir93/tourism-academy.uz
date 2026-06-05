@extends('layouts.dashboard-new')

@section('title', 'Asosiy Sozlamalar')
@section('page-title', 'Asosiy Sozlamalar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-sliders-h me-2"></i>Asosiy Sozlamalar</h4>
                            <p class="mb-0 opacity-75">Sayt nomi, logo, til va kontakt ma'lumotlarini boshqarish</p>
                        </div>
                        <a href="{{ route('settings.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Orqaga
                        </a>
                    </div>
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

    <form action="{{ route('settings.general.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Sayt Ma'lumotlari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-globe text-primary me-2"></i>Sayt Ma'lumotlari</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sayt nomi <span class="text-danger">*</span></label>
                            <input type="text" name="site_name" class="form-control @error('site_name') is-invalid @enderror"
                                   value="{{ old('site_name', $settings->where('key', 'site_name')->first()?->value ?? 'HEMIS') }}" required>
                            @error('site_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sayt tavsifi</label>
                            <textarea name="site_description" class="form-control" rows="3">{{ old('site_description', $settings->where('key', 'site_description')->first()?->value ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Muassasa nomi</label>
                            <input type="text" name="institution_name" class="form-control"
                                   value="{{ old('institution_name', $settings->where('key', 'institution_name')->first()?->value ?? 'Samarqand Davlat Turizm Akademiyasi') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Manzil</label>
                            <input type="text" name="address" class="form-control"
                                   value="{{ old('address', $settings->where('key', 'address')->first()?->value ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontakt Ma'lumotlari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-phone text-success me-2"></i>Kontakt Ma'lumotlari</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Telefon raqam</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $settings->where('key', 'phone')->first()?->value ?? '+998 66 233-00-00') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $settings->where('key', 'email')->first()?->value ?? 'info@samtuit.uz') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fax</label>
                            <input type="text" name="fax" class="form-control"
                                   value="{{ old('fax', $settings->where('key', 'fax')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Veb sayt</label>
                            <input type="url" name="website" class="form-control"
                                   value="{{ old('website', $settings->where('key', 'website')->first()?->value ?? 'https://samtuit.uz') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Til va Vaqt -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-language text-info me-2"></i>Til va Vaqt Zonasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Asosiy til <span class="text-danger">*</span></label>
                            <select name="language" class="form-select" required>
                                <option value="uz" {{ old('language', $settings->where('key', 'language')->first()?->value ?? 'uz') == 'uz' ? 'selected' : '' }}>O'zbekcha</option>
                                <option value="ru" {{ old('language', $settings->where('key', 'language')->first()?->value ?? 'uz') == 'ru' ? 'selected' : '' }}>Русский</option>
                                <option value="en" {{ old('language', $settings->where('key', 'language')->first()?->value ?? 'uz') == 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Vaqt zonasi <span class="text-danger">*</span></label>
                            <select name="timezone" class="form-select" required>
                                <option value="Asia/Tashkent" {{ old('timezone', $settings->where('key', 'timezone')->first()?->value ?? 'Asia/Tashkent') == 'Asia/Tashkent' ? 'selected' : '' }}>Toshkent (UTC+5)</option>
                                <option value="Europe/Moscow" {{ old('timezone', $settings->where('key', 'timezone')->first()?->value ?? 'Asia/Tashkent') == 'Europe/Moscow' ? 'selected' : '' }}>Moskva (UTC+3)</option>
                                <option value="UTC" {{ old('timezone', $settings->where('key', 'timezone')->first()?->value ?? 'Asia/Tashkent') == 'UTC' ? 'selected' : '' }}>UTC</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sana formati</label>
                            <select name="date_format" class="form-select">
                                <option value="d.m.Y" {{ old('date_format', $settings->where('key', 'date_format')->first()?->value ?? 'd.m.Y') == 'd.m.Y' ? 'selected' : '' }}>25.12.2024</option>
                                <option value="d/m/Y" {{ old('date_format', $settings->where('key', 'date_format')->first()?->value ?? 'd.m.Y') == 'd/m/Y' ? 'selected' : '' }}>25/12/2024</option>
                                <option value="Y-m-d" {{ old('date_format', $settings->where('key', 'date_format')->first()?->value ?? 'd.m.Y') == 'Y-m-d' ? 'selected' : '' }}>2024-12-25</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo va Favicon -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-image text-warning me-2"></i>Logo va Favicon</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $resolveImg = function($p){
                                if(!$p) return null;
                                if(\Illuminate\Support\Str::startsWith($p,['http','storage/','assets/','images/'])) return asset($p);
                                return asset('storage/'.ltrim($p,'/'));
                            };
                            $currentLogo = $settings->where('key', 'site_logo')->first()?->value;
                            $currentFavicon = $settings->where('key', 'site_favicon')->first()?->value;
                        @endphp
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sayt logosi</label>
                            <div class="mb-2">
                                <img id="logo_preview" src="{{ $currentLogo ? $resolveImg($currentLogo) : '' }}" alt="Logo"
                                     class="img-thumbnail" style="max-height:60px;{{ $currentLogo ? '' : 'display:none' }}">
                            </div>
                            {{-- name yo'q + data-no-waf: WAF-safe, AJAX orqali yuklanadi --}}
                            <input type="file" id="site_logo_input" class="form-control" accept="image/*" data-no-waf>
                            <input type="hidden" name="site_logo_path" id="site_logo_path">
                            <div id="logo_status" style="font-size:12px;margin-top:4px"></div>
                            <small class="text-muted">Tavsiya: PNG yoki SVG, max 2MB</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Favicon</label>
                            <div class="mb-2">
                                <img id="favicon_preview" src="{{ $currentFavicon ? $resolveImg($currentFavicon) : '' }}" alt="Favicon"
                                     class="img-thumbnail" style="max-height:32px;{{ $currentFavicon ? '' : 'display:none' }}">
                            </div>
                            <input type="file" id="site_favicon_input" class="form-control" accept="image/*" data-no-waf>
                            <input type="hidden" name="site_favicon_path" id="site_favicon_path">
                            <div id="favicon_status" style="font-size:12px;margin-top:4px"></div>
                            <small class="text-muted">Tavsiya: ICO yoki PNG 32x32, max 1MB</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interaktiv Xarita va Virtual Tur -->
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-map-marked-alt text-success me-2"></i>Interaktiv Xarita va 360° Virtual Tur</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-map text-primary me-1"></i>
                                        Interaktiv xarita URL manzili
                                    </label>
                                    <input type="url" name="interactive_map_url" class="form-control"
                                           value="{{ old('interactive_map_url', $settings->where('key', 'interactive_map_url')->first()?->value ?? '') }}"
                                           placeholder="https://example.com/map">
                                    <small class="text-muted">Kampus interaktiv xaritasi uchun tashqi havola</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-vr-cardboard text-info me-1"></i>
                                        360° Virtual tur URL manzili
                                    </label>
                                    <input type="url" name="virtual_tour_url" class="form-control"
                                           value="{{ old('virtual_tour_url', $settings->where('key', 'virtual_tour_url')->first()?->value ?? '') }}"
                                           placeholder="https://example.com/360tour">
                                    <small class="text-muted">360 darajali virtual sayohat uchun tashqi havola</small>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Bu havolalar bosh sahifadagi "Tezkor kirish" bo'limida ko'rsatiladi.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-end">
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-undo me-1"></i> Bekor qilish
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Saqlash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<script>
// WAF-safe logo/favicon upload: AJAX base64 → hidden path field
function wafUpload(inputId, pathId, previewId, statusId) {
    var input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;
        var status = document.getElementById(statusId);
        var reader = new FileReader();
        reader.onload = function(ev) {
            var prev = document.getElementById(previewId);
            if (prev) { prev.src = ev.target.result; prev.style.display = 'inline-block'; }
            status.textContent = 'Yuklanmoqda...'; status.style.color = '#f59e0b';
            fetch('{{ route('cms.upload.image.b64') }}', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                body: JSON.stringify({ name: file.name, type: file.type, data: ev.target.result.split(',')[1] })
            })
            .then(function(r){ return r.ok ? r.json() : Promise.reject('HTTP '+r.status); })
            .then(function(res){
                if (res.path) {
                    document.getElementById(pathId).value = res.path;
                    status.textContent = '✓ Yuklandi — saqlashni bosing'; status.style.color = '#10b981';
                } else { throw new Error(res.error || 'xato'); }
            })
            .catch(function(err){ status.textContent = '✗ Xato: ' + err; status.style.color = '#ef4444'; });
        };
        reader.readAsDataURL(file);
    });
}
wafUpload('site_logo_input', 'site_logo_path', 'logo_preview', 'logo_status');
wafUpload('site_favicon_input', 'site_favicon_path', 'favicon_preview', 'favicon_status');
</script>
@endsection
