@extends('layouts.dashboard-new')

@section('title', $faculty->name_uz . ' - Fakultet')

@section('page-title', $faculty->name_uz)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('structure.faculties.index') }}">Fakultetlar</a></li>
                    <li class="breadcrumb-item active">{{ $faculty->name_uz }}</li>
                </ol>
            </nav>
            <h1 class="h2">{{ $faculty->name_uz }}</h1>
            @if($faculty->name_ru || $faculty->name_en)
                <p class="text-muted">
                    {{ $faculty->name_ru }} 
                    @if($faculty->name_en) / {{ $faculty->name_en }} @endif
                </p>
            @endif
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('structure.faculties.edit', $faculty) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Tahrirlash
            </a>
            <a href="{{ route('structure.faculties.departments', $faculty) }}" class="btn btn-info">
                <i class="fas fa-building"></i> Kafedralar
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ $stats['departments_count'] }}</h3>
                    <small class="text-muted">Kafedralar</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">{{ $stats['specialties_count'] }}</h3>
                    <small class="text-muted">Yo'nalishlar</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-info">{{ $stats['students_count'] }}</h3>
                    <small class="text-muted">Talabalar</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-warning">{{ $stats['staff_count'] }}</h3>
                    <small class="text-muted">Xodimlar</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-danger">{{ $stats['vacancy_count'] }}</h3>
                    <small class="text-muted">Bo'sh o'rinlar</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    @if($faculty->is_active)
                        <span class="badge bg-success fs-6">Faol</span>
                    @else
                        <span class="badge bg-danger fs-6">Nofaol</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- General Information -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Umumiy ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Kod:</th>
                            <td>{{ $faculty->code }}</td>
                        </tr>
                        <tr>
                            <th>Qisqartma:</th>
                            <td>{{ $faculty->abbreviation ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dekan:</th>
                            <td>
                                @if($faculty->dean)
                                    {{ $faculty->dean->name }}
                                @else
                                    <span class="text-muted">Tayinlanmagan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Telefon:</th>
                            <td>{{ $faculty->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $faculty->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Xona:</th>
                            <td>{{ $faculty->room ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Veb-sayt:</th>
                            <td>
                                @if($faculty->website)
                                    <a href="{{ $faculty->website }}" target="_blank">{{ $faculty->website }}</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tashkil etilgan:</th>
                            <td>
                                @if($faculty->established_date)
                                    {{ $faculty->established_date->format('d.m.Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Capacity Information -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Sig'im ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="50%">Talabalar sig'imi:</th>
                            <td>{{ $faculty->student_capacity ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>O'qituvchilar sig'imi:</th>
                            <td>{{ $faculty->teacher_capacity ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Grant o'rinlari:</th>
                            <td>{{ $faculty->state_funded_places ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kontrakt o'rinlari:</th>
                            <td>{{ $faculty->contract_places ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Current Leadership -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Rahbariyat</h5>
                </div>
                <div class="card-body">
                    @forelse($faculty->positions->where('position.category', 'leadership') as $position)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $position->position->name_uz }}</strong><br>
                                {{ $position->employee->name ?? 'Bo\'sh' }}
                            </div>
                            <span class="badge bg-{{ $position->appointment_type == 'main' ? 'success' : 'warning' }}">
                                {{ $position->appointment_type == 'main' ? 'Asosiy' : 'Vaqtinchalik' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Rahbariyat tayinlanmagan</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Departments -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Kafedralar</h5>
            <a href="{{ route('structure.faculties.createDepartment', $faculty) }}" class="btn btn-sm btn-light">
                <i class="fas fa-plus"></i> Yangi kafedra
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nomi</th>
                            <th>Turi</th>
                            <th>Mudiri</th>
                            <th>Xona</th>
                            <th>Yo'nalishlar</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faculty->departments as $department)
                        <tr>
                            <td>{{ $department->name_uz }}</td>
                            <td>
                                @if($department->type)
                                    <span class="badge bg-secondary">{{ $department->type }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $department->head->name ?? '-' }}</td>
                            <td>{{ $department->room_number ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $department->specialties->count() }}</span>
                            </td>
                            <td>
                                <a href="{{ route('structure.departments.show', $department) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Kafedralar mavjud emas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Specialties -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Ta'lim yo'nalishlari</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kod</th>
                            <th>Nomi</th>
                            <th>Kafedra</th>
                            <th>Ta'lim turi</th>
                            <th>Ta'lim muddati</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faculty->specialties as $specialty)
                        <tr>
                            <td>{{ $specialty->code }}</td>
                            <td>{{ $specialty->name_uz }}</td>
                            <td>{{ optional($specialty->department)->name_uz ?? '-' }}</td>
                            <td>{{ $specialty->degree_type ?? '-' }}</td>
                            <td>{{ $specialty->duration ?? '-' }} yil</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Yo'nalishlar mavjud emas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection