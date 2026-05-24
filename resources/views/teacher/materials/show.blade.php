@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('teacher.materials.index') }}" class="text-white-50">Materiallar</a>
                            </li>
                            <li class="breadcrumb-item text-white active">{{ Str::limit($material->title, 30) }}</li>
                        </ol>
                    </nav>
                    <h4 class="mb-1">
                        <i class="{{ $material->type_icon }} me-2"></i>
                        {{ $material->title }}
                    </h4>
                    <p class="mb-0 opacity-75">
                        <span class="badge bg-light text-dark">{{ $material->type_label }}</span>
                        <span class="ms-2">{{ $material->subject?->name ?? 'N/A' }}</span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('teacher.materials.edit', $material->id) }}" class="btn btn-light btn-sm me-2">
                        <i class="fas fa-edit me-1"></i>Tahrirlash
                    </a>
                    <a href="{{ route('teacher.materials.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
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

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Material ma'lumotlari
                    </h5>
                </div>
                <div class="card-body">
                    @if($material->description)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Tavsif</h6>
                        <p>{{ $material->description }}</p>
                    </div>
                    @endif

                    <!-- File/Video/Link Preview -->
                    @if($material->type == 'video' && $material->video_url)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Video</h6>
                        <div class="ratio ratio-16x9">
                            @if(str_contains($material->video_url, 'youtube.com') || str_contains($material->video_url, 'youtu.be'))
                                @php
                                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $material->video_url, $matches);
                                    $videoId = $matches[1] ?? '';
                                @endphp
                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
                            @else
                                <a href="{{ $material->video_url }}" target="_blank" class="btn btn-danger btn-lg">
                                    <i class="fas fa-play me-2"></i>Videoni ochish
                                </a>
                            @endif
                        </div>
                    </div>
                    @elseif($material->type == 'link' && $material->external_link)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Tashqi havola</h6>
                        <a href="{{ $material->external_link }}" target="_blank" class="btn btn-info">
                            <i class="fas fa-external-link-alt me-2"></i>Havolani ochish
                        </a>
                    </div>
                    @elseif($material->file_path)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Fayl</h6>
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex align-items-center">
                                <i class="{{ $material->type_icon }} fa-2x text-{{ $material->type_color }} me-3"></i>
                                <div class="flex-grow-1">
                                    <strong>{{ basename($material->file_path) }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ strtoupper($material->file_type) }}
                                        @if($material->formatted_file_size)
                                        · {{ $material->formatted_file_size }}
                                        @endif
                                    </small>
                                </div>
                                <a href="{{ route('teacher.materials.download', $material->id) }}" class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i>Yuklash
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 mb-4">
            <!-- Stats Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistika
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Ko'rishlar</span>
                        <strong>{{ $material->views_count ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Yuklashlar</span>
                        <strong>{{ $material->downloads_count ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Yaratilgan</span>
                        <strong>{{ $material->created_at->format('d.m.Y') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Groups Card -->
            @if(count($groups) > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-users me-2"></i>Guruhlar
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($groups as $group)
                    <span class="badge bg-secondary me-1 mb-1">{{ $group->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Delete Button -->
            <form action="{{ route('teacher.materials.destroy', $material->id) }}" method="POST"
                  onsubmit="return confirm('Bu materialni o\'chirishni xohlaysizmi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="fas fa-trash me-2"></i>Materialni o'chirish
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
