@extends('layouts.dashboard-new')

@section('title', 'Davomat - ' . ($journal->subject->name_uz ?? 'Fan'))

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

    #attendanceTable th {
        writing-mode: vertical-lr;
        text-orientation: mixed;
        min-width: 35px;
        max-width: 35px;
        height: 100px;
        background: var(--very-light-green);
        color: var(--text-dark);
        font-weight: 600;
    }

    #attendanceTable th:first-child,
    #attendanceTable th:nth-child(2),
    #attendanceTable th:last-child,
    #attendanceTable th:nth-last-child(2) {
        writing-mode: initial;
        height: auto;
        min-width: unset;
        max-width: unset;
    }

    .attendance-cell {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .attendance-cell:hover {
        transform: scale(1.2);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    #webcam-container {
        position: relative;
        width: 100%;
        max-width: 640px;
        margin: 0 auto;
    }

    #webcam-container video {
        width: 100%;
        border-radius: 8px;
        border: 3px solid var(--border-green);
    }

    .face-detection-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .face-box {
        position: absolute;
        border: 2px solid var(--accent-green);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .face-box.recognized {
        border-color: var(--secondary-green);
        background: rgba(22, 160, 133, 0.1);
    }

    .face-label {
        position: absolute;
        bottom: -25px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--primary-dark-green);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 p-4 rounded-lg" style="background: linear-gradient(135deg, var(--primary-dark-green) 0%, var(--secondary-green) 100%);">
        <div class="col-md-8">
            <h1 class="h2 text-white">Davomat jurnali</h1>
            <p class="text-white opacity-90">
                <strong>{{ $journal->subject->name_uz ?? 'Fan' }}</strong> |
                {{ $journal->group->name ?? 'Guruh' }} |
                {{ $journal->semester ?? '1' }}-semestr
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('attendance.face-recognition') }}" class="btn text-white"
               style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-camera"></i> Yuz orqali davomat
            </a>
        </div>
    </div>

    <!-- Face Recognition Modal -->
    <div class="modal fade" id="faceRecognitionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border: 2px solid var(--border-green);">
                <div class="modal-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                    <h5 class="modal-title" style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-user-check" style="color: var(--secondary-green);"></i>
                        Yuz orqali davomat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="webcam-container">
                        <video id="webcam" autoplay muted></video>
                        <canvas id="face-canvas" class="face-detection-overlay"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="alert" style="background: var(--light-green); border: 1px solid var(--border-green); color: var(--text-dark);">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-2" style="color: var(--secondary-green);"></div>
                                <span>Yuzlar aniqlanmoqda...</span>
                            </div>
                        </div>
                        <div id="detected-students" class="row g-2 mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                    <button type="button" class="btn" style="background: #6c757d; color: white;" data-bs-dismiss="modal">
                        Yopish
                    </button>
                    <button type="button" class="btn text-white" onclick="startFaceRecognition()"
                            style="background: var(--primary-dark-green);"
                            onmouseover="this.style.background='var(--secondary-green)'"
                            onmouseout="this.style.background='var(--primary-dark-green)'">
                        <i class="fas fa-play"></i> Boshlash
                    </button>
                    <button type="button" class="btn text-white" onclick="captureAttendance()"
                            style="background: var(--secondary-green);"
                            onmouseover="this.style.background='var(--primary-dark-green)'"
                            onmouseout="this.style.background='var(--secondary-green)'">
                        <i class="fas fa-camera"></i> Suratga olish
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark Today Modal -->
    <div class="modal fade" id="markTodayModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border: 2px solid var(--border-green);">
                <div class="modal-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                    <h5 class="modal-title" style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-calendar-check" style="color: var(--secondary-green);"></i>
                        Bugungi davomat - <span id="todayDate"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="markTodayForm" method="POST" action="{{ route('attendance.store', $journal->id) }}">
                    @csrf
                    <input type="hidden" name="lesson_date" id="lessonDate">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Dars turi</label>
                                <select name="lesson_type" class="form-select" required>
                                    <option value="lecture">Ma'ruza</option>
                                    <option value="practice">Amaliyot</option>
                                    <option value="lab">Laboratoriya</option>
                                    <option value="seminar">Seminar</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vaqt</label>
                                <select name="time_slot" class="form-select" required>
                                    <option value="08:30-10:00">08:30 - 10:00 (1-para)</option>
                                    <option value="10:10-11:40">10:10 - 11:40 (2-para)</option>
                                    <option value="12:00-13:30">12:00 - 13:30 (3-para)</option>
                                    <option value="13:40-15:10">13:40 - 15:10 (4-para)</option>
                                    <option value="15:20-16:50">15:20 - 16:50 (5-para)</option>
                                    <option value="17:00-18:30">17:00 - 18:30 (6-para)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-success me-2" onclick="markAllPresent()">
                                <i class="fas fa-check-double"></i> Hammasini "Bor"
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="markAllAbsent()">
                                <i class="fas fa-times"></i> Hammasini "Yo'q"
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="todayAttendanceTable">
                                <thead style="background: var(--very-light-green);">
                                    <tr>
                                        <th>№</th>
                                        <th>Talaba</th>
                                        <th class="text-center">Holat</th>
                                        <th>Izoh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students ?? [] as $student)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $student->last_name }} {{ $student->first_name }}</td>
                                        <td class="text-center">
                                            <input type="hidden" name="attendance[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                            <select name="attendance[{{ $loop->index }}][status]" class="form-select form-select-sm attendance-status" style="width: 120px;">
                                                <option value="present">+ Bor</option>
                                                <option value="absent">- Yo'q</option>
                                                <option value="excused">s Sababli</option>
                                                <option value="late">k Kechikkan</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="attendance[{{ $loop->index }}][notes]" class="form-control form-control-sm" placeholder="Izoh...">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn text-white" style="background: var(--primary-dark-green);">
                            <i class="fas fa-save"></i> Saqlash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Journal Info -->
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important;">
        <div class="card-body" style="background: linear-gradient(135deg, var(--very-light-green), white);">
            <div class="row">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2" style="background: var(--light-green);">
                            <i class="fas fa-book" style="color: var(--primary-dark-green);"></i>
                        </div>
                        <div>
                            <small style="color: #7f8c8d;">Fan</small>
                            <div style="color: var(--text-dark); font-weight: 600;">{{ $journal->subject->name_uz ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2" style="background: var(--light-green);">
                            <i class="fas fa-users" style="color: var(--secondary-green);"></i>
                        </div>
                        <div>
                            <small style="color: #7f8c8d;">Guruh</small>
                            <div style="color: var(--text-dark); font-weight: 600;">{{ $journal->group->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2" style="background: var(--light-green);">
                            <i class="fas fa-chalkboard-teacher" style="color: var(--accent-green);"></i>
                        </div>
                        <div>
                            <small style="color: #7f8c8d;">O'qituvchi</small>
                            <div style="color: var(--text-dark); font-weight: 600;">
                                {{ $journal->teacher->first_name ?? '' }} {{ $journal->teacher->last_name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2" style="background: var(--light-green);">
                            <i class="fas fa-calendar" style="color: var(--primary-dark-green);"></i>
                        </div>
                        <div>
                            <small style="color: #7f8c8d;">Semestr</small>
                            <div style="color: var(--text-dark); font-weight: 600;">{{ $journal->semester ?? '1' }}-semestr</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important;">
        <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                    <i class="fas fa-user-check" style="color: var(--secondary-green);"></i>
                    Davomat jadvali
                </h5>
                <div>
                    <button class="btn btn-sm text-white" onclick="markToday()"
                            style="background: var(--primary-dark-green);"
                            onmouseover="this.style.background='var(--secondary-green)'"
                            onmouseout="this.style.background='var(--primary-dark-green)'">
                        <i class="fas fa-check-circle"></i> Bugungi kun
                    </button>
                    <button class="btn btn-sm text-white" onclick="exportAttendance()"
                            style="background: var(--secondary-green);"
                            onmouseover="this.style.background='var(--primary-dark-green)'"
                            onmouseout="this.style.background='var(--secondary-green)'">
                        <i class="fas fa-file-excel"></i> Export
                    </button>
                    <button class="btn btn-sm" onclick="showStatistics()"
                            style="background: var(--accent-green); color: white;"
                            onmouseover="this.style.background='var(--secondary-green)'"
                            onmouseout="this.style.background='var(--accent-green)'">
                        <i class="fas fa-chart-pie"></i> Statistika
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="attendanceTable">
                    <thead>
                        <tr>
                            <th rowspan="2" class="align-middle" style="background: var(--very-light-green);">№</th>
                            <th rowspan="2" class="align-middle" style="background: var(--very-light-green);">Talaba F.I.O</th>
                            @foreach($attendances ?? [] as $date => $records)
                            <th class="text-center">
                                {{ \Carbon\Carbon::parse($date)->format('d.m') }}
                                <br>
                                <small>{{ $records->first()->lesson_type ?? '' }}</small>
                            </th>
                            @endforeach
                            <th rowspan="2" class="align-middle text-center" style="background: var(--very-light-green);">Jami</th>
                            <th rowspan="2" class="align-middle text-center" style="background: var(--very-light-green);">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students ?? [] as $student)
                        <tr onmouseover="this.style.background='var(--very-light-green)'" onmouseout="this.style.background='white'">
                            <td style="color: var(--text-dark);">{{ $loop->iteration }}</td>
                            <td style="color: var(--text-dark); font-weight: 600;">
                                {{ $student->last_name }} {{ $student->first_name }}
                            </td>
                            @php
                                $presentCount = 0;
                                $totalCount = 0;
                            @endphp
                            @foreach($attendances ?? [] as $date => $records)
                            @php
                                $record = $records->where('student_id', $student->id)->first();
                                $totalCount++;
                                if($record && in_array($record->status, ['present', 'late'])) {
                                    $presentCount++;
                                }
                            @endphp
                            <td class="text-center attendance-cell" onclick="toggleAttendance(this, {{ $student->id }}, '{{ $date }}')">
                                @if($record)
                                    @switch($record->status)
                                        @case('present')
                                            <span class="badge" style="background: var(--secondary-green); color: white;">+</span>
                                            @break
                                        @case('absent')
                                            <span class="badge" style="background: #dc3545; color: white;">-</span>
                                            @break
                                        @case('excused')
                                            <span class="badge" style="background: #ffc107; color: #333;">s</span>
                                            @break
                                        @case('late')
                                            <span class="badge" style="background: var(--accent-green); color: white;">k</span>
                                            @break
                                    @endswitch
                                @else
                                    <span class="text-muted">·</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="text-center">
                                <strong style="color: var(--text-dark);">{{ $presentCount }}/{{ $totalCount }}</strong>
                            </td>
                            <td class="text-center">
                                @php
                                    $percentage = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 1) : 0;
                                    $color = $percentage >= 75 ? 'var(--secondary-green)' : ($percentage >= 50 ? '#ffc107' : '#dc3545');
                                @endphp
                                <strong style="color: {{ $color }};">{{ $percentage }}%</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Legend -->
            <div class="p-3" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                <strong style="color: var(--text-dark);">Belgilar:</strong>
                <span class="badge ms-2" style="background: var(--secondary-green); color: white;">+</span> Bor
                <span class="badge ms-2" style="background: #dc3545; color: white;">-</span> Yo'q
                <span class="badge ms-2" style="background: #ffc107; color: #333;">s</span> Sababli
                <span class="badge ms-2" style="background: var(--accent-green); color: white;">k</span> Kechikkan
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important; background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ count($attendances ?? []) }}</h3>
                    <p class="mb-0 opacity-90">Jami darslar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important; background: linear-gradient(135deg, var(--secondary-green), var(--accent-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">85.5%</h3>
                    <p class="mb-0 opacity-90">O'rtacha davomat</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: #fff3cd;">
                            <i class="fas fa-exclamation-triangle" style="color: #f39c12;"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: #f39c12;">5</h3>
                    <p class="mb-0" style="color: #7f8c8d;">Ogohlantirilgan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: #fef0f0;">
                            <i class="fas fa-user-times" style="color: #dc3545;"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: #dc3545;">2</h3>
                    <p class="mb-0" style="color: #7f8c8d;">Kritik (< 75%)</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/tensorflow/tf.min.js') }}"></script>
<script src="{{ asset('vendor/tensorflow/face-landmarks-detection.js') }}"></script>
<script>
let webcamStream = null;
let faceDetectionModel = null;
let detectionInterval = null;

// Toggle Face Recognition Modal
function toggleFaceRecognition() {
    const modal = new bootstrap.Modal(document.getElementById('faceRecognitionModal'));
    modal.show();
    initializeWebcam();
}

// Initialize Webcam
async function initializeWebcam() {
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
    } catch (error) {
        console.error('Kamera ochilmadi:', error);
        alert('Kamerani ochishda xatolik yuz berdi. Kamera ruxsatini tekshiring.');
    }
}

// Start Face Recognition
async function startFaceRecognition() {
    if (!faceDetectionModel) {
        // Load face detection model
        const model = faceLandmarksDetection.SupportedModels.MediaPipeFaceMesh;
        const detectorConfig = {
            runtime: 'tfjs',
            refineLandmarks: true
        };
        faceDetectionModel = await faceLandmarksDetection.createDetector(model, detectorConfig);
    }

    // Start detection loop
    detectionInterval = setInterval(async () => {
        await detectFaces();
    }, 100);
}

// Detect Faces
async function detectFaces() {
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('face-canvas');
    const ctx = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    if (faceDetectionModel && video.readyState === 4) {
        const faces = await faceDetectionModel.estimateFaces(video);

        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw face boxes
        faces.forEach((face, index) => {
            const keypoints = face.keypoints;

            // Find bounding box
            let minX = Infinity, minY = Infinity;
            let maxX = -Infinity, maxY = -Infinity;

            keypoints.forEach(point => {
                minX = Math.min(minX, point.x);
                minY = Math.min(minY, point.y);
                maxX = Math.max(maxX, point.x);
                maxY = Math.max(maxY, point.y);
            });

            // Draw rectangle around face
            ctx.strokeStyle = '#48c9b0';
            ctx.lineWidth = 2;
            ctx.strokeRect(minX, minY, maxX - minX, maxY - minY);

            // Simulate face recognition (in real app, this would call API)
            setTimeout(() => {
                recognizeFace(index, minX, minY, maxX - minX, maxY - minY);
            }, 1000);
        });

        updateDetectedStudents(faces.length);
    }
}

// Simulate Face Recognition
function recognizeFace(faceIndex, x, y, width, height) {
    // Simulated student data
    const students = [
        { id: 1, name: 'Aliyev Vali', photo: 'student1.jpg' },
        { id: 2, name: 'Karimova Nodira', photo: 'student2.jpg' },
        { id: 3, name: 'Rahimov Jasur', photo: 'student3.jpg' }
    ];

    // Randomly assign a student (in real app, this would use face recognition API)
    if (Math.random() > 0.3 && faceIndex < students.length) {
        const student = students[faceIndex];
        showRecognizedStudent(student);
    }
}

// Update Detected Students List
function updateDetectedStudents(count) {
    const container = document.getElementById('detected-students');
    if (count > 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert" style="background: var(--light-green); border: 1px solid var(--border-green); color: var(--text-dark);">
                    <i class="fas fa-check-circle" style="color: var(--secondary-green);"></i>
                    <strong>${count}</strong> ta yuz aniqlandi
                </div>
            </div>
        `;
    }
}

// Show Recognized Student
function showRecognizedStudent(student) {
    const container = document.getElementById('detected-students');
    const studentCard = `
        <div class="col-md-6">
            <div class="card" style="border: 1px solid var(--border-green);">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2" style="background: var(--light-green);">
                            <i class="fas fa-user-check" style="color: var(--secondary-green);"></i>
                        </div>
                        <div>
                            <strong style="color: var(--text-dark);">${student.name}</strong>
                            <div class="text-success small">Aniqlandi</div>
                        </div>
                        <button class="btn btn-sm ms-auto" style="background: var(--secondary-green); color: white;"
                                onclick="markPresent(${student.id})">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.innerHTML += studentCard;
}

// Capture Attendance
function captureAttendance() {
    const video = document.getElementById('webcam');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);

    // Convert to base64
    const imageData = canvas.toDataURL('image/jpeg');

    // Send to server for processing
    fetch('/api/attendance/face-recognition', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            image: imageData,
            journal_id: {{ $journal->id ?? 0 }},
            date: new Date().toISOString().split('T')[0]
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`${data.recognized} ta talaba aniqlandi va davomat belgilandi!`);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Xatolik:', error);
    });
}

// Mark student as present
function markPresent(studentId) {
    // Implementation for marking individual student as present
    console.log('Marking student', studentId, 'as present');
}

// Toggle attendance status
function toggleAttendance(cell, studentId, date) {
    const statuses = ['present', 'absent', 'excused', 'late'];
    const badges = {
        'present': '<span class="badge" style="background: var(--secondary-green); color: white;">+</span>',
        'absent': '<span class="badge" style="background: #dc3545; color: white;">-</span>',
        'excused': '<span class="badge" style="background: #ffc107; color: #333;">s</span>',
        'late': '<span class="badge" style="background: var(--accent-green); color: white;">k</span>'
    };

    let currentStatus = 'absent';
    if (cell.innerHTML.includes('+')) currentStatus = 'present';
    else if (cell.innerHTML.includes('-')) currentStatus = 'absent';
    else if (cell.innerHTML.includes('s')) currentStatus = 'excused';
    else if (cell.innerHTML.includes('k')) currentStatus = 'late';

    const currentIndex = statuses.indexOf(currentStatus);
    const newStatus = statuses[(currentIndex + 1) % statuses.length];

    cell.innerHTML = badges[newStatus];

    // Save to server
    // Implementation would go here
}

// Mark today's attendance
function markToday() {
    const today = new Date().toISOString().split('T')[0];
    const formattedDate = new Date().toLocaleDateString('uz-UZ', { year: 'numeric', month: 'long', day: 'numeric' });

    document.getElementById('todayDate').textContent = formattedDate;
    document.getElementById('lessonDate').value = today;

    const modal = new bootstrap.Modal(document.getElementById('markTodayModal'));
    modal.show();
}

// Mark all students as present
function markAllPresent() {
    document.querySelectorAll('.attendance-status').forEach(select => {
        select.value = 'present';
    });
}

// Mark all students as absent
function markAllAbsent() {
    document.querySelectorAll('.attendance-status').forEach(select => {
        select.value = 'absent';
    });
}

// Export attendance
function exportAttendance() {
    window.location.href = '{{ route("attendance.report", $journal->id ?? 0) }}';
}

// Show statistics
function showStatistics() {
    window.location.href = '{{ route("attendance.statistics", $journal->id ?? 0) }}';
}

// Cleanup on modal close
document.getElementById('faceRecognitionModal').addEventListener('hidden.bs.modal', function () {
    if (webcamStream) {
        webcamStream.getTracks().forEach(track => track.stop());
        webcamStream = null;
    }
    if (detectionInterval) {
        clearInterval(detectionInterval);
        detectionInterval = null;
    }
});
</script>
@endpush