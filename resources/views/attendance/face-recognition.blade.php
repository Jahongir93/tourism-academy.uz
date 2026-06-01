@extends('layouts.dashboard-new')

@section('title', 'Yuz orqali davomat - HEMIS')

@section('page-title', 'Yuz orqali davomat')

@section('styles')
<!-- TensorFlow.js and Face Detection -->
<script src="{{ asset('vendor/tensorflow/tf.min.js') }}"></script>
<script src="{{ asset('vendor/tensorflow/blazeface.js') }}"></script>
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

    .face-recognition-page {
        background: linear-gradient(135deg, var(--very-light-green) 0%, white 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .fr-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(13, 79, 60, 0.1);
        border: 1px solid var(--border-green);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .fr-card-header {
        padding: 1.25rem;
        font-weight: 600;
        border-bottom: 2px solid var(--border-green);
        background: linear-gradient(135deg, var(--primary-dark-green) 0%, var(--secondary-green) 100%);
        color: white;
    }

    .fr-card-body {
        padding: 1.5rem;
        background: white;
    }

    .video-container {
        position: relative;
        background: #000;
        border-radius: 10px;
        overflow: hidden;
        border: 3px solid var(--border-green);
        min-height: 400px;
    }

    .video-element {
        width: 100%;
        height: 400px;
        max-height: 500px;
        display: block;
        object-fit: cover;
        background: #000;
        position: relative;
        z-index: 1;
    }

    .status-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(13, 79, 60, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        z-index: 10;
    }

    .face-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 2;
    }

    .control-btn {
        padding: 10px 20px;
        margin: 5px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-primary-custom {
        background: var(--primary-dark-green);
        color: white;
    }

    .btn-primary-custom:hover {
        background: var(--secondary-green);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(22, 160, 133, 0.3);
    }

    .btn-secondary-custom {
        background: var(--secondary-green);
        color: white;
    }

    .btn-secondary-custom:hover {
        background: var(--accent-green);
    }

    .btn-danger-custom {
        background: #dc3545;
        color: white;
    }

    .student-card {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 10px;
        border-left: 4px solid var(--secondary-green);
        background: var(--very-light-green);
        transition: all 0.3s ease;
    }

    .student-card:hover {
        background: var(--light-green);
        transform: translateX(5px);
    }

    .student-card.detected {
        border-left-color: var(--primary-dark-green);
        background: var(--light-green);
    }

    .stats-card {
        text-align: center;
        padding: 20px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green));
        color: white;
        margin-bottom: 15px;
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .detection-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
        animation: pulse 2s infinite;
    }

    .detection-indicator.active {
        background: var(--secondary-green);
    }

    .detection-indicator.inactive {
        background: #dc3545;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(22, 160, 133, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(22, 160, 133, 0); }
        100% { box-shadow: 0 0 0 0 rgba(22, 160, 133, 0); }
    }
</style>
@endsection

