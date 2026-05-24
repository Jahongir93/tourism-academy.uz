@extends('layouts.dashboard-new')

@section('title', ($position->name_uz ?? $position->name) . ' - Lavozim')

@section('page-title', $position->name_uz ?? $position->name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('structure.positions.index') }}">Lavozimlar</a></li>
                    <li class="breadcrumb-item active">{{ $position->name_uz ?? $position->name }}</li>
                </ol>
            </nav>
            @if($position->name_ru || $position->name_en)
                <p class="text-muted">
                    {{ $position->name_ru }} 
                    @if($position->name_en) / {{ $position->name_en }} @endif
                </p>
            @endif
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('structure.positions.edit', $position) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Tahrirlash
            </a>
            <a href="{{ route('structure.positions.hierarchy') }}" class="btn btn-info">
                <i class="fas fa-sitemap"></i> Ierarxiya
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ $stats['total_allocated'] }}</h3>
                    <small class="text-muted">Jami shtat</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">{{ $stats['total_filled'] }}</h3>
                    <small class="text-muted">Band qilingan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-danger">{{ $stats['total_vacancy'] }}</h3>
                    <small class="text-muted">Bo'sh o'rinlar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-info">{{ $stats['active_assignments'] }}</h3>
                    <small class="text-muted">Faol tayinlashlar</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Position Information -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lavozim ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Kod:</th>
                            <td>{{ $position->code }}</td>
                        </tr>
                        <tr>
                            <th>Kategoriya:</th>
                            <td>
                                @switch($position->category)
                                    @case('leadership')
                                        <span class="badge bg-primary">Rahbariyat</span>
                                        @break
                                    @case('academic')
                                        <span class="badge bg-success">Akademik</span>
                                        @break
                                    @case('administrative')
                                        <span class="badge bg-info">Ma'muriy</span>
                                        @break
                                    @case('support')
                                        <span class="badge bg-warning">Yordamchi</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <th>Daraja:</th>
                            <td>{{ $position->level }}-daraja</td>
                        </tr>
                        <tr>
                            <th>Maosh darajasi:</th>
                            <td>{{ $position->salary_grade ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Holati:</th>
                            <td>
                                @if($position->is_active)
                                    <span class="badge bg-success">Faol</span>
                                @else
                                    <span class="badge bg-danger">Nofaol</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Hierarchy -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Ierarxiya</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-muted">Kimga bo'ysunadi:</h6>
                    @if($position->reportsTo->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($position->reportsTo as $superior)
                                <li>
                                    <a href="{{ route('structure.positions.show', $superior) }}">
                                        <i class="fas fa-arrow-up text-primary"></i> {{ $superior->name_uz ?? $superior->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Hech kimga bo'ysunmaydi</p>
                    @endif

                    <h6 class="text-muted mt-3">Kimlar bo'ysunadi:</h6>
                    @if($position->subordinates->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($position->subordinates as $subordinate)
                                <li>
                                    <a href="{{ route('structure.positions.show', $subordinate) }}">
                                        <i class="fas fa-arrow-down text-success"></i> {{ $subordinate->name_uz ?? $subordinate->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Hech kim bo'ysunmaydi</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Requirements and Responsibilities -->
        <div class="col-md-6">
            @if($position->requirements && count($position->requirements) > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Talablar</h5>
                </div>
                <div class="card-body">
                    <ul>
                        @foreach($position->requirements as $requirement)
                            @if($requirement)
                                <li>{{ $requirement }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            @if($position->responsibilities && count($position->responsibilities) > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Vazifalar</h5>
                </div>
                <div class="card-body">
                    <ul>
                        @foreach($position->responsibilities as $responsibility)
                            @if($responsibility)
                                <li>{{ $responsibility }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Current Assignments -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Hozirgi tayinlashlar</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Xodim</th>
                            <th>Tashkilot</th>
                            <th>Tayinlash turi</th>
                            <th>Sana</th>
                            <th>Stavka</th>
                            <th>Holati</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td>
                                <strong>{{ optional($assignment->employee)->name ?? 'Bo\'sh' }}</strong>
                            </td>
                            <td>
                                @if($assignment->orgUnit())
                                    {{ optional($assignment->orgUnit())->name ?? $assignment->org_unit_type }}
                                @else
                                    {{ $assignment->org_unit_type }}
                                @endif
                            </td>
                            <td>
                                @switch($assignment->appointment_type)
                                    @case('main')
                                        <span class="badge bg-success">Asosiy</span>
                                        @break
                                    @case('acting')
                                        <span class="badge bg-warning">Vaqtinchalik</span>
                                        @break
                                    @case('temporary')
                                        <span class="badge bg-info">Muddatli</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $assignment->appointment_date->format('d.m.Y') }}</td>
                            <td>{{ $assignment->workload_percentage }}%</td>
                            <td>
                                @if($assignment->is_active)
                                    <span class="badge bg-success">Faol</span>
                                @else
                                    <span class="badge bg-secondary">Nofaol</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                                    <p>Hozircha tayinlashlar yo'q</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Staff Allocations by Organization -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Tashkilotlar bo'yicha shtat birliklari</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tashkilot</th>
                            <th>Turi</th>
                            <th>Ajratilgan</th>
                            <th>Band</th>
                            <th>Bo'sh</th>
                            <th>Byudjet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allocations as $allocation)
                        <tr>
                            <td>
                                @if($allocation->orgUnit())
                                    {{ optional($allocation->orgUnit())->name ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $allocation->org_unit_type }}</td>
                            <td>{{ $allocation->allocated_count }}</td>
                            <td>{{ $allocation->filled_count }}</td>
                            <td>
                                @if($allocation->vacancy_count > 0)
                                    <span class="text-danger">{{ $allocation->vacancy_count }}</span>
                                @else
                                    <span class="text-success">0</span>
                                @endif
                            </td>
                            <td>
                                @if($allocation->budget_allocated)
                                    {{ number_format($allocation->budget_allocated, 0, ',', ' ') }} so'm
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                Shtat birliklari ajratilmagan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection