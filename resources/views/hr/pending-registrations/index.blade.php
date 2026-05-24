@extends('layouts.dashboard-new')

@section('title', 'Kutilayotgan registratsiyalar')
@section('page-title', 'Kutilayotgan registratsiyalar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-user-clock me-2"></i>Kutilayotgan registratsiyalar</h4>
                            <p class="mb-0 opacity-75">Xodimlar va talabalar ro'yxatdan o'tish arizalarini ko'rib chiqish</p>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark fs-6">
                                <i class="fas fa-clock me-1"></i>
                                Jami: {{ $registrations->total() }}
                            </span>
                        </div>
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

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filtrlar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('hr.pending-registrations.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Turi</label>
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Barchasi</option>
                        <option value="student" {{ request('type') == 'student' ? 'selected' : '' }}>Talabalar</option>
                        <option value="employee" {{ request('type') == 'employee' ? 'selected' : '' }}>Xodimlar</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Holati</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Barchasi</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Tasdiqlangan</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rad etilgan</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a href="{{ route('hr.pending-registrations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Filterni tozalash
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Registratsiyalar jadvali -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($registrations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Ism va Familiya</th>
                            <th>Turi</th>
                            <th>Fuqarolik</th>
                            <th>Aloqa</th>
                            <th>Lavozim</th>
                            <th>Sana</th>
                            <th>Holati</th>
                            <th class="text-center">Harakatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrations as $registration)
                        <tr>
                            <td class="fw-bold">#{{ $registration->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="fas fa-user text-muted"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $registration->full_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($registration->type === 'student')
                                    <span class="badge bg-info">
                                        <i class="fas fa-graduation-cap me-1"></i>Talaba
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="fas fa-briefcase me-1"></i>Xodim
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($registration->user_type === 'uzbek')
                                    <span class="badge bg-success">O'zbekiston</span>
                                @else
                                    <span class="badge bg-secondary">Xorijiy</span>
                                @endif
                            </td>
                            <td>
                                @if($registration->phone)
                                    <small><i class="fas fa-phone me-1"></i>{{ $registration->phone }}</small>
                                @endif
                                @if($registration->email)
                                    <small><i class="fas fa-envelope me-1"></i>{{ $registration->email }}</small>
                                @endif
                            </td>
                            <td>
                                @if($registration->position)
                                    <small class="text-muted">{{ $registration->position }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $registration->created_at->format('d.m.Y H:i') }}
                                </small>
                            </td>
                            <td>
                                @if($registration->status === 'pending')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>Kutilmoqda
                                    </span>
                                @elseif($registration->status === 'approved')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Tasdiqlangan
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Rad etilgan
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('hr.pending-registrations.show', $registration->id) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Ko'rish">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($registration->isPending())
                                    <form action="{{ route('hr.pending-registrations.approve', $registration->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Ushbu registratsiyani tasdiqlaysizmi?')">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-success"
                                                title="Tasdiqlash">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Rad etish"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $registration->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>

                                <!-- Reject Modal -->
                                @if($registration->isPending())
                                <div class="modal fade" id="rejectModal{{ $registration->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('hr.pending-registrations.reject', $registration->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Registratsiyani rad etish</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Ism: <strong>{{ $registration->full_name }}</strong></p>
                                                    <div class="mb-3">
                                                        <label for="rejection_reason{{ $registration->id }}" class="form-label">Rad etish sababi <span class="text-danger">*</span></label>
                                                        <textarea name="rejection_reason"
                                                                  id="rejection_reason{{ $registration->id }}"
                                                                  class="form-control"
                                                                  rows="3"
                                                                  required
                                                                  placeholder="Rad etish sababini kiriting..."></textarea>
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-3">
                {{ $registrations->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">Hech qanday registratsiya topilmadi</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
