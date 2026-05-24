@extends('layouts.dashboard-new')

@section('title', 'Yordam')
@section('page-title', 'Yordam markazi')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">
                <i class="fas fa-life-ring text-primary me-2"></i>Yordam markazi
            </h3>
            <p class="text-muted mb-0">HEMIS tizimidan foydalanish bo'yicha qo'llanma va ko'mak</p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Orqaga
        </a>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        @foreach($quickLinks as $link)
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="{{ route($link['route']) }}" class="text-decoration-none">
                <div class="card border-left-{{ $link['color'] }} h-100 hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-{{ $link['color'] }}-light me-3">
                                <i class="{{ $link['icon'] }} text-{{ $link['color'] }} fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-dark">{{ $link['title'] }}</h6>
                                <small class="text-muted">Tezkor kirish</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <div class="row">
        <!-- FAQ Section -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>Tez-tez beriladigan savollar (FAQ)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        @foreach($faqCategories as $categoryIndex => $category)
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="{{ $category['icon'] }} me-2"></i>{{ $category['name'] }}
                            </h6>
                            @foreach($category['faqs'] as $faqIndex => $faq)
                            <div class="accordion-item border rounded mb-2">
                                <h2 class="accordion-header" id="heading{{ $categoryIndex }}{{ $faqIndex }}">
                                    <button class="accordion-button {{ $categoryIndex == 0 && $faqIndex == 0 ? '' : 'collapsed' }}"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $categoryIndex }}{{ $faqIndex }}"
                                            aria-expanded="{{ $categoryIndex == 0 && $faqIndex == 0 ? 'true' : 'false' }}"
                                            aria-controls="collapse{{ $categoryIndex }}{{ $faqIndex }}">
                                        {{ $faq['question'] }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $categoryIndex }}{{ $faqIndex }}"
                                     class="accordion-collapse collapse {{ $categoryIndex == 0 && $faqIndex == 0 ? 'show' : '' }}"
                                     aria-labelledby="heading{{ $categoryIndex }}{{ $faqIndex }}"
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body bg-light">
                                        {{ $faq['answer'] }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Video Tutorials -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-video me-2"></i>Video qo'llanmalar
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="video-card">
                                <div class="video-thumbnail">
                                    <i class="fas fa-play-circle fa-4x text-primary"></i>
                                </div>
                                <h6 class="mt-3">HEMIS tizimiga kirish</h6>
                                <small class="text-muted">Davomiyligi: 3:45</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="video-card">
                                <div class="video-thumbnail">
                                    <i class="fas fa-play-circle fa-4x text-primary"></i>
                                </div>
                                <h6 class="mt-3">Topshiriqlarni topshirish</h6>
                                <small class="text-muted">Davomiyligi: 5:20</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="video-card">
                                <div class="video-thumbnail">
                                    <i class="fas fa-play-circle fa-4x text-primary"></i>
                                </div>
                                <h6 class="mt-3">Baholarni ko'rish</h6>
                                <small class="text-muted">Davomiyligi: 4:15</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="video-card">
                                <div class="video-thumbnail">
                                    <i class="fas fa-play-circle fa-4x text-primary"></i>
                                </div>
                                <h6 class="mt-3">Profilni sozlash</h6>
                                <small class="text-muted">Davomiyligi: 2:50</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact & Support -->
        <div class="col-lg-4">
            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-phone-alt me-2"></i>Bog'lanish
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($contacts as $contact)
                    <div class="contact-item mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-start">
                            <div class="icon-circle bg-success-light me-3">
                                <i class="{{ $contact['icon'] }} text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">{{ $contact['title'] }}</small>
                                @if($contact['type'] == 'email')
                                <a href="mailto:{{ $contact['value'] }}" class="text-decoration-none">
                                    {{ $contact['value'] }}
                                </a>
                                @elseif($contact['type'] == 'phone')
                                <a href="tel:{{ $contact['value'] }}" class="text-decoration-none">
                                    {{ $contact['value'] }}
                                </a>
                                @else
                                <strong>{{ $contact['value'] }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Support Ticket -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>Muammo haqida xabar berish
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Agar muammo yuzaga kelsa, quyidagi ma'lumotlarni ko'rsating:</p>
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>Muammoning to'liq tavsifi
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>Qaysi sahifada yuz bergan
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>Screenshot (agar mumkin bo'lsa)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>Sizning student ID
                        </li>
                    </ul>
                    <a href="mailto:support@tourism-academy.uz?subject=HEMIS Support - {{ $student->student_no ?? '' }}"
                       class="btn btn-warning w-100">
                        <i class="fas fa-envelope me-2"></i>Xabar yuborish
                    </a>
                </div>
            </div>

            <!-- Useful Links -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-link me-2"></i>Foydali havolalar
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="https://hemis.uz" target="_blank" class="list-group-item list-group-item-action">
                            <i class="fas fa-external-link-alt me-2 text-primary"></i>HEMIS rasmiy sayti
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-book me-2 text-primary"></i>Foydalanuvchi qo'llanmasi
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-pdf me-2 text-primary"></i>Talaba nizomi
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-question-circle me-2 text-primary"></i>Ko'p beriladigan savollar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: all 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }

.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-primary-light { background: rgba(78, 115, 223, 0.1); }
.bg-success-light { background: rgba(28, 200, 138, 0.1); }
.bg-warning-light { background: rgba(246, 158, 11, 0.1); }
.bg-info-light { background: rgba(54, 185, 204, 0.1); }

.video-card {
    text-align: center;
    padding: 20px;
    border: 1px solid #e3e6f0;
    border-radius: 10px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.video-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-3px);
}

.video-thumbnail {
    background: #f8f9fc;
    padding: 40px;
    border-radius: 10px;
    margin-bottom: 15px;
}

.contact-item {
    transition: all 0.2s ease;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fc;
    color: #4e73df;
}
</style>
@endsection
