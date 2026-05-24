@extends('layouts.dashboard-new')

@section('title', $course->title . ' — LMS')
@section('page-title', $course->title)

@section('styles')
<style>
.tab-btn { background:none;border:none;padding:10px 18px;font-size:13px;font-weight:600;color:var(--c-text-3);cursor:pointer;border-bottom:2px solid transparent;transition:all .15s;white-space:nowrap; }
.tab-btn.active { color:var(--c-teal);border-bottom-color:var(--c-teal); }
.tab-btn:hover:not(.active) { color:var(--c-text-2);border-bottom-color:var(--c-border); }
.tab-pane { display:none; }
.tab-pane.active { display:block; }
.topic-item { border:1px solid var(--c-border);border-radius:10px;padding:14px 16px;margin-bottom:10px;transition:border-color .15s; }
.topic-item:hover { border-color:var(--c-teal); }
</style>
@endsection

@section('content')

<x-lms-alerts />

{{-- Stats row --}}
<div class="row g-3 mb-4">
    @php
        $statCards = [
            ['fas fa-user-tie','var(--c-sky)','rgba(14,165,233,.1)',$course->teacher?->name ?? 'N/A',"O'qituvchi"],
            ['fas fa-users','var(--c-violet)','rgba(124,58,237,.1)',$course->enrollment_count ?? 0,'Talabalar'],
            ['fas fa-eye','var(--c-teal)','rgba(20,184,166,.1)',$course->view_count ?? 0,"Ko'rishlar"],
            ['fas fa-star','var(--c-amber)','rgba(245,158,11,.1)',number_format($course->rating ?? 0, 1),'Reyting'],
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

<div class="row g-4">
    {{-- Main --}}
    <div class="col-lg-8">
        @if($course->description)
        <div class="mb-3 p-3 rounded" style="background:rgba(20,184,166,.06);border:1px solid rgba(20,184,166,.15)">
            <p style="font-size:14px;color:var(--c-text-2);margin:0;line-height:1.7">{{ $course->description }}</p>
        </div>
        @endif

        {{-- Badges --}}
        <div class="d-flex flex-wrap gap-2 mb-4">
            @if($course->subject)
            <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal)">
                <i class="fas fa-book me-1"></i>{{ $course->subject->name_uz }}
            </span>
            @endif
            <span class="badge" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-layer-group me-1"></i>
                @if($course->level == 'beginner') Boshlang'ich
                @elseif($course->level == 'intermediate') O'rta
                @else Yuqori @endif
            </span>
            @if($course->certificate_available)
            <span class="badge" style="background:rgba(245,158,11,.15);color:var(--c-amber)">
                <i class="fas fa-certificate me-1"></i>Sertifikat beriladi
            </span>
            @endif
        </div>

        {{-- Tabs --}}
        <div class="card">
            <div class="card-header p-0" style="border-bottom:1px solid var(--c-border)">
                <div class="d-flex overflow-auto">
                    <button class="tab-btn active" onclick="showTab('overview', this)">
                        <i class="fas fa-info-circle me-1"></i>Umumiy
                    </button>
                    <button class="tab-btn" onclick="showTab('curriculum', this)">
                        <i class="fas fa-list me-1"></i>O'quv rejasi
                    </button>
                    <button class="tab-btn" onclick="showTab('requirements', this)">
                        <i class="fas fa-clipboard-list me-1"></i>Talablar
                    </button>
                </div>
            </div>
            <div class="card-body">

                {{-- Overview --}}
                <div id="tab-overview" class="tab-pane active">
                    @if($course->intro_video)
                    <div class="mb-4 rounded overflow-hidden" style="border:1px solid var(--c-border)">
                        <video controls style="width:100%;max-height:360px;display:block">
                            <source src="{{ asset('storage/' . $course->intro_video) }}" type="video/mp4">
                        </video>
                    </div>
                    @endif

                    @if($course->objectives)
                    <div class="mb-4">
                        <div style="font-size:13px;font-weight:700;color:var(--c-text);margin-bottom:8px">
                            <i class="fas fa-bullseye me-2" style="color:var(--c-teal)"></i>Kurs maqsadlari
                        </div>
                        <div class="p-3 rounded" style="background:rgba(20,184,166,.06);border:1px solid rgba(20,184,166,.12);font-size:13px;color:var(--c-text-2);line-height:1.7">
                            {!! nl2br(e($course->objectives)) !!}
                        </div>
                    </div>
                    @endif

                    @if($course->tags && count($course->tags) > 0)
                    <div>
                        <div style="font-size:12px;color:var(--c-text-3);margin-bottom:8px">Teglar</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($course->tags as $tag)
                            <span class="badge" style="background:var(--c-bg);color:var(--c-text-2);border:1px solid var(--c-border);font-size:11px">#{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Curriculum --}}
                <div id="tab-curriculum" class="tab-pane">
                    @php
                        $topics = $course->topics()->orderedByWeek()->with('resources')->get()->groupBy('week_number');
                    @endphp
                    @forelse($topics as $week => $weekTopics)
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:32px;height:32px;background:linear-gradient(135deg,var(--c-teal),var(--c-emerald));border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0">
                                {{ $week }}
                            </div>
                            <span style="font-size:14px;font-weight:700;color:var(--c-text)">{{ $week }}-hafta</span>
                        </div>
                        @foreach($weekTopics as $topic)
                        <div class="topic-item">
                            <div class="d-flex align-items-start justify-content-between mb-1">
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:var(--c-text)">
                                        {{ $topic->order_number }}. {{ $topic->title }}
                                    </div>
                                    @if($topic->description)
                                    <div style="font-size:12px;color:var(--c-text-3);margin-top:2px">{{ $topic->description }}</div>
                                    @endif
                                </div>
                                @if($topic->duration_minutes)
                                <span class="badge ms-2" style="background:rgba(14,165,233,.12);color:var(--c-sky);font-size:10px;flex-shrink:0">
                                    <i class="fas fa-clock me-1"></i>{{ $topic->duration_minutes }} daq
                                </span>
                                @endif
                            </div>
                            @if($topic->resources->count() > 0)
                            <div class="mt-2 pt-2" style="border-top:1px solid var(--c-border)">
                                <div style="font-size:11px;color:var(--c-text-3);margin-bottom:6px">
                                    <i class="fas fa-paperclip me-1"></i>Resurslar ({{ $topic->resources->count() }})
                                </div>
                                <div class="row g-1">
                                    @foreach($topic->resources as $resource)
                                    @php
                                        $rIcon = match($resource->resource_type) {
                                            'material' => 'fas fa-book',
                                            'video' => 'fas fa-video',
                                            'test' => 'fas fa-clipboard-check',
                                            'file' => 'fas fa-file-pdf',
                                            'image' => 'fas fa-image',
                                            default => 'fas fa-link'
                                        };
                                        $rColor = match($resource->resource_type) {
                                            'material' => 'var(--c-sky)',
                                            'video' => 'var(--c-violet)',
                                            'test' => 'var(--c-emerald)',
                                            'file' => 'var(--c-rose)',
                                            'image' => 'var(--c-amber)',
                                            default => 'var(--c-text-3)'
                                        };
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:var(--c-bg);border:1px solid var(--c-border)">
                                            <i class="{{ $rIcon }}" style="color:{{ $rColor }};font-size:12px;flex-shrink:0"></i>
                                            <span style="font-size:11px;color:var(--c-text-2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1">
                                                {{ $resource->file_name ?? $resource->description ?? ucfirst($resource->resource_type) }}
                                            </span>
                                            @if($resource->is_mandatory)
                                            <span class="badge" style="background:rgba(244,63,94,.12);color:var(--c-rose);font-size:9px;flex-shrink:0">Majburiy</span>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @empty
                    <div class="text-center py-5" style="color:var(--c-text-3)">
                        <i class="fas fa-book-open mb-2" style="display:block;font-size:32px"></i>
                        <div style="font-size:14px">O'quv rejasi mavjud emas</div>
                    </div>
                    @endforelse
                </div>

                {{-- Requirements --}}
                <div id="tab-requirements" class="tab-pane">
                    @if($course->requirements)
                    <div class="p-3 rounded" style="background:rgba(14,165,233,.06);border:1px solid rgba(14,165,233,.12);font-size:13px;color:var(--c-text-2);line-height:1.7">
                        {!! nl2br(e($course->requirements)) !!}
                    </div>
                    @else
                    <div class="text-center py-5" style="color:var(--c-text-3)">
                        <i class="fas fa-check-circle mb-2" style="display:block;font-size:32px;color:var(--c-emerald)"></i>
                        <div style="font-size:14px">Maxsus talablar yo'q</div>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Enroll card --}}
        <div class="card mb-3">
            @if($course->thumbnail)
            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                 style="width:100%;height:180px;object-fit:cover;border-radius:inherit;border-bottom-left-radius:0;border-bottom-right-radius:0">
            @else
            <div style="height:140px;background:linear-gradient(135deg,var(--c-teal),var(--c-emerald));display:flex;align-items:center;justify-content:center;border-radius:inherit;border-bottom-left-radius:0;border-bottom-right-radius:0">
                <i class="fas fa-graduation-cap" style="font-size:48px;color:#fff"></i>
            </div>
            @endif
            <div class="card-body">
                @if($course->price > 0)
                <div style="font-size:24px;font-weight:700;color:var(--c-text);margin-bottom:12px">
                    {{ number_format($course->price, 0, ',', ' ') }} so'm
                </div>
                @else
                <div style="font-size:20px;font-weight:700;color:var(--c-emerald);margin-bottom:12px">
                    <i class="fas fa-gift me-1"></i>Bepul
                </div>
                @endif

                @auth
                    @if(isset($isEnrolled) && $isEnrolled)
                    <a href="{{ route('lms.courses.learn', $course) }}" class="btn w-100 mb-2"
                       style="background:var(--c-teal);color:#fff">
                        <i class="fas fa-play me-1"></i>Davom etish
                    </a>
                    @if(isset($enrollment) && $enrollment?->enrolled_at)
                    <div style="font-size:12px;color:var(--c-text-3);text-align:center">
                        <i class="fas fa-calendar me-1"></i>Ro'yxatdan o'tgan: {{ $enrollment->enrolled_at->format('d.m.Y') }}
                    </div>
                    @endif
                    @elseif($course->isEnrollmentOpen())
                    <form action="{{ route('lms.courses.enroll', $course) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn w-100" style="background:var(--c-teal);color:#fff">
                            <i class="fas fa-user-plus me-1"></i>Ro'yxatdan o'tish
                        </button>
                    </form>
                    @else
                    <button class="btn w-100 btn-outline-secondary" disabled>
                        <i class="fas fa-lock me-1"></i>Ro'yxatdan o'tish yopiq
                    </button>
                    @endif
                @else
                <a href="{{ route('login') }}" class="btn w-100" style="background:var(--c-teal);color:#fff">
                    <i class="fas fa-sign-in-alt me-1"></i>Kirish
                </a>
                @endauth

                @can('update', $course)
                <hr style="border-color:var(--c-border);margin:12px 0">
                <a href="{{ route('lms.courses.edit', $course) }}" class="btn btn-sm w-100 mb-2"
                   style="background:var(--c-amber);color:#fff">
                    <i class="fas fa-edit me-1"></i>Tahrirlash
                </a>
                <a href="{{ route('lms.courses.resources', $course) }}" class="btn btn-sm w-100"
                   style="background:var(--c-sky);color:#fff">
                    <i class="fas fa-folder me-1"></i>Resurslarni boshqarish
                </a>
                @endcan
            </div>
        </div>

        {{-- Course info --}}
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-info-circle" style="color:var(--c-violet)"></i>
                <span>Kurs ma'lumotlari</span>
            </div>
            <div class="card-body p-0">
                @php
                    $courseInfo = [];
                    if ($course->duration_weeks) $courseInfo[] = ['fas fa-calendar-alt','var(--c-sky)','Davomiyligi', $course->duration_weeks . ' hafta'];
                    if ($course->hours_per_week) $courseInfo[] = ['fas fa-clock','var(--c-emerald)','Haftada', $course->hours_per_week . ' soat'];
                    if ($course->credit_hours) $courseInfo[] = ['fas fa-award','var(--c-violet)','Kredit', $course->credit_hours . ' soat'];
                    if ($course->passing_score) $courseInfo[] = ['fas fa-percentage','var(--c-amber)',"O'tish bali", $course->passing_score . '%'];
                @endphp
                @foreach($courseInfo as [$icon,$color,$label,$value])
                <div class="d-flex align-items-center px-4 py-2" style="border-bottom:1px solid var(--c-border)">
                    <i class="fas {{ $icon }} me-2" style="color:{{ $color }};width:14px"></i>
                    <span style="width:110px;font-size:12px;color:var(--c-text-3);flex-shrink:0">{{ $label }}</span>
                    <span style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showTab(name, btn) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}
</script>
@endpush
