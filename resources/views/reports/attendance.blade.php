@extends('layouts.dashboard-new')

@section('title', 'Davomat hisoboti')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Davomat hisoboti</h2>
        <div>
            <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button class="btn btn-danger btn-sm" onclick="exportToPDF()">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> Chop etish
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.attendance') }}" class="row">
                <div class="col-md-3">
                    <label>Boshlanish sanasi</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label>Tugash sanasi</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label>Guruh</label>
                    <select name="group_id" class="form-control form-control-sm">
                        <option value="">Barchasi</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">Barchasi</option>
                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Kelgan</option>
                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Kelmagan</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Kechikkan</option>
                        <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>Sababli</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter"></i> Filtr
                        </button>
                        <a href="{{ route('reports.attendance') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-redo"></i> Tozalash
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Davomatchilik</div>
                    <div class="h5 mb-0">{{ number_format($attendanceRate, 1) }}%</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Kelgan</div>
                    <div class="h5 mb-0">{{ $presentCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Kelmagan</div>
                    <div class="h5 mb-0">{{ $absentCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Kechikkan</div>
                    <div class="h5 mb-0">{{ $lateCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Davomat yozuvlari</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered" id="attendanceTable">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Sana</th>
                            <th>Talaba</th>
                            <th>Guruh</th>
                            <th>Dars</th>
                            <th>Vaqt</th>
                            <th>Status</th>
                            <th>Izoh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendanceRecords as $index => $record)
                        <tr>
                            <td>{{ $attendanceRecords->firstItem() + $index }}</td>
                            <td>{{ $record->date }}</td>
                            <td>{{ $record->student_name ?? 'N/A' }}</td>
                            <td>{{ $record->group_name ?? 'N/A' }}</td>
                            <td>{{ $record->subject_name ?? 'N/A' }}</td>
                            <td>{{ $record->check_in_time ?? '-' }}</td>
                            <td>
                                @if($record->status == 'present')
                                    <span class="badge badge-success">Kelgan</span>
                                @elseif($record->status == 'absent')
                                    <span class="badge badge-danger">Kelmagan</span>
                                @elseif($record->status == 'late')
                                    <span class="badge badge-warning">Kechikkan</span>
                                @else
                                    <span class="badge badge-info">Sababli</span>
                                @endif
                            </td>
                            <td>{{ $record->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $attendanceRecords->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Student Attendance Summary -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Talabalar davomati</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Talaba</th>
                            <th>Guruh</th>
                            <th class="text-center">Kelgan</th>
                            <th class="text-center">Kelmagan</th>
                            <th class="text-center">Kechikkan</th>
                            <th class="text-center">Davomatchilik %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentAttendance as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->group_name }}</td>
                            <td class="text-center">{{ $student->present_count }}</td>
                            <td class="text-center">{{ $student->absent_count }}</td>
                            <td class="text-center">{{ $student->late_count }}</td>
                            <td class="text-center">
                                @php
                                    $totalDays = $student->present_count + $student->absent_count + $student->late_count;
                                    $attendancePercent = $totalDays > 0 ? round(($student->present_count / $totalDays) * 100, 1) : 0;
                                @endphp
                                <span class="badge {{ $attendancePercent >= 80 ? 'badge-success' : ($attendancePercent >= 60 ? 'badge-warning' : 'badge-danger') }}">
                                    {{ $attendancePercent }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Daily Attendance Chart -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Kunlik davomat grafigi</h6>
        </div>
        <div class="card-body">
            <canvas id="attendanceChart" height="80"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
// Daily Attendance Chart
new Chart(document.getElementById('attendanceChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyAttendance->pluck('date')->toArray()) !!},
        datasets: [{
            label: 'Kelgan',
            data: {!! json_encode($dailyAttendance->pluck('present')->toArray()) !!},
            borderColor: 'rgb(28, 200, 138)',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            tension: 0.1
        }, {
            label: 'Kelmagan',
            data: {!! json_encode($dailyAttendance->pluck('absent')->toArray()) !!},
            borderColor: 'rgb(231, 74, 59)',
            backgroundColor: 'rgba(231, 74, 59, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

function exportToExcel() {
    alert('Excel export funksiyasi ishlab chiqilmoqda...');
    // Implementation pending
}

function exportToPDF() {
    alert('PDF export funksiyasi ishlab chiqilmoqda...');
    // Implementation pending
}
</script>

<style>
@media print {
    .btn, .card-header, nav, form { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
@endsection
