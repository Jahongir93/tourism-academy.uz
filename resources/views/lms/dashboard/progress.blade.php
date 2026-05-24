@extends('layouts.dashboard-new')

@section('title', 'Mening yutuqlarim — LMS')
@section('page-title', 'Mening yutuqlarim')

@section('content')

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
        $statCards = [
            ['fas fa-eye','var(--c-sky)','rgba(14,165,233,.1)',$overallStats['total_viewed'] ?? 0,"Ko'rilgan kontentlar"],
            ['fas fa-check-circle','var(--c-emerald)','rgba(16,185,129,.1)',$overallStats['total_completed'] ?? 0,'Tugallangan'],
            ['fas fa-clock','var(--c-violet)','rgba(124,58,237,.1)',gmdate('H:i', $overallStats['total_time'] ?? 0),'Umumiy vaqt'],
            ['fas fa-certificate','var(--c-amber)','rgba(245,158,11,.1)',$overallStats['certificates_earned'] ?? 0,'Sertifikatlar'],
        ];
    @endphp
    @foreach($statCards as [$icon,$color,$bg,$value,$label])
    <div class="col-6 col-sm-3">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:{{ $bg }};color:{{ $color }}"><i class="{{ $icon }}"></i></div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $value }}</div>
            <div class="stat-card-label">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Certificates --}}
@if($certificates->count() > 0)
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-certificate" style="color:var(--c-amber)"></i>
        <span>Mening sertifikatlarim</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($certificates as $certificate)
            <div class="col-md-6 col-lg-4">
                <div class="p-3 rounded h-100" style="border:1px solid var(--c-border);transition:box-shadow .15s"
                     onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.08)'" onmouseout="this.style.boxShadow=''">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:40px;height:40px;background:rgba(245,158,11,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-certificate" style="color:var(--c-amber);font-size:16px"></i>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:var(--c-text)">{{ $certificate->subject?->name_uz ?? 'Sertifikat' }}</div>
                            <div style="font-size:11px;color:var(--c-text-3)">{{ $certificate->issue_date?->format('d.m.Y') ?? '' }}</div>
                        </div>
                    </div>
                    <div style="font-size:12px;color:var(--c-text-3);margin-bottom:10px">Kod: {{ $certificate->certificate_code }}</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route('lms.certificates.show', $certificate) }}"
                           style="font-size:12px;color:var(--c-teal);font-weight:600">Ko'rish →</a>
                        <a href="{{ route('lms.certificates.download', $certificate) }}"
                           class="btn btn-sm btn-outline-secondary" style="font-size:11px">
                            <i class="fas fa-download me-1"></i>Yuklash
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- History --}}
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-history" style="color:var(--c-violet)"></i>
        <span>Ko'rishlar tarixi</span>
    </div>
    @if($contentViews->count() > 0)
    <div class="card-body p-0">
        @php
            $typeIcons = [
                'App\Models\LmsMaterial'     => ['fa-file-alt','var(--c-sky)'],
                'App\Models\LmsVideo'        => ['fa-video','var(--c-violet)'],
                'App\Models\LmsCourse'       => ['fa-graduation-cap','var(--c-emerald)'],
                'App\Models\LmsLibraryBook'  => ['fa-book','var(--c-rose)'],
            ];
        @endphp
        @foreach($contentViews as $view)
        @php [$vIcon,$vColor] = $typeIcons[$view->viewable_type] ?? ['fa-file','var(--c-text-3)']; @endphp
        <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid var(--c-border)">
            <div style="width:36px;height:36px;border-radius:8px;background:var(--c-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid var(--c-border)">
                <i class="fas {{ $vIcon }}" style="color:{{ $vColor }};font-size:14px"></i>
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:600;color:var(--c-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {{ $view->viewable?->title ?? "Noma'lum" }}
                </div>
                <div class="d-flex gap-3" style="font-size:11px;color:var(--c-text-3);margin-top:2px">
                    <span><i class="fas fa-eye me-1"></i>{{ $view->view_count }} marta</span>
                    <span><i class="fas fa-clock me-1"></i>{{ gmdate('H:i:s', $view->view_duration ?? 0) }}</span>
                    <span>{{ $view->last_viewed_at?->diffForHumans() ?? '' }}</span>
                </div>
            </div>
            <div style="flex-shrink:0">
                @if($view->is_completed)
                <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:10px">
                    <i class="fas fa-check me-1"></i>Tugallangan
                </span>
                @elseif($view->progress_percentage > 0)
                <div class="d-flex align-items-center gap-2">
                    <div style="width:60px;height:6px;background:var(--c-border);border-radius:3px;overflow:hidden">
                        <div style="width:{{ $view->progress_percentage }}%;height:100%;background:var(--c-sky)"></div>
                    </div>
                    <span style="font-size:11px;color:var(--c-text-3)">{{ round($view->progress_percentage) }}%</span>
                </div>
                @else
                <span class="badge" style="background:var(--c-bg);color:var(--c-text-3);border:1px solid var(--c-border);font-size:10px">Boshlangan</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="card-body py-3" style="border-top:1px solid var(--c-border)">
        {{ $contentViews->links() }}
    </div>
    @else
    <div class="card-body text-center py-5" style="color:var(--c-text-3)">
        <i class="fas fa-history mb-2" style="display:block;font-size:32px"></i>
        <div style="font-size:14px;color:var(--c-text-2)">Hozircha ko'rishlar tarixi mavjud emas</div>
        <div style="font-size:12px;margin-top:4px">O'quv materiallarini ko'rib chiqishni boshlang!</div>
        <a href="{{ route('lms.dashboard') }}" class="btn btn-sm mt-3" style="background:var(--c-teal);color:#fff">
            LMS ga qaytish
        </a>
    </div>
    @endif
</div>

@endsection
