@extends('layouts.dashboard-new')

@section('title', 'Davomat statistikasi - ' . ($journal->subject->name_uz ?? 'Fan'))

@section('styles')
<style>
    :root {
        --primary-dark-green: #0d4f3c;
        --secondary-green: #16a085;
        --light-green: #e8f5f0;
        --accent-green: #48c9b0;
        --border-green: #c3e6d8;
        --text-dark: #2c3e50;
        --very-light-green: #f0f9f6;
    }

    .stat-card {
        border-radius: 12px;
        border: 1px solid var(--border-green);
        transition: all 0.3s;
    }

    .stat-card:hover {
        box-shadow: 0 5px 20px rgba(13, 79, 60, 0.15);
    }

    .progress {
        height: 10px;
        border-radius: 5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 p-4 rounded-lg" style="background: linear-gradient(135deg, var(--primary-dark-green) 0%, var(--secondary-green) 100%);">
        <div class="col-md-8">
            <h1 class="h2 text-white">
                <i class="fas fa-chart-pie me-2"></i>Davomat statistikasi
            </h1>
            <p class="text-white opacity-90">
                <strong>{{ $journal->subject->name_uz ?? 'Fan' }}</strong> |
                {{ $journal->group->name ?? 'Guruh' }} |
                {{ $journal->semester ?? '1' }}-semestr
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('attendance.index', $journal->id) }}" class="btn text-white"
               style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-arrow-left me-2"></i>Orqaga
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    @php
                        $totalStudents = \App\Models\Student::where('group_id', $journal->group_id)->count();
                    @endphp
                    <h2 class="mb-0">{{ $totalStudents }}</h2>
                    <p class="mb-0 opacity-90">Jami talabalar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, var(--secondary-green), var(--accent-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    @php
                        $totalLessons = \App\Models\AttendanceRecord::where('journal_entry_id', $journal->id)
                            ->select('lesson_date')->distinct()->count();
                    @endphp
                    <h2 class="mb-0">{{ $totalLessons }}</h2>
                    <p class="mb-0 opacity-90">Jami darslar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-2" style="background: #d4edda;">
                            <i class="fas fa-check text-success"></i>
                        </div>
                    </div>
                    @php
                        $presentCount = \App\Models\AttendanceRecord::where('journal_entry_id', $journal->id)
                            ->whereIn('status', ['present', 'late'])->count();
                        $totalRecords = \App\Models\AttendanceRecord::where('journal_entry_id', $journal->id)->count();
                        $avgAttendance = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : 0;
                    @endphp
                    <h2 class="mb-0 text-success">{{ $avgAttendance }}%</h2>
                    <p class="mb-0 text-muted">O'rtacha davomat</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-2" style="background: #f8d7da;">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 text-danger">{{ $stats['low_attendance']->count() }}</h2>
                    <p class="mb-0 text-muted">Past davomat (< 75%)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- By Lesson Type -->
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                    <h5 class="mb-0" style="color: var(--text-dark);">
                        <i class="fas fa-chart-bar me-2" style="color: var(--secondary-green);"></i>
                        Dars turi bo'yicha
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $lessonTypes = [
                            'lecture' => 'Ma\'ruza',
                            'practice' => 'Amaliyot',
                            'lab' => 'Laboratoriya',
                            'seminar' => 'Seminar'
                        ];
                    @endphp
                    @forelse($stats['by_lesson_type']->groupBy('lesson_type') as $type => $records)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="color: var(--text-dark);">{{ $lessonTypes[$type] ?? $type }}</span>
                                @php
                                    $present = $records->where('status', 'present')->sum('count') + $records->where('status', 'late')->sum('count');
                                    $total = $records->sum('count');
                                    $percent = $total > 0 ? round(($present / $total) * 100) : 0;
                                @endphp
                                <span class="text-muted">{{ $percent }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ $percent }}%; background: var(--secondary-green);">
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Ma'lumot mavjud emas</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- By Month -->
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                    <h5 class="mb-0" style="color: var(--text-dark);">
                        <i class="fas fa-calendar-alt me-2" style="color: var(--secondary-green);"></i>
                        Oy bo'yicha
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $months = [
                            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
                            5 => 'May', 6 => 'Iyun', 7 => 'Iyul', 8 => 'Avgust',
                            9 => 'Sentabr', 10 => 'Oktabr', 11 => 'Noyabr', 12 => 'Dekabr'
                        ];
                    @endphp
                    @forelse($stats['by_month']->groupBy('month') as $month => $records)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="color: var(--text-dark);">{{ $months[$month] ?? $month }}</span>
                                @php
                                    $present = $records->where('status', 'present')->sum('count') + $records->where('status', 'late')->sum('count');
                                    $total = $records->sum('count');
                                    $percent = $total > 0 ? round(($present / $total) * 100) : 0;
                                @endphp
                                <span class="text-muted">{{ $percent }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ $percent }}%; background: var(--accent-green);">
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Ma'lumot mavjud emas</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Low Attendance Students -->
    <div class="card stat-card">
        <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
            <h5 class="mb-0" style="color: var(--text-dark);">
                <i class="fas fa-user-times me-2 text-danger"></i>
                Past davomati bor talabalar (< 75%)
            </h5>
        </div>
        <div class="card-body">
            @if($stats['low_attendance']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="background: var(--very-light-green);">
                                <th>№</th>
                                <th>Talaba</th>
                                <th>Jami darslar</th>
                                <th>Qatnashgan</th>
                                <th>Foiz</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['low_attendance'] as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $student->last_name }} {{ $student->first_name }}</strong>
                                    </td>
                                    <td>{{ $student->total_classes }}</td>
                                    <td>{{ $student->attended_classes }}</td>
                                    <td>
                                        @php
                                            $percent = $student->total_classes > 0
                                                ? round(($student->attended_classes / $student->total_classes) * 100, 1)
                                                : 0;
                                        @endphp
                                        <span class="badge bg-danger">{{ $percent }}%</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">Barcha talabalar davomati 75% dan yuqori!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
