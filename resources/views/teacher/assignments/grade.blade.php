@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-star me-2"></i>
                        Topshiriqni baholash
                    </h4>
                    <p class="mb-0 opacity-75">{{ $submission->assignment->title }}</p>
                </div>
                <div>
                    <a href="{{ route('teacher.assignments.show', $submission->assignment_id) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Submission Details -->
        <div class="col-lg-4 mb-4">
            <!-- Student Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 2rem;">
                        {{ strtoupper(substr($submission->student->user->name, 0, 2)) }}
                    </div>
                    <h5 class="mb-1">{{ $submission->student->user->name }}</h5>
                    <p class="text-muted mb-2">{{ $submission->student->user->email }}</p>
                    <span class="badge bg-secondary">{{ $submission->student->student_id }}</span>
                    <span class="badge bg-info">{{ $submission->student->group->name }}</span>
                </div>
            </div>

            <!-- Submission Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Topshiriq ma'lumotlari
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Assignment Title -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Topshiriq</small>
                        <h6 class="mb-0">{{ $submission->assignment->title }}</h6>
                    </div>

                    <!-- Subject -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Fan</small>
                        <h6 class="mb-0">{{ $submission->assignment->subject->name }}</h6>
                    </div>

                    <!-- Submitted At -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Topshirgan vaqti</small>
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>
                            {{ $submission->submitted_at ? $submission->submitted_at->format('d.m.Y H:i') : '-' }}
                        </h6>
                    </div>

                    <!-- Deadline -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Muddat</small>
                        <h6 class="mb-0">
                            <i class="fas fa-clock text-danger me-1"></i>
                            {{ \Carbon\Carbon::parse($submission->assignment->deadline)->format('d.m.Y H:i') }}
                        </h6>
                        @if($submission->submitted_at && $submission->submitted_at > $submission->assignment->deadline)
                        <span class="badge bg-danger mt-2">Kech topshirdi</span>
                        @else
                        <span class="badge bg-success mt-2">O'z vaqtida</span>
                        @endif
                    </div>

                    <!-- Max Score -->
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Maksimal ball</small>
                        <h4 class="mb-0 text-warning">{{ $submission->assignment->max_score }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Submission Content & Grading Form -->
        <div class="col-lg-8 mb-4">
            <!-- Submission Content -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        Topshirilgan ish
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Submission Text -->
                    @if($submission->submission_text)
                    <div class="mb-3">
                        <label class="fw-bold mb-2">Javob matni:</label>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $submission->submission_text }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Submission File -->
                    @if($submission->file_path)
                    <div class="mb-3">
                        <label class="fw-bold mb-2">Biriktirma:</label>
                        <div>
                            <a href="{{ Storage::url($submission->file_path) }}"
                               target="_blank"
                               class="btn btn-outline-primary">
                                <i class="fas fa-download me-2"></i>
                                Faylni yuklash
                            </a>
                        </div>
                    </div>
                    @endif

                    @if(!$submission->submission_text && !$submission->file_path)
                    <p class="text-muted text-center py-3">Javob topilmadi</p>
                    @endif
                </div>
            </div>

            <!-- Grading Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>
                        Baholash
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.assignments.storeGrade', $submission->id) }}" method="POST">
                        @csrf

                        <!-- Score -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-star text-warning me-1"></i>
                                Ball (0 - {{ $submission->assignment->max_score }})
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   name="score"
                                   class="form-control form-control-lg @error('score') is-invalid @enderror"
                                   value="{{ old('score', $submission->score) }}"
                                   min="0"
                                   max="{{ $submission->assignment->max_score }}"
                                   step="0.01"
                                   required
                                   autofocus>
                            @error('score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Feedback -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-comment text-primary me-1"></i>
                                Izoh va tavsiyalar
                            </label>
                            <textarea name="feedback"
                                      class="form-control @error('feedback') is-invalid @enderror"
                                      rows="5"
                                      placeholder="Talabaga izoh va tavsiyalar yozing...">{{ old('feedback', $submission->feedback) }}</textarea>
                            @error('feedback')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Grade Info -->
                        @if($submission->score !== null)
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <strong>Joriy baho:</strong> {{ $submission->score }} ball
                                    @if($submission->graded_at)
                                    <br><small>Baholangan: {{ $submission->graded_at->format('d.m.Y H:i') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check me-2"></i>
                                {{ $submission->score !== null ? 'Bahoni yangilash' : 'Bahoni saqlash' }}
                            </button>
                            <a href="{{ route('teacher.assignments.show', $submission->assignment_id) }}"
                               class="btn btn-outline-secondary">
                                Bekor qilish
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
