@extends('layouts.dashboard-new')

@section('title', 'Imtihon jadvali')
@section('page-title', 'Imtihon jadvali')

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-danger text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-file-alt me-2"></i>Imtihon jadvali</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }} imtihonlari</p>
                        </div>
                        <div>
                            <a href="{{ route('dean.schedule.index') }}" class="btn btn-light me-2">
                                <i class="fas fa-calendar-alt me-1"></i> Dars jadvali
                            </a>
                            <a href="{{ route('dean.schedule.exams.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Yangi imtihon
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtrlar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('dean.schedule.exams') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Qoralama</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Rejalashtirilgan</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Yakunlangan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Imtihon turi</label>
                    <select name="exam_type" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="joriy" {{ request('exam_type') == 'joriy' ? 'selected' : '' }}>Joriy nazorat</option>
                        <option value="oraliq" {{ request('exam_type') == 'oraliq' ? 'selected' : '' }}>Oraliq nazorat</option>
                        <option value="yakuniy" {{ request('exam_type') == 'yakuniy' ? 'selected' : '' }}>Yakuniy nazorat</option>
                        <option value="practice" {{ request('exam_type') == 'practice' ? 'selected' : '' }}>Mashq test</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Qidirish
                    </button>
                    <a href="{{ route('dean.schedule.exams') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Imtihonlar ro'yxati -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list text-primary me-2"></i>Imtihonlar ro'yxati</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Imtihon nomi</th>
                            <th class="border-0">Fan</th>
                            <th class="border-0">Turi</th>
                            <th class="border-0">Boshlanish</th>
                            <th class="border-0">Tugash</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $exam->title }}</div>
                                <small class="text-muted">{{ $exam->duration_minutes }} daqiqa</small>
                            </td>
                            <td>{{ $exam->subject?->name_uz ?? '-' }}</td>
                            <td>
                                @switch($exam->exam_type)
                                    @case('joriy')
                                        <span class="badge bg-info">Joriy</span>
                                        @break
                                    @case('oraliq')
                                        <span class="badge bg-warning text-dark">Oraliq</span>
                                        @break
                                    @case('yakuniy')
                                        <span class="badge bg-danger">Yakuniy</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $exam->exam_type }}</span>
                                @endswitch
                            </td>
                            <td>{{ $exam->start_time ? $exam->start_time->format('d.m.Y H:i') : '-' }}</td>
                            <td>{{ $exam->end_time ? $exam->end_time->format('d.m.Y H:i') : '-' }}</td>
                            <td>
                                @switch($exam->status)
                                    @case('draft')
                                        <span class="badge bg-secondary">Qoralama</span>
                                        @break
                                    @case('scheduled')
                                        <span class="badge bg-info">Rejalashtirilgan</span>
                                        @break
                                    @case('active')
                                        <span class="badge bg-success">Faol</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-primary">Yakunlangan</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $exam->status }}</span>
                                @endswitch
                                @if($exam->is_published)
                                <span class="badge bg-success">E'lon qilingan</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('dean.schedule.exams.show', $exam) }}" class="btn btn-sm btn-outline-info" title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('dean.schedule.exams.edit', $exam) }}" class="btn btn-sm btn-outline-primary" title="Tahrirlash">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('dean.schedule.exams.destroy', $exam) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Imtihonni o\'chirishni tasdiqlaysizmi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="O'chirish">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-file-alt fa-3x mb-3 d-block opacity-50"></i>
                                Imtihonlar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($exams->hasPages())
        <div class="card-footer bg-white">{{ $exams->withQueryString()->links() }}</div>
        @endif
    </div>
</div>

<style>.bg-gradient-danger { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); }</style>
@endsection
