@extends('layouts.dashboard-new')

@section('title', 'HEMIS Sozlamalari')
@section('page-title', 'HEMIS Sozlamalari')

@section('content')
<div class="container-fluid px-0">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Configuration Status Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-2">
                                <i class="fas fa-plug me-2 text-primary"></i>
                                HEMIS Ulanish Holati
                            </h5>
                            <p class="text-muted mb-0">
                                @if($isConfigured)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Sozlangan
                                    </span>
                                    HEMIS tizimi bilan integratsiya sozlangan
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Sozlanmagan
                                    </span>
                                    HEMIS tizimi bilan integratsiyani sozlang
                                @endif
                            </p>
                        </div>
                        <button type="button" class="btn btn-outline-primary" onclick="testConnection()">
                            <i class="fas fa-check me-2"></i>
                            Ulanishni Tekshirish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2 text-primary"></i>
                        HEMIS API Sozlamalari
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('hemis.settings.update') }}">
                        @csrf

                        <!-- HEMIS URL -->
                        <div class="mb-4">
                            <label for="hemis_url" class="form-label fw-semibold">
                                HEMIS API URL
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="url"
                                class="form-control @error('hemis_url') is-invalid @enderror"
                                id="hemis_url"
                                name="hemis_url"
                                value="{{ old('hemis_url', $hemisUrl) }}"
                                placeholder="https://hemis.samdu.uz/api"
                                required
                            >
                            @error('hemis_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                HEMIS tizimining API asosiy URL manzili
                            </small>
                        </div>

                        <!-- Client ID -->
                        <div class="mb-4">
                            <label for="client_id" class="form-label fw-semibold">
                                Client ID
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control @error('client_id') is-invalid @enderror"
                                id="client_id"
                                name="client_id"
                                value="{{ old('client_id', $clientId) }}"
                                placeholder="your-client-id"
                                required
                            >
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                HEMIS tizimidan olingan Client ID
                            </small>
                        </div>

                        <!-- Client Secret -->
                        <div class="mb-4">
                            <label for="client_secret" class="form-label fw-semibold">
                                Client Secret
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    class="form-control @error('client_secret') is-invalid @enderror"
                                    id="client_secret"
                                    name="client_secret"
                                    placeholder="••••••••••••••••"
                                    required
                                >
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                                @error('client_secret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                HEMIS tizimidan olingan maxfiy kalit
                            </small>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Saqlash
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Bekor qilish
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2 text-info"></i>
                        Yordam
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">HEMIS integratsiyasi qanday ishlaydi?</h6>
                    <ol class="small">
                        <li class="mb-2">HEMIS administrator panelidan API kalitlarini oling</li>
                        <li class="mb-2">Yuqoridagi formaga ma'lumotlarni kiriting</li>
                        <li class="mb-2">"Saqlash" tugmasini bosing</li>
                        <li class="mb-2">"Ulanishni Tekshirish" tugmasini bosib tekshiring</li>
                    </ol>

                    <hr class="my-3">

                    <h6 class="fw-semibold mb-3">Qo'shimcha imkoniyatlar:</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Talabalarni avtomatik sinxronlash
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            O'qituvchilarni avtomatik sinxronlash
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Baholarni avtomatik import qilish
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Dars jadvalini olish
                        </li>
                    </ul>

                    <hr class="my-3">

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Eslatma:</strong> HEMIS tizimi bilan integratsiya uchun HEMIS administratoridan API kalitlarini so'rang.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
}

.card-header {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}
</style>

<script>
function togglePassword() {
    const input = document.getElementById('client_secret');
    const icon = document.getElementById('toggleIcon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function testConnection() {
    // Show loading
    const btn = event.target;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Tekshirilmoqda...';
    btn.disabled = true;

    // Make AJAX request
    fetch('{{ route('hemis.test') }}')
        .then(response => response.json())
        .then(data => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;

            if (data.success) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Muvaffaqiyatli!',
                    text: data.message,
                    confirmButtonColor: '#667eea'
                });
            } else {
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Xatolik!',
                    text: data.message,
                    confirmButtonColor: '#667eea'
                });
            }
        })
        .catch(error => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;

            Swal.fire({
                icon: 'error',
                title: 'Xatolik!',
                text: 'Ulanishni tekshirishda xatolik yuz berdi',
                confirmButtonColor: '#667eea'
            });
        });
}
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
