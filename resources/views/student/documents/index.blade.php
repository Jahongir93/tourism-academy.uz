@extends('layouts.dashboard-new')

@section('title', 'Hujjatlar - HEMIS')
@section('page-title', 'Hujjatlar')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="mb-2"><i class="fas fa-file-alt text-primary"></i> Hujjatlar markazi</h4>
                    <p class="text-muted mb-0">O'qishingiz davomida kerakli bo'ladigan barcha hujjatlarni bu yerdan yuklab olishingiz mumkin</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Documents Grid -->
    <div class="row g-4">
        <!-- Ma'lumotnoma (Reference Letter) -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="fas fa-file-alt fa-2x text-primary"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0">Ma'lumotnoma</h5>
                            <small class="text-muted">Umumiy ma'lumot</small>
                        </div>
                    </div>
                    <p class="text-muted mb-4">
                        O'qishingiz haqida umumiy ma'lumot beruvchi rasmiy hujjat. Bank, elchilik yoki boshqa tashkilotlar uchun ishlatiladi.
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">
                            <i class="fas fa-check"></i> Mavjud
                        </span>
                        <a href="{{ route('student.documents.reference') }}" class="btn btn-primary btn-sm" target="_blank">
                            <i class="fas fa-download"></i> Yuklab olish
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Transcript -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="fas fa-graduation-cap fa-2x text-info"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0">Akademik ma'lumotnoma</h5>
                            <small class="text-muted">Baholar va fanlar</small>
                        </div>
                    </div>
                    <p class="text-muted mb-4">
                        O'zlashtirilgan fanlar, olingan baholar va GPA ko'rsatkichlari ko'rsatilgan to'liq akademik hisobot.
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">
                            <i class="fas fa-check"></i> Mavjud
                        </span>
                        <a href="{{ route('student.documents.transcript') }}" class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-download"></i> Yuklab olish
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Certificate -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="fas fa-certificate fa-2x text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0">Sertifikat</h5>
                            <small class="text-muted">Kurs yakunlandi</small>
                        </div>
                    </div>
                    <p class="text-muted mb-4">
                        Qo'shimcha kurs yoki treningni muvaffaqiyatli yakunlaganingizni tasdiqlovchi sertifikat.
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        @if($hasCertificate ?? false)
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i> Mavjud
                            </span>
                            <a href="{{ route('student.documents.certificate') }}" class="btn btn-warning btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Yuklab olish
                            </a>
                        @else
                            <span class="badge bg-secondary">
                                <i class="fas fa-lock"></i> Hozircha mavjud emas
                            </span>
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="fas fa-download"></i> Yuklab olish
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Diploma -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="fas fa-award fa-2x text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0">Diplom</h5>
                            <small class="text-muted">Elektron nusxa</small>
                        </div>
                    </div>
                    <p class="text-muted mb-4">
                        Ta'limni muvaffaqiyatli yakunlaganingizni tasdiqlovchi diplomning elektron nusxasi.
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        @if($hasGraduated ?? false)
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i> Mavjud
                            </span>
                            <a href="{{ route('student.documents.diploma') }}" class="btn btn-success btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Yuklab olish
                            </a>
                        @else
                            <span class="badge bg-secondary">
                                <i class="fas fa-lock"></i> O'qishni yakunlang
                            </span>
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="fas fa-download"></i> Yuklab olish
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Diploma Supplement -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger bg-opacity-10 rounded-3 p-3">
                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0">Diplom ilovasi</h5>
                            <small class="text-muted">Qo'shimcha ma'lumot</small>
                        </div>
                    </div>
                    <p class="text-muted mb-4">
                        Diplomga ilova - o'zlashtirilgan barcha fanlar, baholar va akademik yutuqlar to'g'risida batafsil ma'lumot.
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        @if($hasGraduated ?? false)
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i> Mavjud
                            </span>
                            <a href="{{ route('student.documents.diploma-supplement') }}" class="btn btn-danger btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Yuklab olish
                            </a>
                        @else
                            <span class="badge bg-secondary">
                                <i class="fas fa-lock"></i> O'qishni yakunlang
                            </span>
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="fas fa-download"></i> Yuklab olish
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm bg-light">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <div class="text-center mb-3">
                        <i class="fas fa-question-circle fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-center mb-3">Yordam kerakmi?</h5>
                    <p class="text-muted text-center mb-4">
                        Hujjatlar bilan bog'liq savollaringiz bormi? Biz yordam berishga tayyormiz.
                    </p>
                    <div class="text-center">
                        <a href="{{ route('student.help') }}" class="btn btn-outline-primary">
                            <i class="fas fa-life-ring"></i> Yordam markaziga o'tish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Notes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body p-4">
                    <h5 class="text-info mb-3"><i class="fas fa-info-circle"></i> Muhim eslatmalar</h5>
                    <ul class="mb-0 text-muted">
                        <li class="mb-2">Barcha hujjatlar PDF formatida yuklab olinadi</li>
                        <li class="mb-2">Hujjatlar elektron imzo bilan tasdiqlangan va rasmiy hisoblanadi</li>
                        <li class="mb-2">Diplom va diplom ilovasi faqat o'qishni to'liq yakunlaganingizdan keyin mavjud bo'ladi</li>
                        <li class="mb-2">Sertifikat olish uchun tegishli kursni muvaffaqiyatli yakunlashingiz kerak</li>
                        <li class="mb-0">Agar hujjat yuklab olinmasa yoki xatolik yuz bersa, yordam markaziga murojaat qiling</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.icon-box {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
}

.transition {
    transition: all 0.3s ease;
}
</style>
@endsection
