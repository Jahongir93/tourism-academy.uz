@extends('layouts.dashboard-new')

@section('title', 'Topshiriq tafsilotlari')
@section('page-title', 'Topshiriq tafsilotlari')

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('student.assignments.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Topshiriqlarga qaytish
        </a>
    </div>

    <div class="row">
        <!-- Assignment Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>{{ $assignment->title }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Assignment Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Fan:</label>
                                <div><strong>{{ $assignment->subject->name ?? 'N/A' }}</strong></div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">O'qituvchi:</label>
                                <div><strong>{{ $assignment->teacher->user->name ?? 'N/A' }}</strong></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Topshiriq turi:</label>
                                <div>
                                    <span class="badge bg-info">
                                        @switch($assignment->type)
                                            @case('lab') Laboratoriya @break
                                            @case('homework') Uy vazifasi @break
                                            @case('course_work') Kurs ishi @break
                                            @case('independent') Mustaqil ish @break
                                            @default {{ $assignment->type }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Muddat:</label>
                                <div>
                                    <strong class="@if(\Carbon\Carbon::parse($assignment->deadline)->isPast() && !$submission) text-danger @endif">
                                        <i class="fas fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('d F Y, H:i') }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment Description -->
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">Topshiriq tavsifi</h5>
                        <div class="mt-3">
                            {!! nl2br(e($assignment->description)) !!}
                        </div>
                    </div>

                    <!-- Assignment Attachments -->
                    @if($assignment->attachments && count($assignment->attachments) > 0)
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">Biriktirilgan fayllar</h5>
                        <div class="mt-3">
                            @foreach($assignment->attachments as $attachment)
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-paperclip text-primary me-2"></i>
                                <a href="{{ Storage::url($attachment['path'] ?? '') }}" target="_blank" class="text-decoration-none">
                                    {{ $attachment['name'] ?? 'File' }}
                                </a>
                                <small class="text-muted ms-2">({{ number_format(($attachment['size'] ?? 0) / 1024, 2) }} KB)</small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Max Score and Penalty -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <i class="fas fa-star"></i> Maksimal ball: <strong>{{ $assignment->max_score }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Kechikish jarima: <strong>{{ $assignment->late_penalty_percent }}%</strong> har kun uchun
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Section -->
        <div class="col-lg-4">
            @if($submission)
            <!-- Submitted -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Topshirilgan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Topshirilgan vaqt:</label>
                        <div><strong>{{ \Carbon\Carbon::parse($submission->submitted_at)->translatedFormat('d F Y, H:i') }}</strong></div>
                    </div>

                    @if($submission->status == 'graded')
                    <div class="mb-3">
                        <label class="text-muted small">Ball:</label>
                        <div>
                            <h3 class="mb-0 text-success">
                                {{ $submission->score }}/{{ $assignment->max_score }}
                            </h3>
                        </div>
                    </div>

                    @if($submission->feedback)
                    <div class="mb-3">
                        <label class="text-muted small">O'qituvchi izohi:</label>
                        <div class="alert alert-light">
                            {{ $submission->feedback }}
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="text-muted small">Baholagan vaqt:</label>
                        <div><small>{{ \Carbon\Carbon::parse($submission->graded_at)->translatedFormat('d F Y, H:i') }}</small></div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-hourglass-half"></i> Baholanish kutilmoqda...
                    </div>
                    @endif

                    @if($submission->text_content)
                    <div class="mb-3">
                        <label class="text-muted small">Matn:</label>
                        <div class="p-2 bg-light rounded">
                            {{ $submission->text_content }}
                        </div>
                    </div>
                    @endif

                    @if($submission->files && count($submission->files) > 0)
                    <div class="mb-3">
                        <label class="text-muted small">Yuklangan fayllar:</label>
                        @foreach($submission->files as $file)
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-file text-primary me-2"></i>
                            <a href="{{ Storage::url($file['path'] ?? '') }}" target="_blank" class="text-decoration-none">
                                {{ $file['name'] ?? 'File' }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @else
            <!-- Submit Form -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-upload me-2"></i>Topshirish
                    </h5>
                </div>
                <div class="card-body">
                    @if(\Carbon\Carbon::parse($assignment->deadline)->isPast())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Ogohlantirish!</strong> Muddat o'tgan. Kech topshirish uchun {{ $assignment->late_penalty_percent }}% jarima qo'llanadi.
                    </div>
                    @endif

                    <form action="{{ route('student.assignments.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="text_content" class="form-label">Matn (ixtiyoriy)</label>
                            <textarea class="form-control @error('text_content') is-invalid @enderror"
                                      id="text_content"
                                      name="text_content"
                                      rows="5"
                                      placeholder="Topshiriq haqida izoh yozing..."></textarea>
                            @error('text_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="files" class="form-label">Fayllar (ixtiyoriy)</label>
                            <input type="file"
                                   class="form-control @error('files.*') is-invalid @enderror"
                                   id="files"
                                   name="files[]"
                                   multiple
                                   accept=".pdf,.doc,.docx,.txt,.zip,.rar,.jpg,.jpeg,.png">
                            <small class="text-muted">Maksimal fayl hajmi: 10MB. Bir nechta faylni tanlash mumkin.</small>
                            @error('files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Topshirish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Assignment Stats -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Statistika</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Jami topshirganlar:</span>
                        <strong>{{ $assignment->submissions->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Baholangan:</span>
                        <strong>{{ $assignment->submissions->where('status', 'graded')->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
