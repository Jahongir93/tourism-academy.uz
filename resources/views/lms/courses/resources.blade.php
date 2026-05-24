@extends('layouts.dashboard-new')

@section('title', 'Kurs resurslari — ' . $course->title)
@section('page-title', 'Kurs resurslari')

@section('content')

<x-lms-alerts />

{{-- Header --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('lms.courses.show', $course) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Orqaga
                </a>
                <div style="width:1px;height:24px;background:var(--c-border)"></div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:var(--c-text)">{{ $course->title }}</div>
                    <div style="font-size:11px;color:var(--c-text-3)">Kurs resurslari</div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm" style="background:var(--c-teal);color:#fff"
                        data-bs-toggle="modal" data-bs-target="#materialModal">
                    <i class="fas fa-file-alt me-1"></i>Material
                </button>
                <button class="btn btn-sm" style="background:var(--c-rose);color:#fff"
                        data-bs-toggle="modal" data-bs-target="#videoModal">
                    <i class="fas fa-video me-1"></i>Video
                </button>
                <button class="btn btn-sm" style="background:var(--c-violet);color:#fff"
                        data-bs-toggle="modal" data-bs-target="#testModal">
                    <i class="fas fa-clipboard-check me-1"></i>Test
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
        $statCards = [
            ['fas fa-folder','var(--c-teal)','rgba(20,184,166,.1)',isset($resources) ? $resources->count() : 0,'Jami resurslar'],
            ['fas fa-users','var(--c-sky)','rgba(14,165,233,.1)',$course->enrollment_count ?? 0,'Talabalar'],
            ['fas fa-file-alt','var(--c-violet)','rgba(124,58,237,.1)',isset($resources) ? $resources->where('resource_type','document')->count() : 0,'Materiallar'],
            ['fas fa-video','var(--c-rose)','rgba(244,63,94,.1)',isset($resources) ? $resources->where('resource_type','video')->count() : 0,'Videolar'],
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

{{-- Resources --}}
@if(isset($resources) && $resources->count() > 0)
    @php $groupedResources = $resources->groupBy('week_number'); @endphp
    @foreach($groupedResources->sortKeys() as $week => $weekResources)
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-calendar-week" style="color:var(--c-teal)"></i>
                <span style="font-size:13px;font-weight:700">{{ $week ? $week.'-hafta' : 'Umumiy resurslar' }}</span>
            </div>
            <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">
                {{ $weekResources->count() }} ta resurs
            </span>
        </div>
        <div class="card-body p-0">
            @php
                $typeMap = [
                    'video'        => ['fa-video','var(--c-rose)','rgba(244,63,94,.1)'],
                    'document'     => ['fa-file-alt','var(--c-sky)','rgba(14,165,233,.1)'],
                    'presentation' => ['fa-file-powerpoint','var(--c-amber)','rgba(245,158,11,.1)'],
                    'audio'        => ['fa-music','var(--c-violet)','rgba(124,58,237,.1)'],
                    'link'         => ['fa-link','var(--c-teal)','rgba(20,184,166,.1)'],
                    'assignment'   => ['fa-tasks','var(--c-amber)','rgba(245,158,11,.1)'],
                    'quiz'         => ['fa-clipboard-check','var(--c-emerald)','rgba(16,185,129,.1)'],
                ];
            @endphp
            @foreach($weekResources->sortBy('order_number') as $resource)
            @php [$rIcon,$rColor,$rBg] = $typeMap[$resource->resource_type] ?? ['fa-file','var(--c-text-3)','var(--c-bg)']; @endphp
            <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid var(--c-border)">
                <div style="width:40px;height:40px;border-radius:10px;background:{{ $rBg }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas {{ $rIcon }}" style="color:{{ $rColor }};font-size:16px"></i>
                </div>
                <div style="flex:1;min-width:0">
                    <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                        <span style="font-size:13px;font-weight:700;color:var(--c-text)">{{ $resource->title }}</span>
                        @if($resource->is_mandatory)
                        <span class="badge" style="background:rgba(244,63,94,.12);color:var(--c-rose);font-size:9px">
                            <i class="fas fa-exclamation-circle me-1"></i>Majburiy
                        </span>
                        @endif
                        @if($resource->is_published)
                        <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:9px">
                            <i class="fas fa-check-circle me-1"></i>Nashr
                        </span>
                        @else
                        <span class="badge" style="background:rgba(245,158,11,.12);color:var(--c-amber);font-size:9px">
                            <i class="fas fa-clock me-1"></i>Qoralama
                        </span>
                        @endif
                    </div>
                    @if($resource->description)
                    <div style="font-size:12px;color:var(--c-text-3);margin-bottom:4px">{{ $resource->description }}</div>
                    @endif
                    <div class="d-flex flex-wrap gap-3" style="font-size:11px;color:var(--c-text-3)">
                        @if($resource->file_size)
                        <span><i class="fas fa-database me-1"></i>{{ $resource->file_size_formatted ?? '' }}</span>
                        @endif
                        <span><i class="fas fa-eye me-1"></i>{{ $resource->view_count ?? 0 }} ko'rildi</span>
                        @if($resource->is_downloadable)
                        <span><i class="fas fa-download me-1"></i>{{ $resource->download_count ?? 0 }} yuklandi</span>
                        @endif
                        @if($resource->available_from && $resource->available_from > now())
                        <span style="color:var(--c-amber)"><i class="fas fa-calendar me-1"></i>{{ $resource->available_from->format('d.m.Y') }} dan</span>
                        @endif
                    </div>
                </div>
                <div class="d-flex gap-1 flex-shrink-0">
                    @if($resource->file_path)
                    <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank"
                       class="action-btn" style="background:rgba(14,165,233,.1);color:var(--c-sky)" title="Ko'rish">
                        <i class="fas fa-eye"></i>
                    </a>
                    @elseif($resource->external_url)
                    <a href="{{ $resource->external_url }}" target="_blank"
                       class="action-btn" style="background:rgba(14,165,233,.1);color:var(--c-sky)" title="Ochish">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    @endif
                    <form action="{{ route('lms.courses.deleteResource', [$course, $resource]) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Rostdan ham bu resursni o\'chirmoqchimisiz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn" style="background:rgba(244,63,94,.1);color:var(--c-rose)" title="O'chirish">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
@else
<div class="card">
    <div class="card-body text-center py-5" style="color:var(--c-text-3)">
        <i class="fas fa-folder-open mb-2" style="display:block;font-size:36px"></i>
        <div style="font-size:14px;color:var(--c-text-2);margin-bottom:4px">Hozircha resurslar mavjud emas</div>
        <div style="font-size:12px">Yuqoridagi tugmalardan foydalanib kursga resurslar qo'shing</div>
    </div>
</div>
@endif

{{-- Material Modal --}}
<div class="modal fade" id="materialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('lms.courses.attachMaterial', $course) }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--c-border)">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="fas fa-file-alt" style="color:var(--c-teal)"></i>
                        Material biriktirish
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">
                            Material tanlang <span class="text-danger">*</span>
                        </label>
                        <select name="material_id" class="form-select" required>
                            <option value="">Materialni tanlang...</option>
                            @foreach(\App\Models\LmsMaterial::where('is_active', true)->where('subject_id', $course->subject_id)->orderBy('title')->get() as $material)
                            <option value="{{ $material->id }}">{{ $material->title }} ({{ $material->subject?->name_uz }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Hafta raqami</label>
                            <input type="number" name="week_number" class="form-control" min="1" max="52" placeholder="Masalan: 1">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Tartib raqami</label>
                            <input type="number" name="order_number" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_mandatory" value="1" id="mat_mandatory" class="form-check-input">
                        <label for="mat_mandatory" class="form-check-label" style="font-size:13px">Majburiy resurs</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--c-border)">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--c-teal);color:#fff">
                        <i class="fas fa-link me-1"></i>Biriktirish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Video Modal --}}
<div class="modal fade" id="videoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('lms.courses.attachVideo', $course) }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--c-border)">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="fas fa-video" style="color:var(--c-rose)"></i>
                        Video biriktirish
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">
                            Video tanlang <span class="text-danger">*</span>
                        </label>
                        <select name="video_id" class="form-select" required>
                            <option value="">Videoni tanlang...</option>
                            @foreach(\App\Models\LmsVideo::where('is_active', true)->where('subject_id', $course->subject_id)->orderBy('title')->get() as $video)
                            <option value="{{ $video->id }}">{{ $video->title }} ({{ $video->subject?->name_uz }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Hafta raqami</label>
                            <input type="number" name="week_number" class="form-control" min="1" max="52" placeholder="Masalan: 1">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Tartib raqami</label>
                            <input type="number" name="order_number" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_mandatory" value="1" id="vid_mandatory" class="form-check-input">
                        <label for="vid_mandatory" class="form-check-label" style="font-size:13px">Majburiy resurs</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--c-border)">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--c-rose);color:#fff">
                        <i class="fas fa-link me-1"></i>Biriktirish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Test Modal --}}
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('lms.courses.attachTest', $course) }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--c-border)">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="fas fa-clipboard-check" style="color:var(--c-violet)"></i>
                        Test biriktirish
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">
                            Test tanlang <span class="text-danger">*</span>
                        </label>
                        <select name="test_id" class="form-select" required>
                            <option value="">Testni tanlang...</option>
                            @foreach(\App\Models\LmsPracticeTest::where('is_active', true)->where('subject_id', $course->subject_id)->orderBy('title')->get() as $test)
                            <option value="{{ $test->id }}">{{ $test->title }} ({{ $test->subject?->name_uz }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Hafta raqami</label>
                            <input type="number" name="week_number" class="form-control" min="1" max="52" placeholder="Masalan: 1">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Tartib raqami</label>
                            <input type="number" name="order_number" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_mandatory" value="1" id="test_mandatory" class="form-check-input">
                        <label for="test_mandatory" class="form-check-label" style="font-size:13px">Majburiy resurs</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--c-border)">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--c-violet);color:#fff">
                        <i class="fas fa-link me-1"></i>Biriktirish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
