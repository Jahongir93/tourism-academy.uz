@extends('layouts.dashboard-new')

@section('title', $exam->title . ' — LMS')
@section('page-title', $exam->title)

@section('content')

<x-lms-alerts />

<div class="row g-4">
    {{-- Main info --}}
    <div class="col-lg-8">

        {{-- Exam header card --}}
        <div class="card mb-4" style="border-color:var(--c-violet);background:rgba(124,58,237,.04)">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <span class="badge mb-2" style="background:rgba(124,58,237,.12);color:var(--c-violet)">{{ $exam->getExamTypeLabel() }}</span>
                        <h2 style="font-size:22px;font-weight:700;color:var(--c-text);margin-bottom:4px">{{ $exam->title }}</h2>
                        <p style="font-size:13px;color:var(--c-text-3);margin:0">{{ $exam->subject->name ?? '—' }}</p>
                    </div>
                    <div class="text-end">
                        <div style="font-size:32px;font-weight:700;color:var(--c-violet)">{{ $exam->max_score }}</div>
                        <div style="font-size:12px;color:var(--c-text-3)">ball</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Exam details --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-info-circle" style="color:var(--c-sky)"></i>
                <span>Imtihon haqida</span>
            </div>
            @if($exam->description)
            <div class="card-body" style="border-bottom:1px solid var(--c-border)">
                <p style="font-size:13px;color:var(--c-text-2);margin:0">{{ $exam->description }}</p>
            </div>
            @endif
            <div class="card-body p-0">
                <div class="row g-0">
                    @foreach([
                        ['fas fa-question-circle','var(--c-violet)',$exam->questions()->count(),'Savollar'],
                        ['fas fa-clock','var(--c-sky)',$exam->duration_minutes,'Daqiqa'],
                        ['fas fa-check','var(--c-emerald)',$exam->passing_score,"O'tish bali"],
                        ['fas fa-redo','var(--c-amber)',$exam->max_attempts,'Maks. urinish'],
                    ] as [$icon,$color,$val,$label])
                    <div class="col-6 col-sm-3 text-center p-3" style="border-bottom:1px solid var(--c-border);border-right:1px solid var(--c-border)">
                        <i class="{{ $icon }}" style="color:{{ $color }};font-size:18px;display:block;margin-bottom:4px"></i>
                        <div style="font-size:20px;font-weight:700;color:var(--c-text)">{{ $val }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">{{ $label }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($exam->instructions)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-list" style="color:var(--c-teal)"></i>
                <span>Ko'rsatmalar</span>
            </div>
            <div class="card-body">
                <div style="font-size:13px;color:var(--c-text-2);line-height:1.7">{!! nl2br(e($exam->instructions)) !!}</div>
            </div>
        </div>
        @endif

        {{-- Rules --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-shield-alt" style="color:var(--c-amber)"></i>
                <span>Qoidalar</span>
            </div>
            <div class="card-body p-0">
                @php $rules = [
                    ['fas fa-clock','var(--c-sky)', "Imtihon uchun {$exam->duration_minutes} daqiqa vaqt beriladi. Vaqt tugashi bilan javoblar avtomatik saqlanadi."],
                    ['fas fa-redo','var(--c-violet)', "Siz maksimum {$exam->max_attempts} marta urinishingiz mumkin."],
                ]; @endphp
                @if($exam->shuffle_questions) @php $rules[] = ['fas fa-random','var(--c-teal)','Savollar tasodifiy tartibda beriladi.']; @endphp @endif
                @if($exam->prevent_copy_paste) @php $rules[] = ['fas fa-ban','var(--c-rose)','Nusxalash va joylashtirish taqiqlangan.']; @endphp @endif
                @if(!$exam->show_correct_answers) @php $rules[] = ['fas fa-eye-slash','var(--c-amber)',"To'g'ri javoblar ko'rsatilmaydi."]; @endphp @endif
                @foreach($rules as [$icon,$color,$text])
                <div class="d-flex align-items-start gap-3 px-4 py-3" style="border-bottom:1px solid var(--c-border)">
                    <i class="{{ $icon }}" style="color:{{ $color }};margin-top:2px;flex-shrink:0"></i>
                    <span style="font-size:13px;color:var(--c-text-2)">{{ $text }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Previous attempts --}}
        @if($previousAttempts->count() > 0)
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-history" style="color:var(--c-violet)"></i>
                <span>Oldingi urinishlar</span>
            </div>
            <div class="card-body p-0">
                @foreach($previousAttempts as $attempt)
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid var(--c-border)">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:32px;height:32px;border-radius:8px;background:{{ $attempt->passed ? 'rgba(16,185,129,.1)' : 'rgba(244,63,94,.1)' }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}">
                            {{ $attempt->attempt_number }}
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--c-text)">Urinish #{{ $attempt->attempt_number }}</div>
                            <div style="font-size:11px;color:var(--c-text-3)">
                                {{ $attempt->finished_at ? $attempt->finished_at->format('d.m.Y H:i') : 'Tugallanmagan' }}
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:16px;font-weight:700;color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}">
                            {{ $attempt->score ?? '—' }} / {{ $exam->max_score }}
                        </div>
                        <span class="badge" style="font-size:10px;background:{{ $attempt->passed ? 'rgba(16,185,129,.12)' : 'rgba(244,63,94,.12)' }};color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}">
                            {{ $attempt->getStatusLabel() }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="card sticky-top" style="top:80px">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-play-circle" style="color:var(--c-violet)"></i>
                <span>Imtihonni boshlash</span>
            </div>
            <div class="card-body">
                @if($exam->start_time || $exam->end_time)
                <div class="mb-3 p-2 rounded" style="background:var(--c-bg);border:1px solid var(--c-border);font-size:12px;color:var(--c-text-2)">
                    @if($exam->start_time)
                    <div class="mb-1"><i class="fas fa-play me-1" style="color:var(--c-emerald)"></i>{{ $exam->start_time->format('d.m.Y H:i') }}</div>
                    @endif
                    @if($exam->end_time)
                    <div><i class="fas fa-stop me-1" style="color:var(--c-rose)"></i>{{ $exam->end_time->format('d.m.Y H:i') }}</div>
                    @endif
                </div>
                @endif

                @if($canAttempt['can_attempt'])
                    @if($exam->access_password)
                    <form method="POST" action="{{ route('lms.exams.start', $exam) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" style="font-size:12px">Kirish paroli</label>
                            <input type="password" name="password" class="form-control" required placeholder="Parolni kiriting">
                        </div>
                        <button type="submit" class="btn w-100" style="background:var(--c-violet);color:#fff">
                            <i class="fas fa-play me-1"></i>
                            {{ isset($canAttempt['attempt']) ? 'Davom ettirish' : 'Imtihonni boshlash' }}
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('lms.exams.start', $exam) }}">
                        @csrf
                        <button type="submit" class="btn w-100" style="background:var(--c-violet);color:#fff">
                            <i class="fas fa-play me-1"></i>
                            {{ isset($canAttempt['attempt']) ? 'Davom ettirish' : 'Imtihonni boshlash' }}
                        </button>
                    </form>
                    @endif
                    <p style="font-size:12px;color:var(--c-text-3);text-align:center;margin-top:8px">
                        Urinish {{ $previousAttempts->count() + 1 }} / {{ $exam->max_attempts }}
                    </p>
                @else
                <div class="p-3 rounded" style="background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.2)">
                    <span style="font-size:13px;color:var(--c-rose)"><i class="fas fa-lock me-1"></i>{{ $canAttempt['reason'] }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
