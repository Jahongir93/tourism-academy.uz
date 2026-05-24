<!-- Face Attendance Widget Component -->
<div class="card h-100" style="border-radius: 12px; border: 1px solid #e1e8ed; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
    <div class="card-header" style="background: linear-gradient(135deg, #0d4f3c 0%, #16a085 100%); border-radius: 12px 12px 0 0; border: none;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white">
                <i class="fas fa-user-clock"></i> Davomat
            </h5>
            <small class="text-white">{{ now()->format('d/m/Y') }}</small>
        </div>
    </div>
    <div class="card-body">
        @if($hasRegisteredFace ?? false)
            <!-- Today's Attendance Status -->
            @if($todayAttendance ?? false)
                <div class="text-center mb-3">
                    <h6 style="color: #2c3e50; font-weight: 600;">Bugungi davomat</h6>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Kirish</small>
                            <div class="h4" style="color: #16a085;">
                                {{ $todayAttendance['check_in'] ?? '--:--' }}
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Chiqish</small>
                            <div class="h4" style="color: #e74c3c;">
                                {{ $todayAttendance['check_out'] ?? '--:--' }}
                            </div>
                        </div>
                    </div>
                    @if($todayAttendance['working_hours'] ?? false)
                        <div class="mt-2">
                            <small class="text-muted">Ish vaqti:</small>
                            <strong style="color: #2c3e50;">{{ $todayAttendance['working_hours'] }}</strong>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="d-grid gap-2">
                    @if(!$todayAttendance['check_in'])
                        <button class="btn" style="background: #0d4f3c; color: white; border-radius: 8px; border: none; transition: all 0.3s;"
                               onmouseover="this.style.background='#16a085'" onmouseout="this.style.background='#0d4f3c'"
                               onclick="openAttendanceModal('check-in')">
                            <i class="fas fa-sign-in-alt"></i> Check In
                        </button>
                    @elseif(!$todayAttendance['check_out'])
                        <button class="btn" style="background: #e74c3c; color: white; border-radius: 8px; border: none; transition: all 0.3s;"
                               onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='#e74c3c'"
                               onclick="openAttendanceModal('check-out')">
                            <i class="fas fa-sign-out-alt"></i> Check Out
                        </button>
                    @else
                        <div class="alert" style="background: #e8f5f0; color: #2c3e50; border: none; border-radius: 8px;">
                            <i class="fas fa-check-circle" style="color: #16a085;"></i> Bugungi davomat yakunlandi
                        </div>
                    @endif
                </div>
            @else
                <!-- No attendance yet -->
                <div class="text-center">
                    <div class="mb-3 d-inline-flex align-items-center justify-content-center"
                         style="width: 64px; height: 64px; background: #e8f5f0; border-radius: 12px;">
                        <i class="fas fa-user-clock fs-3" style="color: #16a085;"></i>
                    </div>
                    <p style="color: #2c3e50;">Bugun hali kirmadingiz</p>
                    <button class="btn btn-lg" style="background: #0d4f3c; color: white; border-radius: 8px; border: none; transition: all 0.3s;"
                           onmouseover="this.style.background='#16a085'" onmouseout="this.style.background='#0d4f3c'"
                           onclick="openAttendanceModal('check-in')">
                        <i class="fas fa-sign-in-alt"></i> Check In qilish
                    </button>
                </div>
            @endif

            <!-- Attendance History -->
            @if($attendanceHistory ?? false)
                <hr style="border-color: #e8f5f0;">
                <h6 style="color: #2c3e50; font-weight: 600;">Oxirgi 7 kunlik davomat</h6>
                <div class="attendance-history">
                    @foreach($attendanceHistory as $record)
                        <div class="d-flex justify-content-between border-bottom py-1" style="border-color: #e8f5f0 !important;">
                            <small style="color: #2c3e50;">{{ $record['date'] }} ({{ $record['day'] }})</small>
                            <small>
                                @if($record['status'] == 'present')
                                    <span class="badge" style="background: #e8f5f0; color: #16a085; border-radius: 6px;">Keldi</span>
                                @elseif($record['status'] == 'late')
                                    <span class="badge" style="background: #fff3cd; color: #f39c12; border-radius: 6px;">Kechikdi</span>
                                @else
                                    <span class="badge" style="background: #fef0f0; color: #e74c3c; border-radius: 6px;">Kelmadi</span>
                                @endif
                            </small>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <!-- Face not registered -->
            <div class="text-center">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center"
                     style="width: 64px; height: 64px; background: #f0f9f6; border-radius: 12px;">
                    <i class="fas fa-user-slash fs-3" style="color: #f39c12;"></i>
                </div>
                <h6 style="color: #2c3e50; font-weight: 600;">Yuzingiz ro'yxatdan o'tmagan</h6>
                <p class="text-muted">Davomat tizimidan foydalanish uchun yuzingizni ro'yxatdan o'tkazing</p>
                <a href="{{ route('attendance.face-recognition') }}" class="btn" style="background: #0d4f3c; color: white; border-radius: 8px; border: none; transition: all 0.3s;"
                   onmouseover="this.style.background='#16a085'" onmouseout="this.style.background='#0d4f3c'">
                    <i class="fas fa-camera"></i> Yuzni ro'yxatdan o'tkazish
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0d4f3c 0%, #16a085 100%); border-radius: 12px 12px 0 0; border: none;">
                <h5 class="modal-title text-white" id="attendanceModalTitle">Davomat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <video id="modalVideo" width="100%" style="max-width: 500px; border-radius: 12px;" autoplay></video>
                    <canvas id="modalCanvas" style="display:none;"></canvas>
                </div>
                <div id="attendanceResult" class="mt-3"></div>
            </div>
            <div class="modal-footer" style="background: #f0f9f6; border-radius: 0 0 12px 12px; border: none;">
                <button type="button" class="btn" style="background: #e8f5f0; color: #2c3e50; border-radius: 8px; border: none;" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn" id="captureBtn" style="background: #0d4f3c; color: white; border-radius: 8px; border: none; transition: all 0.3s;"
                       onmouseover="this.style.background='#16a085'" onmouseout="this.style.background='#0d4f3c'"
                       onclick="captureAndSubmit()">
                    <i class="fas fa-camera"></i> Suratga olish
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentAction = '';
let modalStream = null;

