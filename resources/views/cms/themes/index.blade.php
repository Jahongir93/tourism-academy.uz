@extends('layouts.dashboard-new')

@section('title', 'CMS — Tema sozlamalari')
@section('page-title', 'Sayt dizayni')

@section('styles')
<style>
.theme-card { border:2px solid var(--c-border);border-radius:14px;overflow:hidden;cursor:pointer;transition:all .2s;background:var(--c-bg); }
.theme-card:hover { transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.1); }
.theme-card.active { border-width:2px; }
</style>
@endsection

@section('content')

<x-lms-alerts />

{{-- Current theme --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-palette" style="color:var(--c-violet)"></i>
        <span>Joriy tema</span>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <div style="width:48px;height:48px;border-radius:50%;background:{{ $themes[$currentTheme]['primary'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fas fa-check text-white"></i>
            </div>
            <div>
                <div style="font-size:15px;font-weight:700;color:var(--c-text)">{{ $themes[$currentTheme]['name'] }}</div>
                <div style="font-size:13px;color:var(--c-text-3)">{{ $themes[$currentTheme]['description'] }}</div>
            </div>
            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-external-link-alt me-1"></i>Saytni ko'rish
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Theme selection --}}
<form action="{{ route('cms.themes.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-3 mb-4">
        @foreach($themes as $themeKey => $theme)
        <div class="col-lg-4 col-md-6">
            <div class="theme-card {{ $currentTheme == $themeKey ? 'active' : '' }}"
                 style="{{ $currentTheme == $themeKey ? 'border-color:'.$theme['primary'].';box-shadow:0 0 0 3px '.$theme['primary'].'30' : '' }}"
                 onclick="selectTheme('{{ $themeKey }}')">
                {{-- Color preview header (uses PHP vars — print-safe) --}}
                <div style="height:100px;background:linear-gradient(135deg,{{ $theme['primary'] }} 0%,{{ $theme['secondary'] }} 100%);position:relative;overflow:hidden">
                    <div style="position:absolute;top:10px;left:10px;right:10px;height:24px;background:rgba(255,255,255,.2);border-radius:6px"></div>
                    <div style="position:absolute;bottom:10px;left:10px;width:64px;height:20px;background:{{ $theme['accent'] }};border-radius:5px"></div>
                    <div style="position:absolute;bottom:10px;right:10px;width:48px;height:20px;background:rgba(255,255,255,.25);border-radius:5px"></div>
                    @if($currentTheme == $themeKey)
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
                        <div style="width:40px;height:40px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center">
                            <i class="fas fa-check" style="color:{{ $theme['primary'] }};font-size:18px"></i>
                        </div>
                    </div>
                    @endif
                </div>
                <div style="padding:14px">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div style="font-size:14px;font-weight:700;color:var(--c-text)">{{ $theme['name'] }}</div>
                        <input class="form-check-input" type="radio" name="theme"
                               id="theme_{{ $themeKey }}" value="{{ $themeKey }}"
                               {{ $currentTheme == $themeKey ? 'checked' : '' }}
                               style="width:18px;height:18px;flex-shrink:0">
                    </div>
                    <div style="font-size:12px;color:var(--c-text-3);margin-bottom:10px">{{ $theme['description'] }}</div>
                    <div class="d-flex gap-2">
                        <div style="width:22px;height:22px;border-radius:50%;background:{{ $theme['primary'] }};border:2px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,.15)" title="Primary"></div>
                        <div style="width:22px;height:22px;border-radius:50%;background:{{ $theme['secondary'] }};border:2px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,.15)" title="Secondary"></div>
                        <div style="width:22px;height:22px;border-radius:50%;background:{{ $theme['accent'] }};border:2px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,.15)" title="Accent"></div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('cms.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Orqaga
        </a>
        <button type="submit" class="btn" style="background:var(--c-violet);color:#fff;padding:8px 28px">
            <i class="fas fa-save me-1"></i>Saqlash
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
function selectTheme(themeKey) {
    document.getElementById('theme_' + themeKey).checked = true;
    document.querySelectorAll('.theme-card').forEach(c => {
        c.classList.remove('active');
        c.style.borderColor = '';
        c.style.boxShadow   = '';
    });
    const radio = document.getElementById('theme_' + themeKey);
    const card  = radio.closest('.theme-card');
    card.classList.add('active');
}
</script>
@endpush