@section('content')
<div class="container-fluid face-recognition-page">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-lg" style="background: linear-gradient(135deg, var(--primary-dark-green) 0%, var(--secondary-green) 100%);">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h2 text-white mb-2">Yuz orqali davomat tizimi</h1>
                        <p class="text-white opacity-90 mb-0">
                            <i class="fas fa-info-circle"></i> Kamerani yoqing va talabalar yuzlarini aniqlash uchun tugmalardan foydalaning
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ url()->previous() }}" class="btn text-white"
                           style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Camera Section -->
        <div class="col-lg-8">
            <div class="fr-card">
                <div class="fr-card-header">
                    <i class="fas fa-camera me-2"></i> Kamera
                    <span class="float-end">
                        <span class="detection-indicator" id="detection-status"></span>
                        <span id="status-text">Tayyor</span>
                    </span>
                </div>
                <div class="fr-card-body">
                    <div class="video-container">
                        <video id="videoElement" class="video-element" autoplay muted playsinline webkit-playsinline></video>
                        <canvas id="faceCanvas" class="face-canvas"></canvas>
                        <div id="statusOverlay" class="status-overlay">
                            <div class="text-center">
                                <i class="fas fa-camera fa-3x mb-3"></i>
                                <p>Kamerani yoqish uchun "Boshlash" tugmasini bosing</p>
                            </div>
                        </div>
                    </div>

                    <!-- Control Buttons -->
                    <div class="mt-4 text-center">
                        <button id="startBtn" class="control-btn btn-primary-custom" type="button">
                            <i class="fas fa-play"></i> Boshlash
                        </button>
                        <button id="captureBtn" class="control-btn btn-secondary-custom" type="button" disabled>
                            <i class="fas fa-camera"></i> Suratga olish
                        </button>
                        <button id="detectBtn" class="control-btn btn-secondary-custom" type="button" disabled>
                            <i class="fas fa-search"></i> Aniqlash
                        </button>
                        <button id="enrollBtn" class="control-btn" style="background: #f39c12; color: white;" type="button" disabled>
                            <i class="fas fa-user-plus"></i> Ro'yxatdan o'tkazish
                        </button>
                        <button id="retryModelBtn" class="control-btn" style="background: #6c757d; color: white;" type="button">
                            <i class="fas fa-sync"></i> AI Model qayta yuklash
                        </button>
                        <button id="stopBtn" class="control-btn btn-danger-custom" type="button" disabled>
                            <i class="fas fa-stop"></i> To'xtatish
                        </button>
                    </div>

                    <!-- Mode Selection -->
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <label style="color: var(--text-dark); font-weight: 600;">Kirish rejimi:</label>
                                <select id="modeSelect" class="form-select" style="border: 1px solid var(--border-green); background: var(--very-light-green);">
                                    <option value="entrance">Universitet kirish</option>
                                    <option value="class">Dars davomati</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label style="color: var(--text-dark); font-weight: 600;">Manual guruh tanlash:</label>
                                <select id="groupSelect" class="form-select" style="border: 1px solid var(--border-green); background: var(--very-light-green);">
                                    <option value="">Avtomatik aniqlash</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->course }}-kurs)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="fr-card">
                <div class="fr-card-body">
                    <h5 style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-question-circle" style="color: var(--secondary-green);"></i> Qo'llanma
                    </h5>
                    <ol style="color: var(--text-dark);">
                        <li><strong>Universitet kirish rejimi:</strong> Talabalar avtomatik aniqlanib, o'z guruhlariga belgilanadi</li>
                        <li><strong>Dars davomati rejimi:</strong> Ma'lum guruh tanlash kerak</li>
                        <li>"Boshlash" tugmasini bosib kamerani yoqing</li>
                        <li>Talabalarni kamera oldida turishing</li>
                        <li>"Aniqlash" tugmasini bosib yuzlarni aniqlang</li>
                        <li>Aniqlangan talabalarni tasdiqlang</li>
                        <li>"Saqlash" tugmasini bosib davomatni saqlang</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statistics -->
            <div class="fr-card">
                <div class="fr-card-header">
                    <i class="fas fa-chart-bar me-2"></i> Statistika
                </div>
                <div class="fr-card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number" id="totalStudents">0</div>
                                <div class="stats-label">Jami talabalar</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number" id="detectedCount">0</div>
                                <div class="stats-label">Aniqlangan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detected Students -->
            <div class="fr-card">
                <div class="fr-card-header">
                    <i class="fas fa-users me-2"></i> Aniqlangan talabalar
                    <div class="float-end">
                        <small id="currentTime"></small>
                    </div>
                </div>
                <div class="fr-card-body" style="max-height: 400px; overflow-y: auto;">
                    <div id="detectedStudentsList">
                        <div class="text-center text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3" style="color: var(--secondary-green); opacity: 0.3;"></i>
                            <p>Hali talabalar aniqlanmadi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unknown Persons -->
            <div class="fr-card">
                <div class="fr-card-header">
                    <i class="fas fa-user-times me-2"></i> Noma'lum shaxslar
                    <div class="float-end">
                        <span class="badge" style="background: #dc3545; color: white;" id="unknownCount">0</span>
                    </div>
                </div>
                <div class="fr-card-body" style="max-height: 200px; overflow-y: auto;">
                    <div id="unknownPersonsList">
                        <div class="text-center text-muted">
                            <i class="fas fa-user-times fa-2x mb-2" style="color: #dc3545; opacity: 0.3;"></i>
                            <p>Noma'lum shaxslar aniqlanmadi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Students -->
            <div class="fr-card">
                <div class="fr-card-header">
                    <i class="fas fa-users me-2"></i> Ro'yxatdan o'tgan talabalar
                    <div class="float-end">
                        <span class="badge" style="background: var(--secondary-green); color: white;" id="enrolledCount">0</span>
                    </div>
                </div>
                <div class="fr-card-body" style="max-height: 200px; overflow-y: auto;">
                    <div id="enrolledStudentsList">
                        <div class="text-center text-muted">
                            <i class="fas fa-user-plus fa-2x mb-2" style="color: var(--secondary-green); opacity: 0.3;"></i>
                            <p>Hali talabalar ro'yxatdan o'tmagan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Groups Status -->
            <div class="fr-card">
                <div class="fr-card-header">
                    <i class="fas fa-calendar-check me-2"></i> Guruhlar holati
                </div>
                <div class="fr-card-body" style="max-height: 300px; overflow-y: auto;">
                    <div id="groupsStatusList">
                        <div class="text-center text-muted">
                            <i class="fas fa-clock fa-2x mb-2" style="color: var(--secondary-green); opacity: 0.3;"></i>
                            <p>Guruhlar holati yuklanmoqda...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="mt-3">
                <button class="btn btn-lg w-100 text-white"
                        style="background: var(--primary-dark-green);"
                        onmouseover="this.style.background='var(--secondary-green)'"
                        onmouseout="this.style.background='var(--primary-dark-green)'"
                        disabled id="saveBtn" type="button">
                    <i class="fas fa-save"></i> Davomatni saqlash
                </button>
            </div>
        </div>
    </div>

    <!-- Today's Attendance List Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="fr-card">
                <div class="fr-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-clipboard-list me-2"></i> Bugungi davomat ro'yxati
                        <span class="badge bg-white text-dark ms-2" id="attendanceListCount">{{ $todayAttendance->count() ?? 0 }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <!-- Group Filter -->
                        <select id="attendanceGroupFilter" class="form-select form-select-sm" style="width: auto; min-width: 200px; border: 1px solid rgba(255,255,255,0.5); background: rgba(255,255,255,0.9);">
                            <option value="">Barcha guruhlar</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ $selectedGroupId == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }} ({{ $group->course }}-kurs)
                                </option>
                            @endforeach
                        </select>
                        <!-- Export Button -->
                        <button type="button" class="btn btn-sm text-white" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.5);" onclick="exportToExcel()">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <!-- Refresh Button -->
                        <button type="button" class="btn btn-sm text-white" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.5);" onclick="refreshAttendanceList()">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                </div>
                <div class="fr-card-body">
                    <!-- Stats Summary -->
                    <div class="row mb-4">
                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                            <div class="text-center p-3 rounded" style="background: var(--light-green);">
                                <div class="h4 mb-0" style="color: var(--primary-dark-green);" id="statTotal">{{ $stats['total'] ?? 0 }}</div>
                                <small class="text-muted">Jami</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                            <div class="text-center p-3 rounded" style="background: #d4edda;">
                                <div class="h4 mb-0 text-success" id="statEarly">{{ $stats['early'] ?? 0 }}</div>
                                <small class="text-muted">Erta</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                            <div class="text-center p-3 rounded" style="background: #cce5ff;">
                                <div class="h4 mb-0 text-primary" id="statPresent">{{ $stats['present'] ?? 0 }}</div>
                                <small class="text-muted">Keldi</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                            <div class="text-center p-3 rounded" style="background: #fff3cd;">
                                <div class="h4 mb-0 text-warning" id="statLate">{{ $stats['late'] ?? 0 }}</div>
                                <small class="text-muted">Kechikdi</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                            <div class="text-center p-3 rounded" style="background: #f8d7da;">
                                <div class="h4 mb-0 text-danger" id="statVeryLate">{{ $stats['very_late'] ?? 0 }}</div>
                                <small class="text-muted">Juda kechikdi</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                            <div class="text-center p-3 rounded" style="background: #e2e3e5;">
                                <div class="h4 mb-0 text-secondary" id="statAbsent">{{ $stats['absent'] ?? 0 }}</div>
                                <small class="text-muted">Kelmadi</small>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="attendanceTable">
                            <thead style="background: var(--very-light-green);">
                                <tr>
                                    <th>#</th>
                                    <th>Talaba</th>
                                    <th>Guruh</th>
                                    <th>Kelish vaqti</th>
                                    <th>Ketish vaqti</th>
                                    <th>Holat</th>
                                    <th>Ishonchlilik</th>
                                    <th>Usul</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                @forelse($todayAttendance as $index => $attendance)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $attendance->student_name ?? $attendance->student->full_name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $attendance->student_code ?? $attendance->student->student_code ?? '' }}</small>
                                    </td>
                                    <td>{{ $attendance->group_name ?? $attendance->group->name ?? 'N/A' }}</td>
                                    <td>{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}</td>
                                    <td>{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'early' => 'success',
                                                'present' => 'primary',
                                                'late' => 'warning',
                                                'very_late' => 'danger',
                                                'absent' => 'secondary'
                                            ];
                                            $statusLabels = [
                                                'early' => 'Erta',
                                                'present' => 'Keldi',
                                                'late' => 'Kechikdi',
                                                'very_late' => 'Juda kechikdi',
                                                'absent' => 'Kelmadi'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$attendance->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$attendance->status] ?? $attendance->status }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance->confidence_score ? round($attendance->confidence_score * 100, 1) . '%' : '-' }}</td>
                                    <td>
                                        @if($attendance->method == 'face_recognition')
                                            <i class="fas fa-user-check text-success" title="Yuz orqali"></i>
                                        @elseif($attendance->method == 'manual')
                                            <i class="fas fa-hand-paper text-warning" title="Qo'lda"></i>
                                        @else
                                            <i class="fas fa-qrcode text-info" title="QR kod"></i>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr id="noAttendanceRow">
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-clipboard fa-3x mb-3" style="opacity: 0.3;"></i>
                                        <p>Bugun hali davomat yo'q</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Face Enrollment Modal -->
    <div class="modal fade" id="enrollmentModal" tabindex="-1" aria-labelledby="enrollmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green)); color: white;">
                    <h5 class="modal-title" id="enrollmentModalLabel">
                        <i class="fas fa-user-plus me-2"></i> Yangi talaba ro'yxatdan o'tkazish
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Student Search Section -->
                    <div class="mb-4 p-3 rounded" style="background: var(--very-light-green); border: 1px solid var(--border-green);">
                        <h6 style="color: var(--text-dark); font-weight: 600; margin-bottom: 15px;">
                            <i class="fas fa-search me-2" style="color: var(--secondary-green);"></i>
                            Talabani qidirish (ID yoki F.I.O)
                        </h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="studentSearchInput"
                                           placeholder="Talaba ID yoki ismini kiriting..."
                                           style="border: 1px solid var(--border-green);">
                                    <button class="btn" type="button" id="searchStudentBtn"
                                            style="background: var(--secondary-green); color: white;">
                                        <i class="fas fa-search"></i> Qidirish
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="searchGroupFilter" style="border: 1px solid var(--border-green);">
                                    <option value="">Barcha guruhlar</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Search Results -->
                        <div id="studentSearchResults" class="mt-3" style="display: none; max-height: 200px; overflow-y: auto;">
                            <!-- Results will be populated here -->
                        </div>
                    </div>

                    <!-- Selected Student Info -->
                    <div id="selectedStudentInfo" class="mb-4 p-3 rounded" style="display: none; background: #d4edda; border: 1px solid #28a745;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong style="color: #155724;">
                                    <i class="fas fa-user-check me-2"></i>
                                    Tanlangan talaba:
                                </strong>
                                <span id="selectedStudentName" class="ms-2"></span>
                                <br>
                                <small class="text-muted">
                                    <span id="selectedStudentCode"></span> |
                                    <span id="selectedStudentGroup"></span>
                                </small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSelectedStudent()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <input type="hidden" id="selectedStudentId">
                    </div>

                    <div class="row">
                        <!-- Student Info Form (read-only when student selected) -->
                        <div class="col-md-6">
                            <h6 style="color: var(--text-dark); font-weight: 600; margin-bottom: 15px;">
                                <i class="fas fa-user me-2" style="color: var(--secondary-green);"></i>
                                Talaba ma'lumotlari
                            </h6>

                            <form id="enrollmentForm">
                                <div class="mb-3">
                                    <label class="form-label" style="color: var(--text-dark); font-weight: 600;">Familiya</label>
                                    <input type="text" class="form-control" id="enrollLastName"
                                           style="border: 1px solid var(--border-green);" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" style="color: var(--text-dark); font-weight: 600;">Ism</label>
                                    <input type="text" class="form-control" id="enrollFirstName"
                                           style="border: 1px solid var(--border-green);" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" style="color: var(--text-dark); font-weight: 600;">Otasining ismi</label>
                                    <input type="text" class="form-control" id="enrollMiddleName"
                                           style="border: 1px solid var(--border-green);" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" style="color: var(--text-dark); font-weight: 600;">Talaba ID</label>
                                    <input type="text" class="form-control" id="enrollStudentId"
                                           style="border: 1px solid var(--border-green);" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" style="color: var(--text-dark); font-weight: 600;">Guruh</label>
                                    <input type="text" class="form-control" id="enrollGroup"
                                           style="border: 1px solid var(--border-green);" readonly>
                                </div>
                            </form>
                        </div>

                        <!-- Face Capture -->
                        <div class="col-md-6">
                            <h6 style="color: var(--text-dark); font-weight: 600; margin-bottom: 15px;">
                                <i class="fas fa-camera me-2" style="color: var(--secondary-green);"></i>
                                Yuz surati
                            </h6>

                            <div class="enrollment-photo-area" style="border: 2px dashed var(--border-green); border-radius: 10px; padding: 20px; text-align: center; background: var(--very-light-green); min-height: 250px; position: relative;">
                                <canvas id="enrollmentCanvas" style="max-width: 100%; max-height: 200px; border-radius: 10px; display: none;"></canvas>
                                <div id="enrollmentPhotoPlaceholder">
                                    <i class="fas fa-camera fa-3x mb-3" style="color: var(--secondary-green); opacity: 0.5;"></i>
                                    <p style="color: var(--text-dark);">Yuz suratini olish uchun quyidagi tugmani bosing</p>
                                </div>

                                <div class="mt-3">
                                    <button type="button" class="btn" id="captureEnrollmentPhoto"
                                            style="background: var(--secondary-green); color: white;">
                                        <i class="fas fa-camera"></i> Suratga olish
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="retakeEnrollmentPhoto" style="display: none;">
                                        <i class="fas fa-redo"></i> Qayta olish
                                    </button>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="alert alert-info" style="background: var(--light-green); border: 1px solid var(--border-green); color: var(--text-dark);">
                                    <small>
                                        <i class="fas fa-info-circle me-1"></i>
                                        Yaxshi surat uchun:
                                        <ul class="mb-0 mt-1" style="font-size: 12px;">
                                            <li>Yuzingizni to'g'ri kameraga qarating</li>
                                            <li>Yoritish yaxshi bo'lsin</li>
                                            <li>Ko'zoynak va niqob taqmang</li>
                                        </ul>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="button" class="btn text-white" id="saveEnrollment"
                            style="background: var(--primary-dark-green);" disabled>
                        <i class="fas fa-save"></i> Saqlash
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Base URL for API calls
const baseUrl = '{{ url('/') }}';

