@extends('layouts.dashboard-new')

@section('title', 'Mening profilim')
@section('page-title', 'Mening profilim')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Mening profilim</h3>
            <p class="text-muted mb-0">Shaxsiy ma'lumotlar va sozlamalar</p>
        </div>
        <div>
            <a href="{{ route('student.documents.index') }}" class="btn btn-success me-2">
                <i class="fas fa-file-alt me-2"></i>Hujjatlar
            </a>
            <a href="{{ route('student.profile.id-card') }}" class="btn btn-primary me-2" target="_blank">
                <i class="fas fa-id-card me-2"></i>ID Karta
            </a>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Orqaga
            </a>
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
        <!-- Left Column - Profile Info -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($user->profile_photo_path)
                        <img src="{{ Storage::url($user->profile_photo_path) }}"
                             alt="Profile Photo"
                             class="rounded-circle"
                             style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                             style="width: 150px; height: 150px; font-size: 48px;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        @endif
                    </div>
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $academicInfo['student_id'] }}</p>
                    <span class="badge bg-{{ $academicInfo['status'] == 'active' ? 'success' : 'warning' }} mb-3">
                        {{ $academicInfo['status'] == 'active' ? 'Faol' : 'Nofaol' }}
                    </span>

                    <div class="text-start mt-4">
                        <div class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <small>{{ $user->email ?? 'Email kiritilmagan' }}</small>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <small>{{ $user->phone ?? 'Telefon kiritilmagan' }}</small>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <small>{{ $student->address ?? 'Manzil kiritilmagan' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Info Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>Akademik ma'lumotlar
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Fakultet</small>
                        <strong>{{ $academicInfo['faculty'] }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Ta'lim yo'nalishi</small>
                        <strong>{{ $academicInfo['specialty'] }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Guruh</small>
                        <strong>{{ $academicInfo['group'] }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Kurs</small>
                        <strong>{{ $academicInfo['course'] }}-kurs</strong>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Qabul qilingan sana</small>
                        <strong>{{ $academicInfo['admitted_on'] ? \Carbon\Carbon::parse($academicInfo['admitted_on'])->translatedFormat('d F Y') : 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Edit Forms -->
        <div class="col-lg-8">
            <!-- Edit Profile Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>Profilni tahrirlash
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">To'liq ism <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control bg-light"
                                       id="name"
                                       value="{{ $user->name }}"
                                       disabled>
                                <small class="text-muted">Ismni o'zgartirish uchun admin bilan bog'laning</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $user->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefon raqami</label>
                                <input type="text"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="+998 90 123 45 67">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="photo" class="form-label">Profil rasmi</label>
                                <input type="file"
                                       class="form-control @error('photo') is-invalid @enderror"
                                       id="photo"
                                       name="photo"
                                       accept="image/*">
                                <small class="text-muted">Maksimal hajm: 2MB (JPG, PNG)</small>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">Yashash manzili</label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address"
                                          name="address"
                                          rows="3"
                                          placeholder="Shahar, tuman, mahalla, ko'cha...">{{ old('address', $student->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Saqlash
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lock me-2"></i>Parolni o'zgartirish
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.profile.change-password') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Joriy parol <span class="text-danger">*</span></label>
                            <input type="password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password"
                                   name="current_password"
                                   required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Yangi parol <span class="text-danger">*</span></label>
                            <input type="password"
                                   class="form-control @error('new_password') is-invalid @enderror"
                                   id="new_password"
                                   name="new_password"
                                   required
                                   minlength="8">
                            <small class="text-muted">Kamida 8 ta belgi</small>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Yangi parolni tasdiqlang <span class="text-danger">*</span></label>
                            <input type="password"
                                   class="form-control"
                                   id="new_password_confirmation"
                                   name="new_password_confirmation"
                                   required
                                   minlength="8">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Parolni o'zgartirish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
