@extends('layouts.dashboard-new')

@section('title', 'Hisobotlar')

@section('page-title', 'Hisobotlar markazi')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="h4 mb-2">Hisobotlar markazi</h2>
        <p class="text-muted">Tizim bo'yicha batafsil hisobotlar</p>
    </div>

    <!-- Report Categories -->
    <div class="row">
        <!-- Students Report -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Talabalar hisoboti</h5>
                        <p class="card-text text-muted small">Talabalar bo'yicha batafsil ma'lumot</p>
                        <a href="{{ route('reports.students') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-file-alt"></i> Ko'rish
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finance Report -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-dollar-sign fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Moliya hisoboti</h5>
                        <p class="card-text text-muted small">Daromad va xarajatlar tahlili</p>
                        <a href="{{ route('reports.finance') }}" class="btn btn-success btn-sm mt-2">
                            <i class="fas fa-file-alt"></i> Ko'rish
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Report -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-book fa-3x text-info mb-3"></i>
                        <h5 class="card-title">O'quv hisoboti</h5>
                        <p class="card-text text-muted small">O'quv jarayoni statistikasi</p>
                        <a href="{{ route('reports.academic') }}" class="btn btn-info btn-sm mt-2">
                            <i class="fas fa-file-alt"></i> Ko'rish
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Report -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-calendar-check fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Davomat hisoboti</h5>
                        <p class="card-text text-muted small">Talabalar davomati tahlili</p>
                        <a href="{{ route('reports.attendance') }}" class="btn btn-warning btn-sm mt-2">
                            <i class="fas fa-file-alt"></i> Ko'rish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Tezkor harakatlar</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <button class="btn btn-outline-primary btn-block" onclick="window.print()">
                        <i class="fas fa-print"></i> Chop etish
                    </button>
                </div>
                <div class="col-md-4 mb-3">
                    <button class="btn btn-outline-success btn-block" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Excel'ga eksport
                    </button>
                </div>
                <div class="col-md-4 mb-3">
                    <button class="btn btn-outline-danger btn-block" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf"></i> PDF'ga eksport
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">So'nggi hisobotlar</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hisobot turi</th>
                            <th>Sana</th>
                            <th>Foydalanuvchi</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="fas fa-user-graduate text-primary"></i> Talabalar hisoboti</td>
                            <td>{{ now()->format('d.m.Y H:i') }}</td>
                            <td>{{ auth()->user()->name }}</td>
                            <td>
                                <a href="{{ route('reports.students') }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <button class="btn btn-sm btn-success" onclick="downloadReport('students')"><i class="fas fa-download"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-dollar-sign text-success"></i> Moliya hisoboti</td>
                            <td>{{ now()->subHour()->format('d.m.Y H:i') }}</td>
                            <td>{{ auth()->user()->name }}</td>
                            <td>
                                <a href="{{ route('reports.finance') }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <button class="btn btn-sm btn-success" onclick="downloadReport('finance')"><i class="fas fa-download"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportToExcel() {
    alert('Excel eksport funksiyasi ishlab chiqilmoqda...');
    // Implementation: Use Laravel Excel package or JS library
}

function exportToPDF() {
    alert('PDF eksport funksiyasi ishlab chiqilmoqda...');
    // Implementation: Use Laravel PDF package or JS library
}

function downloadReport(type) {
    alert(type.charAt(0).toUpperCase() + type.slice(1) + ' hisobotini yuklab olish funksiyasi ishlab chiqilmoqda...');
    // Implementation: Make AJAX call to download endpoint
}
</script>
@endpush
@endsection
