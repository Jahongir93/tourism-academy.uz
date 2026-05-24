@extends('layouts.dashboard-new')

@section('title', $faculty->name_uz . ' - Kafedralar')

@section('page-title', $faculty->name_uz . ' - Kafedralar')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('structure.faculties.index') }}">Fakultetlar</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('structure.faculties.show', $faculty) }}">{{ $faculty->name_uz }}</a></li>
                    <li class="breadcrumb-item active">Kafedralar</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('structure.faculties.createDepartment', $faculty) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi kafedra
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kod</th>
                            <th>Nomi</th>
                            <th>Turi</th>
                            <th>Mudiri</th>
                            <th>Xona</th>
                            <th>Yo'nalishlar</th>
                            <th>Xodimlar</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $department->code }}</span>
                            </td>
                            <td>
                                <strong>{{ $department->name_uz }}</strong>
                                @if($department->name_ru)
                                    <br><small class="text-muted">{{ $department->name_ru }}</small>
                                @endif
                            </td>
                            <td>
                                @if($department->type)
                                    <span class="badge bg-info">{{ $department->type }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ optional($department->head)->name ?? 'Tayinlanmagan' }}</td>
                            <td>{{ $department->room_number ?? '-' }}</td>
                            <td>
                                <span class="badge bg-success">{{ $department->specialties->count() }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $department->positions()->where('is_active', true)->count() }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('structure.departments.show', $department) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ko'rish">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('structure.departments.edit', $department) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Tahrirlash">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('structure.departments.staffing', $department) }}" 
                                       class="btn btn-sm btn-outline-success" title="Shtat">
                                        <i class="fas fa-users"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                                    <p>Bu fakultetda hozircha kafedralar yo'q</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($departments->hasPages())
            <div class="mt-3">
                {{ $departments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection