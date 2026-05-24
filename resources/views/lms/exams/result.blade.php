@extends('layouts.dashboard-new')

@section('title', 'Natija — ' . $exam->title)
@section('page-title', 'Imtihon natijasi')

@section('content')

@php
    $passed = $attempt->passed;
    $resultColor = $passed ? 'var(--c-emerald)' : 'var(--c-rose)';
    $resultBg = $passed ? 'rgba(16,185,129,.1)' : 'rgba(244,63,94,.1)';
@endphp

{{-- Result hero --}}
<div class="card mb-4 text-center" style="border-color:{{ $resultColor }};background:{{ $resultBg }}">
    <div class="card-body py-4">
        <div style="width:64px;height:64px;border-radius:50%;background:{{ $resultBg }};border:2px solid {{ $resultColor }};display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
            <i class="fas {{ $passed ? 'fa-check-circle' : 'fa-times-circle' }}" style="font-size:28px;color:{{ $resultColor }}"></i>
        </div>
        <h2 style="font-size:22px;font-weight:700;color:{{ $resultColor }};margin-bottom:4px">
            {{ $passed ? 'Tabriklaymiz!' : 'Afsuski...' }}
        </h2>
        <p style="font-size:14px;color:var(--c-text-2);margin-bottom:20px">{{ $exam->title }}</p>

        <div class="row g-3 justify-content-center">
            @foreach([
                ['Ball', number_format($attempt->score, 1)],
                ['Foiz', $attempt->percentage.'%'],
                ['Baho', $attempt->getGrade()],
                ['Vaqt', $attempt->getFormattedTimeSpent()],
            ] as [$label,$val])
            <div class="col-6 col-sm-3">
                <div class="p-3 rounded" style="background:rgba(255,255,255,.6);border:1px solid {{ $resultColor }}20">
                    <div style="font-size:24px;font-weight:700;color:{{ $resultColor }}">{{ $val }}</div>
                    <div style="font-size:11px;color:var(--c-text-3)">{{ $label }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
        $cards = [
            ['fas fa-check','var(--c-emerald)','rgba(16,185,129,.1)',$attempt->correct_answers,"To'g'ri javoblar"],
            ['fas fa-times','var(--c-rose)','rgba(244,63,94,.1)',$attempt->wrong_answers,"Noto'g'ri javoblar"],
            ['fas fa-minus','var(--c-text-3)','rgba(0,0,0,.06)',$attempt->unanswered,'Javobsiz'],
        ];
    @endphp
    @foreach($cards as [$icon,$color,$bg,$val,$label])
    <div class="col-4">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:{{ $bg }};color:{{ $color }}"><i class="{{ $icon }}"></i></div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $val }}</div>
            <div class="stat-card-label">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Answers review --}}
@if($showAnswers)
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-list-check" style="color:var(--c-teal)"></i>
        <span>Javoblar tahlili</span>
    </div>
    @foreach($attempt->answers as $index => $answer)
    @php
        $ac = $answer->is_correct;
        $rowBg = $ac ? 'rgba(16,185,129,.05)' : ($ac === false ? 'rgba(244,63,94,.05)' : 'var(--c-bg)');
        $numBg = $ac ? 'var(--c-emerald)' : ($ac === false ? 'var(--c-rose)' : 'var(--c-text-3)');
    @endphp
    <div style="border-bottom:1px solid var(--c-border);padding:16px 20px;background:{{ $rowBg }}">
        <div class="d-flex align-items-start gap-3">
            <div style="width:32px;height:32px;border-radius:8px;background:{{ $numBg }};display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0">
                {{ $index + 1 }}
            </div>
            <div style="flex:1;min-width:0">
                <p style="font-size:13px;color:var(--c-text);margin-bottom:10px">{!! nl2br(e($answer->question->question_text)) !!}</p>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <div class="p-2 rounded" style="background:{{ $ac ? 'rgba(16,185,129,.1)' : 'rgba(244,63,94,.1)' }};border:1px solid {{ $ac ? 'rgba(16,185,129,.2)' : 'rgba(244,63,94,.2)' }}">
                            <div style="font-size:10px;color:var(--c-text-3);margin-bottom:2px">Sizning javobingiz:</div>
                            <div style="font-size:12px;font-weight:600;color:{{ $ac ? 'var(--c-emerald)' : 'var(--c-rose)' }}">{{ $answer->getFormattedAnswer() }}</div>
                        </div>
                    </div>
                    @if(!$ac)
                    <div class="col-md-6">
                        <div class="p-2 rounded" style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2)">
                            <div style="font-size:10px;color:var(--c-text-3);margin-bottom:2px">To'g'ri javob:</div>
                            <div style="font-size:12px;font-weight:600;color:var(--c-emerald)">{{ $answer->getCorrectAnswerFormatted() }}</div>
                        </div>
                    </div>
                    @endif
                </div>
                @if($answer->question->explanation)
                <div class="p-2 rounded" style="background:rgba(14,165,233,.08);border:1px solid rgba(14,165,233,.15)">
                    <div style="font-size:10px;color:var(--c-sky);margin-bottom:2px">Izoh:</div>
                    <div style="font-size:12px;color:var(--c-text-2)">{{ $answer->question->explanation }}</div>
                </div>
                @endif
                <div style="font-size:11px;color:var(--c-text-3);margin-top:6px">
                    <i class="fas fa-star me-1" style="color:var(--c-amber)"></i>
                    {{ $answer->points_earned ?? 0 }} / {{ $answer->question->points }} ball
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card mb-4">
    <div class="card-body text-center py-5" style="color:var(--c-text-3)">
        <i class="fas fa-eye-slash mb-2" style="display:block;font-size:28px"></i>
        <div style="font-size:14px;color:var(--c-text-2)">Javoblar ko'rsatilmaydi</div>
        <div style="font-size:12px;margin-top:4px">Bu imtihonda to'g'ri javoblarni ko'rish imkoniyati yoqilmagan</div>
    </div>
</div>
@endif

{{-- Actions --}}
<div class="d-flex justify-content-center gap-3 flex-wrap">
    <a href="{{ route('lms.exams.my-list') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Imtihonlarga qaytish
    </a>
    @if($exam->canStudentAttempt(auth()->user()->student ?? new \App\Models\Student)['can_attempt'])
    <a href="{{ route('lms.exams.info', $exam) }}" class="btn"
       style="background:var(--c-violet);color:#fff">
        <i class="fas fa-redo me-1"></i>Qayta topshirish
    </a>
    @endif
</div>

@endsection