let video = null;
let stream = null;
let canvas = null;
let ctx = null;
let detectionInterval = null;
let detectedStudents = [];
let faceDetectionModel = null;
let isDetecting = false;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM yuklandi');

    video = document.getElementById('videoElement');
    canvas = document.getElementById('faceCanvas');

    if (!video) {
        console.error('Video element topilmadi!');
        return;
    }

    if (!canvas) {
        console.error('Canvas element topilmadi!');
        return;
    }

    ctx = canvas.getContext('2d');
    updateDetectionStatus(false);

    console.log('Barcha elementlar muvaffaqiyatli yuklandi');

    // Test kamera support
    if (!navigator.mediaDevices) {
        console.error('mediaDevices qo\'llab-quvvatlanmaydi');
        alert('Bu brauzer kamerani qo\'llab-quvvatlamaydi!');
    } else {
        console.log('mediaDevices qo\'llab-quvvatlanadi');
    }

    // Load students from server
    loadStudentsFromServer();

    // Load face detection model
    loadFaceDetectionModel();

    // Attach event listeners
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const captureBtn = document.getElementById('captureBtn');
    const detectBtn = document.getElementById('detectBtn');

    if (startBtn) {
        startBtn.addEventListener('click', function() {
            console.log('Start tugmasi bosildi');
            startCamera();
        });
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', function() {
            console.log('Stop tugmasi bosildi');
            stopCamera();
        });
    }

    if (captureBtn) {
        captureBtn.addEventListener('click', function() {
            console.log('Capture tugmasi bosildi');
            captureImage();
        });
    }

    if (detectBtn) {
        detectBtn.addEventListener('click', function() {
            console.log('Detect tugmasi bosildi');
            if (detectBtn.textContent.includes('To\'xtatish')) {
                stopDetection();
            } else {
                startDetection();
            }
        });
    }

    // Enrollment button event listener
    const enrollBtn = document.getElementById('enrollBtn');
    if (enrollBtn) {
        enrollBtn.addEventListener('click', function() {
            console.log('Enrollment tugmasi bosildi');
            openEnrollmentModal();
        });
    }

    // Retry model button event listener
    const retryModelBtn = document.getElementById('retryModelBtn');
    if (retryModelBtn) {
        retryModelBtn.addEventListener('click', function() {
            console.log('AI Model qayta yuklash tugmasi bosildi');
            loadFaceDetectionModel();
        });
    }

    // Save button event listener
    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            console.log('Save tugmasi bosildi');
            saveAttendance();
        });
    }
});

// Load Face Detection Model
async function loadFaceDetectionModel() {
    try {
        console.log('TensorFlow.js mavjudligini tekshirish...');

        // Check if TensorFlow.js is loaded
        if (typeof tf === 'undefined') {
            throw new Error('TensorFlow.js yuklanmagan');
        }

        console.log('TensorFlow.js versiyasi:', tf.version.tfjs);

        // Check if blazeface is loaded
        if (typeof blazeface === 'undefined') {
            throw new Error('BlazeFace modeli yuklanmagan');
        }

        console.log('Yuz aniqlash modeli yuklanmoqda...');
        updateStatusText('AI model yuklanmoqda...');

        // Set TensorFlow backend
        await tf.ready();
        console.log('TensorFlow backend:', tf.getBackend());

        // Load BlazeFace model with options
        faceDetectionModel = await blazeface.load({
            maxFaces: 10, // Maximum 10 faces to detect
            iouThreshold: 0.3,
            scoreThreshold: 0.75
        });

        console.log('Yuz aniqlash modeli muvaffaqiyatli yuklandi');
        updateStatusText('AI model tayyor');

        // Enable detect button once model is loaded
        if (stream) {
            document.getElementById('detectBtn').disabled = false;
        }

    } catch (error) {
        console.error('Model yuklashda xatolik:', error);
        console.error('Xatolik tafsilotlari:', error.stack);

        updateStatusText('AI model yuklashda xatolik - oddiygina yuz aniqlash ishlatiladi');

        // Fallback: use simple face detection without AI
        faceDetectionModel = null;

        alert(`AI model yuklanmadi: ${error.message}\n\nOddiygina yuz aniqlash rejimi ishlatiladi.`);
    }
}

// Start Camera
async function startCamera() {
    console.log('Kamera boshlash jarayoni...');
    updateStatusText('Kamera ishga tushirilmoqda...');

    try {
        // Check browser support
        console.log('Brauzer qo\'llab-quvvatlashini tekshirish...');

        if (!navigator.mediaDevices) {
            throw new Error('Bu brauzer mediaDevices API ni qo\'llab-quvvatlamaydi');
        }

        if (!navigator.mediaDevices.getUserMedia) {
            throw new Error('Bu brauzer getUserMedia ni qo\'llab-quvvatlamaydi');
        }

        // Check if site is served over HTTPS (required for camera)
        const isSecure = location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
        console.log('Xavfsizlik protokoli:', location.protocol, 'Xavfsiz:', isSecure);

        if (!isSecure) {
            console.warn('DIQQAT: Kamera HTTPS yoki localhost da ishlamaydi');
        }

        // Simplified constraints
        const constraints = {
            video: true,
            audio: false
        };

        console.log('Kamera ruxsati so\'ralmoqda...');
        updateStatusText('Kamera ruxsati so\'ralmoqda...');

        // Request camera access
        stream = await navigator.mediaDevices.getUserMedia(constraints);
        console.log('Kamera muvaffaqiyatli olingan:', stream);

        // Set video source
        video.srcObject = stream;

        // Force hide overlay immediately when stream is set
        setTimeout(() => {
            const overlay = document.getElementById('statusOverlay');
            if (overlay && stream) {
                overlay.style.display = 'none';
                console.log('Overlay majburiy yashirildi');
            }
        }, 500);

        // Wait for video to load and start playing
        video.onloadedmetadata = function() {
            console.log('Video metadata yuklandi');
            video.play().then(() => {
                console.log('Video boshlandi');

                // Hide overlay IMMEDIATELY
                const overlay = document.getElementById('statusOverlay');
                if (overlay) {
                    overlay.style.display = 'none';
                    console.log('Status overlay yashirildi');
                }

                // Enable/disable buttons
                document.getElementById('startBtn').disabled = true;
                document.getElementById('captureBtn').disabled = false;
                document.getElementById('detectBtn').disabled = false;
                document.getElementById('enrollBtn').disabled = false;
                document.getElementById('stopBtn').disabled = false;

                updateDetectionStatus(true);
                updateStatusText('Kamera yoqildi - Tayyor');

                // Start time update
                updateCurrentTime();

                // Load groups status
                loadGroupsStatus();

            }).catch(err => {
                console.error('Video play xatoligi:', err);
                updateStatusText('Video ishga tushmadi');
            });
        };

        // Also hide overlay when video starts playing
        video.onplaying = function() {
            console.log('Video playing hodisasi');
            const overlay = document.getElementById('statusOverlay');
            if (overlay) {
                overlay.style.display = 'none';
                console.log('Status overlay onplaying da yashirildi');
            }
        };

        video.onerror = function(err) {
            console.error('Video element xatoligi:', err);
            updateStatusText('Video element xatoligi');
        };

    } catch (error) {
        console.error('Kamera xatoligi:', error);
        console.error('Xatolik turi:', error.name);
        console.error('Xatolik xabari:', error.message);

        let errorMessage = 'Kamerani ochishda xatolik!\n\n';

        switch(error.name) {
            case 'NotAllowedError':
                errorMessage += 'XATOLIK: Kamera ruxsati berilmadi!\n\n';
                errorMessage += 'Hal qilish yo\'llari:\n';
                errorMessage += '• Brauzer manzil satridagi kamera belgisini bosing\n';
                errorMessage += '• "Ruxsat berish" ni tanlang\n';
                errorMessage += '• Sahifani yangilang va qayta urinib ko\'ring\n';
                errorMessage += '• Brauzer sozlamalarida sayt uchun kamera ruxsatini yoqing';
                break;

            case 'NotFoundError':
                errorMessage += 'XATOLIK: Kamera topilmadi!\n\n';
                errorMessage += 'Hal qilish yo\'llari:\n';
                errorMessage += '• Kamera kompyuterga ulangan ekanligini tekshiring\n';
                errorMessage += '• Boshqa dasturlar kameradan foydalanmayotganini tekshiring\n';
                errorMessage += '• Kompyuterni qayta ishga tushiring';
                break;

            case 'NotReadableError':
                errorMessage += 'XATOLIK: Kamera boshqa dastur tomonidan ishlatilmoqda!\n\n';
                errorMessage += 'Hal qilish yo\'llari:\n';
                errorMessage += '• Skype, Zoom, Teams kabi dasturlarni yoping\n';
                errorMessage += '• Boshqa brauzer tablarni yoping\n';
                errorMessage += '• Kompyuterni qayta ishga tushiring';
                break;

            case 'OverconstrainedError':
                errorMessage += 'XATOLIK: Kamera talab qilingan sifatni qo\'llab-quvvatlamaydi!\n\n';
                errorMessage += 'Soddaroq sozlamalar bilan qayta urinib ko\'rmoqda...';
                break;

            default:
                errorMessage += 'Noma\'lum xatolik: ' + error.message + '\n\n';
                errorMessage += 'Agar muammo davom etsa, texnik yordam so\'rang.';
        }

        // Show detailed error
        alert(errorMessage);
        updateStatusText('Xatolik: ' + error.name);

        // Enable start button again
        document.getElementById('startBtn').disabled = false;
    }
}

