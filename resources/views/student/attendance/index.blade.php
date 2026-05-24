@extends('layouts.dashboard-new')

@section('title', 'Davomat')
@section('page-title', 'Davomat hisoboti')

@section('styles')
<style>
    .attendance-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid #E5E7EB;
        margin-bottom: 20px;
    }

    .attendance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    .summary-card {
        background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
        color: white;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 32px;
    }

    .subject-row {
        padding: 20px;
        border-bottom: 1px solid #E5E7EB;
        transition: all 0.3s ease;
    }

    .subject-row:last-child {
        border-bottom: none;
    }

    .subject-row:hover {
        background: #F9FAFB;
    }

    .hours-badge {
        background: #EF4444;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 18px;
    }

    .hours-badge.low {
        background: #10B981;
    }

    .hours-badge.medium {
        background: #F59E0B;
    }

    .hours-badge.high {
        background: #EF4444;
    }

    .stat-box {
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
    }

    .progress-bar-custom {
        height: 8px;
        border-radius: 10px;
        background: #E5E7EB;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    .progress-fill.excellent { background: #10B981; }
    .progress-fill.good { background: #3B82F6; }
    .progress-fill.warning { background: #F59E0B; }
    .progress-fill.danger { background: #EF4444; }

    .no-schedule-card {
        text-align: center;
        padding: 60px 20px;
    }

    .no-schedule-icon {
        font-size: 80px;
        color: #D1D5DB;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    @if(!$hasSchedule)
        <!-- Ma'lumot yo'q holati -->
        <div class="attendance-card p-5">
            <div class="no-schedule-card">
                <i class="fas fa-calendar-times no-schedule-icon"></i>
                <h3 class="text-muted mb-3">{{ $message }}</h3>
                <p class="text-muted">Guruhingizga darslar biriktirilgandan so'ng davomat ma'lumotlari bu yerda ko'rinadi.</p>
                <a href="{{ route('student.dashboard') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-arrow-left me-2"></i>Dashboardga qaytish
                </a>
            </div>
        </div>
    @else
        <!-- Umumiy xulosa -->
        <div class="summary-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-3">
                        <i class="fas fa-clock me-2"></i>Umumiy davomat xulosasi
                    </h2>
                    <p class="opacity-90 mb-4">
                        Quyida sizning barcha fanlar bo'yicha davomat ma'lumotlaringiz va qoldirgan dars soatlaringiz ko'rsatilgan.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="stat-box">
                                <div class="opacity-75 small mb-1">Jami fanlar</div>
                                <h3 class="mb-0 fw-bold">{{ count($attendanceData) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box">
                                <div class="opacity-75 small mb-1">Jami qoldirilgan</div>
                                <h3 class="mb-0 fw-bold">{{ $totalHoursMissed }} soat</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box">
                                <div class="opacity-75 small mb-1">O'rtacha davomat</div>
                                <h3 class="mb-0 fw-bold">
                                    @php
                                        $avgAttendance = count($attendanceData) > 0
                                            ? collect($attendanceData)->avg('attendance_percentage')
                                            : 0;
                                    @endphp
                                    {{ round($avgAttendance, 1) }}%
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div style="font-size: 120px; opacity: 0.3;">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fanlar bo'yicha davomat -->
        <div class="attendance-card">
            <div class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">
                        <i class="fas fa-list-ul text-primary me-2"></i>
                        Fanlar bo'yicha davomat
                    </h4>
                    <div>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Chop etish
                        </button>
                    </div>
                </div>
            </div>

            @if(count($attendanceData) == 0)
                <div class="p-5 text-center">
                    <i class="fas fa-inbox text-muted" style="font-size: 60px; opacity: 0.3;"></i>
                    <h5 class="text-muted mt-3">Hali davomat ma'lumotlari mavjud emas</h5>
                    <p class="text-muted">Darslar boshlanganidan keyin davomat ma'lumotlari bu yerda ko'rinadi.</p>
                </div>
            @else
                @foreach($attendanceData as $index => $subject)
                <div class="subject-row">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 48px; height: 48px; font-weight: bold; font-size: 18px;">
                                        {{ $index + 1 }}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $subject['subject_name'] }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-code"></i> {{ $subject['subject_code'] }} |
                                        <i class="fas fa-calendar-week"></i> {{ $subject['weekly_lessons'] }} dars/hafta
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-2">
                                <small class="text-muted d-flex justify-content-between mb-1">
                                    <span>Davomat: {{ $subject['attendance_percentage'] }}%</span>
                                    <span>
                                        <i class="fas fa-check-circle text-success"></i> {{ $subject['present'] }} |
                                        <i class="fas fa-clock text-warning"></i> {{ $subject['late'] }} |
                                        <i class="fas fa-times-circle text-danger"></i> {{ $subject['absent'] }}
                                    </span>
                                </small>
                                <div class="progress-bar-custom">
                                    <div class="progress-fill
                                        @if($subject['attendance_percentage'] >= 90) excellent
                                        @elseif($subject['attendance_percentage'] >= 75) good
                                        @elseif($subject['attendance_percentage'] >= 60) warning
                                        @else danger
                                        @endif"
                                        style="width: {{ $subject['attendance_percentage'] }}%">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 text-center">
                            <div class="text-muted small mb-1">Qoldirilgan</div>
                            <span class="hours-badge
                                @if($subject['hours_missed'] == 0) low
                                @elseif($subject['hours_missed'] <= 5) medium
                                @else high
                                @endif">
                                {{ $subject['hours_missed'] }} soat
                            </span>
                        </div>

                        <div class="col-md-2 text-end">
                            @if($subject['attendance_percentage'] >= 90)
                                <span class="badge bg-success" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fas fa-star"></i> A'lo
                                </span>
                            @elseif($subject['attendance_percentage'] >= 75)
                                <span class="badge bg-primary" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fas fa-thumbs-up"></i> Yaxshi
                                </span>
                            @elseif($subject['attendance_percentage'] >= 60)
                                <span class="badge bg-warning" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fas fa-exclamation-triangle"></i> Kam
                                </span>
                            @else
                                <span class="badge bg-danger" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fas fa-times"></i> Juda kam
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <!-- Qo'shimcha ma'lumot -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading fw-bold">Ma'lumot:</h6>
                            <p class="mb-1">• Har bir dars juftligi {{ $hoursPerLesson }} soatni tashkil etadi (1 soat 20 daqiqa)</p>
                            <p class="mb-1">• Kechikish yarmi hisobga olinadi ({{ $hoursPerLesson / 2 }} soat)</p>
                            <p class="mb-1">• 90% va undan yuqori davomat - A'lo ko'rsatkich</p>
                            <p class="mb-0">• 75% dan past davomat - Stipendiya va imtihonga kirishga ta'sir qilishi mumkin</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate progress bars
        const progressBars = document.querySelectorAll('.progress-fill');
        progressBars.forEach((bar, index) => {
            const width = bar.style.width;
            bar.style.width = '0%';

            setTimeout(() => {
                bar.style.width = width;
            }, 100 + (index * 100));
        });

        // Animate cards
        const cards = document.querySelectorAll('.subject-row');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateX(-20px)';
            card.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateX(0)';
            }, index * 100);
        });
    });
</script>
@endsection
