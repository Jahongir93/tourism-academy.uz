@extends('layouts.dashboard-new')

@section('title', 'Imtihon ma\'lumotlari')
@section('page-title', 'Imtihon ma\'lumotlari')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-file-alt me-2"></i>{{ $exam->title }}</h4>
                            <p class="mb-0 opacity-75">
                                {{ $exam->subject?->name_uz ?? '' }}
                                @switch($exam->status)
                                    @case('draft')
                                        <span class="badge bg-secondary ms-2">Qoralama</span>
                                        @break
                                    @case('scheduled')
                                        <span class="badge bg-info ms-2">Rejalashtirilgan</span>
                                        @break
                                    @case('active')
                                        <span class="badge bg-success ms-2">Faol</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-primary ms-2">Yakunlangan</span>
                                        @break
                                @endswitch
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('dean.schedule.exams.edit', $exam) }}" class="btn btn-light me-2">
                                <i class="fas fa-edit me-1"></i> Tahrirlash
                            </a>
                            <a href="{{ route('dean.schedule.exams') }}" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-1"></i> Orqaga
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistika -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-primary">{{ $exam->questions->count() }}</h3>
                    <p class="text-muted mb-0">Savollar soni</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-success">{{ $exam->attempts->count() }}</h3>
                    <p class="text-muted mb-0">Jami urinishlar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-info">{{ number_format($exam->getAverageScore() ?? 0, 1) }}</h3>
                    <p class="text-muted mb-0">O'rtacha ball</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-warning">{{ number_format($exam->getPassRate(), 1) }}%</h3>
                    <p class="text-muted mb-0">O'tish foizi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Asosiy ma'lumotlar -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Asosiy ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Imtihon turi:</td>
                            <td>
                                @switch($exam->exam_type)
                                    @case('joriy')
                                        <span class="badge bg-info">Joriy nazorat</span>
                                        @break
                                    @case('oraliq')
                                        <span class="badge bg-warning text-dark">Oraliq nazorat</span>
                                        @break
                                    @case('yakuniy')
                                        <span class="badge bg-danger">Yakuniy nazorat</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $exam->exam_type }}</span>
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fan:</td>
                            <td>{{ $exam->subject?->name_uz ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">O'qituvchi:</td>
                            <td>{{ $exam->teacher?->full_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Davomiylik:</td>
                            <td>{{ $exam->duration_minutes }} daqiqa</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Maksimal ball:</td>
                            <td>{{ $exam->max_score }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">O'tish balli:</td>
                            <td>{{ $exam->passing_score }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Maks. urinishlar:</td>
                            <td>{{ $exam->max_attempts }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">E'lon qilingan:</td>
                            <td>
                                @if($exam->is_published)
                                    <span class="badge bg-success">Ha</span>
                                @else
                                    <span class="badge bg-secondary">Yo'q</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Vaqt ma'lumotlari -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-clock text-success me-2"></i>Vaqt ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Boshlanish:</td>
                            <td>{{ $exam->start_time ? $exam->start_time->format('d.m.Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tugash:</td>
                            <td>{{ $exam->end_time ? $exam->end_time->format('d.m.Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Holat:</td>
                            <td>
                                @if($exam->isExpired())
                                    <span class="badge bg-secondary">Vaqti o'tgan</span>
                                @elseif($exam->isAvailable())
                                    <span class="badge bg-success">Mavjud</span>
                                @else
                                    <span class="badge bg-warning text-dark">Kutilmoqda</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Yaratilgan:</td>
                            <td>{{ $exam->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Yangilangan:</td>
                            <td>{{ $exam->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($exam->description)
                    <hr>
                    <h6 class="fw-semibold">Tavsif:</h6>
                    <p class="text-muted mb-0">{{ $exam->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Guruhlar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-users text-info me-2"></i>Ishtirokchi guruhlar</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                @forelse($groups as $group)
                <span class="badge bg-primary fs-6 py-2 px-3">
                    <i class="fas fa-users me-1"></i> {{ $group->name }}
                </span>
                @empty
                <p class="text-muted mb-0">Guruhlar tanlanmagan</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Urinishlar -->
    @if($exam->attempts->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-history text-warning me-2"></i>So'nggi urinishlar</h5>
            <span class="badge bg-info">{{ $exam->attempts->count() }} ta</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Talaba</th>
                            <th class="border-0">Boshlangan</th>
                            <th class="border-0">Yakunlangan</th>
                            <th class="border-0">Ball</th>
                            <th class="border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exam->attempts->take(10) as $attempt)
                        <tr>
                            <td>{{ $attempt->student?->full_name ?? '-' }}</td>
                            <td>{{ $attempt->started_at ? $attempt->started_at->format('d.m.Y H:i') : '-' }}</td>
                            <td>{{ $attempt->finished_at ? $attempt->finished_at->format('d.m.Y H:i') : '-' }}</td>
                            <td>
                                <span class="badge {{ $attempt->passed ? 'bg-success' : 'bg-danger' }}">
                                    {{ $attempt->score ?? '-' }} / {{ $exam->max_score }}
                                </span>
                            </td>
                            <td>
                                @switch($attempt->status)
                                    @case('in_progress')
                                        <span class="badge bg-warning text-dark">Jarayonda</span>
                                        @break
                                    @case('submitted')
                                        <span class="badge bg-info">Topshirildi</span>
                                        @break
                                    @case('graded')
                                        <span class="badge bg-success">Baholandi</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $attempt->status }}</span>
                                @endswitch
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<style>.bg-gradient-info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }</style>
@endsection