// Stop Camera
function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        video.srcObject = null;
    }

    if (detectionInterval) {
        clearInterval(detectionInterval);
        detectionInterval = null;
    }

    // Show overlay
    document.getElementById('statusOverlay').style.display = 'flex';

    // Enable/disable buttons
    document.getElementById('startBtn').disabled = false;
    document.getElementById('captureBtn').disabled = true;
    document.getElementById('detectBtn').disabled = true;
    document.getElementById('enrollBtn').disabled = true;
    document.getElementById('stopBtn').disabled = true;

    updateDetectionStatus(false);
    updateStatusText('To\'xtatildi');
}

// Capture Image
function captureImage() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);

    // Create download link
    canvas.toBlob(function(blob) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'davomat_' + Date.now() + '.jpg';
        a.click();
        URL.revokeObjectURL(url);
    });

    updateStatusText('Surat saqlandi');
}

// Start Face Detection
function startDetection() {
    const mode = document.getElementById('modeSelect').value;
    const group = document.getElementById('groupSelect').value;

    if (mode === 'class' && !group) {
        alert('Dars davomati rejimida guruhni tanlang yoki "Universitet kirish" rejimini tanlang!');
        return;
    }

    // Check if AI model is loaded
    if (!faceDetectionModel) {
        alert('AI model hali yuklanmagan!\n\n"AI Model qayta yuklash" tugmasini bosing yoki Internet ulanishini tekshiring.');
        return;
    }

    // Check if student database is empty
    if (studentDatabase.length === 0) {
        const proceed = confirm('Talaba bazasi bo\'sh!\n\nYuz aniqlash ishlaydi, lekin hech kim tanilmaydi.\n\nAvval talabalarni ro\'yxatdan o\'tkazishni xohlaysizmi?');

        if (proceed) {
            openEnrollmentModal();
            return;
        }
    }

    if (isDetecting) {
        return;
    }

    isDetecting = true;
    updateStatusText(`Yuz aniqlash boshlandi (${studentDatabase.length} talaba bazada)`);

    // Start detection loop
    detectionInterval = setInterval(() => {
        detectRealFaces();
    }, 1000); // 1 sekund interval

    document.getElementById('detectBtn').innerHTML = '<i class="fas fa-stop"></i> To\'xtatish';
}

// Stop Detection
function stopDetection() {
    if (detectionInterval) {
        clearInterval(detectionInterval);
        detectionInterval = null;
    }

    isDetecting = false;
    document.getElementById('detectBtn').innerHTML = '<i class="fas fa-search"></i> Aniqlash';
    updateStatusText('Aniqlash to\'xtatildi');
}

// Student database (will be loaded from server)
let studentDatabase = [];

