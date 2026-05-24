@extends('layouts.dashboard-new')

@section('title', 'Transcript')
@section('page-title', 'Academic Transcript')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">{{ $transcript['student']['name'] ?? 'N/A' }}</h5>
            <p class="text-muted mb-0">Student ID: {{ $transcript['student']['student_id'] ?? 'N/A' }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('gpa.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Orqaga
            </a>
            <a href="{{ route('gpa.transcript.download', $transcript['student']['id'] ?? 0) }}" class="btn btn-primary">
                <i class="fas fa-download me-2"></i>PDF yuklash
            </a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">Talaba ma'lumotlari</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-2"><strong>F.I.O:</strong> {{ $transcript['student']['name'] ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>Student ID:</strong> {{ $transcript['student']['student_id'] ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-2"><strong>Fakultet:</strong> {{ $transcript['student']['faculty'] ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>Yo'nalish:</strong> {{ $transcript['student']['specialty'] ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-2"><strong>Guruh:</strong> {{ $transcript['student']['group'] ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>O'qish shakli:</strong> {{ $transcript['student']['study_form'] ?? 'Kunduzgi' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- GPA Summary -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Umumiy GPA</h6>
                    <h2 class="mb-0 text-primary">{{ number_format($transcript['cumulative_gpa'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Jami kreditlar</h6>
                    <h2 class="mb-0 text-success">{{ $transcript['total_credits'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">O'tgan fanlar</h6>
                    <h2 class="mb-0 text-info">{{ $transcript['completed_courses'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Status</h6>
                    <h2 class="mb-0">
                        @if(($transcript['cumulative_gpa'] ?? 0) >= 3.7)
                            <span class="badge bg-success">A'lo</span>
                        @elseif(($transcript['cumulative_gpa'] ?? 0) >= 3.0)
                            <span class="badge bg-primary">Yaxshi</span>
                        @else
                            <span class="badge bg-warning">Qoniqarli</span>
                        @endif
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses by Semester -->
    @if(isset($transcript['semesters']))
        @foreach($transcript['semesters'] as $semester)
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ $semester['name'] ?? 'N/A' }}</h6>
                    <span class="badge bg-primary">GPA: {{ number_format($semester['gpa'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fan kodi</th>
                                <th>Fan nomi</th>
                                <th class="text-center">Kredit</th>
                                <th class="text-center">Ball</th>
                                <th class="text-center">Harf baho</th>
                                <th class="text-center">GPA ball</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($semester['courses'] ?? [] as $course)
                            <tr>
                                <td>{{ $course['code'] ?? 'N/A' }}</td>
                                <td>{{ $course['name'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $course['credits'] ?? 0 }}</td>
                                <td class="text-center">{{ $course['score'] ?? 0 }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $course['grade'] == 'F' ? 'bg-danger' : 'bg-success' }}">
                                        {{ $course['grade'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">{{ number_format($course['grade_points'] ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Fanlar topilmadi</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="2">Semestr jami:</th>
                                <th class="text-center">{{ $semester['total_credits'] ?? 0 }}</th>
                                <th colspan="2"></th>
                                <th class="text-center">{{ number_format($semester['gpa'] ?? 0, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    @endif

    <!-- Overall Statistics -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0">Umumiy natijalar</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td><strong>Jami o'rganilgan fanlar:</strong></td>
                        <td>{{ $transcript['completed_courses'] ?? 0 }}</td>
                        <td><strong>Jami kreditlar:</strong></td>
                        <td>{{ $transcript['total_credits'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td><strong>Umumiy GPA:</strong></td>
                        <td>{{ number_format($transcript['cumulative_gpa'] ?? 0, 2) }}</td>
                        <td><strong>Sifat ko'rsatkichi:</strong></td>
                        <td>{{ number_format($transcript['quality_points'] ?? 0, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
