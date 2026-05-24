@extends('layouts.dashboard-new')

@section('title', 'Yangi xodim qo\'shish')
@section('page-title', 'Yangi xodim qo\'shish')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-user-plus me-2"></i>Yangi xodim qo'shish</h4>
                            <p class="mb-0 opacity-75">Faqat ism va familiyani kiriting - qolganini xodim o'zi to'ldiradi</p>
                        </div>
                        <a href="{{ route('hr.employees.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Orqaga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('hr.employees.store') }}" method="POST">
        @csrf

        <!-- Majburiy ma'lumotlar -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Majburiy ma'lumotlar</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Familiya <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control form-control-lg @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name') }}" placeholder="Abdullayev" required>
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ism <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control form-control-lg @error('first_name') is-invalid @enderror"
                                       value="{{ old('first_name') }}" placeholder="Abbos" required>
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Otasining ismi</label>
                                <input type="text" name="middle_name" class="form-control form-control-lg"
                                       value="{{ old('middle_name') }}" placeholder="Aliyevich">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Xodim turi</label>
                                <select name="employee_type" class="form-select form-select-lg">
                                    <option value="staff" {{ old('employee_type') == 'staff' ? 'selected' : '' }}>Xodim</option>
                                    <option value="teacher" {{ old('employee_type') == 'teacher' ? 'selected' : '' }}>O'qituvchi</option>
                                    <option value="admin" {{ old('employee_type') == 'admin' ? 'selected' : '' }}>Ma'muriyat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Qo'shimcha ma'lumotlar (ixtiyoriy) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <button type="button" class="btn btn-link text-decoration-none p-0 w-100 text-start d-flex justify-content-between align-items-center"
                                data-bs-toggle="collapse" data-bs-target="#optionalFields">
                            <span><i class="fas fa-plus-circle me-2 text-muted"></i>Qo'shimcha ma'lumotlar (ixtiyoriy)</span>
                            <i class="fas fa-chevron-down text-muted"></i>
                        </button>
                    </div>
                    <div class="collapse" id="optionalFields">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" placeholder="xodim@tas.uz">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Telefon</label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ old('phone') }}" placeholder="+998 90 123 45 67">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tug'ilgan sana</label>
                                    <input type="date" name="birth_date" class="form-control"
                                           value="{{ old('birth_date') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Bo'lim</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">Tanlang...</option>
                                        @foreach($departments ?? [] as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Lavozim</label>
                                    <input type="text" name="position" class="form-control"
                                           value="{{ old('position') }}" placeholder="Masalan: Katta o'qituvchi">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Ishga qabul sanasi</label>
                                    <input type="date" name="hire_date" class="form-control"
                                           value="{{ old('hire_date', date('Y-m-d')) }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Manzil</label>
                                    <textarea name="address" class="form-control" rows="2" placeholder="Yashash manzili">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <p class="mb-0 text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Login va parol avtomatik yaratiladi
                        </p>
                        <div>
                            <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-1"></i> Bekor qilish
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-1"></i> Saqlash
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
