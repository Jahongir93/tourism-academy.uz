@extends('layouts.dashboard-new')

@section('title', 'Akademik hisobot')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Akademik hisobot</h2>
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
            <form method="GET" action="{{ route('reports.academic') }}" class="row">
                <div class="col-md-3">
                    <label>O'quv yili</label>
                    <select name="academic_year" class="form-control form-control-sm">
                        <option value="">Joriy yil</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>
                            {{ $year->year }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Semestr</label>
                    <select name="semester" class="form-control form-control-sm">
                        <option value="">Barchasi</option>
                        <option value="1" {{ request('semester') == '1' ? 'selected' : '' }}>1-semestr</option>
                        <option value="2" {{ request('semester') == '2' ? 'selected' : '' }}>2-semestr</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Fakultet</label>
                    <select name="faculty_id" class="form-control form-control-sm">
                        <option value="">Barchasi</option>
                        @foreach($faculties as $faculty)
                        <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                            {{ $faculty->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Kurs</label>
                    <select name="course" class="form-control form-control-sm">
                        <option value="">Barchasi</option>
                        @for($i = 1; $i <= 4; $i++)
                        <option value="{{ $i }}" {{ request('course') == $i ? 'selected' : '' }}>{{ $i }}-kurs</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter"></i> Filtr
                        </button>
                        <a href="{{ route('reports.academic') }}" class="btn btn-secondary btn-sm">
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
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Guruhlar</div>
                    <div class="h5 mb-0">{{ $totalGroups }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Talabalar</div>
                    <div class="h5 mb-0">{{ $totalStudents }}</div>
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

    <!-- Groups Table -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Guruhlar ro'yxati</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Guruh</th>
                            <th>Fakultet</th>
                            <th>Mutaxassislik</th>
                            <th>Kurs</th>
                            <th>Talabalar</th>
                            <th>Fanlar</th>
                            <th>O'qituvchilar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groups as $index => $group)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="font-weight-bold">{{ $group->name }}</td>
                            <td>{{ $group->specialty->faculty->name ?? 'N/A' }}</td>
                            <td>{{ $group->specialty->name ?? 'N/A' }}</td>
                            <td>{{ $group->course_year }}</td>
                            <td class="text-center">{{ $group->students_count ?? 0 }}</td>
                            <td class="text-center">{{ $group->subjects_count ?? 0 }}</td>
                            <td class="text-center">{{ $group->teachers_count ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Subjects by Faculty -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Fakultetlar bo'yicha fanlar</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Fakultet</th>
                            <th>Guruhlar</th>
                            <th>Fanlar</th>
                            <th>Talabalar</th>
                            <th>O'qituvchilar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facultyStats as $index => $stat)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="font-weight-bold">{{ $stat->faculty_name }}</td>
                            <td class="text-center">{{ $stat->groups ?? 0 }}</td>
                            <td class="text-center">{{ $stat->subjects ?? 0 }}</td>
                            <td class="text-center">{{ $stat->students ?? 0 }}</td>
                            <td class="text-center">{{ $stat->teachers ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Grade Distribution -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Baholar taqsimoti</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Baho</th>
                                <th class="text-right">Soni</th>
                                <th class="text-right">Foiz</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gradeDistribution as $grade)
                            <tr>
                                <td>
                                    @if($grade->grade >= 86)
                                        <span class="badge badge-success">A (5)</span>
                                    @elseif($grade->grade >= 71)
                                        <span class="badge badge-info">B (4)</span>
                                    @elseif($grade->grade >= 56)
                                        <span class="badge badge-warning">C (3)</span>
                                    @else
                                        <span class="badge badge-danger">F (2)</span>
                                    @endif
                                </td>
                                <td class="text-right">{{ $grade->count }}</td>
                                <td class="text-right">
                                    {{ $totalGrades > 0 ? round(($grade->count / $totalGrades) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Akademik ko'rsatkichlar</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td>O'rtacha baho (GPA)</td>
                                <td class="text-right font-weight-bold">{{ number_format($averageGPA, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Muvaffaqiyat darajasi</td>
                                <td class="text-right font-weight-bold">{{ $successRate }}%</td>
                            </tr>
                            <tr>
                                <td>A'lo talabalar</td>
                                <td class="text-right font-weight-bold">{{ $excellentStudents }}</td>
                            </tr>
                            <tr>
                                <td>Qoniqarsiz natijalar</td>
                                <td class="text-right font-weight-bold text-danger">{{ $failedGrades }}</td>
                            </tr>
                            <tr>
                                <td>Davomatchilik</td>
                                <td class="text-right font-weight-bold">{{ $attendanceRate }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
