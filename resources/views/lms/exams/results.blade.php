@extends('layouts.dashboard-new')

@section('title', 'Natijalar — ' . $exam->title)
@section('page-title', $exam->title . ' — Natijalar')

@section('content')

<x-lms-alerts />

{{-- Action buttons --}}
<div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
    @if($exam->sync_to_journal)
    <form method="POST" action="{{ route('lms.exams.sync-journal', $exam) }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm" style="background:var(--c-violet);color:#fff">
            <i class="fas fa-sync me-1"></i>Jurnalga sinxronlash
        </button>
    </form>
    @endif
    <a href="{{ route('lms.exams.show', $exam) }}" class="btn btn-sm btn-outline-secondary ms-auto">
        <i class="fas fa-arrow-left me-1"></i>Ortga
    </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
        $statCards = [
            ['fas fa-list-ol','var(--c-violet)','rgba(124,58,237,.1)',$attempts->total(),'Jami urinishlar'],
            ['fas fa-check-circle','var(--c-emerald)','rgba(16,185,129,.1)',$attempts->where('passed',true)->count(),"O'tganlar"],
            ['fas fa-times-circle','var(--c-rose)','rgba(244,63,94,.1)',$attempts->where('passed',false)->count(),'Yiqilganlar'],
            ['fas fa-star','var(--c-amber)','rgba(245,158,11,.1)',number_format($exam->getAverageScore() ?? 0,1),"O'rtacha ball"],
            ['fas fa-percentage','var(--c-sky)','rgba(14,165,233,.1)',$exam->getPassRate().'%',"O'tish foizi"],
        ];
    @endphp
    @foreach($statCards as [$icon,$color,$bg,$value,$label])
    <div class="col-6 col-sm-4 col-lg">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:{{ $bg }};color:{{ $color }}"><i class="{{ $icon }}"></i></div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $value }}</div>
            <div class="stat-card-label">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Results table --}}
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-table" style="color:var(--c-teal)"></i>
        <span>Natijalar jadvali</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:13px">
            <thead style="background:var(--c-bg)">
                <tr style="color:var(--c-text-3);font-size:11px;text-transform:uppercase">
                    <th class="px-4 py-3 fw-semibold">#</th>
                    <th class="px-4 py-3 fw-semibold">Talaba</th>
                    <th class="px-4 py-3 fw-semibold">Guruh</th>
                    <th class="px-4 py-3 fw-semibold">Ball</th>
                    <th class="px-4 py-3 fw-semibold">Foiz</th>
                    <th class="px-4 py-3 fw-semibold">Baho</th>
                    <th class="px-4 py-3 fw-semibold">Vaqt</th>
                    <th class="px-4 py-3 fw-semibold">Holat</th>
                    <th class="px-4 py-3 fw-semibold">Sana</th>
                    <th class="px-4 py-3 fw-semibold"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $index => $attempt)
                <tr>
                    <td class="px-4 py-3" style="color:var(--c-text-3)">{{ $attempts->firstItem() + $index }}</td>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;background:rgba(124,58,237,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fas fa-user" style="color:var(--c-violet);font-size:11px"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;color:var(--c-text)">{{ $attempt->student->full_name ?? "Noma'lum" }}</div>
                                <div style="font-size:11px;color:var(--c-text-3)">{{ $attempt->student->student_id ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3" style="color:var(--c-text-2)">{{ $attempt->student->group->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span style="font-size:15px;font-weight:700;color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}">
                            {{ number_format($attempt->score ?? 0, 1) }}
                        </span>
                        <span style="color:var(--c-text-3);font-size:11px">/ {{ $exam->max_score }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress" style="width:50px;height:6px;background:var(--c-border);border-radius:3px;overflow:hidden">
                                <div style="width:{{ $attempt->percentage ?? 0 }}%;height:100%;background:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}"></div>
                            </div>
                            <span style="font-size:12px;color:var(--c-text-2)">{{ $attempt->percentage ?? 0 }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span style="font-size:18px;font-weight:700;color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}">
                            {{ $attempt->getGrade() }}
                        </span>
                    </td>
                    <td class="px-4 py-3" style="color:var(--c-text-2)">{{ $attempt->getFormattedTimeSpent() }}</td>
                    <td class="px-4 py-3">
                        <span class="badge" style="font-size:10px;background:{{ $attempt->passed ? 'rgba(16,185,129,.12)' : 'rgba(244,63,94,.12)' }};color:{{ $attempt->passed ? 'var(--c-emerald)' : 'var(--c-rose)' }}">
                            {{ $attempt->getStatusLabel() }}
                        </span>
                        @if($attempt->synced_to_journal)
                        <span class="badge d-block mt-1" style="font-size:10px;background:rgba(124,58,237,.12);color:var(--c-violet)">
                            <i class="fas fa-check me-1"></i>Jurnalda
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3" style="color:var(--c-text-2);font-size:12px">
                        {{ $attempt->finished_at ? $attempt->finished_at->format('d.m.Y H:i') : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('lms.exams.attempt', $attempt) }}"
                           style="color:var(--c-teal);font-size:13px" title="Batafsil">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5" style="color:var(--c-text-3)">
                        <i class="fas fa-inbox mb-1" style="display:block;font-size:28px"></i>Hali natijalar yo'q
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($attempts->hasPages())
    <div class="card-body py-3" style="border-top:1px solid var(--c-border)">
        {{ $attempts->links() }}
    </div>
    @endif
</div>

@endsection
