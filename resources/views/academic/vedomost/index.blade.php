@extends('layouts.dashboard-new')

@section('title', 'Vedomost (Baho varaqalari)')
@section('page-title', 'Vedomost (Baho varaqalari)')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <p class="text-muted">Baholar vedomosti - talabalar baholari varaqalarini boshqarish</p>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="btn-group">
            <a href="{{ route('vedomost.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Yangi vedomost
            </a>
            <a href="{{ route('vedomost.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Excel
            </a>
            <a href="{{ route('vedomost.statistics') }}" class="btn btn-info">
                <i class="fas fa-chart-bar me-2"></i>Statistika
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtrlash
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('vedomost.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Guruh</label>
                        <select name="group_id" class="form-select">
                            <option value="">Barchasi</option>
                            @if(isset($groups))
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Fan</label>
                        <select name="subject_id" class="form-select">
                            <option value="">Barchasi</option>
                            @if(isset($subjects))
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Semestr</label>
                        <select name="semester" class="form-select">
                            <option value="">Barchasi</option>
                            <option value="1" {{ request('semester') == '1' ? 'selected' : '' }}>1-semestr</option>
                            <option value="2" {{ request('semester') == '2' ? 'selected' : '' }}>2-semestr</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search me-2"></i>Qidirish
                            </button>
                            <a href="{{ route('vedomost.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Vedomost List -->
    <div class="row g-4">
        @forelse($vedomosts ?? [] as $vedomost)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">{{ $vedomost->subject->name ?? 'N/A' }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Guruh</small>
                        <strong>{{ $vedomost->group->name ?? 'N/A' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">O'qituvchi</small>
                        <strong>{{ $vedomost->teacher->name ?? 'N/A' }}</strong>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Semestr</small>
                            <strong>{{ $vedomost->semester ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Talabalar</small>
                            <strong>{{ $vedomost->students_count ?? 0 }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Holat</small>
                        @if($vedomost->status == 'approved')
                            <span class="badge bg-success">Tasdiqlangan</span>
                        @elseif($vedomost->status == 'submitted')
                            <span class="badge bg-info">Topshirilgan</span>
                        @elseif($vedomost->status == 'in_progress')
                            <span class="badge bg-warning">Jarayonda</span>
                        @else
                            <span class="badge bg-secondary">Qoralama</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">To'ldirilgan</small>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $vedomost->completion_percentage ?? 0 }}%">
                                {{ $vedomost->completion_percentage ?? 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex gap-2">
                        <a href="{{ route('vedomost.fill', $vedomost->id) }}" class="btn btn-sm btn-primary flex-fill">
                            <i class="fas fa-edit me-1"></i>To'ldirish / Tahrirlash
                        </a>
                        <a href="{{ route('vedomost.export-word', $vedomost->id) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-file-word me-1"></i>Word
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center text-muted py-5">
                    <i class="fas fa-inbox fs-1 d-block mb-3"></i>
                    <h5>Vedomost topilmadi</h5>
                    <p>Yangi vedomost yaratish uchun "Yangi vedomost" tugmasini bosing</p>
                    <a href="{{ route('vedomost.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Yangi vedomost
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if(isset($vedomosts) && method_exists($vedomosts, 'links'))
        <div class="mt-4">
            {{ $vedomosts->links() }}
        </div>
    @endif
</div>
@endsection
