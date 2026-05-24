@extends('layouts.dashboard-new')

@section('title', 'GPA Kalkulyator')
@section('page-title', 'GPA Kalkulyator')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <p class="text-muted">Talabalarning o'rtacha ball (GPA) kalkulyatori va tahlili</p>
    </div>

    <!-- GPA Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">O'rtacha GPA</h6>
                    <h2 class="card-title mb-0">{{ number_format($summary['average_gpa'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Eng yuqori GPA</h6>
                    <h2 class="card-title mb-0">{{ number_format($summary['highest_gpa'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Eng past GPA</h6>
                    <h2 class="card-title mb-0">{{ number_format($summary['lowest_gpa'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Jami talabalar</h6>
                    <h2 class="card-title mb-0">{{ $summary['total_students'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Student -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-search me-2"></i>Talaba qidirish
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('gpa.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="student_id" class="form-control" placeholder="Talaba ID" value="{{ request('student_id') }}">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control" placeholder="Talaba ismi" value="{{ request('name') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Qidirish
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students GPA List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-graduation-cap me-2"></i>Talabalar GPA ro'yxati
            </h5>
            <a href="{{ route('gpa.index', ['export' => 1]) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-2"></i>Excel
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Talaba ID</th>
                            <th>F.I.O</th>
                            <th>Guruh</th>
                            <th>Semestr GPA</th>
                            <th>Umumiy GPA</th>
                            <th>Status</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students ?? [] as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->group->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ ($student->semester_gpa ?? 0) >= 3.5 ? 'bg-success' : (($student->semester_gpa ?? 0) >= 3.0 ? 'bg-primary' : 'bg-warning') }}">
                                    {{ number_format($student->semester_gpa ?? 0, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ ($student->cumulative_gpa ?? 0) >= 3.5 ? 'bg-success' : (($student->cumulative_gpa ?? 0) >= 3.0 ? 'bg-primary' : 'bg-warning') }}">
                                    {{ number_format($student->cumulative_gpa ?? 0, 2) }}
                                </span>
                            </td>
                            <td>
                                @if(($student->cumulative_gpa ?? 0) >= 3.7)
                                    <span class="badge bg-success">A'lo</span>
                                @elseif(($student->cumulative_gpa ?? 0) >= 3.0)
                                    <span class="badge bg-primary">Yaxshi</span>
                                @elseif(($student->cumulative_gpa ?? 0) >= 2.0)
                                    <span class="badge bg-warning">Qoniqarli</span>
                                @else
                                    <span class="badge bg-danger">Qoniqarsiz</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('gpa.semester', $student->id) }}" class="btn btn-primary" title="Semestrlar bo'yicha">
                                        <i class="fas fa-calendar"></i>
                                    </a>
                                    <a href="{{ route('gpa.transcript', $student->id) }}" class="btn btn-info" title="Transcript">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                    <a href="{{ route('gpa.trend', $student->id) }}" class="btn btn-success" title="O'sish grafigi">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fas fa-inbox fs-3 d-block mb-2"></i>
                                Talabalar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($students) && method_exists($students, 'links'))
                <div class="mt-3">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
