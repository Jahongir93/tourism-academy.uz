@extends('layouts.dashboard-new')

@section('title', $faculty->name_uz . ' - Shtat birliklari')

@section('page-title', $faculty->name_uz . ' - Shtat birliklari')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('structure.faculties.index') }}">Fakultetlar</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('structure.faculties.show', $faculty) }}">{{ $faculty->name_uz }}</a></li>
                    <li class="breadcrumb-item active">Shtat birliklari</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#allocateStaffModal">
                <i class="fas fa-plus"></i> Shtat birligi qo'shish
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ $staffAllocations->sum('allocated_count') }}</h3>
                    <small class="text-muted">Jami shtat birliklari</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">{{ $staffAllocations->sum('filled_count') }}</h3>
                    <small class="text-muted">Band qilingan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-danger">{{ $staffAllocations->sum('vacancy_count') }}</h3>
                    <small class="text-muted">Bo'sh o'rinlar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-info">
                        @if($staffAllocations->sum('allocated_count') > 0)
                            {{ round(($staffAllocations->sum('filled_count') / $staffAllocations->sum('allocated_count')) * 100) }}%
                        @else
                            0%
                        @endif
                    </h3>
                    <small class="text-muted">To'ldirilganlik</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Allocations -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Shtat jadvali</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Lavozim</th>
                            <th>Kategoriya</th>
                            <th>Shtat birliklari</th>
                            <th>Band qilingan</th>
                            <th>Bo'sh o'rinlar</th>
                            <th>Byudjet (oylik)</th>
                            <th>To'ldirilganlik</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffAllocations as $allocation)
                        <tr>
                            <td>
                                <strong>{{ $allocation->position->name_uz }}</strong>
                            </td>
                            <td>
                                @switch($allocation->position->category)
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
                            <td>
                                <div class="progress" style="height: 20px;">
                                    @php
                                        $percentage = $allocation->allocated_count > 0 
                                            ? round(($allocation->filled_count / $allocation->allocated_count) * 100) 
                                            : 0;
                                    @endphp
                                    <div class="progress-bar bg-{{ $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%">
                                        {{ $percentage }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                                    <p>Shtat birliklari belgilanmagan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Current Staff -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Hozirgi xodimlar</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>F.I.O</th>
                            <th>Lavozim</th>
                            <th>Tayinlash turi</th>
                            <th>Tayinlash sanasi</th>
                            <th>Stavka</th>
                            <th>Maosh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($positions as $position)
                        <tr>
                            <td>
                                <strong>{{ optional($position->employee)->name ?? 'Bo\'sh' }}</strong>
                            </td>
                            <td>{{ $position->position->name_uz }}</td>
                            <td>
                                @switch($position->appointment_type)
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
                            <td>{{ $position->appointment_date->format('d.m.Y') }}</td>
                            <td>{{ $position->workload_percentage }}%</td>
                            <td>
                                @if($position->salary)
                                    {{ number_format($position->salary, 0, ',', ' ') }} so'm
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                                    <p>Xodimlar tayinlanmagan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Allocate Staff Modal -->
<div class="modal fade" id="allocateStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('structure.faculties.allocateStaff', $faculty) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Shtat birligi qo'shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="position_id" class="form-label">Lavozim <span class="text-danger">*</span></label>
                        <select class="form-select" name="position_id" required>
                            <option value="">Tanlang...</option>
                            @foreach(\App\Models\Position::where('is_active', true)->get() as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->name_uz }} ({{ $pos->category }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="allocated_count" class="form-label">Shtat birliklari soni <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="allocated_count" min="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="budget_allocated" class="form-label">Oylik byudjet (so'm)</label>
                        <input type="number" class="form-control" name="budget_allocated" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection