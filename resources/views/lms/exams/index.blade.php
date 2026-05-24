@extends('layouts.dashboard-new')

@section('title', 'Imtihonlar — LMS')
@section('page-title', 'Imtihonlar')

@section('styles')
<style>
.action-btn { display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;border:none;background:transparent;cursor:pointer;transition:all .15s;font-size:13px;padding:0;text-decoration:none; }
.action-btn:hover { background:var(--c-bg); }
</style>
@endsection

@section('content')

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('lms.exams.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Qidirish</label>
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Imtihon nomi..." class="form-control" style="padding-left:34px!important">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Turi</label>
                    <select name="exam_type" class="form-select">
                        <option value="">Barcha turlar</option>
                        <option value="joriy"   {{ request('exam_type') == 'joriy'   ? 'selected' : '' }}>Joriy nazorat</option>
                        <option value="oraliq"  {{ request('exam_type') == 'oraliq'  ? 'selected' : '' }}>Oraliq nazorat</option>
                        <option value="yakuniy" {{ request('exam_type') == 'yakuniy' ? 'selected' : '' }}>Yakuniy nazorat</option>
                        <option value="practice"{{ request('exam_type') == 'practice'? 'selected' : '' }}>Mashq test</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Holat</label>
                    <select name="status" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="draft"     {{ request('status') == 'draft'     ? 'selected' : '' }}>Qoralama</option>
                        <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Faol</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Yakunlangan</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Fan</label>
                    <select name="subject_id" class="form-select">
                        <option value="">Barcha fanlar</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2 align-self-end">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="fas fa-search me-1"></i>Qidirish</button>
                    <a href="{{ route('lms.exams.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-file-alt" style="color:var(--c-teal)"></i>
            <span>Imtihonlar ro'yxati</span>
            @if($exams->total())
            <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">{{ $exams->total() }} ta</span>
            @endif
        </div>
        <a href="{{ route('lms.exams.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Yangi imtihon
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Imtihon</th>
                        <th style="width:130px">Fan</th>
                        <th style="width:110px" class="text-center">Turi</th>
                        <th style="width:140px">Vaqt</th>
                        <th style="width:90px" class="text-center">Savollar</th>
                        <th style="width:100px" class="text-center">Holat</th>
                        <th style="width:120px" class="text-center">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:40px;height:40px;background:rgba(20,184,166,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="fas fa-file-alt" style="color:var(--c-teal)"></i>
                                </div>
                                <div>
                                    <a href="{{ route('lms.exams.show', $exam) }}"
                                       style="font-weight:700;font-size:13px;color:var(--c-text);text-decoration:none">
                                        {{ $exam->title }}
                                    </a>
                                    <div style="font-size:11px;color:var(--c-text-3)">{{ $exam->duration_minutes }} daqiqa</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;color:var(--c-text-2)">{{ $exam->subject->name ?? '—' }}</td>
                        <td class="text-center">
                            @php
                                $typeColors = [
                                    'joriy'   => ['rgba(14,165,233,.12)','var(--c-sky)','Joriy'],
                                    'oraliq'  => ['rgba(245,158,11,.12)','var(--c-amber)','Oraliq'],
                                    'yakuniy' => ['rgba(244,63,94,.12)','var(--c-rose)','Yakuniy'],
                                    'practice'=> ['rgba(16,185,129,.12)','var(--c-emerald)','Mashq'],
                                ];
                                [$ebg,$ec,$elabel] = $typeColors[$exam->exam_type] ?? ['rgba(148,163,184,.12)','var(--c-text-3)',$exam->getExamTypeLabel()];
                            @endphp
                            <span class="badge" style="background:{{ $ebg }};color:{{ $ec }};font-size:11px">
                                {{ $elabel }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--c-text-2)">
                            @if($exam->start_time)
                            <div><i class="fas fa-play me-1" style="color:var(--c-emerald)"></i>{{ $exam->start_time->format('d.m.Y H:i') }}</div>
                            @endif
                            @if($exam->end_time)
                            <div><i class="fas fa-stop me-1" style="color:var(--c-rose)"></i>{{ $exam->end_time->format('d.m.Y H:i') }}</div>
                            @endif
                            @if(!$exam->start_time && !$exam->end_time)
                            <span style="color:var(--c-text-3)">Cheklanmagan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">
                                {{ $exam->questions()->count() }} ta
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">
                                {{ $exam->getStatusLabel() }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('lms.exams.show', $exam) }}" class="action-btn" title="Ko'rish" style="color:var(--c-sky)">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('lms.exams.questions', $exam) }}" class="action-btn" title="Savollar" style="color:var(--c-teal)">
                                    <i class="fas fa-list-ol"></i>
                                </a>
                                <a href="{{ route('lms.exams.edit', $exam) }}" class="action-btn" title="Tahrirlash" style="color:var(--c-amber)">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('lms.exams.results', $exam) }}" class="action-btn" title="Natijalar" style="color:var(--c-violet)">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                @if(!$exam->is_published)
                                <form method="POST" action="{{ route('lms.exams.publish', $exam) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="action-btn" title="E'lon qilish" style="color:var(--c-emerald)">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('lms.exams.unpublish', $exam) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="action-btn" title="To'xtatish" style="color:var(--c-amber)">
                                        <i class="fas fa-pause-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state py-5">
                                <div class="empty-state-icon"><i class="fas fa-file-alt"></i></div>
                                <div class="empty-state-sub">Imtihonlar topilmadi</div>
                                <div class="mt-3">
                                    <a href="{{ route('lms.exams.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Imtihon yaratish
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($exams->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid var(--c-border)">
            {{ $exams->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
