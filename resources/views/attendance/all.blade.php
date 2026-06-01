@extends('layouts.dashboard-new')

@section('title', 'Davomat boshqaruvi - HEMIS')

@section('page-title', 'Davomat boshqaruvi')

@section('styles')
<style>
    :root {
        --primary-dark-green: #0d4f3c;
        --secondary-green: #16a085;
        --light-green: #e8f5f0;
        --accent-green: #48c9b0;
        --border-green: #c3e6d8;
        --text-dark: #2c3e50;
        --hover-green: #0a3d2e;
        --very-light-green: #f0f9f6;
    }

    .stat-card {
        transition: all 0.3s ease;
        border: 1px solid var(--border-green) !important;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(13, 79, 60, 0.2) !important;
    }

    .attendance-badge {
        padding: 5px 10px;
        border-radius: 15px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .attendance-badge:hover {
        transform: scale(1.1);
    }

    .filter-card {
        background: linear-gradient(135deg, var(--very-light-green), white);
        border: 1px solid var(--border-green);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }

    .progress-ring {
        transform: rotate(-90deg);
    }

    .progress-ring-circle {
        transition: stroke-dashoffset 1s ease;
    }

    .date-picker {
        border: 1px solid var(--border-green);
        border-radius: 5px;
        padding: 8px 12px;
        background: var(--very-light-green);
    }

    .date-picker:focus {
        border-color: var(--secondary-green);
        box-shadow: 0 0 0 0.2rem rgba(22, 160, 133, 0.25);
        outline: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 p-4 rounded-lg" style="background: linear-gradient(135deg, var(--primary-dark-green) 0%, var(--secondary-green) 100%);">
        <div class="col-md-8">
            <h1 class="h2 text-white">Davomat boshqaruvi</h1>
            <p class="text-white opacity-90">
                Barcha guruhlar bo'yicha davomat ma'lumotlari va statistika
            </p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ route('attendance.face-recognition') }}" class="btn text-white"
                   style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);"
                   onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                   onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <i class="fas fa-camera"></i> Yuz orqali
                </a>
                <a href="{{ route('journal.index') }}" class="btn text-white ms-2"
                   style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);"
                   onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                   onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <i class="fas fa-book"></i> Jurnallar
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-card">
        <div class="row align-items-center">
            <div class="col-md-2">
                <label style="color: var(--text-dark); font-weight: 600;">Fakultet</label>
                <select class="form-select" style="border: 1px solid var(--border-green); background: white;">
                    <option>Barcha fakultetlar</option>
                    <option>Axborot texnologiyalari</option>
                    <option>Iqtisodiyot</option>
                    <option>Matematika</option>
                </select>
            </div>
            <div class="col-md-2">
                <label style="color: var(--text-dark); font-weight: 600;">Yo'nalish</label>
                <select class="form-select" style="border: 1px solid var(--border-green); background: white;">
                    <option>Barcha yo'nalishlar</option>
                    <option>Dasturiy injiniring</option>
                    <option>Kompyuter ilmlari</option>
                </select>
            </div>
            <div class="col-md-2">
                <label style="color: var(--text-dark); font-weight: 600;">Guruh</label>
                <select class="form-select" style="border: 1px solid var(--border-green); background: white;">
                    <option>Barcha guruhlar</option>
                    @foreach($groups ?? [] as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label style="color: var(--text-dark); font-weight: 600;">Boshlanish</label>
                <input type="date" class="form-control date-picker" value="{{ date('Y-m-01') }}">
            </div>
            <div class="col-md-2">
                <label style="color: var(--text-dark); font-weight: 600;">Tugash</label>
                <input type="date" class="form-control date-picker" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn text-white w-100"
                        style="background: var(--primary-dark-green); margin-top: 23px;"
                        onmouseover="this.style.background='var(--secondary-green)'"
                        onmouseout="this.style.background='var(--primary-dark-green)'">
                    <i class="fas fa-search"></i> Qidirish
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-list"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ number_format($statistics['total_records'] ?? 0) }}</h3>
                    <p class="mb-0 opacity-90">Jami yozuvlar</p>
                    <div class="progress mt-2" style="height: 5px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar" role="progressbar" style="width: 100%; background: white;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="background: linear-gradient(135deg, var(--secondary-green), var(--accent-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ number_format($statistics['present_count'] ?? 0) }}</h3>
                    <p class="mb-0 opacity-90">Kelganlar</p>
                    <div class="progress mt-2" style="height: 5px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ ($statistics['present_count'] ?? 0) / max(($statistics['total_records'] ?? 1), 1) * 100 }}%; background: white;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: #fef0f0;">
                            <i class="fas fa-user-times" style="color: #dc3545;"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: #dc3545;">{{ number_format($statistics['absent_count'] ?? 0) }}</h3>
                    <p class="mb-0" style="color: #7f8c8d;">Kelmaganlar</p>
                    <div class="progress mt-2" style="height: 5px; background: var(--light-green);">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ ($statistics['absent_count'] ?? 0) / max(($statistics['total_records'] ?? 1), 1) * 100 }}%; background: #dc3545;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: #fff3cd;">
                            <i class="fas fa-clock" style="color: #f39c12;"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: #f39c12;">{{ number_format($statistics['late_count'] ?? 0) }}</h3>
                    <p class="mb-0" style="color: #7f8c8d;">Kechikkanlar</p>
                    <div class="progress mt-2" style="height: 5px; background: var(--light-green);">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ ($statistics['late_count'] ?? 0) / max(($statistics['total_records'] ?? 1), 1) * 100 }}%; background: #f39c12;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                    <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-chart-line" style="color: var(--secondary-green);"></i>
                        Haftalik davomat dinamikasi
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" width="400" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                    <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-chart-pie" style="color: var(--secondary-green);"></i>
                        Bugungi holat
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div style="position: relative; display: inline-block;">
                        <svg width="200" height="200">
                            <circle cx="100" cy="100" r="80" fill="none" stroke="#e0e0e0" stroke-width="15"></circle>
                            <circle cx="100" cy="100" r="80" fill="none" stroke="#16a085" stroke-width="15"
                                    stroke-dasharray="502" stroke-dashoffset="126"
                                    class="progress-ring-circle progress-ring"></circle>
                        </svg>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <h2 style="color: var(--primary-dark-green); margin: 0;">75%</h2>
                            <p style="color: #7f8c8d; margin: 0;">Davomat</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <small style="color: #7f8c8d;">Kelgan</small>
                            <h5 style="color: var(--secondary-green);">450</h5>
                        </div>
                        <div class="col-6">
                            <small style="color: #7f8c8d;">Kelmagan</small>
                            <h5 style="color: #dc3545;">150</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Groups -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-header" style="background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green)); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy"></i>
                        Eng yaxshi davomat (guruhlar)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach(['DI-101' => 95, 'KI-201' => 92, 'AT-301' => 90, 'MI-102' => 88, 'IQ-202' => 85] as $group => $percentage)
                        <div class="list-group-item" style="border-left: 4px solid var(--secondary-green);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0" style="color: var(--text-dark);">{{ $group }}</h6>
                                    <small style="color: #7f8c8d;">{{ rand(25, 35) }} talaba</small>
                                </div>
                                <div class="text-end">
                                    <h5 class="mb-0" style="color: var(--primary-dark-green);">{{ $percentage }}%</h5>
                                    <div class="progress" style="height: 5px; width: 100px; background: var(--light-green);">
                                        <div class="progress-bar" role="progressbar"
                                             style="width: {{ $percentage }}%; background: var(--secondary-green);"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-header" style="background: linear-gradient(135deg, #dc3545, #f39c12); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        E'tibor talab etuvchi guruhlar
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach(['TI-303' => 65, 'BI-104' => 68, 'FI-205' => 70, 'XI-401' => 72, 'SI-302' => 73] as $group => $percentage)
                        <div class="list-group-item" style="border-left: 4px solid #dc3545;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0" style="color: var(--text-dark);">{{ $group }}</h6>
                                    <small style="color: #7f8c8d;">{{ rand(20, 30) }} talaba</small>
                                </div>
                                <div class="text-end">
                                    <h5 class="mb-0" style="color: #dc3545;">{{ $percentage }}%</h5>
                                    <div class="progress" style="height: 5px; width: 100px; background: #fef0f0;">
                                        <div class="progress-bar" role="progressbar"
                                             style="width: {{ $percentage }}%; background: #dc3545;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
        <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                    <i class="fas fa-list" style="color: var(--secondary-green);"></i>
                    So'nggi davomat yozuvlari
                </h5>
                <div>
                    <button class="btn btn-sm text-white"
                            style="background: var(--secondary-green);"
                            onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Export
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($attendanceRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: var(--very-light-green);">
                            <tr>
                                <th style="color: var(--text-dark); font-weight: 600;">Sana</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Fan</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Guruh</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Talaba</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Status</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Dars turi</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Vaqt</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Harakatlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceRecords ?? [] as $record)
                            <tr onmouseover="this.style.background='var(--very-light-green)'" onmouseout="this.style.background='white'">
                                <td>
                                    <span class="badge" style="background: var(--light-green); color: var(--primary-dark-green);">
                                        {{ \Carbon\Carbon::parse($record->lesson_date)->format('d.m.Y') }}
                                    </span>
                                </td>
                                <td style="color: var(--text-dark); font-weight: 600;">
                                    {{ $record->journalEntry->subject->name ?? 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--accent-green); color: white;">
                                        {{ $record->journalEntry->group->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td style="color: var(--text-dark);">
                                    {{ $record->student->last_name ?? '' }} {{ $record->student->first_name ?? '' }}
                                </td>
                                <td>
                                    @if($record->status == 'present')
                                        <span class="attendance-badge" style="background: var(--secondary-green); color: white;">
                                            <i class="fas fa-check-circle"></i> Keldi
                                        </span>
                                    @elseif($record->status == 'absent')
                                        <span class="attendance-badge" style="background: #dc3545; color: white;">
                                            <i class="fas fa-times-circle"></i> Kelmadi
                                        </span>
                                    @elseif($record->status == 'late')
                                        <span class="attendance-badge" style="background: #f39c12; color: white;">
                                            <i class="fas fa-clock"></i> Kechikdi
                                        </span>
                                    @else
                                        <span class="attendance-badge" style="background: #17a2b8; color: white;">
                                            <i class="fas fa-info-circle"></i> Sababli
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $typeLabels = [
                                            'lecture' => ['label' => "Ma'ruza", 'icon' => 'fa-chalkboard-teacher'],
                                            'practice' => ['label' => 'Amaliyot', 'icon' => 'fa-laptop-code'],
                                            'lab' => ['label' => 'Laboratoriya', 'icon' => 'fa-flask'],
                                            'seminar' => ['label' => 'Seminar', 'icon' => 'fa-users']
                                        ];
                                        $type = $typeLabels[$record->lesson_type] ?? ['label' => 'Noma\'lum', 'icon' => 'fa-question'];
                                    @endphp
                                    <span style="color: var(--text-dark);">
                                        <i class="fas {{ $type['icon'] }}" style="color: var(--secondary-green);"></i>
                                        {{ $type['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--very-light-green); color: var(--text-dark);">
                                        {{ $record->time_slot ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('journal.show', $record->journal_entry_id) }}" class="btn btn-sm"
                                       style="border: 1px solid var(--secondary-green); color: var(--secondary-green);"
                                       onmouseover="this.style.background='var(--light-green)'"
                                       onmouseout="this.style.background='transparent'">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($attendanceRecords) && $attendanceRecords->hasPages())
                <div class="px-4 py-3" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                    {{ $attendanceRecords->links() }}
                </div>
                @endif
            @else
                <div class="alert m-4" style="background: var(--light-green); border: 1px solid var(--border-green); color: var(--text-dark);">
                    <i class="fas fa-info-circle me-2" style="color: var(--secondary-green);"></i>
                    Hozircha davomat yozuvlari mavjud emas.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
// Weekly Attendance Chart
const ctx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba'],
        datasets: [{
            label: 'Kelganlar',
            data: [450, 460, 445, 470, 465, 430],
            borderColor: '#16a085',
            backgroundColor: 'rgba(22, 160, 133, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Kelmaganlar',
            data: [50, 40, 55, 30, 35, 70],
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Kechikkanlar',
            data: [20, 15, 25, 18, 22, 30],
            borderColor: '#f39c12',
            backgroundColor: 'rgba(243, 156, 18, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#e8f5f0'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Face Recognition Center
function openFaceRecognitionCenter() {
    // Create and show face recognition modal
    const modalHtml = `
        <div class="modal fade" id="faceRecognitionModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content" style="border: 2px solid var(--border-green);">
                    <div class="modal-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                        <h5 class="modal-title" style="color: var(--text-dark); font-weight: 600;">
                            <i class="fas fa-camera" style="color: var(--secondary-green);"></i>
                            Yuz orqali davomat markazi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div id="webcam-container" style="position: relative;">
                                    <video id="webcam" autoplay muted style="width: 100%; border-radius: 8px; border: 3px solid var(--border-green);"></video>
                                    <canvas id="face-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></canvas>
                                </div>
                                <div class="mt-3 d-flex justify-content-center gap-2">
                                    <button class="btn text-white" onclick="startCamera()"
                                            style="background: var(--primary-dark-green);">
                                        <i class="fas fa-play"></i> Kamerani yoqish
                                    </button>
                                    <button class="btn text-white" onclick="startDetection()"
                                            style="background: var(--secondary-green);">
                                        <i class="fas fa-search"></i> Aniqlashni boshlash
                                    </button>
                                    <button class="btn text-white" onclick="capturePhoto()"
                                            style="background: var(--accent-green);">
                                        <i class="fas fa-camera"></i> Suratga olish
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 style="color: var(--text-dark); font-weight: 600;">Aniqlangan talabalar</h6>
                                <div id="detected-list" class="mt-3">
                                    <div class="alert" style="background: var(--light-green); border: 1px solid var(--border-green); color: var(--text-dark);">
                                        <i class="fas fa-info-circle" style="color: var(--secondary-green);"></i>
                                        Kamerani yoqing va aniqlashni boshlang
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                        <button type="button" class="btn" style="background: #6c757d; color: white;" data-bs-dismiss="modal">
                            Yopish
                        </button>
                        <button type="button" class="btn text-white" onclick="saveAllAttendance()"
                                style="background: var(--primary-dark-green);">
                            <i class="fas fa-save"></i> Davomatni saqlash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if present
    const existingModal = document.getElementById('faceRecognitionModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('faceRecognitionModal'));
    modal.show();
}

let webcamStream = null;
let detectionInterval = null;

// Start Camera
async function startCamera() {
    try {
        const video = document.getElementById('webcam');
        webcamStream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: 640,
                height: 480,
                facingMode: 'user'
            }
        });
        video.srcObject = webcamStream;

        // Update status
        document.getElementById('detected-list').innerHTML = `
            <div class="alert" style="background: var(--light-green); border: 1px solid var(--border-green); color: var(--text-dark);">
                <i class="fas fa-check-circle" style="color: var(--secondary-green);"></i>
                Kamera tayyor!
            </div>
        `;
    } catch (error) {
        console.error('Kamera xatosi:', error);
        alert('Kamerani ochishda xatolik yuz berdi. Brauzer sozlamalarini tekshiring.');
    }
}

// Start Face Detection
function startDetection() {
    if (!webcamStream) {
        alert('Avval kamerani yoqing!');
        return;
    }

    // Simulate face detection
    detectionInterval = setInterval(() => {
        detectFaces();
    }, 1000);

    document.getElementById('detected-list').innerHTML = `
        <div class="alert" style="background: var(--light-green); border: 1px solid var(--border-green); color: var(--text-dark);">
            <div class="spinner-border spinner-border-sm me-2" style="color: var(--secondary-green);"></div>
            Yuzlar aniqlanmoqda...
        </div>
    `;
}

// Detect Faces (Simulation)
function detectFaces() {
    const canvas = document.getElementById('face-canvas');
    const ctx = canvas.getContext('2d');
    const video = document.getElementById('webcam');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Simulate detected faces
    const faces = [
        {x: 150, y: 100, width: 120, height: 150, name: 'Aliyev Vali'},
        {x: 350, y: 120, width: 120, height: 150, name: 'Karimova Nodira'}
    ];

    // Draw face boxes
    ctx.strokeStyle = '#48c9b0';
    ctx.lineWidth = 3;

    faces.forEach(face => {
        ctx.strokeRect(face.x, face.y, face.width, face.height);

        // Draw name label
        ctx.fillStyle = '#0d4f3c';
        ctx.fillRect(face.x, face.y + face.height + 5, face.width, 25);
        ctx.fillStyle = 'white';
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(face.name, face.x + face.width/2, face.y + face.height + 22);
    });

    // Update detected list
    updateDetectedList(faces);
}

// Update Detected Students List
function updateDetectedList(faces) {
    let html = '<div class="list-group">';
    faces.forEach((face, index) => {
        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center" style="border-left: 3px solid var(--secondary-green);">
                <div>
                    <strong style="color: var(--text-dark);">${face.name}</strong>
                    <br><small style="color: #7f8c8d;">Aniqlandi</small>
                </div>
                <button class="btn btn-sm text-white" style="background: var(--secondary-green);">
                    <i class="fas fa-check"></i>
                </button>
            </div>
        `;
    });
    html += '</div>';

    document.getElementById('detected-list').innerHTML = html;
}

// Capture Photo
function capturePhoto() {
    const video = document.getElementById('webcam');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);

    // Show captured image
    const img = canvas.toDataURL('image/jpeg');

    // Create a download link
    const link = document.createElement('a');
    link.download = 'davomat_' + Date.now() + '.jpg';
    link.href = img;
    link.click();

    alert('Surat saqlandi!');
}

// Save All Attendance
function saveAllAttendance() {
    alert('Davomat ma\'lumotlari saqlanmoqda...');

    // In real implementation, this would send data to server
    setTimeout(() => {
        alert('Davomat muvaffaqiyatli saqlandi!');

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('faceRecognitionModal'));
        modal.hide();

        // Stop camera
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }

        // Stop detection
        if (detectionInterval) {
            clearInterval(detectionInterval);
            detectionInterval = null;
        }
    }, 1000);
}

// Cleanup on modal close
document.addEventListener('hidden.bs.modal', function (event) {
    if (event.target.id === 'faceRecognitionModal') {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
        if (detectionInterval) {
            clearInterval(detectionInterval);
            detectionInterval = null;
        }
    }
});

// Export to Excel
function exportToExcel() {
    window.location.href = '/attendance/export-all';
}

// Filter functions
document.querySelectorAll('.form-select, .date-picker').forEach(element => {
    element.addEventListener('change', function() {
        // Filter implementation would go here
        console.log('Filter changed');
    });
});
</script>
@endpush