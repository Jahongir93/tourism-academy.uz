@extends('layouts.dashboard-new')

@section('title', 'Ta\'til arizalari')
@section('page-title', 'Ta\'til arizalari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-file-alt me-2"></i>Ta'til arizalari</h4>
                            <p class="mb-0 opacity-75">Xodimlar ta'til arizalarini ko'rish va boshqarish</p>
                        </div>
                        <a href="{{ route('hr.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filtrlar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('hr.leave.requests') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Tasdiqlangan</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rad etilgan</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filtrlash
                    </button>
                    <a href="{{ route('hr.leave.requests') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Arizalar jadvali -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Xodim</th>
                            <th class="border-0">Ta'til turi</th>
                            <th class="border-0">Boshlanish</th>
                            <th class="border-0">Tugash</th>
                            <th class="border-0">Kunlar</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $request->employee?->last_name }} {{ $request->employee?->first_name }}</h6>
                                        <small class="text-muted">{{ $request->employee?->department?->name ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $request->leaveType?->name ?? 'Ta\'til' }}</td>
                            <td>{{ $request->start_date?->format('d.m.Y') }}</td>
                            <td>{{ $request->end_date?->format('d.m.Y') }}</td>
                            <td>
                                @if($request->start_date && $request->end_date)
                                    {{ $request->start_date->diffInDays($request->end_date) + 1 }} kun
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($request->status == 'approved')
                                    <span class="badge bg-success">Tasdiqlangan</span>
                                @elseif($request->status == 'rejected')
                                    <span class="badge bg-danger">Rad etilgan</span>
                                @else
                                    <span class="badge bg-warning">Kutilmoqda</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($request->status == 'pending')
                                <form action="{{ route('hr.leave.approve', $request) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success" title="Tasdiqlash">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" title="Rad etish"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                    <i class="fas fa-times"></i>
                                </button>

                                <!-- Rad etish modal -->
                                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('hr.leave.reject', $request) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Arizani rad etish</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label">Rad etish sababi</label>
                                                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                                                    <button type="submit" class="btn btn-danger">Rad etish</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3 d-block opacity-50"></i>
                                <p class="text-muted mb-0">Ta'til arizalari topilmadi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($requests->hasPages())
        <div class="card-footer bg-white">
            {{ $requests->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>
@endsection
