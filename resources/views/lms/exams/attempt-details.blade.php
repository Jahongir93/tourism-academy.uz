@extends('layouts.dashboard-new')

@section('title', 'Urinish tafsilotlari — LMS')
@section('page-title', 'Urinish tafsilotlari')

@section('content')

<x-lms-alerts />

{{-- Back --}}
<div class="mb-4">
    <a href="{{ route('lms.exams.results', $attempt->exam) }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Natijalarga qaytish
    </a>
</div>

{{-- Student info + Result --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-user" style="color:var(--c-violet)"></i>
                <span>Talaba ma'lumotlari</span>
            </div>
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:52px;height:52px;background:rgba(124,58,237,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-user" style="color:var(--c-violet);font-size:20px"></i>
                </div>
                <div>
                    <div style="font-size:16px;font-weight:700;color:var(--c-text)">{{ $attempt->student->full_name ?? "Noma'lum" }}</div>
                    <div style="font-size:12px;color:var(--c-text-3)">{{ $attempt->student->student_id ?? '—' }}</div>
                    <div style="font-size:12px;color:var(--c-text-3)">{{ $attempt->student->group->name ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100" style="border-color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }};background:{{ $attempt->passed ? 'rgba(16,185,129,.06)' : 'rgba(244,63,94,.06)' }}">
            <div class="card-header d-flex align-items-center gap-2" style="border-color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}">
                <i class="fas fa-chart-bar" style="color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}"></i>
                <span>Natija</span>
                @if(!$attempt->synced_to_journal && $attempt->exam->sync_to_journal)
                <span class="badge ms-auto" style="background:rgba(245,158,11,.15);color:var(--c-amber);font-size:10px">
                    <i class="fas fa-exclamation-triangle me-1"></i>Jurnalga o'tkazilmagan
                </span>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-2 text-center">
                    @foreach([
                        ['Ball', number_format($attempt->score ?? 0,1)],
                        ['Foiz', ($attempt->percentage ?? 0).'%'],
                        ['Baho', $attempt->getGrade()],
                    ] as [$label,$val])
                    <div class="col-4">
                        <div style="font-size:24px;font-weight:700;color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}">{{ $val }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">{{ $label }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
        $statCards = [
            ['fas fa-check','var(--c-emerald)','rgba(16,185,129,.1)',$attempt->correct_answers,"To'g'ri"],
            ['fas fa-times','var(--c-rose)','rgba(244,63,94,.1)',$attempt->wrong_answers,"Noto'g'ri"],
            ['fas fa-minus','var(--c-text-3)','rgba(0,0,0,.06)',$attempt->unanswered,'Javobsiz'],
            ['fas fa-clock','var(--c-sky)','rgba(14,165,233,.1)',$attempt->getFormattedTimeSpent(),'Vaqt'],
            ['fas fa-window-restore','var(--c-amber)','rgba(245,158,11,.1)',$attempt->tab_switches,'Tab alm.'],
        ];
    @endphp
    @foreach($statCards as [$icon,$color,$bg,$val,$label])
    <div class="col-6 col-sm-4 col-lg">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:{{ $bg }};color:{{ $color }}"><i class="{{ $icon }}"></i></div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $val }}</div>
            <div class="stat-card-label">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Answers --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-list-check" style="color:var(--c-teal)"></i>
        <span>Javoblar tahlili</span>
    </div>
    @foreach($attempt->answers as $index => $answer)
    @php
        $ac = $answer->is_correct;
        $rowBg = $ac ? 'rgba(16,185,129,.04)' : ($ac === false ? 'rgba(244,63,94,.04)' : 'rgba(245,158,11,.04)');
        $numColor = $ac ? 'var(--c-emerald)' : ($ac === false ? 'var(--c-rose)' : 'var(--c-amber)');
        $numBg = $ac ? 'rgba(16,185,129,.15)' : ($ac === false ? 'rgba(244,63,94,.15)' : 'rgba(245,158,11,.15)');
    @endphp
    <div style="border-bottom:1px solid var(--c-border);padding:16px 20px;background:{{ $rowBg }}">
        <div class="d-flex align-items-start gap-3">
            <div style="width:32px;height:32px;border-radius:8px;background:{{ $numBg }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:{{ $numColor }};flex-shrink:0">
                {{ $index + 1 }}
            </div>
            <div style="flex:1;min-width:0">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <p style="font-size:13px;color:var(--c-text);margin:0">{!! nl2br(e($answer->question->question_text)) !!}</p>
                    <span style="font-size:12px;font-weight:600;color:{{ ($answer->points_earned ?? 0) > 0 ? 'var(--c-emerald)' : 'var(--c-rose)' }};white-space:nowrap;flex-shrink:0">
                        {{ $answer->points_earned ?? 0 }} / {{ $answer->question->points }} ball
                    </span>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <div class="p-2 rounded" style="background:{{ $ac ? 'rgba(16,185,129,.1)' : 'rgba(244,63,94,.1)' }};border:1px solid {{ $ac ? 'rgba(16,185,129,.2)' : 'rgba(244,63,94,.2)' }}">
                            <div style="font-size:10px;color:var(--c-text-3);margin-bottom:2px">Talaba javobi:</div>
                            <div style="font-size:12px;font-weight:600;color:{{ $ac ? 'var(--c-emerald)' : 'var(--c-rose)' }}">
                                {{ $answer->getFormattedAnswer() ?: 'Javob berilmagan' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 rounded" style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2)">
                            <div style="font-size:10px;color:var(--c-text-3);margin-bottom:2px">To'g'ri javob:</div>
                            <div style="font-size:12px;font-weight:600;color:var(--c-emerald)">{{ $answer->getCorrectAnswerFormatted() }}</div>
                        </div>
                    </div>
                </div>

                @if($answer->question->question_type == 'essay' && $answer->is_correct === null)
                <form method="POST" action="{{ route('lms.exams.grade-answer', $answer) }}"
                      class="p-3 rounded mt-2" style="background:var(--c-bg);border:2px dashed var(--c-border)">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px">Ball</label>
                            <input type="number" name="points" min="0" max="{{ $answer->question->points }}" step="0.5"
                                   value="{{ $answer->points_earned ?? '' }}" required class="form-control form-control-sm">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label" style="font-size:12px">Izoh</label>
                            <input type="text" name="feedback" value="{{ $answer->feedback }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm mt-2" style="background:var(--c-violet);color:#fff">
                        <i class="fas fa-check me-1"></i>Baholash
                    </button>
                </form>
                @endif

                @if($answer->feedback)
                <div class="p-2 rounded mt-2" style="background:rgba(14,165,233,.08);border:1px solid rgba(14,165,233,.15)">
                    <div style="font-size:10px;color:var(--c-sky);margin-bottom:2px">O'qituvchi izohi:</div>
                    <div style="font-size:12px;color:var(--c-text-2)">{{ $answer->feedback }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Activity log --}}
@if($attempt->activity_log && count($attempt->activity_log) > 0)
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-history" style="color:var(--c-text-3)"></i>
        <span>Faollik logi</span>
    </div>
    <div class="card-body p-0" style="max-height:240px;overflow-y:auto">
        @foreach($attempt->activity_log as $log)
        <div class="d-flex align-items-center gap-3 px-4 py-2" style="border-bottom:1px solid var(--c-border);font-size:12px">
            <span style="color:var(--c-text-3);flex-shrink:0">{{ \Carbon\Carbon::parse($log['timestamp'])->format('H:i:s') }}</span>
            <span style="color:var(--c-text-2)">{{ $log['action'] }}</span>
            @if(isset($log['ip']))<span style="color:var(--c-text-3)">{{ $log['ip'] }}</span>@endif
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
