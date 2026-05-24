@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-folder-open me-2"></i>
                        O'quv materiallari
                    </h4>
                    <p class="mb-0 opacity-75">Darslar uchun materiallar va videolar</p>
                </div>
                <div>
                    <a href="{{ route('teacher.materials.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-upload me-1"></i>Yangi material
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-folder fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Jami</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="fas fa-file-alt fa-lg text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Hujjatlar</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['documents'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                            <i class="fas fa-video fa-lg text-danger"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Videolar</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['videos'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="fas fa-file-powerpoint fa-lg text-warning"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Taqdimotlar</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['presentations'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('teacher.materials.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Turi</label>
                    <select name="type" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Hujjat</option>
                        <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="presentation" {{ request('type') == 'presentation' ? 'selected' : '' }}>Taqdimot</option>
                        <option value="link" {{ request('type') == 'link' ? 'selected' : '' }}>Havola</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Fan</label>
                    <select name="subject_id" class="form-select">
                        <option value="">Barcha fanlar</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('teacher.materials.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i>Tozalash
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Materials List -->
    @if($materials->count() > 0)
    <div class="row">
        @foreach($materials as $material)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <!-- Icon and Type -->
                    <div class="d-flex align-items-start mb-3">
                        <div class="rounded-circle bg-{{ $material->type_color }} bg-opacity-10 p-3 me-3">
                            <i class="{{ $material->type_icon }} fa-lg text-{{ $material->type_color }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <span class="badge bg-{{ $material->type_color }} mb-2">{{ $material->type_label }}</span>
                            <h5 class="mb-1">{{ Str::limit($material->title, 40) }}</h5>
                            <p class="text-muted mb-0 small">
                                <i class="fas fa-book me-1"></i>{{ $material->subject->name }}
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($material->description)
                    <p class="text-muted small mb-3" style="max-height: 60px; overflow: hidden;">
                        {{ Str::limit($material->description, 100) }}
                    </p>
                    @endif

                    <!-- File Info -->
                    @if($material->file_path)
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-file me-1"></i>{{ strtoupper($material->file_type) }}
                            @if($material->formatted_file_size)
                            · {{ $material->formatted_file_size }}
                            @endif
                        </small>
                    </div>
                    @endif

                    <!-- Stats -->
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <small class="text-muted">
                            <i class="fas fa-eye me-1"></i>{{ $material->views_count ?? 0 }} ko'rishlar
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-download me-1"></i>{{ $material->downloads_count ?? 0 }} yuklashlar
                        </small>
                    </div>

                    <!-- Date -->
                    <p class="text-muted small mb-3">
                        <i class="fas fa-clock me-1"></i>{{ $material->created_at->format('d.m.Y H:i') }}
                    </p>

                    <!-- Actions -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('teacher.materials.show', $material->id) }}"
                           class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fas fa-eye me-1"></i>Ko'rish
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('teacher.materials.edit', $material->id) }}">
                                        <i class="fas fa-edit me-2"></i>Tahrirlash
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="deleteMaterial({{ $material->id }})">
                                        <i class="fas fa-trash me-2"></i>O'chirish
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $materials->links() }}
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Materiallar yo'q</h5>
            <p class="text-muted mb-3">Birinchi materialni yuklash uchun tugmani bosing</p>
            <a href="{{ route('teacher.materials.create') }}" class="btn btn-primary">
                <i class="fas fa-upload me-1"></i>Yangi material
            </a>
        </div>
    </div>
    @endif
</div>

<style>
.hover-lift {
    transition: all 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<script>
function deleteMaterial(id) {
    if (confirm('Bu materialni o\'chirishni xohlaysizmi?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ url('teacher/materials') }}/" + id;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
        `;

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