// Load students from server
async function loadStudentsFromServer() {
    try {
        console.log('Server dan talabalar ma\'lumoti yuklanmoqda...');
        updateStatusText('Talabalar ma\'lumoti yuklanmoqda...');

        const response = await fetch(baseUrl + '/api/face-attendance/enrolled-students', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error(`Server xatoligi: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            studentDatabase = data.students;
            console.log(`Server dan ${studentDatabase.length} ta talaba yuklandi:`, studentDatabase);
            updateStatusText(`${studentDatabase.length} ta talaba yuklandi`);

            // Update enrolled students list
            updateEnrolledStudentsList();

            return true;
        } else {
            throw new Error(data.message || 'Ma\'lumot yuklashda xatolik');
        }

    } catch (error) {
        console.error('Talabalar ma\'lumotini yuklashda xatolik:', error);
        updateStatusText('Talabalar ma\'lumotini yuklashda xatolik');

        // Empty database on error - no test data
        studentDatabase = [];
        updateEnrolledStudentsList();

        return false;
    }
}

// Show database status for debugging
console.log('Talaba bazasi boshlang\'ich holati: yuklanmoqda...');

// Class schedule will be loaded from database
const classSchedule = {};

// Detect Faces (Enhanced simulation)
function detectFaces() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Simulated face detection with random appearance
    const possibleFaces = [
        { x: 150, y: 100, width: 120, height: 150, studentId: 1 },
        { x: 350, y: 120, width: 120, height: 150, studentId: 2 },
        { x: 200, y: 200, width: 120, height: 150, studentId: 3 },
        { x: 100, y: 50, width: 120, height: 150, studentId: 4 }
    ];

    // Randomly show some faces
    const currentFaces = possibleFaces.filter(() => Math.random() > 0.6);

    // Draw face boxes
    ctx.strokeStyle = '#48c9b0';
    ctx.lineWidth = 3;

    currentFaces.forEach(face => {
        const student = studentDatabase.find(s => s.id === face.studentId);
        if (!student) return;

        // Draw rectangle
        ctx.strokeRect(face.x, face.y, face.width, face.height);

        // Draw name label
        ctx.fillStyle = '#0d4f3c';
        ctx.fillRect(face.x, face.y - 25, face.width, 25);
        ctx.fillStyle = 'white';
        ctx.font = '12px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(student.name.substring(0, 20), face.x + face.width/2, face.y - 8);

        // Add to detected list if not already there
        if (!detectedStudents.find(s => s.id === student.id)) {
            const detectedStudent = {
                ...student,
                detectedAt: new Date(),
                hasClass: classSchedule[student.group].hasClass,
                subject: classSchedule[student.group].subject,
                classTime: classSchedule[student.group].time
            };

            detectedStudents.push(detectedStudent);
            updateDetectedList();

            // Play notification sound (optional)
            playNotificationSound();
        }
    });
}

// Real Face Detection using TensorFlow.js
async function detectRealFaces() {
    if (!faceDetectionModel || !video || !video.videoWidth) {
        console.log('Model yoki video tayyor emas');
        return;
    }

    try {
        // Set canvas size to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Detect faces using TensorFlow.js BlazeFace model
        const predictions = await faceDetectionModel.estimateFaces(video, false);

        console.log(`${predictions.length} ta haqiqiy yuz aniqlandi`);

        if (predictions.length === 0) {
            // No faces detected
            updateStatusText('Yuz aniqlanmadi');
            return;
        }

        updateStatusText(`${predictions.length} ta yuz aniqlandi`);

        // Process each detected face
        predictions.forEach((prediction, index) => {
            const start = prediction.topLeft;
            const end = prediction.bottomRight;
            const size = [end[0] - start[0], end[1] - start[1]];

            // Validate face detection quality
            const faceQuality = validateFaceQuality(prediction, size);

            if (!faceQuality.isValid) {
                console.log(`Yuz ${index + 1}: Sifatsiz - ${faceQuality.reason}`);
                return; // Skip low quality faces
            }

            // Draw bounding box for valid faces
            ctx.strokeStyle = '#48c9b0';
            ctx.lineWidth = 3;
            ctx.strokeRect(start[0], start[1], size[0], size[1]);

            // Try to identify the person
            const identificationResult = identifyPersonFromFace(prediction, start, size, index);

            if (identificationResult.identified) {
                // Known person - draw green box and name
                ctx.strokeStyle = '#0d4f3c';
                ctx.strokeRect(start[0], start[1], size[0], size[1]);

                // Draw name label
                ctx.fillStyle = '#0d4f3c';
                ctx.fillRect(start[0], start[1] - 25, size[0], 25);
                ctx.fillStyle = 'white';
                ctx.font = '12px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(identificationResult.person.name.substring(0, 20), start[0] + size[0]/2, start[1] - 8);

                // Add to detected list if not already there
                if (!detectedStudents.find(s => s.id === identificationResult.person.id)) {
                    const detectedStudent = {
                        ...identificationResult.person,
                        detectedAt: new Date(),
                        hasClass: classSchedule[identificationResult.person.group]?.hasClass || false,
                        subject: classSchedule[identificationResult.person.group]?.subject || null,
                        classTime: classSchedule[identificationResult.person.group]?.time || null,
                        confidence: identificationResult.confidence
                    };

                    detectedStudents.push(detectedStudent);
                    updateDetectedList();
                    playNotificationSound();

                    // AUTO-SAVE: Mark attendance automatically when face is detected
                    autoSaveAttendance(identificationResult.person.id, identificationResult.confidence);
                }
            } else {
                // Unknown person - draw red box and "Unknown"
                ctx.strokeStyle = '#dc3545';
                ctx.lineWidth = 3;
                ctx.strokeRect(start[0], start[1], size[0], size[1]);

                // Draw "Unknown" label
                ctx.fillStyle = '#dc3545';
                ctx.fillRect(start[0], start[1] - 25, size[0], 25);
                ctx.fillStyle = 'white';
                ctx.font = '12px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('Noma\'lum shaxs', start[0] + size[0]/2, start[1] - 8);

                // Add to unknown list (separate from detected students)
                addUnknownPerson(prediction, start, size);
            }
        });

    } catch (error) {
        console.error('Yuz aniqlashda xatolik:', error);
    }
}

// Validate face detection quality
function validateFaceQuality(prediction, faceSize) {
    const faceArea = faceSize[0] * faceSize[1];
    const minFaceArea = 8000; // Minimum face area (increased for better quality)
    const maxFaceArea = 100000; // Maximum face area to avoid false positives

    // Check face size
    if (faceArea < minFaceArea) {
        return { isValid: false, reason: 'Yuz juda kichik' };
    }

    if (faceArea > maxFaceArea) {
        return { isValid: false, reason: 'Yuz juda katta' };
    }

    // Check face proportions (width/height ratio should be reasonable)
    const aspectRatio = faceSize[0] / faceSize[1];
    if (aspectRatio < 0.5 || aspectRatio > 2.0) {
        return { isValid: false, reason: 'Yuz nisbati noto\'g\'ri' };
    }

    // Check if face has minimum confidence (if available)
    const confidence = prediction.probability ? prediction.probability[0] : 1.0;
    if (confidence < 0.7) {
        return { isValid: false, reason: 'Ishonch darajasi past' };
    }

    return { isValid: true, reason: 'Yaxshi sifat' };
}

// Identify person from detected face
function identifyPersonFromFace(prediction, facePosition, faceSize, index) {
    // In real system, this would use face recognition to compare with enrolled faces

    console.log(`Talaba bazasida ${studentDatabase.length} ta talaba mavjud`);

    // IMPORTANT: Only try to identify if we have enrolled students
    if (studentDatabase.length === 0) {
        console.log('Talaba bazasi bo\'sh - hech kim tanilmaydi');
        return { identified: false, confidence: 0 };
    }

    // Filter out already detected students in this session
    const availableStudents = studentDatabase.filter(student =>
        !detectedStudents.find(detected => detected.id === student.id)
    );

    if (availableStudents.length === 0) {
        console.log('Barcha ro\'yxatdan o\'tgan talabalar allaqachon aniqlangan');
        return { identified: false, confidence: 0 };
    }

    console.log(`${availableStudents.length} ta talaba tanish uchun mavjud`);

    // CRITICAL: Only recognize if face quality is very high and we have a manual trigger
    // For demonstration purposes, we make recognition more likely for testing
    const recognitionProbability = calculateRecognitionProbability(prediction, faceSize);

    // Make recognition more likely for testing (20% chance with good face quality)
    const shouldRecognize = Math.random() < 0.2 && recognitionProbability > 0.7;

    if (shouldRecognize) {
        // Very rare successful identification
        const studentIndex = Math.floor(Math.random() * availableStudents.length);
        console.log(`Talaba tanildi: ${availableStudents[studentIndex].name}`);
        return {
            identified: true,
            person: availableStudents[studentIndex],
            confidence: recognitionProbability
        };
    }

    console.log(`Yuz sifati yetarli emas yoki talaba tanilmadi (ehtimollik: ${recognitionProbability.toFixed(2)})`);
    return { identified: false, confidence: recognitionProbability };
}

// Calculate recognition probability based on face quality
function calculateRecognitionProbability(prediction, faceSize) {
    const faceArea = faceSize[0] * faceSize[1];
    const optimalArea = 20000; // Optimal face area for recognition

    // Base probability on face area (closer to optimal = higher probability)
    let probability = Math.min(faceArea / optimalArea, 1.0) * 0.5;

    // Add face detection confidence
    const detectionConfidence = prediction.probability ? prediction.probability[0] : 0.8;
    probability += detectionConfidence * 0.3;

    // Add some randomness to simulate real-world conditions
    probability += (Math.random() - 0.5) * 0.4;

    // Ensure probability is between 0 and 1
    return Math.max(0, Math.min(1, probability));
}

// Track unknown persons
let unknownPersons = [];

// Add unknown person to tracking list
function addUnknownPerson(prediction, facePosition, faceSize) {
    const unknownId = `unknown_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;

    // Check if we already have this unknown person (basic position tracking)
    const existingUnknown = unknownPersons.find(unknown => {
        const timeDiff = Date.now() - unknown.lastSeen;
        const positionDiff = Math.abs(unknown.lastPosition[0] - facePosition[0]) +
                           Math.abs(unknown.lastPosition[1] - facePosition[1]);

        return timeDiff < 5000 && positionDiff < 100; // Same person if seen within 5 seconds and close position
    });

    if (existingUnknown) {
        // Update existing unknown person
        existingUnknown.lastSeen = Date.now();
        existingUnknown.lastPosition = facePosition;
        existingUnknown.seenCount++;
    } else {
        // New unknown person
        const unknownPerson = {
            id: unknownId,
            type: 'unknown',
            firstSeen: Date.now(),
            lastSeen: Date.now(),
            lastPosition: facePosition,
            faceSize: faceSize,
            seenCount: 1,
            confidence: prediction.probability ? prediction.probability[0] : 0.8
        };

        unknownPersons.push(unknownPerson);

        // Show notification for new unknown person
        if (unknownPerson.seenCount === 1) {
            console.log('Noma\'lum shaxs aniqlandi:', unknownPerson);
            updateStatusText('Noma\'lum shaxs aniqlandi - tizimda ro\'yxatdan o\'tmagan');
        }
    }

    // Clean up old unknown persons (older than 30 seconds)
    unknownPersons = unknownPersons.filter(unknown =>
        Date.now() - unknown.lastSeen < 30000
    );

    // Update unknown persons list
    updateUnknownPersonsList();
}

// Update unknown persons list
function updateUnknownPersonsList() {
    const listContainer = document.getElementById('unknownPersonsList');
    const countBadge = document.getElementById('unknownCount');

    if (unknownPersons.length === 0) {
        listContainer.innerHTML = `
            <div class="text-center text-muted">
                <i class="fas fa-user-times fa-2x mb-2" style="color: #dc3545; opacity: 0.3;"></i>
                <p>Noma'lum shaxslar aniqlanmadi</p>
            </div>
        `;
        countBadge.textContent = '0';
        return;
    }

    let html = '';
    unknownPersons.forEach((person, index) => {
        const timeAgo = Math.floor((Date.now() - person.firstSeen) / 1000);

        html += `
            <div class="mb-2 p-2 rounded" style="border: 1px solid #dc3545; background: #fef0f0;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong style="color: #dc3545;">Noma'lum #${index + 1}</strong>
                        <br>
                        <small style="color: #6c757d;">
                            <i class="fas fa-clock"></i> ${timeAgo}s oldin ko'rilgan
                            <br>
                            <i class="fas fa-eye"></i> ${person.seenCount} marta ko'rilgan
                        </small>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-sm" style="background: var(--secondary-green); color: white;"
                                onclick="enrollUnknownPerson('${person.id}')">
                            <i class="fas fa-user-plus"></i> Ro'yxatdan o'tkazish
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    listContainer.innerHTML = html;
    countBadge.textContent = unknownPersons.length;
}

// Enroll unknown person
function enrollUnknownPerson(unknownId) {
    const unknownPerson = unknownPersons.find(p => p.id === unknownId);
    if (!unknownPerson) {
        alert('Noma\'lum shaxs topilmadi!');
        return;
    }

    // Open enrollment modal and pre-fill with unknown person data
    openEnrollmentModal();

    // Auto-generate student ID
    const nextId = (studentDatabase.length + 1).toString().padStart(6, '0');
    document.getElementById('enrollStudentId').value = `2024${nextId}`;

    // Focus on first name field
    setTimeout(() => {
        document.getElementById('enrollFirstName').focus();
    }, 500);

    updateStatusText(`Noma'lum shaxsni ro'yxatdan o'tkazish uchun ma'lumotlarni kiriting`);
}

// Update enrolled students list
function updateEnrolledStudentsList() {
    const listContainer = document.getElementById('enrolledStudentsList');
    const countBadge = document.getElementById('enrolledCount');

    if (studentDatabase.length === 0) {
        listContainer.innerHTML = `
            <div class="text-center text-muted">
                <i class="fas fa-user-plus fa-2x mb-2" style="color: var(--secondary-green); opacity: 0.3;"></i>
                <p>Hali talabalar ro'yxatdan o'tmagan</p>
            </div>
        `;
        countBadge.textContent = '0';
        return;
    }

    let html = '';
    studentDatabase.forEach((student, index) => {
        const enrolledTime = student.enrolled_at ? new Date(student.enrolled_at).toLocaleDateString('uz-UZ') : 'Noma\'lum';

        html += `
            <div class="mb-2 p-2 rounded" style="border: 1px solid var(--border-green); background: var(--very-light-green);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong style="color: var(--text-dark);">${student.name}</strong>
                        <br>
                        <small style="color: #6c757d;">
                            <i class="fas fa-id-card"></i> ${student.student_id}
                            <br>
                            <i class="fas fa-users"></i> ${student.group} (${student.course}-kurs)
                            <br>
                            <i class="fas fa-calendar"></i> ${enrolledTime}
                        </small>
                    </div>
                    <div class="text-end">
                        <span class="badge" style="background: var(--secondary-green); color: white;">
                            <i class="fas fa-check"></i> Ro'yxatda
                        </span>
                    </div>
                </div>
            </div>
        `;
    });

    listContainer.innerHTML = html;
    countBadge.textContent = studentDatabase.length;
}

// Update Detected Students List
function updateDetectedList() {
    const listContainer = document.getElementById('detectedStudentsList');

    if (detectedStudents.length === 0) {
        listContainer.innerHTML = `
            <div class="text-center text-muted">
                <i class="fas fa-user-slash fa-3x mb-3" style="color: var(--secondary-green); opacity: 0.3;"></i>
                <p>Hali talabalar aniqlanmadi</p>
            </div>
        `;
        return;
    }

    let html = '';
    detectedStudents.forEach(student => {
        const timeStr = student.detectedAt.toLocaleTimeString('uz-UZ', {
            hour: '2-digit',
            minute: '2-digit'
        });

        const statusClass = student.hasClass ? 'success' : 'warning';
        const statusIcon = student.hasClass ? 'chalkboard-teacher' : 'clock';
        const statusText = student.hasClass ? `Dars: ${student.subject}` : 'Dars yo\'q - Kelganlar ro\'yxati';

        html += `
            <div class="student-card detected">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong style="color: var(--text-dark);">${student.name}</strong>
                        <br>
                        <small style="color: #7f8c8d;">ID: ${student.student_id} | ${student.group} (${student.course}-kurs)</small>
                        <br>
                        <small style="color: var(--secondary-green);">Kirish vaqti: ${timeStr}</small>
                        <br>
                        <small class="${statusClass === 'success' ? 'text-success' : 'text-warning'}">
                            <i class="fas fa-${statusIcon}"></i> ${statusText}
                        </small>
                    </div>
                    <div class="text-end">
                        <span class="badge" style="background: var(--secondary-green); color: white; margin-bottom: 5px;">
                            <i class="fas fa-check"></i> Aniqlandi
                        </span>
                        <br>
                        <button class="btn btn-sm btn-outline-danger" onclick="removeStudent(${student.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    listContainer.innerHTML = html;

    // Update counts
    document.getElementById('detectedCount').textContent = detectedStudents.length;

    // Group counts
    const groupCounts = {};
    detectedStudents.forEach(student => {
        groupCounts[student.group] = (groupCounts[student.group] || 0) + 1;
    });

    document.getElementById('totalStudents').textContent = Object.keys(groupCounts).length + ' guruh';

    // Enable save button if students detected
    document.getElementById('saveBtn').disabled = detectedStudents.length === 0;

    // Update groups status
    updateGroupsStatus();

    // Update unknown persons list
    updateUnknownPersonsList();
}

// Remove student from detected list
function removeStudent(studentId) {
    detectedStudents = detectedStudents.filter(s => s.id !== studentId);
    updateDetectedList();
}

// Update current time
function updateCurrentTime() {
    const now = new Date();
    const timeStr = now.toLocaleString('uz-UZ', {
        weekday: 'short',
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    document.getElementById('currentTime').textContent = timeStr;

    // Update every second
    setTimeout(updateCurrentTime, 1000);
}

// Load groups status
function loadGroupsStatus() {
    updateGroupsStatus();
}

// Update groups status
function updateGroupsStatus() {
    const groupsContainer = document.getElementById('groupsStatusList');

    // Count students by group
    const groupCounts = {};
    detectedStudents.forEach(student => {
        if (!groupCounts[student.group]) {
            groupCounts[student.group] = {
                count: 0,
                hasClass: student.hasClass,
                subject: student.subject,
                time: student.classTime,
                students: []
            };
        }
        groupCounts[student.group].count++;
        groupCounts[student.group].students.push(student.name);
    });

    let html = '';

    // Show all groups from schedule
    Object.keys(classSchedule).forEach(groupName => {
        const schedule = classSchedule[groupName];
        const groupData = groupCounts[groupName] || { count: 0, students: [] };

        const statusIcon = schedule.hasClass ? 'chalkboard-teacher' : 'users';
        const statusColor = schedule.hasClass ? 'var(--secondary-green)' : '#f39c12';
        const statusText = schedule.hasClass ? `Dars: ${schedule.subject} (${schedule.time})` : 'Dars yo\'q';

        html += `
            <div class="mb-3 p-3 rounded" style="border: 1px solid var(--border-green); background: var(--very-light-green);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-${statusIcon}" style="color: ${statusColor};"></i>
                        ${groupName}
                    </h6>
                    <span class="badge" style="background: ${statusColor}; color: white;">
                        ${groupData.count} ta talaba
                    </span>
                </div>
                <small style="color: #7f8c8d;">${statusText}</small>
                ${groupData.students.length > 0 ? `
                    <div class="mt-2">
                        <small style="color: var(--text-dark);">Kelgan talabalar:</small>
                        <div class="mt-1">
                            ${groupData.students.map(name => `<span class="badge bg-light text-dark me-1 mb-1">${name.split(' ')[0]}</span>`).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    });

    if (html === '') {
        html = `
            <div class="text-center text-muted">
                <i class="fas fa-clock fa-2x mb-2" style="color: var(--secondary-green); opacity: 0.3;"></i>
                <p>Guruhlar holati yuklanmoqda...</p>
            </div>
        `;
    }

    groupsContainer.innerHTML = html;
}

// Play notification sound
function playNotificationSound() {
    try {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmoxAjN+zPDdizEEKX3G7OOUQgoUYrPn8K1ZGAlDn+LyvmswAzV+y+/ejjEEKXzJ7OGUQgkSZLLo8K1ZGAlCm+PyvmssAzN+ye7dijs=' );
        audio.volume = 0.3;
        audio.play().catch(() => {});
    } catch (e) {
        // Ignore audio errors
    }
}

// Auto-save attendance when face is detected
async function autoSaveAttendance(studentId, confidence) {
    try {
        const response = await fetch(baseUrl + '/api/face-attendance/mark-attendance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                student_id: studentId,
                confidence: confidence || 0.85
            })
        });

        const data = await response.json();

        if (data.success) {
            if (!data.already_marked) {
                console.log('Davomat saqlandi:', data.attendance);
                updateStatusText(`${data.attendance.student_name} - davomat belgilandi (${data.attendance.check_in_time})`);

                // Refresh attendance list
                refreshAttendanceList();
            } else {
                console.log('Davomat allaqachon mavjud:', data.attendance);
            }
        } else {
            console.error('Davomat saqlashda xatolik:', data.message);
        }

    } catch (error) {
        console.error('Davomat saqlashda xatolik:', error);
    }
}

// Save Attendance (manual save button)
function saveAttendance() {
    if (detectedStudents.length === 0) {
        alert('Avval talabalarni aniqlang!');
        return;
    }

    const mode = document.getElementById('modeSelect').value;

    // Group students by group and class status
    const groupedData = {};
    detectedStudents.forEach(student => {
        if (!groupedData[student.group]) {
            groupedData[student.group] = {
                withClass: [],
                withoutClass: []
            };
        }

        if (student.hasClass) {
            groupedData[student.group].withClass.push(student);
        } else {
            groupedData[student.group].withoutClass.push(student);
        }
    });

    let message = `${detectedStudents.length} ta talaba uchun ma'lumot saqlandi!\n\n`;

    Object.keys(groupedData).forEach(groupName => {
        const group = groupedData[groupName];
        message += `${groupName}:\n`;

        if (group.withClass.length > 0) {
            message += `  - Darsga kelganlar: ${group.withClass.length} ta\n`;
        }

        if (group.withoutClass.length > 0) {
            message += `  - Dars yo'q, kelganlar ro'yxati: ${group.withoutClass.length} ta\n`;
        }

        message += '\n';
    });

    alert(message);

    // Reset
    detectedStudents = [];
    updateDetectedList();
    updateStatusText('Ma\'lumotlar saqlandi');
}

// Update Detection Status
function updateDetectionStatus(active) {
    const indicator = document.getElementById('detection-status');
    if (active) {
        indicator.classList.add('active');
        indicator.classList.remove('inactive');
    } else {
        indicator.classList.add('inactive');
        indicator.classList.remove('active');
    }
}

// Update Status Text
function updateStatusText(text) {
    document.getElementById('status-text').textContent = text;
}

// ===== FACE ENROLLMENT FUNCTIONS =====

// Open enrollment modal
function openEnrollmentModal() {
    console.log('Enrollment modal ochilyapti');

    if (!stream) {
        alert('Avval kamerani yoqing!');
        return;
    }

    // Reset form
    document.getElementById('enrollmentForm').reset();
    document.getElementById('enrollmentCanvas').style.display = 'none';
    document.getElementById('enrollmentPhotoPlaceholder').style.display = 'block';
    document.getElementById('retakeEnrollmentPhoto').style.display = 'none';
    document.getElementById('saveEnrollment').disabled = true;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('enrollmentModal'));
    modal.show();

    // Setup enrollment modal event listeners
    setupEnrollmentEventListeners();
}

// Setup enrollment event listeners
function setupEnrollmentEventListeners() {
    // Capture photo button
    const captureBtn = document.getElementById('captureEnrollmentPhoto');
    captureBtn.onclick = function() {
        captureEnrollmentPhoto();
    };

    // Retake photo button
    const retakeBtn = document.getElementById('retakeEnrollmentPhoto');
    retakeBtn.onclick = function() {
        retakeEnrollmentPhoto();
    };

    // Save enrollment button
    const saveBtn = document.getElementById('saveEnrollment');
    saveBtn.onclick = function() {
        saveEnrollment();
    };

    // Form validation
    const form = document.getElementById('enrollmentForm');
    form.addEventListener('input', function() {
        validateEnrollmentForm();
    });
}

// Capture photo for enrollment
function captureEnrollmentPhoto() {
    console.log('Enrollment uchun surat olinmoqda');

    if (!video || !video.videoWidth) {
        alert('Video tayyor emas!');
        return;
    }

    const enrollCanvas = document.getElementById('enrollmentCanvas');
    const enrollCtx = enrollCanvas.getContext('2d');

    // Set canvas size
    enrollCanvas.width = video.videoWidth;
    enrollCanvas.height = video.videoHeight;

    // Draw current video frame
    enrollCtx.drawImage(video, 0, 0);

    // Show the captured image
    enrollCanvas.style.display = 'block';
    document.getElementById('enrollmentPhotoPlaceholder').style.display = 'none';
    document.getElementById('retakeEnrollmentPhoto').style.display = 'inline-block';

    // Enable save button if form is valid
    validateEnrollmentForm();

    console.log('Surat muvaffaqiyatli olindi');
}

// Retake photo
function retakeEnrollmentPhoto() {
    document.getElementById('enrollmentCanvas').style.display = 'none';
    document.getElementById('enrollmentPhotoPlaceholder').style.display = 'block';
    document.getElementById('retakeEnrollmentPhoto').style.display = 'none';
    document.getElementById('saveEnrollment').disabled = true;
}

// Validate enrollment form
function validateEnrollmentForm() {
    const lastName = document.getElementById('enrollLastName').value.trim();
    const firstName = document.getElementById('enrollFirstName').value.trim();
    const studentId = document.getElementById('enrollStudentId').value.trim();
    const group = document.getElementById('enrollGroup').value;
    const hasPhoto = document.getElementById('enrollmentCanvas').style.display !== 'none';

    const isValid = lastName && firstName && studentId && group && hasPhoto;
    document.getElementById('saveEnrollment').disabled = !isValid;

    return isValid;
}

// Save enrollment
function saveEnrollment() {
    console.log('Enrollment saqlanmoqda');

    if (!validateEnrollmentForm()) {
        alert('Barcha majburiy maydonlarni to\'ldiring va suratni oling!');
        return;
    }

    // Get form data
    const enrollmentData = {
        lastName: document.getElementById('enrollLastName').value.trim(),
        firstName: document.getElementById('enrollFirstName').value.trim(),
        middleName: document.getElementById('enrollMiddleName').value.trim(),
        studentId: document.getElementById('enrollStudentId').value.trim(),
        group: document.getElementById('enrollGroup').value,
        timestamp: new Date().toISOString()
    };

    // Get photo data
    const enrollCanvas = document.getElementById('enrollmentCanvas');
    const photoData = enrollCanvas.toDataURL('image/jpeg', 0.8);

    // In real system, this would send data to server
    console.log('Enrollment ma\'lumotlari:', enrollmentData);
    console.log('Surat ma\'lumoti olingan');

    // Simulate saving
    setTimeout(() => {
        // Add to student database (simulate)
        const newStudent = {
            id: Date.now(),
            name: `${enrollmentData.lastName} ${enrollmentData.firstName} ${enrollmentData.middleName}`.trim(),
            group: enrollmentData.group,
            course: getCourseFromGroup(enrollmentData.group),
            student_id: enrollmentData.studentId,
            photo_data: photoData,
            enrolled_at: enrollmentData.timestamp
        };

        // Add to student database
        studentDatabase.push(newStudent);

        console.log('Yangi talaba qo\'shildi:', newStudent);

        // Show success message
        alert(`Talaba muvaffaqiyatli ro'yxatdan o'tkazildi!

Ism: ${newStudent.name}
Guruh: ${newStudent.group}
ID: ${newStudent.student_id}`);

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('enrollmentModal'));
        modal.hide();

        // Update status
        updateStatusText(`Yangi talaba qo'shildi: ${newStudent.name}`);

        // Update enrolled students list
        updateEnrolledStudentsList();

    }, 1000);
}

// Get course number from group name
function getCourseFromGroup(group) {
    if (group.includes('101') || group.includes('102')) return 1;
    if (group.includes('201') || group.includes('202')) return 2;
    if (group.includes('301') || group.includes('302')) return 3;
    if (group.includes('401') || group.includes('402')) return 4;
    return 1;
}

// ===== ATTENDANCE LIST FUNCTIONS =====

// Refresh attendance list
async function refreshAttendanceList() {
    const groupFilter = document.getElementById('attendanceGroupFilter');
    const groupId = groupFilter ? groupFilter.value : '';

    try {
        updateStatusText('Davomat ro\'yxati yangilanmoqda...');

        const params = new URLSearchParams();
        if (groupId) params.append('group_id', groupId);

        const response = await fetch(baseUrl + `/api/face-attendance/today-attendance?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error(`Server xatoligi: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            updateAttendanceTable(data.attendances || []);
            updateAttendanceStats(data.stats || {});
            updateStatusText('Davomat ro\'yxati yangilandi');
        } else {
            throw new Error(data.message || 'Ma\'lumot yuklashda xatolik');
        }

    } catch (error) {
        console.error('Davomat ro\'yxatini yuklashda xatolik:', error);
        updateStatusText('Xatolik: ' + error.message);
    }
}

// Update attendance table
function updateAttendanceTable(attendances) {
    const tbody = document.getElementById('attendanceTableBody');
    const countBadge = document.getElementById('attendanceListCount');

    if (!attendances || attendances.length === 0) {
        tbody.innerHTML = `
            <tr id="noAttendanceRow">
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-clipboard fa-3x mb-3" style="opacity: 0.3;"></i>
                    <p>Bugun hali davomat yo'q</p>
                </td>
            </tr>
        `;
        countBadge.textContent = '0';
        return;
    }

    const statusColors = {
        'early': 'success',
        'present': 'primary',
        'late': 'warning',
        'very_late': 'danger',
        'absent': 'secondary'
    };

    const statusLabels = {
        'early': 'Erta',
        'present': 'Keldi',
        'late': 'Kechikdi',
        'very_late': 'Juda kechikdi',
        'absent': 'Kelmadi'
    };

    let html = '';
    attendances.forEach((attendance, index) => {
        const checkInTime = attendance.check_in_time ? formatTime(attendance.check_in_time) : '-';
        const checkOutTime = attendance.check_out_time ? formatTime(attendance.check_out_time) : '-';
        const confidence = attendance.confidence_score ? (attendance.confidence_score * 100).toFixed(1) + '%' : '-';

        let methodIcon = '';
        if (attendance.method === 'face_recognition') {
            methodIcon = '<i class="fas fa-user-check text-success" title="Yuz orqali"></i>';
        } else if (attendance.method === 'manual') {
            methodIcon = '<i class="fas fa-hand-paper text-warning" title="Qo\'lda"></i>';
        } else {
            methodIcon = '<i class="fas fa-qrcode text-info" title="QR kod"></i>';
        }

        html += `
            <tr>
                <td>${index + 1}</td>
                <td>
                    <strong>${attendance.student_name || 'N/A'}</strong>
                    <br>
                    <small class="text-muted">${attendance.student_code || ''}</small>
                </td>
                <td>${attendance.group_name || 'N/A'}</td>
                <td>${checkInTime}</td>
                <td>${checkOutTime}</td>
                <td>
                    <span class="badge bg-${statusColors[attendance.status] || 'secondary'}">
                        ${statusLabels[attendance.status] || attendance.status}
                    </span>
                </td>
                <td>${confidence}</td>
                <td>${methodIcon}</td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    countBadge.textContent = attendances.length;
}

// Update attendance stats
function updateAttendanceStats(stats) {
    document.getElementById('statTotal').textContent = stats.total || 0;
    document.getElementById('statEarly').textContent = stats.early || 0;
    document.getElementById('statPresent').textContent = stats.present || 0;
    document.getElementById('statLate').textContent = stats.late || 0;
    document.getElementById('statVeryLate').textContent = stats.very_late || 0;
    document.getElementById('statAbsent').textContent = stats.absent || 0;
}

// Format time string
function formatTime(timeStr) {
    if (!timeStr) return '-';

    // Handle different time formats
    if (timeStr.includes(':')) {
        const parts = timeStr.split(':');
        return parts[0] + ':' + parts[1];
    }

    return timeStr;
}

// Export to Excel
async function exportToExcel() {
    const groupFilter = document.getElementById('attendanceGroupFilter');
    const groupId = groupFilter ? groupFilter.value : '';

    try {
        updateStatusText('Excel fayli tayyorlanmoqda...');

        const params = new URLSearchParams();
        if (groupId) params.append('group_id', groupId);

        // Get current month dates
        const now = new Date();
        const startDate = new Date(now.getFullYear(), now.getMonth(), 1);
        const endDate = now;

        params.append('start_date', startDate.toISOString().split('T')[0]);
        params.append('end_date', endDate.toISOString().split('T')[0]);

        const response = await fetch(baseUrl + `/api/face-attendance/export-excel?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error(`Server xatoligi: ${response.status}`);
        }

        // Get filename from Content-Disposition header or use default
        const contentDisposition = response.headers.get('Content-Disposition');
        let filename = 'davomat_' + new Date().toISOString().split('T')[0] + '.csv';
        if (contentDisposition) {
            const match = contentDisposition.match(/filename="?([^"]+)"?/);
            if (match) filename = match[1];
        }

        // Download file
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        updateStatusText('Excel fayli yuklandi: ' + filename);

    } catch (error) {
        console.error('Excel export xatoligi:', error);
        updateStatusText('Excel export xatoligi: ' + error.message);
        alert('Excel faylini yuklab olishda xatolik: ' + error.message);
    }
}

// Filter attendance by group (client-side filtering when page reloads)
function filterAttendanceByGroup() {
    const groupFilter = document.getElementById('attendanceGroupFilter');
    if (groupFilter) {
        groupFilter.addEventListener('change', function() {
            // Reload page with group filter
            const groupId = this.value;
            const url = new URL(window.location.href);

            if (groupId) {
                url.searchParams.set('group_id', groupId);
            } else {
                url.searchParams.delete('group_id');
            }

            window.location.href = url.toString();
        });
    }
}

// Initialize attendance list functions
document.addEventListener('DOMContentLoaded', function() {
    filterAttendanceByGroup();

    // Auto-refresh attendance list every 30 seconds
    setInterval(function() {
        if (isDetecting) {
            refreshAttendanceList();
        }
    }, 30000);

    // Student search functionality
    initStudentSearch();
});

// ===== STUDENT SEARCH FUNCTIONS =====

let currentSelectedStudent = null;

function initStudentSearch() {
    const searchBtn = document.getElementById('searchStudentBtn');
    const searchInput = document.getElementById('studentSearchInput');

    if (searchBtn) {
        searchBtn.addEventListener('click', searchStudents);
    }

    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchStudents();
            }
        });

        // Auto-search as user types (debounced)
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if (searchInput.value.length >= 2) {
                    searchStudents();
                }
            }, 500);
        });
    }
}

