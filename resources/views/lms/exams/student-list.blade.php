@extends('layouts.dashboard-new')

@section('title', 'Imtihonlar — LMS')
@section('page-title', 'Mening imtihonlarim')

@section('content')

{{-- Filter tabs --}}
<div class="d-flex gap-2 mb-4 flex-wrap">
    @php
        $tabs = [
            ['Barchasi', null],
            ['Joriy', 'joriy'],
            ['Oraliq', 'oraliq'],
            ['Yakuniy', 'yakuniy'],
        ];
    @endphp
    @foreach($tabs as [$label, $type])
    @php $active = request('exam_type') == $type; @endphp
    <a href="{{ route('lms.exams.my-list', $type ? ['exam_type' => $type] : []) }}"
       class="btn btn-sm {{ $active ? '' : 'btn-outline-secondary' }}"
       style="{{ $active ? 'background:var(--c-violet);color:#fff' : '' }}">
        {{ $label }}
    </a>
    @endforeach
    <span class="ms-auto" style="font-size:13px;color:var(--c-text-3);align-self:center">
        Jami: {{ $exams->total() }} ta imtihon
    </span>
</div>

{{-- Exams grid --}}
<div class="row g-3">
    @forelse($exams as $exam)
    @php
        $attemptCount = $attemptCounts[$exam->id] ?? 0;
        $canAttempt = $exam->canStudentAttempt($student);
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-2">
                <span class="badge" style="background:rgba(124,58,237,.12);color:var(--c-violet);font-size:11px">
                    {{ $exam->getExamTypeLabel() }}
                </span>
                <span style="font-size:12px;color:var(--c-text-3)">
                    <i class="fas fa-clock me-1"></i>{{ $exam->duration_minutes }} daqiqa
                </span>
            </div>
            <div class="card-body">
                <h5 style="font-size:14px;font-weight:700;color:var(--c-text);margin-bottom:4px">{{ $exam->title }}</h5>
                <p style="font-size:12px;color:var(--c-text-3);margin-bottom:12px">{{ $exam->subject->name ?? '—' }}</p>

                <div style="font-size:12px;color:var(--c-text-2)">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fas fa-question-circle me-1" style="color:var(--c-violet)"></i>Savollar</span>
                        <strong>{{ $exam->questions()->count() }} ta</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fas fa-star me-1" style="color:var(--c-amber)"></i>Maksimal ball</span>
                        <strong>{{ $exam->max_score }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-redo me-1" style="color:var(--c-emerald)"></i>Urinishlar</span>
                        <strong>{{ $attemptCount }} / {{ $exam->max_attempts }}</strong>
                    </div>
                </div>

                @if($exam->start_time || $exam->end_time)
                <div class="p-2 rounded mb-2" style="background:var(--c-bg);border:1px solid var(--c-border);font-size:11px;color:var(--c-text-3)">
                    @if($exam->start_time)
                    <div><i class="fas fa-play me-1" style="color:var(--c-emerald)"></i>{{ $exam->start_time->format('d.m.Y H:i') }}</div>
                    @endif
                    @if($exam->end_time)
                    <div><i class="fas fa-stop me-1" style="color:var(--c-rose)"></i>{{ $exam->end_time->format('d.m.Y H:i') }}</div>
                    @endif
                </div>
                @endif

                @if($canAttempt['can_attempt'])
                <a href="{{ route('lms.exams.info', $exam) }}" class="btn btn-sm w-100"
                   style="background:var(--c-violet);color:#fff">
                    <i class="fas fa-play me-1"></i>
                    {{ isset($canAttempt['attempt']) ? 'Davom ettirish' : 'Boshlash' }}
                </a>
                @else
                <div class="p-2 rounded text-center" style="background:var(--c-bg);border:1px solid var(--c-border);font-size:12px;color:var(--c-text-3)">
                    <i class="fas fa-lock me-1"></i>{{ $canAttempt['reason'] }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5" style="color:var(--c-text-3)">
                <i class="fas fa-file-alt mb-2" style="display:block;font-size:36px"></i>
                <div style="font-size:14px;color:var(--c-text-2)">Imtihonlar topilmadi</div>
                <div style="font-size:12px;margin-top:4px">Hozircha sizga tayinlangan imtihonlar mavjud emas</div>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($exams->hasPages())
<div class="mt-4">
    {{ $exams->links() }}
</div>
@endif

@endsection