function openAttendanceModal(action) {
    currentAction = action;
    document.getElementById('attendanceModalTitle').textContent =
        action === 'check-in' ? 'Check In' : 'Check Out';

    const modal = new bootstrap.Modal(document.getElementById('attendanceModal'));
    modal.show();

    // Start camera
    startModalCamera();
}

async function startModalCamera() {
    try {
        const video = document.getElementById('modalVideo');
        modalStream = await navigator.mediaDevices.getUserMedia({
            video: { width: 640, height: 480 }
        });
        video.srcObject = modalStream;
    } catch (err) {
        console.error('Camera error:', err);
        document.getElementById('attendanceResult').innerHTML =
            '<div class="alert" style="background: #fef0f0; color: #e74c3c; border: none; border-radius: 8px;">Kamera ruxsati berilmadi!</div>';
    }
}

async function captureAndSubmit() {
    const video = document.getElementById('modalVideo');
    const canvas = document.getElementById('modalCanvas');
    const context = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0);

    const imageBase64 = canvas.toDataURL('image/jpeg').split(',')[1];

    document.getElementById('captureBtn').disabled = true;
    document.getElementById('attendanceResult').innerHTML =
        '<div class="alert" style="background: #e8f5f0; color: #2c3e50; border: none; border-radius: 8px;">Yuborilmoqda...</div>';

    try {
        const response = await fetch(`/api/face-attendance/attendance/${currentAction}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                image: imageBase64,
                location: 'Dashboard'
            })
        });

        const result = await response.json();

        if (result.success) {
            document.getElementById('attendanceResult').innerHTML =
                `<div class="alert" style="background: #e8f5f0; color: #16a085; border: none; border-radius: 8px;">
                    <i class="fas fa-check-circle"></i> ${result.message}
                    <br>Vaqt: ${result.data.check_in_time || result.data.check_out_time}
                </div>`;

            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            document.getElementById('attendanceResult').innerHTML =
                `<div class="alert" style="background: #fef0f0; color: #e74c3c; border: none; border-radius: 8px;">
                    <i class="fas fa-exclamation-triangle"></i> ${result.message}
                </div>`;
            document.getElementById('captureBtn').disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('attendanceResult').innerHTML =
            '<div class="alert" style="background: #fef0f0; color: #e74c3c; border: none; border-radius: 8px;">Xatolik yuz berdi!</div>';
        document.getElementById('captureBtn').disabled = false;
    }
}

// Stop camera when modal closes
document.getElementById('attendanceModal').addEventListener('hidden.bs.modal', function () {
    if (modalStream) {
        modalStream.getTracks().forEach(track => track.stop());
        modalStream = null;
    }
});
</script>