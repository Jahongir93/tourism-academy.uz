@extends('layouts.dashboard-new')

@section('title', 'Talabalar hisoboti')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Talabalar hisoboti</h2>
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
            <form method="GET" action="{{ route('reports.students') }}" class="row">
                <div class="col-md-3">
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
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nofaol</option>
                        <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Bitirgan</option>
                        <option value="expelled" {{ request('status') == 'expelled' ? 'selected' : '' }}>Chetlatilgan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter"></i> Filtr
                        </button>
                        <a href="{{ route('reports.students') }}" class="btn btn-secondary btn-sm">
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
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jami talabalar</div>
                    <div class="h5 mb-0">{{ $students->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Faol</div>
                    <div class="h5 mb-0">{{ $activeCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Erkak</div>
                    <div class="h5 mb-0">{{ $maleCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ayol</div>
                    <div class="h5 mb-0">{{ $femaleCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Talabalar ro'yxati</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered" id="studentsTable">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>F.I.Sh</th>
                            <th>Guruh</th>
                            <th>Kurs</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th>Jinsi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                        <tr>
                            <td>{{ $students->firstItem() + $index }}</td>
                            <td>{{ $student->student_id ?? 'N/A' }}</td>
                            <td>{{ $student->user->name ?? 'N/A' }}</td>
                            <td>{{ $student->group->name ?? 'N/A' }}</td>
                            <td>{{ $student->group->course_year ?? 'N/A' }}</td>
                            <td>{{ $student->user->phone ?? 'N/A' }}</td>
                            <td>{{ $student->user->email ?? 'N/A' }}</td>
                            <td>{{ $student->gender == 'male' ? 'Erkak' : 'Ayol' }}</td>
                            <td>
                                @if($student->status == 'active')
                                    <span class="badge badge-success">Faol</span>
                                @elseif($student->status == 'inactive')
                                    <span class="badge badge-secondary">Nofaol</span>
                                @elseif($student->status == 'graduated')
                                    <span class="badge badge-info">Bitirgan</span>
                                @else
                                    <span class="badge badge-danger">Chetlatilgan</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $students->appends(request()->query())->links() }}
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
    .btn, .card-header, nav { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
@endsection
