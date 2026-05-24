@extends('layouts.dashboard-new')

@section('title', 'Sertifikatlar — LMS')
@section('page-title', 'Mening sertifikatlarim')

@section('styles')
<style>
.cert-card {
    background:var(--c-surface);border:1px solid var(--c-border);border-radius:12px;
    overflow:hidden;transition:all .2s;
}
.cert-card:hover { transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.08);border-color:var(--c-teal); }
.cert-header {
    background:linear-gradient(135deg,var(--c-teal),var(--c-emerald));
    padding:20px;position:relative;overflow:hidden;
}
.cert-header::before {
    content:'';position:absolute;top:-20px;right:-20px;
    width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.1);
}
.cert-header::after {
    content:'';position:absolute;bottom:-30px;left:-15px;
    width:70px;height:70px;border-radius:50%;background:rgba(255,255,255,.08);
}
.cert-info-row {
    display:flex;align-items:center;gap:10px;padding:8px 10px;
    background:var(--c-bg);border-radius:8px;margin-bottom:6px;font-size:12px;
}
</style>
@endsection

@section('content')

{{-- Stats --}}
@if(count($certificates ?? []) > 0)
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-teal)">
            <div class="stat-card-icon" style="background:rgba(20,184,166,.12);color:var(--c-teal)">
                <i class="fas fa-award"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ count($certificates ?? []) }}</div>
            <div class="stat-card-label">Jami sertifikatlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-emerald)">
            <div class="stat-card-icon" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ collect($certificates ?? [])->filter(fn($c) => $c->isValid())->count() }}</div>
            <div class="stat-card-label">Faol sertifikatlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-violet)">
            <div class="stat-card-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ collect($certificates ?? [])->avg('score') ? round(collect($certificates)->avg('score')) : 0 }}%</div>
            <div class="stat-card-label">O'rtacha ball</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ collect($certificates ?? [])->max('score') ?? 0 }}%</div>
            <div class="stat-card-label">Eng yuqori ball</div>
        </div>
    </div>
</div>
@endif

<x-lms-alerts />

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-certificate" style="color:var(--c-teal)"></i>
        <span style="font-weight:700;color:var(--c-text)">Sertifikatlar</span>
    </div>
</div>

<div class="row g-4">
    @forelse($certificates ?? [] as $certificate)
    <div class="col-12 col-md-6 col-xl-4">
        <div class="cert-card">
            {{-- Header --}}
            <div class="cert-header">
                <div class="d-flex align-items-start justify-content-between" style="position:relative;z-index:1">
                    <div style="width:52px;height:52px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff">
                        <i class="fas fa-certificate"></i>
                    </div>
                    @if($certificate->isValid())
                        <span class="badge" style="background:rgba(255,255,255,.9);color:var(--c-emerald);font-size:11px">
                            <i class="fas fa-check-circle me-1"></i>Faol
                        </span>
                    @else
                        <span class="badge" style="background:rgba(255,255,255,.9);color:var(--c-rose);font-size:11px">
                            <i class="fas fa-times-circle me-1"></i>Muddati tugagan
                        </span>
                    @endif
                </div>
                <div style="position:relative;z-index:1;margin-top:10px">
                    <div style="font-size:11px;color:rgba(255,255,255,.7)">
                        <i class="fas fa-hashtag me-1"></i>{{ $certificate->certificate_number }}
                    </div>
                </div>
            </div>
            {{-- Details --}}
            <div class="p-4">
                <div style="font-weight:700;font-size:14px;color:var(--c-text);margin-bottom:12px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.8em">
                    {{ $certificate->title }}
                </div>
                @if($certificate->subject)
                <div class="cert-info-row">
                    <div style="width:32px;height:32px;background:rgba(14,165,233,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-book" style="color:var(--c-sky);font-size:13px"></i>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--c-text-3)">Fan:</div>
                        <div style="font-weight:600;color:var(--c-text)">{{ $certificate->subject->name_uz }}</div>
                    </div>
                </div>
                @endif
                <div class="cert-info-row">
                    <div style="width:32px;height:32px;background:rgba(16,185,129,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-calendar" style="color:var(--c-emerald);font-size:13px"></i>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--c-text-3)">Berilgan sana:</div>
                        <div style="font-weight:600;color:var(--c-text)">{{ $certificate->issue_date->format('d.m.Y') }}</div>
                    </div>
                </div>
                @if($certificate->expiry_date)
                <div class="cert-info-row">
                    <div style="width:32px;height:32px;background:rgba(245,158,11,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-clock" style="color:var(--c-amber);font-size:13px"></i>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--c-text-3)">Amal qilish muddati:</div>
                        <div style="font-weight:600;color:var(--c-text)">{{ $certificate->expiry_date->format('d.m.Y') }}</div>
                    </div>
                </div>
                @endif
                @if($certificate->score)
                <div class="cert-info-row">
                    <div style="width:32px;height:32px;background:rgba(124,58,237,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-percentage" style="color:var(--c-violet);font-size:13px"></i>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--c-text-3)">Ball:</div>
                        <div style="font-weight:700;color:var(--c-teal)">{{ $certificate->score }}%</div>
                    </div>
                </div>
                @endif
                {{-- Actions --}}
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('lms.certificates.show', $certificate) }}" class="btn btn-sm flex-fill"
                       style="background:var(--c-teal);color:#fff;font-size:12px">
                        <i class="fas fa-eye me-1"></i>Ko'rish
                    </a>
                    <a href="{{ route('lms.certificates.download', $certificate) }}" class="btn btn-sm flex-fill btn-outline-secondary"
                       style="font-size:12px">
                        <i class="fas fa-download me-1"></i>Yuklash
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="empty-state py-5">
                    <div class="empty-state-icon"><i class="fas fa-award"></i></div>
                    <div class="empty-state-sub">Siz hali sertifikat olmadingiz</div>
                    <div style="font-size:13px;color:var(--c-text-3);margin-top:6px">
                        Kurslarni muvaffaqiyatli tugatganingizda sertifikatlar avtomatik beriladi
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('lms.courses.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-graduation-cap me-1"></i>Kurslarga o'tish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforelse
</div>

@endsection