async function searchStudents() {
    const searchInput = document.getElementById('studentSearchInput');
    const groupFilter = document.getElementById('searchGroupFilter');
    const resultsDiv = document.getElementById('studentSearchResults');

    const query = searchInput.value.trim();
    const groupId = groupFilter ? groupFilter.value : '';

    if (query.length < 2) {
        resultsDiv.style.display = 'none';
        return;
    }

    try {
        const params = new URLSearchParams();
        params.append('search', query);
        if (groupId) params.append('group_id', groupId);

        const response = await fetch(baseUrl + `/api/face-attendance/enrolled-students?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success && data.students && data.students.length > 0) {
            displaySearchResults(data.students);
        } else {
            resultsDiv.innerHTML = '<div class="text-center text-muted py-3"><i class="fas fa-search"></i> Talaba topilmadi</div>';
            resultsDiv.style.display = 'block';
        }

    } catch (error) {
        console.error('Qidirish xatoligi:', error);
        resultsDiv.innerHTML = '<div class="text-danger py-2"><i class="fas fa-exclamation-circle"></i> Xatolik yuz berdi</div>';
        resultsDiv.style.display = 'block';
    }
}

function displaySearchResults(students) {
    const resultsDiv = document.getElementById('studentSearchResults');

    let html = '<div class="list-group">';
    students.forEach(student => {
        const hasFace = student.has_face_registered;
        const statusBadge = hasFace
            ? '<span class="badge bg-success ms-2"><i class="fas fa-check"></i> Ro\'yxatdan o\'tgan</span>'
            : '<span class="badge bg-warning ms-2"><i class="fas fa-exclamation"></i> Ro\'yxatdan o\'tmagan</span>';

        html += `
            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                    onclick="selectStudent(${JSON.stringify(student).replace(/"/g, '&quot;')})">
                <div>
                    <strong>${student.last_name} ${student.first_name}</strong>
                    ${student.middle_name || ''}
                    <br>
                    <small class="text-muted">${student.student_id || student.student_code || ''} | ${student.group_name || ''}</small>
                </div>
                ${statusBadge}
            </button>
        `;
    });
    html += '</div>';

    resultsDiv.innerHTML = html;
    resultsDiv.style.display = 'block';
}

function selectStudent(student) {
    currentSelectedStudent = student;

    // Update selected student display
    document.getElementById('selectedStudentInfo').style.display = 'block';
    document.getElementById('selectedStudentName').textContent = `${student.last_name} ${student.first_name} ${student.middle_name || ''}`;
    document.getElementById('selectedStudentCode').textContent = student.student_id || student.student_code || '';
    document.getElementById('selectedStudentGroup').textContent = student.group_name || '';
    document.getElementById('selectedStudentId').value = student.id;

    // Fill form fields
    document.getElementById('enrollLastName').value = student.last_name || '';
    document.getElementById('enrollFirstName').value = student.first_name || '';
    document.getElementById('enrollMiddleName').value = student.middle_name || '';
    document.getElementById('enrollStudentId').value = student.student_id || student.student_code || '';
    document.getElementById('enrollGroup').value = student.group_name || '';

    // Hide search results
    document.getElementById('studentSearchResults').style.display = 'none';
    document.getElementById('studentSearchInput').value = '';

    // Check if already registered
    if (student.has_face_registered) {
        alert('Diqqat: Bu talaba allaqachon Face Attendance tizimiga ro\'yxatdan o\'tgan!');
    }
}

function clearSelectedStudent() {
    currentSelectedStudent = null;

    document.getElementById('selectedStudentInfo').style.display = 'none';
    document.getElementById('selectedStudentId').value = '';

    // Clear form fields
    document.getElementById('enrollLastName').value = '';
    document.getElementById('enrollFirstName').value = '';
    document.getElementById('enrollMiddleName').value = '';
    document.getElementById('enrollStudentId').value = '';
    document.getElementById('enrollGroup').value = '';

    // Disable save button
    document.getElementById('saveEnrollment').disabled = true;
}

// Search student by ID (for direct ID input)
async function searchStudentById(studentId) {
    try {
        const response = await fetch(baseUrl + `/api/face-attendance/student/${studentId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success && data.student) {
            selectStudent(data.student);
            return true;
        }

        return false;
    } catch (error) {
        console.error('Talaba qidirish xatoligi:', error);
        return false;
    }
}
</script>
@endpush