@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-user-circle me-2"></i>
                        Mening profilim
                    </h4>
                    <p class="mb-0 opacity-75">Shaxsiy ma'lumotlaringizni boshqaring</p>
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Profil ma'lumotlari -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-8x text-gray-300"></i>
                    </div>
                    <h5 class="font-weight-bold">{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->position ?? 'O\'qituvchi' }}</p>
                    <p class="text-muted">{{ $user->department ?? 'Academic' }}</p>

                    <hr>

                    <div class="text-left">
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Telefon:</strong> {{ $user->phone ?? 'Kiritilmagan' }}</p>
                        <p><strong>Manzil:</strong> {{ $user->address ?? 'Kiritilmagan' }}</p>
                        <p><strong>Fan:</strong> {{ $user->subject ?? 'Turizm asoslari' }}</p>
                        <p><strong>Ro'yxatdan o'tgan:</strong> {{ $user->created_at->format('d.m.Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistika -->
            <div class="card shadow mt-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Statistika</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Guruhlar
                            </div>
                            <div class="h5 mb-0 font-weight-bold">5</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Talabalar
                            </div>
                            <div class="h5 mb-0 font-weight-bold">124</div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Darslar
                            </div>
                            <div class="h5 mb-0 font-weight-bold">320</div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Materiallar
                            </div>
                            <div class="h5 mb-0 font-weight-bold">45</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profilni tahrirlash -->
        <div class="col-lg-8 mb-4">
            <!-- Asosiy ma'lumotlar -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Profilni tahrirlash</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Ism Familiya</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email (o'zgartirib bo'lmaydi)</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="phone">Telefon raqam</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                   placeholder="+998 90 123 45 67">
                            @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Manzil</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                   id="address" name="address" value="{{ old('address', $user->address) }}"
                                   placeholder="Shahar, ko'cha, uy">
                            @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="bio">Qisqacha ma'lumot</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror"
                                      id="bio" name="bio" rows="4"
                                      placeholder="O'zingiz haqingizda qisqacha...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Saqlash
                        </button>
                    </form>
                </div>
            </div>

            <!-- Parolni o'zgartirish -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Parolni o'zgartirish</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.profile.change-password') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="current_password">Joriy parol</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password">Yangi parol</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                   id="new_password" name="new_password" required>
                            <small class="form-text text-muted">Kamida 8 ta belgi</small>
                            @error('new_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation">Yangi parolni tasdiqlang</label>
                            <input type="password" class="form-control"
                                   id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Parolni o'zgartirish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection