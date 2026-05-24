@extends('layouts.dashboard-new')

@section('title', 'Dars Jadvali')
@section('page-title', 'Haftalik Dars Jadvali')

@section('styles')
<style>
    .schedule-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .day-header {
        background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        font-size: 18px;
    }

    .lesson-item {
        padding: 20px;
        border-bottom: 1px solid #E5E7EB;
        transition: all 0.3s ease;
    }

    .lesson-item:last-child {
        border-bottom: none;
    }

    .lesson-item:hover {
        background: #F3F4F6;
    }

    .time-badge {
        background: #3B82F6;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
        display: inline-block;
    }

    .type-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .type-lecture {
        background: #DBEAFE;
        color: #1E40AF;
    }

    .type-practice {
        background: #D1FAE5;
        color: #065F46;
    }

    .type-seminar {
        background: #FEF3C7;
        color: #92400E;
    }

    .empty-day {
        text-align: center;
        padding: 40px;
        color: #9CA3AF;
    }

    .legend {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        margin-right: 20px;
        margin-bottom: 10px;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .current-day {
        border: 3px solid #10B981;
    }

    @media print {
        .no-print {
            display: none;
        }

        .schedule-card {
            box-shadow: none;
            border: 1px solid #E5E7EB;
            page-break-inside: avoid;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-2">
                <i class="fas fa-calendar-alt text-primary"></i>
                Haftalik Dars Jadvali
            </h3>
            <p class="text-muted">
                Talaba: <strong>{{ $student->first_name }} {{ $student->last_name }}</strong> |
                Guruh: <strong>DI-301</strong> |
                Semestr: <strong>2024-2025 (Kuz)</strong>
            </p>
        </div>
        <div class="col-md-4 text-end no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>Chop etish
            </button>
            <button onclick="downloadPDF()" class="btn btn-outline-primary">
                <i class="fas fa-download me-2"></i>PDF
            </button>
        </div>
    </div>

    <!-- Legend -->
    <div class="legend no-print">
        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Belgilar</h6>
        <div class="d-flex flex-wrap">
            <div class="legend-item">
                <div class="legend-dot" style="background: #3B82F6;"></div>
                <span>Ma'ruza</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: #10B981;"></div>
                <span>Amaliyot</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: #F59E0B;"></div>
                <span>Seminar</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: #EF4444;"></div>
                <span>Imtihon</span>
            </div>
        </div>
    </div>

    <!-- Schedule -->
    <div class="row">
        @php
            $daysOfWeek = ['Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba'];
            $today = now()->locale('uz')->dayName;
        @endphp

        @foreach($daysOfWeek as $day)
        <div class="col-lg-6 mb-4">
            <div class="schedule-card {{ strtolower($day) === strtolower($today) ? 'current-day' : '' }}">
                <div class="day-header">
                    <i class="fas fa-calendar-day me-2"></i>
                    {{ $day }}
                    @if(strtolower($day) === strtolower($today))
                        <span class="badge bg-success ms-2">Bugun</span>
                    @endif
                </div>

                @if(isset($schedule[$day]) && count($schedule[$day]) > 0)
                    @foreach($schedule[$day] as $lesson)
                    <div class="lesson-item">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="time-badge">{{ $lesson['time'] }}</div>
                                <div class="text-muted small mt-1">{{ $lesson['pair'] }}</div>
                            </div>
                            <div class="col-md-9">
                                <h6 class="mb-2 fw-bold">{{ $lesson['subject'] }}</h6>
                                <div class="mb-2">
                                    @php
                                        $typeClass = match($lesson['type']) {
                                            'Ma\'ruza' => 'type-lecture',
                                            'Amaliyot' => 'type-practice',
                                            'Seminar' => 'type-seminar',
                                            default => 'type-lecture'
                                        };
                                    @endphp
                                    <span class="type-badge {{ $typeClass }}">{{ $lesson['type'] }}</span>
                                </div>
                                <div class="small text-muted">
                                    <i class="fas fa-chalkboard-teacher me-1"></i> {{ $lesson['teacher'] }}<br>
                                    <i class="fas fa-door-open me-1"></i> {{ $lesson['room'] }}, {{ $lesson['building'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="empty-day">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <p>Dam olish kuni</p>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Summary -->
    <div class="row no-print">
        <div class="col-md-12">
            <div class="alert alert-info">
                <h6><i class="fas fa-lightbulb me-2"></i>Eslatma:</h6>
                <ul class="mb-0">
                    <li>Dars vaqtlari o'zgarishi mumkin, doimiy tekshirib turing</li>
                    <li>Online darslar uchun LMS tizimiga kiring</li>
                    <li>Darsga kechikmaslik uchun 10 daqiqa oldin keling</li>
                    <li>Savol-javoblar uchun o'qituvchilar bilan bog'laning</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function downloadPDF() {
        alert('PDF yuklab olish funksiyasi tez orada qo\'shiladi');
    }

    // Bugungi kunni highlight qilish
    document.addEventListener('DOMContentLoaded', function() {
        const currentDay = document.querySelector('.current-day');
        if (currentDay) {
            currentDay.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });
</script>
@endsection
