@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Ariza yuborildi - Tourism Academy')

@section('content')
<!-- Hero Section -->
<section class="success-hero" style="padding-top: 140px; margin-top: -80px;">
    <div class="container">
        <div class="success-content">
            <div class="success-icon-wrapper">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>

            <h1 class="success-title">Arizangiz qabul qilindi!</h1>

            <p class="success-subtitle">
                Hurmatli <strong>{{ $application->full_name }}</strong>,<br>
                <strong>{{ $application->vacancy->title }}</strong> vakansiyasi uchun arizangiz muvaffaqiyatli yuborildi.
            </p>

            <div class="next-steps-card">
                <h3>
                    <i class="fas fa-info-circle me-2"></i>Keyingi qadamlar:
                </h3>
                <ul>
                    <li><i class="fas fa-check me-2"></i>Arizangiz HR bo'limi tomonidan ko'rib chiqiladi</li>
                    <li><i class="fas fa-check me-2"></i>Mos kelgan taqdirda siz bilan bog'lanamiz</li>
                    <li><i class="fas fa-check me-2"></i>Javob {{ $application->email }} ga yuboriladi</li>
                </ul>
            </div>

            <div class="success-actions">
                <a href="{{ route('vacancies.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-briefcase me-2"></i>Boshqa vakansiyalar
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-home me-2"></i>Bosh sahifa
                </a>
            </div>

            <p class="application-number">
                Ariza raqami: <strong>#{{ $application->id }}</strong>
            </p>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .success-hero {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        padding: 80px 0;
    }

    .success-content {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        padding: 60px 40px;
        max-width: 700px;
        margin: 0 auto;
        text-align: center;
    }

    .success-icon-wrapper {
        margin-bottom: 32px;
    }

    .success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        animation: successPulse 2s ease-in-out infinite;
    }

    .success-icon i {
        font-size: 48px;
        color: white;
    }

    @keyframes successPulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 0 0 20px rgba(16, 185, 129, 0);
        }
    }

    .success-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 20px;
    }

    .success-subtitle {
        font-size: 1.1rem;
        color: #666;
        line-height: 1.8;
        margin-bottom: 32px;
    }

    .next-steps-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 32px;
        text-align: left;
    }

    .next-steps-card h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e40af;
        margin-bottom: 16px;
    }

    .next-steps-card ul {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .next-steps-card li {
        color: #3b82f6;
        font-size: 15px;
        margin-bottom: 12px;
        display: flex;
        align-items: flex-start;
    }

    .next-steps-card li:last-child {
        margin-bottom: 0;
    }

    .next-steps-card li i {
        color: #10b981;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .success-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: center;
        margin-bottom: 32px;
    }

    .success-actions .btn {
        padding: 14px 28px;
        font-weight: 600;
        border-radius: 12px;
    }

    .application-number {
        color: #9ca3af;
        font-size: 14px;
        margin-bottom: 0;
    }

    @media (max-width: 576px) {
        .success-content {
            padding: 40px 24px;
        }

        .success-title {
            font-size: 1.75rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
        }

        .success-icon i {
            font-size: 36px;
        }

        .success-actions {
            flex-direction: column;
        }

        .success-actions .btn {
            width: 100%;
        }
    }
</style>
@endsection
