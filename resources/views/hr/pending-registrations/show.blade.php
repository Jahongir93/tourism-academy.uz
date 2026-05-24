@extends('layouts.dashboard-new')

@section('title', 'Registratsiya tafsilotlari')
@section('page-title', 'Registratsiya tafsilotlari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('hr.pending-registrations.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                                <i class="fas fa-arrow-left me-1"></i> Orqaga
                            </a>
                            <h4 class="mb-0"><i class="fas fa-user-circle me-2"></i>Registratsiya #{{ $registration->id }}</h4>
                        </div>
                        <div>
                            @if($registration->status === 'pending')
                                <span class="badge bg-warning fs-6">
                                    <i class="fas fa-clock me-1"></i>Kutilmoqda
                                </span>
                            @elseif($registration->status === 'approved')
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check me-1"></i>Tasdiqlangan
                                </span>
                            @else
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-times me-1"></i>Rad etilgan
                                </span>
                            @endif
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

    <div class="row">
        <!-- Main Info -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Asosiy ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Ism va Familiya:</th>
                            <td class="fw-bold">{{ $registration->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Turi:</th>
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
                        </tr>
                        <tr>
                            <th>Fuqarolik:</th>
                            <td>
                                @if($registration->user_type === 'uzbek')
                                    <span class="badge bg-success">O'zbekiston fuqarosi</span>
                                @else
                                    <span class="badge bg-secondary">Xorijiy fuqaro</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Telefon:</th>
                            <td>
                                @if($registration->phone)
                                    <i class="fas fa-phone me-1 text-primary"></i>{{ $registration->phone }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>
                                @if($registration->email)
                                    <i class="fas fa-envelope me-1 text-primary"></i>{{ $registration->email }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @if($registration->position)
                        <tr>
                            <th>Lavozim:</th>
                            <td>{{ $registration->position }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Ro'yxatdan o'tgan sana:</th>
                            <td>
                                <i class="far fa-calendar me-1"></i>
                                {{ $registration->created_at->format('d.m.Y H:i') }}
                                <small class="text-muted">({{ $registration->created_at->diffForHumans() }})</small>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($registration->additional_info)
            @php
                $info = json_decode($registration->additional_info, true);
            @endphp
            @if(isset($info['extra_info']) && $info['extra_info'])
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Qo'shimcha ma'lumot</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $info['extra_info'] }}</p>
                </div>
            </div>
            @endif
            @endif

            @if($registration->isRejected() && $registration->rejection_reason)
            <div class="card border-0 shadow-sm border-danger mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-times-circle me-2"></i>Rad etish sababi</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $registration->rejection_reason }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Harakatlar</h5>
                </div>
                <div class="card-body">
                    @if($registration->isPending())
                    <form action="{{ route('hr.pending-registrations.approve', $registration->id) }}"
                          method="POST"
                          onsubmit="return confirm('Ushbu registratsiyani tasdiqlaysizmi? Foydalanuvchi yaratiladi va tizimga kirish huquqi beriladi.')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 mb-3">
                            <i class="fas fa-check me-2"></i>Tasdiqlash
                        </button>
                    </form>

                    <button type="button"
                            class="btn btn-danger w-100 mb-3"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectModal">
                        <i class="fas fa-times me-2"></i>Rad etish
                    </button>
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Bu registratsiya allaqachon ko'rib chiqilgan.
                    </div>
                    @endif
                </div>
            </div>

            @if($registration->reviewed_at)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Ko'rib chiqish</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <th>Sana:</th>
                            <td>{{ $registration->reviewed_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Kim tomonidan:</th>
                            <td>
                                @if($registration->reviewer)
                                    {{ $registration->reviewer->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if($registration->isPending())
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hr.pending-registrations.reject', $registration->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Registratsiyani rad etish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Ushbu registratsiyani rad etmoqchisiz. Bu foydalanuvchiga xabar beriladi.
                    </div>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rad etish sababi <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason"
                                  id="rejection_reason"
                                  class="form-control"
                                  rows="4"
                                  required
                                  placeholder="Nima uchun rad etilayotganini batafsil yozing..."></textarea>
                        <small class="text-muted">Bu sabab foydalanuvchiga ko'rinadi</small>
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
@endsection
