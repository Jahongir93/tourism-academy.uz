@extends('layouts.dashboard-new')

@section('title', $department->name_uz . ' - Kafedra')

@section('page-title', $department->name_uz)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('structure.departments.index') }}">Kafedralar</a></li>
                    <li class="breadcrumb-item active">{{ $department->name_uz }}</li>
                </ol>
            </nav>
            @if($department->name_ru || $department->name_en)
                <p class="text-muted">
                    {{ $department->name_ru }} 
                    @if($department->name_en) / {{ $department->name_en }} @endif
                </p>
            @endif
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('structure.departments.edit', $department) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Tahrirlash
            </a>
            <a href="{{ route('structure.departments.staffing', $department) }}" class="btn btn-info">
                <i class="fas fa-users"></i> Shtat
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ $stats['specialties_count'] }}</h3>
                    <small class="text-muted">Yo'nalishlar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">{{ $stats['staff_count'] }}</h3>
                    <small class="text-muted">Xodimlar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-danger">{{ $stats['vacancy_count'] }}</h3>
                    <small class="text-muted">Bo'sh o'rinlar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    @if($department->is_active)
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
                            <td>{{ $department->code }}</td>
                        </tr>
                        <tr>
                            <th>Fakultet:</th>
                            <td>
                                <a href="{{ route('structure.faculties.show', $department->faculty) }}">
                                    {{ $department->faculty->name_uz }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Turi:</th>
                            <td>
                                @if($department->type)
                                    <span class="badge bg-info">{{ $department->type }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Mudiri:</th>
                            <td>
                                @if($department->head)
                                    {{ $department->head->name }}
                                @else
                                    <span class="text-muted">Tayinlanmagan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Xona:</th>
                            <td>{{ $department->room_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Telefon:</th>
                            <td>{{ $department->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $department->email ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Qo'shimcha ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="50%">Qisqa nomi:</th>
                            <td>{{ $department->short_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tashkil etilgan:</th>
                            <td>
                                @if($department->established_date)
                                    {{ $department->established_date->format('d.m.Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Xodimlar sig'imi:</th>
                            <td>{{ $department->staff_capacity ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Current Positions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Asosiy xodimlar</h5>
                </div>
                <div class="card-body">
                    @forelse($department->positions->where('appointment_type', 'main')->take(5) as $position)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $position->position->name_uz }}</strong><br>
                                {{ $position->employee->name ?? 'Bo\'sh' }}
                            </div>
                            <span class="badge bg-{{ $position->is_active ? 'success' : 'secondary' }}">
                                {{ $position->workload_percentage }}%
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Xodimlar tayinlanmagan</p>
                    @endforelse
                    
                    @if($department->positions->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('structure.departments.staffing', $department) }}" class="btn btn-sm btn-outline-success">
                                Barcha xodimlarni ko'rish
                            </a>
                        </div>
                    @endif
                </div>
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
                            <th>Ta'lim turi</th>
                            <th>Ta'lim muddati</th>
                            <th>Talabalar soni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($department->specialties as $specialty)
                        <tr>
                            <td>{{ $specialty->code }}</td>
                            <td>
                                <strong>{{ $specialty->name_uz }}</strong>
                                @if($specialty->name_ru)
                                    <br><small class="text-muted">{{ $specialty->name_ru }}</small>
                                @endif
                            </td>
                            <td>{{ $specialty->degree_type ?? '-' }}</td>
                            <td>{{ $specialty->duration ?? '-' }} yil</td>
                            <td>
                                <span class="badge bg-info">{{ $specialty->students_count ?? 0 }}</span>
                            </td>
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