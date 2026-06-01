@extends('layouts.dashboard-new')

@section('title', 'Akademik statistika')

@section('content')
<div class="container-fluid">
    <h2 class="h4 mb-4">Akademik statistika</h2>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jami talabalar</div>
                    <div class="h5 mb-0">{{ $totalStudents }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Guruhlar</div>
                    <div class="h5 mb-0">{{ $totalGroups }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Fanlar</div>
                    <div class="h5 mb-0">{{ $totalSubjects }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">O'qituvchilar</div>
                    <div class="h5 mb-0">{{ $totalTeachers }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Fakultetlar bo'yicha talabalar</h6>
                </div>
                <div class="card-body">
                    <canvas id="facultyChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Kurslar bo'yicha taqsimot</h6>
                </div>
                <div class="card-body">
                    <canvas id="courseChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Students by Faculty Table -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Fakultetlar statistikasi</h6>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fakultet</th>
                        <th class="text-center">Guruhlar</th>
                        <th class="text-center">Talabalar</th>
                        <th class="text-center">Foiz</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentsByFaculty as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->faculty_name }}</td>
                        <td class="text-center">{{ $item->groups ?? 0 }}</td>
                        <td class="text-center font-weight-bold">{{ $item->total }}</td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $totalStudents > 0 ? round(($item->total / $totalStudents) * 100, 1) : 0 }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Students by Course -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Kurslar bo'yicha taqsimot</h6>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Kurs</th>
                        <th class="text-center">Guruhlar</th>
                        <th class="text-center">Talabalar</th>
                        <th class="text-center">Foiz</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentsByCourse as $item)
                    <tr>
                        <td>{{ $item->course }}-kurs</td>
                        <td class="text-center">{{ $item->groups ?? 0 }}</td>
                        <td class="text-center font-weight-bold">{{ $item->total }}</td>
                        <td class="text-center">
                            <span class="badge badge-primary">{{ $totalStudents > 0 ? round(($item->total / $totalStudents) * 100, 1) : 0 }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
// Faculty Distribution Chart
new Chart(document.getElementById('facultyChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($studentsByFaculty->pluck('faculty_name')->toArray()) !!},
        datasets: [{
            label: 'Talabalar soni',
            data: {!! json_encode($studentsByFaculty->pluck('total')->toArray()) !!},
            backgroundColor: 'rgba(78, 115, 223, 0.8)'
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

// Course Distribution Chart
new Chart(document.getElementById('courseChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($studentsByCourse->map(fn($item) => $item->course . '-kurs')->toArray()) !!},
        datasets: [{
            data: {!! json_encode($studentsByCourse->pluck('total')->toArray()) !!},
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});
</script>
@endpush
@endsection
