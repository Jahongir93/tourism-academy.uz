@extends('layouts.dashboard-new')

@section('title', 'Interaktiv testlar — LMS')
@section('page-title', 'Interaktiv testlar')

@section('styles')
<style>
.lms-test-card {
    background:var(--c-surface);border:1px solid var(--c-border);border-radius:12px;
    overflow:hidden;transition:all .2s;display:flex;flex-direction:column;
}
.lms-test-card:hover { transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.08);border-color:var(--c-teal); }
.lms-info-row {
    display:flex;align-items:center;justify-content:space-between;
    padding:10px 12px;border-radius:8px;margin-bottom:6px;
}
</style>
@endsection

@section('content')

{{-- Stat cards --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-sky)">
            <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ collect($tests ?? [])->where('test_type','quiz')->count() }}</div>
            <div class="stat-card-label">Quiz testlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ collect($tests ?? [])->where('test_type','midterm')->count() }}</div>
            <div class="stat-card-label">Oraliq testlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-rose)">
            <div class="stat-card-icon" style="background:rgba(244,63,94,.12);color:var(--c-rose)">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ collect($tests ?? [])->where('test_type','final')->count() }}</div>
            <div class="stat-card-label">Yakuniy testlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-emerald)">
            <div class="stat-card-icon" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">
                <i class="fas fa-dumbbell"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ collect($tests ?? [])->where('test_type','practice')->count() }}</div>
            <div class="stat-card-label">Mashq testlar</div>
        </div>
    </div>
</div>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-clipboard-list" style="color:var(--c-teal)"></i>
        <span style="font-weight:700;color:var(--c-text)">Testlar ro'yxati</span>
        @if(count($tests ?? []))
        <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">{{ count($tests) }} ta</span>
        @endif
    </div>
    @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
    <div class="d-flex gap-2">
        <a href="{{ route('lms.tests.import') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-file-excel me-1" style="color:var(--c-emerald)"></i>Import
        </a>
        <a href="{{ route('lms.tests.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Test yaratish
        </a>
    </div>
    @endif
</div>

<x-lms-alerts />

{{-- Test cards --}}
<div class="row g-4">
    @forelse($tests ?? [] as $test)
    @php
        $typeConfig = [
            'quiz'     => ['rgba(14,165,233,.12)','var(--c-sky)','Quiz','fa-question'],
            'midterm'  => ['rgba(245,158,11,.12)','var(--c-amber)',"Oraliq",'fa-graduation-cap'],
            'final'    => ['rgba(244,63,94,.12)','var(--c-rose)',"Yakuniy",'fa-graduation-cap'],
            'practice' => ['rgba(16,185,129,.12)','var(--c-emerald)',"Mashq",'fa-dumbbell'],
        ];
        [$tbg,$tc,$tlabel,$ticon] = $typeConfig[$test->test_type] ?? ['rgba(148,163,184,.12)','var(--c-text-3)',$test->test_type,'fa-clipboard'];
    @endphp
    <div class="col-12 col-md-6 col-xl-4">
        <div class="lms-test-card">
            <div class="p-4" style="border-bottom:1px solid var(--c-border)">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <span class="badge" style="background:{{ $tbg }};color:{{ $tc }};font-size:11px;padding:6px 10px">
                        <i class="fas {{ $ticon }} me-1"></i>{{ $tlabel }}
                    </span>
                    @if($test->isAvailable())
                        <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:11px">
                            <i class="fas fa-check-circle me-1"></i>Faol
                        </span>
                    @else
                        <span class="badge" style="background:rgba(244,63,94,.12);color:var(--c-rose);font-size:11px">
                            <i class="fas fa-times-circle me-1"></i>Faol emas
                        </span>
                    @endif
                </div>
                <div style="font-weight:700;font-size:14px;color:var(--c-text);margin-bottom:8px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.8em">
                    {{ $test->title }}
                </div>
                @if($test->subject)
                <span class="badge" style="background:rgba(14,165,233,.12);color:var(--c-sky);font-size:10px">
                    <i class="fas fa-book me-1"></i>{{ $test->subject->name_uz }}
                </span>
                @endif
            </div>
            <div class="p-4" style="flex:1;display:flex;flex-direction:column">
                <div class="lms-info-row mb-0" style="background:rgba(14,165,233,.06)">
                    <div class="d-flex align-items-center gap-2" style="font-size:13px;color:var(--c-text-2)">
                        <i class="fas fa-question-circle" style="color:var(--c-sky)"></i>
                        Savollar soni
                    </div>
                    <strong style="color:var(--c-text)">{{ $test->question_count ?? 0 }}</strong>
                </div>
                <div class="lms-info-row" style="background:rgba(16,185,129,.06)">
                    <div class="d-flex align-items-center gap-2" style="font-size:13px;color:var(--c-text-2)">
                        <i class="fas fa-clock" style="color:var(--c-emerald)"></i>
                        Vaqt
                    </div>
                    <strong style="color:var(--c-text)">{{ $test->time_limit ?? 0 }} daq</strong>
                </div>
                <div class="lms-info-row" style="background:rgba(124,58,237,.06)">
                    <div class="d-flex align-items-center gap-2" style="font-size:13px;color:var(--c-text-2)">
                        <i class="fas fa-percentage" style="color:var(--c-violet)"></i>
                        O'tish bali
                    </div>
                    <strong style="color:var(--c-text)">{{ $test->passing_score ?? 0 }}%</strong>
                </div>

                {{-- Action --}}
                @if($test->isAvailable())
                    <a href="{{ route('lms.tests.take', $test) }}" class="btn btn-sm mt-2"
                       style="background:var(--c-teal);color:#fff">
                        <i class="fas fa-play me-1"></i>Testni boshlash
                    </a>
                @else
                    <button disabled class="btn btn-sm btn-outline-secondary mt-2">
                        <i class="fas fa-lock me-1"></i>Mavjud emas
                    </button>
                @endif

                @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
                    @if(Auth::user()->id == $test->teacher_id || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
                    <div class="d-flex gap-2 mt-2">
                        <a href="{{ route('lms.tests.edit', $test) }}" class="btn btn-sm btn-outline-secondary flex-fill" style="font-size:12px">
                            <i class="fas fa-edit me-1"></i>Tahrirlash
                        </a>
                        <a href="{{ route('lms.tests.results', $test) }}" class="btn btn-sm flex-fill"
                           style="background:rgba(14,165,233,.12);color:var(--c-sky);font-size:12px">
                            <i class="fas fa-chart-bar me-1"></i>Natijalar
                        </a>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="empty-state py-5">
                    <div class="empty-state-icon"><i class="fas fa-clipboard-check"></i></div>
                    <div class="empty-state-sub">Testlar topilmadi</div>
                    @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
                    <div class="mt-3">
                        <a href="{{ route('lms.tests.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Test yaratish
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforelse
</div>

@endsection
