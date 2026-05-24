@extends('layouts.dashboard-new')

@section('title', 'Vedomost - ' . ($subject->name ?? 'N/A'))
@section('page-title', 'Vedomost')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">{{ $subject->name ?? 'N/A' }}</h5>
            <p class="text-muted mb-0">Guruh: {{ $group->name ?? 'N/A' }} | Semestr: {{ $semester ?? 'N/A' }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('vedomost.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Orqaga
            </a>
            <a href="{{ route('vedomost.print', request()->all()) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-print me-2"></i>Chop etish
            </a>
            <a href="{{ route('vedomost.export', request()->all()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Excel
            </a>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Talaba ID</th>
                            <th>F.I.O</th>
                            <th class="text-center">Oraliq 1</th>
                            <th class="text-center">Oraliq 2</th>
                            <th class="text-center">Yakuniy</th>
                            <th class="text-center">Umumiy</th>
                            <th class="text-center">Baho</th>
                            <th class="text-center">Holat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students ?? [] as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->full_name }}</td>
                            <td class="text-center">{{ $student->midterm1_score ?? '-' }}</td>
                            <td class="text-center">{{ $student->midterm2_score ?? '-' }}</td>
                            <td class="text-center">{{ $student->final_score ?? '-' }}</td>
                            <td class="text-center">
                                <strong>{{ $student->total_score ?? 0 }}</strong>
                            </td>
                            <td class="text-center">
                                @if($student->letter_grade)
                                    <span class="badge {{ $student->letter_grade == 'F' ? 'bg-danger' : ($student->letter_grade >= 'C' ? 'bg-success' : 'bg-warning') }}">
                                        {{ $student->letter_grade }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($student->passed)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Ma'lumot topilmadi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    @if(isset($statistics))
    <div class="row g-4 mt-2">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Jami talabalar</h6>
                    <h3>{{ $statistics['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">O'tganlar</h6>
                    <h3 class="text-success">{{ $statistics['passed'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">O'tmaganlar</h6>
                    <h3 class="text-danger">{{ $statistics['failed'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">O'rtacha ball</h6>
                    <h3>{{ number_format($statistics['average'] ?? 0, 1) }}</h3>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
