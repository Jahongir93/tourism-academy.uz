@extends('layouts.app')

@section('title', 'Xodimlar davomati')

@push('styles')
<style>
    :root {
        --primary-dark-green: #0d4f3c;
        --secondary-green: #16a085;
        --light-green: #e8f5f0;
        --very-light-green: #f5faf8;
        --border-green: #a8d5c8;
        --text-dark: #2c3e50;
    }

    .staff-card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(13, 79, 60, 0.1);
        border: none;
        overflow: hidden;
    }

    .staff-card-header {
        background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green));
        color: white;
        padding: 15px 20px;
        font-weight: 600;
    }

    .staff-card-body {
        padding: 20px;
        background: white;
    }

    .camera-container {
        background: #1a1a2e;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
        min-height: 400px;
    }

    .camera-video {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }

    .staff-list-item {
        padding: 12px 15px;
        border-bottom: 1px solid var(--border-green);
        transition: all 0.2s;
    }

    .staff-list-item:hover {
        background: var(--very-light-green);
    }

    .staff-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-green);
    }

    .staff-avatar-placeholder {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: var(--light-green);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-green);
        font-weight: 600;
    }

    .stat-card {
        text-align: center;
        padding: 15px;
        border-radius: 10px;
        background: var(--very-light-green);
    }

    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-dark-green);
    }

    .btn-register {
        background: var(--secondary-green);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-register:hover {
        background: var(--primary-dark-green);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0" style="color: var(--primary-dark-green);">
                <i class="fas fa-user-tie me-2"></i> Xodimlar va O'qituvchilar Davomati
            </h2>
            <p class="text-muted">Yuz orqali davomat tizimi</p>
        </div>
    </div>

    <div class="row">
        <!-- Camera Section -->
        <div class="col-lg-6 mb-4">
            <div class="staff-card">
                <div class="staff-card-header">
                    <i class="fas fa-camera me-2"></i> Kamera
                </div>
                <div class="staff-card-body">
                    <div class="camera-container">
                        <video id="staffVideo" class="camera-video" autoplay muted playsinline></video>
                        <canvas id="staffCanvas" style="display: none;"></canvas>
                        <div id="cameraOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.8);">
                            <div class="text-center text-white">
                                <i class="fas fa-video fa-4x mb-3 opacity-50"></i>
                                <p>Kamerani yoqish uchun tugmani bosing</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-success" id="startStaffCamera">
                            <i class="fas fa-play"></i> Kamerani yoqish
                        </button>
                        <button type="button" class="btn btn-danger" id="stopStaffCamera" disabled>
                            <i class="fas fa-stop"></i> To'xtatish
                        </button>
                    </div>

                    <!-- Status -->
                    <div class="mt-3 text-center">
                        <span id="cameraStatus" class="badge bg-secondary">Kamera o'chirilgan</span>
                    </div>
                </div>
            </div>

            <!-- Manual Check-in -->
            <div class="staff-card mt-4">
                <div class="staff-card-header">
                    <i class="fas fa-hand-point-up me-2"></i> Qo'lda davomat belgilash
                </div>
                <div class="staff-card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <select class="form-select" id="manualStaffSelect">
                                <option value="">Xodimni tanlang...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-success w-100" id="manualCheckIn" disabled>
                                <i class="fas fa-sign-in-alt"></i> Kirish
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff List & Stats -->
        <div class="col-lg-6">
            <!-- Statistics -->
            <div class="staff-card mb-4">
                <div class="staff-card-header">
                    <i class="fas fa-chart-bar me-2"></i> Bugungi statistika
                </div>
                <div class="staff-card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="stat-card">
                                <div class="stat-number" id="statTotal">0</div>
                                <small class="text-muted">Jami keldi</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-card" style="background: #d4edda;">
                                <div class="stat-number text-success" id="statOnTime">0</div>
                                <small class="text-muted">O'z vaqtida</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-card" style="background: #f8d7da;">
                                <div class="stat-number text-danger" id="statLate">0</div>
                                <small class="text-muted">Kechikdi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Attendance -->
            <div class="staff-card">
                <div class="staff-card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list me-2"></i> Bugungi davomat</span>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="roleFilter" style="width: auto; background: rgba(255,255,255,0.9);">
                            <option value="">Barcha xodimlar</option>
                            <option value="teacher">O'qituvchilar</option>
                            <option value="staff">Xodimlar</option>
                            <option value="admin">Adminlar</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-light" onclick="refreshStaffAttendance()">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                </div>
                <div class="staff-card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <div id="staffAttendanceList">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-clipboard fa-3x mb-3 opacity-25"></i>
                            <p>Bugun hali davomat yo'q</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Register New Staff -->
            <div class="mt-4">
                <button type="button" class="btn btn-register w-100" data-bs-toggle="modal" data-bs-target="#staffRegisterModal">
                    <i class="fas fa-user-plus me-2"></i> Yangi xodim ro'yxatdan o'tkazish
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Staff Registration Modal -->
<div class="modal fade" id="staffRegisterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green)); color: white;">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> Xodimni ro'yxatdan o'tkazish</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3"><i class="fas fa-user me-2 text-success"></i> Xodim ma'lumotlari</h6>
                        <div class="mb-3">
                            <label class="form-label">Xodimni tanlang</label>
                            <select class="form-select" id="staffSelect">
                                <option value="">Xodimni tanlang...</option>
                            </select>
                        </div>
                        <div id="selectedStaffInfo" style="display: none;">
                            <div class="alert alert-info">
                                <strong id="staffInfoName"></strong><br>
                                <small id="staffInfoRole" class="text-muted"></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3"><i class="fas fa-camera me-2 text-success"></i> Yuz suratlari</h6>
                        <div class="position-relative bg-dark rounded" style="min-height: 200px;">
                            <video id="staffRegVideo" autoplay muted playsinline class="w-100 rounded" style="height: 200px; object-fit: cover;"></video>
                            <div id="staffRegOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center rounded" style="background: rgba(0,0,0,0.8);">
                                <div class="text-center text-white">
                                    <i class="fas fa-camera fa-2x mb-2 opacity-50"></i>
                                    <p class="small">Kamerani yoqing</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-success btn-sm" id="startRegCamera">
                                <i class="fas fa-play"></i>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="captureStaffPhoto" disabled>
                                <i class="fas fa-camera"></i>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="stopRegCamera" disabled>
                                <i class="fas fa-stop"></i>
                            </button>
                        </div>
                        <div id="capturedStaffPhotos" class="d-flex flex-wrap gap-2 mt-3">
                            <!-- Captured photos will appear here -->
                        </div>
                        <small class="text-muted">Kamida 3 ta surat oling</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-success" id="saveStaffRegistration" disabled>
                    <i class="fas fa-save"></i> Saqlash
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Base URL for API calls
const baseUrl = '{{ url('/') }}';

let staffStream = null;
let regStream = null;
let staffPhotos = [];

document.addEventListener('DOMContentLoaded', function() {
    loadStaffList();
    loadTodayAttendance();

    // Camera controls
    document.getElementById('startStaffCamera').addEventListener('click', startCamera);
    document.getElementById('stopStaffCamera').addEventListener('click', stopCamera);

    // Manual check-in
    document.getElementById('manualStaffSelect').addEventListener('change', function() {
        document.getElementById('manualCheckIn').disabled = !this.value;
    });
    document.getElementById('manualCheckIn').addEventListener('click', manualCheckIn);

    // Registration modal
    document.getElementById('staffSelect').addEventListener('change', onStaffSelect);
    document.getElementById('startRegCamera').addEventListener('click', startRegCamera);
    document.getElementById('captureStaffPhoto').addEventListener('click', capturePhoto);
    document.getElementById('stopRegCamera').addEventListener('click', stopRegCamera);
    document.getElementById('saveStaffRegistration').addEventListener('click', saveRegistration);

    // Role filter
    document.getElementById('roleFilter').addEventListener('change', loadTodayAttendance);

    // Cleanup on modal close
    document.getElementById('staffRegisterModal').addEventListener('hidden.bs.modal', function() {
        stopRegCamera();
        staffPhotos = [];
        updatePhotosDisplay();
    });
});

// Load staff list for dropdowns
async function loadStaffList() {
    try {
        const response = await fetch(baseUrl + '/api/face-attendance/staff/list', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();

        if (data.success && data.staff) {
            const manualSelect = document.getElementById('manualStaffSelect');
            const regSelect = document.getElementById('staffSelect');

            let options = '<option value="">Xodimni tanlang...</option>';
            data.staff.forEach(staff => {
                const hasFace = staff.has_face_registered ? ' (Ro\'yxatdan o\'tgan)' : '';
                options += `<option value="${staff.id}" data-name="${staff.name}" data-role="${staff.role}">${staff.name}${hasFace}</option>`;
            });

            manualSelect.innerHTML = options;
            regSelect.innerHTML = options;
        }
    } catch (error) {
        console.error('Staff list error:', error);
    }
}

// Load today's attendance
async function loadTodayAttendance() {
    try {
        const role = document.getElementById('roleFilter').value;
        const params = role ? `?role=${role}` : '';

        const response = await fetch(baseUrl + `/api/face-attendance/staff/today-attendance${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();

        if (data.success) {
            updateStats(data.stats);
            updateAttendanceList(data.attendances);
        }
    } catch (error) {
        console.error('Attendance load error:', error);
    }
}

function refreshStaffAttendance() {
    loadTodayAttendance();
}

function updateStats(stats) {
    document.getElementById('statTotal').textContent = stats.total || 0;
    document.getElementById('statOnTime').textContent = (stats.early || 0) + (stats.present || 0);
    document.getElementById('statLate').textContent = (stats.late || 0) + (stats.very_late || 0);
}

function updateAttendanceList(attendances) {
    const container = document.getElementById('staffAttendanceList');

    if (!attendances || attendances.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="fas fa-clipboard fa-3x mb-3 opacity-25"></i>
                <p>Bugun hali davomat yo'q</p>
            </div>
        `;
        return;
    }

    const statusLabels = {
        'early': 'Erta',
        'present': 'O\'z vaqtida',
        'late': 'Kechikdi',
        'very_late': 'Juda kechikdi'
    };

    const statusColors = {
        'early': 'success',
        'present': 'primary',
        'late': 'warning',
        'very_late': 'danger'
    };

    let html = '';
    attendances.forEach(att => {
        const initials = att.staff_name ? att.staff_name.split(' ').map(n => n[0]).join('').substring(0, 2) : '??';
        const checkIn = att.check_in_time ? att.check_in_time.substring(0, 5) : '-';
        const checkOut = att.check_out_time ? att.check_out_time.substring(0, 5) : '-';

        html += `
            <div class="staff-list-item d-flex align-items-center">
                <div class="staff-avatar-placeholder me-3">${initials}</div>
                <div class="flex-grow-1">
                    <strong>${att.staff_name}</strong>
                    <br>
                    <small class="text-muted">${att.role || 'Xodim'}</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-${statusColors[att.status] || 'secondary'}">${statusLabels[att.status] || att.status}</span>
                    <br>
                    <small class="text-muted">${checkIn} - ${checkOut}</small>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Camera functions
async function startCamera() {
    try {
        staffStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: 640, height: 480 }
        });
        document.getElementById('staffVideo').srcObject = staffStream;
        document.getElementById('cameraOverlay').style.display = 'none';
        document.getElementById('startStaffCamera').disabled = true;
        document.getElementById('stopStaffCamera').disabled = false;
        document.getElementById('cameraStatus').className = 'badge bg-success';
        document.getElementById('cameraStatus').textContent = 'Kamera yoqilgan';
    } catch (error) {
        alert('Kamerani ochishda xatolik: ' + error.message);
    }
}

function stopCamera() {
    if (staffStream) {
        staffStream.getTracks().forEach(track => track.stop());
        staffStream = null;
    }
    document.getElementById('staffVideo').srcObject = null;
    document.getElementById('cameraOverlay').style.display = 'flex';
    document.getElementById('startStaffCamera').disabled = false;
    document.getElementById('stopStaffCamera').disabled = true;
    document.getElementById('cameraStatus').className = 'badge bg-secondary';
    document.getElementById('cameraStatus').textContent = 'Kamera o\'chirilgan';
}

// Manual check-in
async function manualCheckIn() {
    const staffId = document.getElementById('manualStaffSelect').value;
    if (!staffId) return;

    try {
        const response = await fetch(baseUrl + '/api/face-attendance/staff/recognize-and-mark', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ staff_id: staffId, image: '' })
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message + ' - ' + data.time);
            loadTodayAttendance();
        } else {
            alert('Xatolik: ' + data.message);
        }
    } catch (error) {
        alert('Xatolik: ' + error.message);
    }
}

// Registration functions
function onStaffSelect() {
    const select = document.getElementById('staffSelect');
    const option = select.options[select.selectedIndex];

    if (select.value) {
        document.getElementById('selectedStaffInfo').style.display = 'block';
        document.getElementById('staffInfoName').textContent = option.dataset.name;
        document.getElementById('staffInfoRole').textContent = option.dataset.role || 'Xodim';
    } else {
        document.getElementById('selectedStaffInfo').style.display = 'none';
    }
}

async function startRegCamera() {
    try {
        regStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: 640, height: 480 }
        });
        document.getElementById('staffRegVideo').srcObject = regStream;
        document.getElementById('staffRegOverlay').style.display = 'none';
        document.getElementById('startRegCamera').disabled = true;
        document.getElementById('captureStaffPhoto').disabled = false;
        document.getElementById('stopRegCamera').disabled = false;
    } catch (error) {
        alert('Kamerani ochishda xatolik: ' + error.message);
    }
}

function stopRegCamera() {
    if (regStream) {
        regStream.getTracks().forEach(track => track.stop());
        regStream = null;
    }
    document.getElementById('staffRegVideo').srcObject = null;
    document.getElementById('staffRegOverlay').style.display = 'flex';
    document.getElementById('startRegCamera').disabled = false;
    document.getElementById('captureStaffPhoto').disabled = true;
    document.getElementById('stopRegCamera').disabled = true;
}

function capturePhoto() {
    const video = document.getElementById('staffRegVideo');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    staffPhotos.push(canvas.toDataURL('image/jpeg', 0.8));
    updatePhotosDisplay();

    if (staffPhotos.length >= 3) {
        document.getElementById('saveStaffRegistration').disabled = !document.getElementById('staffSelect').value;
    }
}

function updatePhotosDisplay() {
    const container = document.getElementById('capturedStaffPhotos');
    let html = '';
    staffPhotos.forEach((photo, index) => {
        html += `
            <div class="position-relative" style="width: 60px; height: 60px;">
                <img src="${photo}" class="rounded" style="width: 100%; height: 100%; object-fit: cover;">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="padding: 0 4px; font-size: 10px;" onclick="removePhoto(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    });
    container.innerHTML = html;
}

function removePhoto(index) {
    staffPhotos.splice(index, 1);
    updatePhotosDisplay();
    if (staffPhotos.length < 3) {
        document.getElementById('saveStaffRegistration').disabled = true;
    }
}

async function saveRegistration() {
    const staffId = document.getElementById('staffSelect').value;
    if (!staffId || staffPhotos.length < 3) {
        alert('Xodimni tanlang va kamida 3 ta surat oling!');
        return;
    }

    try {
        document.getElementById('saveStaffRegistration').disabled = true;

        const response = await fetch(baseUrl + '/api/face-attendance/staff/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ staff_id: staffId, images: staffPhotos })
        });

        const data = await response.json();

        if (data.success) {
            alert('Xodim muvaffaqiyatli ro\'yxatdan o\'tkazildi!');
            bootstrap.Modal.getInstance(document.getElementById('staffRegisterModal')).hide();
            loadStaffList();
        } else {
            alert('Xatolik: ' + data.message);
            document.getElementById('saveStaffRegistration').disabled = false;
        }
    } catch (error) {
        alert('Xatolik: ' + error.message);
        document.getElementById('saveStaffRegistration').disabled = false;
    }
}
</script>
@endpush
