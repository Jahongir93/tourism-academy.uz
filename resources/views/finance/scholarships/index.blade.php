@extends('layouts.dashboard-new')

@section('title', 'Grant va Stipendiyalar')

@section('page-title', 'Grant/Stipendiya')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0">Grant va Stipendiyalar</h2>
            <p class="text-muted small">Talabalar uchun moliyaviy yordam dasturlari</p>
        </div>
        <a href="{{ route('finance.scholarships.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yangi grant
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jami grantlar</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $scholarships->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Faol grantlar</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $scholarships->where('status', 'active')->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Grant oluvchilar</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $scholarships->sum('current_recipients') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Jami byudjet</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($scholarships->sum('amount'), 0) }} so'm
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scholarships List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nomi</th>
                            <th>Turi</th>
                            <th>Kategoriya</th>
                            <th>Summa</th>
                            <th>Oluvchilar</th>
                            <th>Holat</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scholarships as $scholarship)
                        <tr>
                            <td>{{ $scholarship->id }}</td>
                            <td>
                                <strong>{{ $scholarship->name }}</strong>
                                @if($scholarship->description)
                                <br><small class="text-muted">{{ Str::limit($scholarship->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $scholarship->type_label }}</span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $scholarship->category_label }}</span>
                            </td>
                            <td class="font-weight-bold">{{ number_format($scholarship->amount, 0) }} so'm</td>
                            <td>
                                {{ $scholarship->current_recipients }}
                                @if($scholarship->max_recipients)
                                    / {{ $scholarship->max_recipients }}
                                @endif
                            </td>
                            <td>
                                @if($scholarship->status === 'active')
                                    <span class="badge badge-success">Faol</span>
                                @elseif($scholarship->status === 'inactive')
                                    <span class="badge badge-secondary">Nofaol</span>
                                @else
                                    <span class="badge badge-danger">Tugagan</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('finance.scholarships.show', $scholarship) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('finance.scholarships.edit', $scholarship) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-graduation-cap fa-3x mb-3 d-block"></i>
                                Grantlar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $scholarships->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
