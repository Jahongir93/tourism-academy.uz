@extends('layouts.dashboard-new')

@section('title', $exam->title . ' — LMS')
@section('page-title', $exam->title)

@section('styles')
<style>
.action-btn { display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;border:none;background:transparent;cursor:pointer;transition:all .15s;font-size:13px;padding:0;text-decoration:none; }
.action-btn:hover { background:var(--c-bg); }
.info-row { display:flex;align-items:flex-start;gap:8px;padding:8px 0;border-bottom:1px solid var(--c-border); }
.info-row:last-child { border-bottom:none; }
</style>
@endsection

@section('content')

{{-- Action buttons row --}}
<div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
    <a href="{{ route('lms.exams.questions', $exam) }}" class="btn btn-sm"
       style="background:var(--c-teal);color:#fff">
        <i class="fas fa-list-ol me-1"></i>Savollar
        <span class="badge ms-1" style="background:rgba(255,255,255,.3)">{{ $stats['total_questions'] }}</span>
    </a>
    <a href="{{ route('lms.exams.results', $exam) }}" class="btn btn-sm"
       style="background:var(--c-violet);color:#fff">
        <i class="fas fa-chart-bar me-1"></i>Natijalar
    </a>
    <a href="{{ route('lms.exams.edit', $exam) }}" class="btn btn-sm"
       style="background:var(--c-amber);color:#fff">
        <i class="fas fa-edit me-1"></i>Tahrirlash
    </a>
    <a href="{{ route('lms.exams.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
        <i class="fas fa-arrow-left me-1"></i>Ortga
    </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
        $statCards = [
            ['fas fa-question-circle','var(--c-sky)','rgba(14,165,233,.1)',$stats['total_questions'],'Savollar'],
            ['fas fa-star','var(--c-teal)','rgba(20,184,166,.1)',$stats['total_points'],'Umumiy ball'],
            ['fas fa-users','var(--c-violet)','rgba(124,58,237,.1)',$stats['total_attempts'],'Urinishlar'],
            ['fas fa-check-circle','var(--c-emerald)','rgba(16,185,129,.1)',$stats['completed_attempts'],'Yakunlangan'],
            ['fas fa-percentage','var(--c-amber)','rgba(245,158,11,.1)',number_format($stats['average_score'] ?? 0, 1),"O'rtacha ball"],
            ['fas fa-trophy','var(--c-rose)','rgba(244,63,94,.1)',($stats['pass_rate'] ?? 0) . '%',"O'tish foizi"],
        ];
    @endphp
    @foreach($statCards as [$icon,$color,$bg,$value,$label])
    <div class="col-6 col-sm-4 col-lg-2">
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
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-file-alt" style="color:var(--c-teal)"></i>
                    <span>Imtihon ma'lumotlari</span>
                </div>
                <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">
                    {{ $exam->getStatusLabel() }}
                </span>
            </div>
            <div class="card-body p-0">
                @php
                    $infoRows = [
                        ["Fan", $exam->subject->name ?? '—'],
                        ["Turi", $exam->getExamTypeLabel()],
                        ["Davomiyligi", $exam->duration_minutes . ' daqiqa'],
                        ["Maksimal urinish", $exam->max_attempts],
                        ["O'tish bali", $exam->passing_score . ' / ' . $exam->max_score],
                        ["Jurnal foizi", $exam->weight_percentage . '%'],
                    ];
                @endphp
                @foreach($infoRows as [$label, $value])
                <div class="d-flex align-items-center px-4 py-2" style="border-bottom:1px solid var(--c-border)">
                    <span style="width:160px;font-size:12px;color:var(--c-text-3);flex-shrink:0">{{ $label }}</span>
                    <span style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $value }}</span>
                </div>
                @endforeach

                @if($exam->start_time || $exam->end_time)
                <div class="d-flex align-items-center px-4 py-2" style="border-bottom:1px solid var(--c-border)">
                    <span style="width:160px;font-size:12px;color:var(--c-text-3);flex-shrink:0">Boshlanish</span>
                    <span style="font-size:13px;font-weight:600;color:var(--c-text)">
                        {{ $exam->start_time ? $exam->start_time->format('d.m.Y H:i') : '—' }}
                    </span>
                </div>
                <div class="d-flex align-items-center px-4 py-2" style="border-bottom:1px solid var(--c-border)">
                    <span style="width:160px;font-size:12px;color:var(--c-text-3);flex-shrink:0">Tugash</span>
                    <span style="font-size:13px;font-weight:600;color:var(--c-text)">
                        {{ $exam->end_time ? $exam->end_time->format('d.m.Y H:i') : '—' }}
                    </span>
                </div>
                @endif

                @if($exam->description)
                <div class="px-4 py-3">
                    <div style="font-size:12px;color:var(--c-text-3);margin-bottom:4px">Tavsif</div>
                    <p style="font-size:13px;color:var(--c-text-2);margin:0">{{ $exam->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-history" style="color:var(--c-violet)"></i>
                    <span>So'nggi urinishlar</span>
                </div>
                <a href="{{ route('lms.exams.results', $exam) }}" style="font-size:12px;color:var(--c-teal)">
                    Barchasini ko'rish <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @forelse($exam->attempts->take(5) as $attempt)
                <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid var(--c-border)">
                    <div style="width:36px;height:36px;background:rgba(124,58,237,.12);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-user" style="color:var(--c-violet);font-size:13px"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $attempt->student->full_name ?? "Noma'lum" }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">{{ $attempt->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:14px;font-weight:700;color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}">
                            {{ $attempt->score ?? '—' }} / {{ $exam->max_score }}
                        </div>
                        <span class="badge" style="font-size:10px;background:{{ $attempt->passed ? 'rgba(16,185,129,.12)' : 'rgba(244,63,94,.12)' }};color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}">
                            {{ $attempt->getStatusLabel() }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-4" style="color:var(--c-text-3);font-size:13px">
                    <i class="fas fa-inbox mb-1" style="display:block;font-size:24px"></i>Hali urinishlar yo'q
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-toggle-on" style="color:var(--c-emerald)"></i>
                <span>Holat va amallar</span>
            </div>
            <div class="card-body">
                @if(!$exam->is_published)
                <div class="d-flex align-items-center gap-2 p-3 mb-3 rounded" style="background:rgba(245,158,11,.1)">
                    <i class="fas fa-exclamation-triangle" style="color:var(--c-amber)"></i>
                    <span style="font-size:13px;color:var(--c-text-2)">Imtihon hali e'lon qilinmagan</span>
                </div>
                @if($stats['total_questions'] > 0)
                <form method="POST" action="{{ route('lms.exams.publish', $exam) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-sm w-100" style="background:var(--c-emerald);color:#fff">
                        <i class="fas fa-check-circle me-1"></i>E'lon qilish
                    </button>
                </form>
                @else
                <p style="font-size:12px;color:var(--c-text-3)">E'lon qilish uchun savollar qo'shing</p>
                @endif
                @else
                <div class="d-flex align-items-center gap-2 p-3 mb-3 rounded" style="background:rgba(16,185,129,.1)">
                    <i class="fas fa-check-circle" style="color:var(--c-emerald)"></i>
                    <span style="font-size:13px;color:var(--c-text-2)">Imtihon faol</span>
                </div>
                <form method="POST" action="{{ route('lms.exams.unpublish', $exam) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-sm w-100" style="background:var(--c-amber);color:#fff">
                        <i class="fas fa-pause-circle me-1"></i>To'xtatish
                    </button>
                </form>
                @endif

                @if($exam->sync_to_journal)
                <form method="POST" action="{{ route('lms.exams.sync-journal', $exam) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm w-100 btn-outline-secondary">
                        <i class="fas fa-sync me-1"></i>Jurnalga sinxronlash
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-cog" style="color:var(--c-violet)"></i>
                <span>Sozlamalar</span>
            </div>
            <div class="card-body p-0">
                @php
                    $settingRows = [
                        ['Savollarni aralashtirish', $exam->shuffle_questions],
                        ['Javoblarni aralashtirish', $exam->shuffle_answers],
                        ["To'g'ri javobni ko'rsatish", $exam->show_correct_answers],
                        ["Natijani darhol ko'rsatish", $exam->show_score_immediately],
                        ["Jurnalga o'tkazish", $exam->sync_to_journal],
                    ];
                @endphp
                @foreach($settingRows as [$label, $val])
                <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-bottom:1px solid var(--c-border)">
                    <span style="font-size:12px;color:var(--c-text-2)">{{ $label }}</span>
                    <i class="fas {{ $val ? 'fa-check' : 'fa-times' }}" style="color:{{ $val ? 'var(--c-emerald)' : 'var(--c-rose)' }}"></i>
                </div>
                @endforeach
            </div>
        </div>

        @if($exam->group_ids && count($exam->group_ids) > 0)
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-users" style="color:var(--c-sky)"></i>
                <span>Guruhlar</span>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-1">
                    @foreach(\App\Models\Group::whereIn('id', $exam->group_ids)->get() as $group)
                    <span class="badge" style="background:rgba(14,165,233,.12);color:var(--c-sky);font-size:11px">{{ $group->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
