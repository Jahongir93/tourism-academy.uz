@extends('layouts.dashboard-new')

@section('title', 'Talabalar')
@section('page-title', 'Talabalar ro\'yxati')

@section('content')

{{-- ── Stat Cards ── --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <a href="{{ route('students.index', ['status' => 'all']) }}" class="text-decoration-none d-block">
            <div class="stat-card {{ request('status') == 'all' ? 'ring-active' : '' }}" style="color:var(--c-sky)">
                <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ \App\Models\Student::count() }}</div>
                <div class="stat-card-label">Jami talabalar</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('students.index', ['status' => 'active']) }}" class="text-decoration-none d-block">
            <div class="stat-card {{ request('status') == 'active' || !request('status') ? 'ring-active' : '' }}" style="color:var(--c-emerald)">
                <div class="stat-card-icon" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ \App\Models\Student::where('status','active')->count() }}</div>
                <div class="stat-card-label">Faol</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('students.index', ['status' => 'graduated']) }}" class="text-decoration-none d-block">
            <div class="stat-card {{ request('status') == 'graduated' ? 'ring-active' : '' }}" style="color:var(--c-amber)">
                <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ \App\Models\Student::where('status','graduated')->count() }}</div>
                <div class="stat-card-label">Bitirgan</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('students.index', ['status' => 'expelled']) }}" class="text-decoration-none d-block">
            <div class="stat-card {{ request('status') == 'expelled' ? 'ring-active' : '' }}" style="color:var(--c-rose)">
                <div class="stat-card-icon" style="background:rgba(244,63,94,.12);color:var(--c-rose)">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ \App\Models\Student::where('status','expelled')->count() }}</div>
                <div class="stat-card-label">Chetlatilgan</div>
            </div>
        </a>
    </div>
</div>

{{-- ── Filters ── --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('students.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Talaba qidirish..."
                               class="form-control ps-4" style="padding-left:34px!important">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select name="faculty_id" class="form-select">
                        <option value="">Barcha fakultetlar</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name_uz ?? $faculty->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="group_id" class="form-select">
                        <option value="">Barcha guruhlar</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-1">
                    <select name="course" class="form-select">
                        <option value="">Kurs</option>
                        @for($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ request('course') == $i ? 'selected' : '' }}>{{ $i }}-kurs</option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Barcha holatlar</option>
                        <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Faol</option>
                        <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Bitirgan</option>
                        <option value="expelled"  {{ request('status') == 'expelled'  ? 'selected' : '' }}>Chetlatilgan</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-1"></i>Qidirish
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── Table ── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-user-graduate" style="color:var(--c-sky)"></i>
            <span>Talabalar ro'yxati</span>
            @if($students->total())
            <span class="badge" style="background:rgba(14,165,233,.12);color:var(--c-sky);font-size:11px">
                {{ number_format($students->total()) }} ta
            </span>
            @endif
        </div>
        <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Yangi talaba
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:48px">#</th>
                        <th>F.I.O</th>
                        <th>ID raqam</th>
                        <th class="d-none d-lg-table-cell">Fakultet</th>
                        <th>Guruh</th>
                        <th class="d-none d-sm-table-cell" style="width:80px">Kurs</th>
                        <th style="width:110px">Holat</th>
                        <th style="width:100px" class="text-center">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td style="color:var(--c-text-3);font-size:12px">
                            {{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}
                        </td>
                        <td>
                            <a href="{{ route('students.show', $student->id) }}"
                               class="text-decoration-none d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--c-sky),#38BDF8);display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0">
                                    {{ strtoupper(substr($student->last_name ?? 'T', 0, 1)) }}
                                </div>
                                <span style="font-weight:600;color:var(--c-text)">
                                    {{ $student->last_name }} {{ $student->first_name }}
                                </span>
                            </a>
                        </td>
                        <td>
                            <span class="badge" style="background:rgba(14,165,233,.1);color:var(--c-sky);font-size:11px">
                                {{ $student->student_id ?? '—' }}
                            </span>
                        </td>
                        <td class="d-none d-lg-table-cell" style="font-size:12px;color:var(--c-text-2)">
                            {{ optional($student->faculty)->name_uz ?? '—' }}
                        </td>
                        <td style="font-weight:500;color:var(--c-text)">
                            {{ optional($student->group)->name ?? '—' }}
                        </td>
                        <td class="d-none d-sm-table-cell" style="color:var(--c-text-2)">
                            {{ $student->course ? $student->course.'-kurs' : '—' }}
                        </td>
                        <td>
                            @if($student->status == 'active')
                                <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">Faol</span>
                            @elseif($student->status == 'graduated')
                                <span class="badge" style="background:rgba(14,165,233,.12);color:var(--c-sky)">Bitirgan</span>
                            @elseif($student->status == 'expelled')
                                <span class="badge" style="background:rgba(244,63,94,.12);color:var(--c-rose)">Chetlatilgan</span>
                            @else
                                <span class="badge" style="background:rgba(100,116,139,.12);color:var(--c-text-2)">{{ $student->status }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <a href="{{ route('students.show', $student->id) }}"
                                   class="action-btn" title="Ko'rish"
                                   style="color:var(--c-sky)">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('students.edit', $student->id) }}"
                                   class="action-btn" title="Tahrirlash"
                                   style="color:var(--c-amber)">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if($student->status == 'active')
                                    @if($student->has_face_registered ?? false)
                                        <button type="button" class="action-btn face-register-btn"
                                                title="Yuz ro'yxatdan o'tgan"
                                                style="color:var(--c-emerald)"
                                                data-student-id="{{ $student->id }}"
                                                data-student-name="{{ $student->last_name }} {{ $student->first_name }}"
                                                data-has-face="true"
                                                data-bs-toggle="modal" data-bs-target="#faceRegisterModal">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                    @else
                                        <button type="button" class="action-btn face-register-btn"
                                                title="Face Attendance ro'yxat"
                                                style="color:var(--c-violet)"
                                                data-student-id="{{ $student->id }}"
                                                data-student-name="{{ $student->last_name }} {{ $student->first_name }}"
                                                data-has-face="false"
                                                data-bs-toggle="modal" data-bs-target="#faceRegisterModal">
                                            <i class="fas fa-user-circle"></i>
                                        </button>
                                    @endif
                                @endif

                                @if($student->status !== 'expelled')
                                    <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Talabani chetlashtirishni tasdiqlaysizmi?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn" title="Chetlashtirish" style="color:var(--c-orange)">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('students.force-delete', $student->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('OGOHLANTIRISH!\n\nBu talabani BUTUNLAY o\'chiradi!\n\nDavom etasizmi?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn" title="Butunlay o'chirish" style="color:var(--c-rose)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state py-5">
                                <div class="empty-state-icon"><i class="fas fa-user-graduate"></i></div>
                                <div class="empty-state-sub">Talabalar topilmadi</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid var(--c-border)">
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ── Face Registration Modal ── --}}
<div class="modal fade" id="faceRegisterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border:1px solid var(--c-border);border-radius:var(--r-lg)">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--c-sky),#38BDF8);border-radius:var(--r-lg) var(--r-lg) 0 0">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user-circle me-2"></i>Face Attendance ro'yxatga olish
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="faceStudentId">
                <div class="alert mb-4" style="background:rgba(14,165,233,.08);border:1px solid rgba(14,165,233,.2);color:var(--c-text)">
                    <i class="fas fa-user me-2" style="color:var(--c-sky)"></i>
                    <strong>Talaba:</strong> <span id="faceStudentName"></span>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3" style="color:var(--c-text)">
                            <i class="fas fa-camera me-2" style="color:var(--c-emerald)"></i>Kamera
                        </h6>
                        <div class="position-relative rounded overflow-hidden" style="min-height:300px;background:#0F172A">
                            <video id="faceVideo" autoplay muted playsinline class="w-100" style="height:300px;object-fit:cover"></video>
                            <div id="faceVideoOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background:rgba(0,0,0,.8)">
                                <div class="text-center text-white">
                                    <i class="fas fa-camera fa-3x mb-3 opacity-50"></i>
                                    <p>Kamerani yoqish uchun tugmani bosing</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-sm" style="background:var(--c-emerald);color:#fff" id="startFaceCamera">
                                <i class="fas fa-play"></i> Boshlash
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="captureFacePhoto" disabled>
                                <i class="fas fa-camera"></i> Suratga olish
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="stopFaceCamera" disabled>
                                <i class="fas fa-stop"></i> To'xtatish
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3" style="color:var(--c-text)">
                            <i class="fas fa-images me-2" style="color:var(--c-sky)"></i>Olingan suratlar (kamida 3 ta)
                        </h6>
                        <div id="capturedPhotosContainer" class="d-flex flex-wrap gap-2 p-3 rounded" style="min-height:300px;background:var(--c-bg);border:1px solid var(--c-border)">
                            <div class="text-center w-100 d-flex align-items-center justify-content-center" style="color:var(--c-text-3)">
                                <div>
                                    <i class="fas fa-images fa-3x mb-2 opacity-25"></i>
                                    <p class="mb-0">Hali suratlar olinmagan</p>
                                </div>
                            </div>
                        </div>
                        <p class="mt-2" style="font-size:11px;color:var(--c-text-3)">
                            <i class="fas fa-info-circle me-1"></i>Yaxshi natija uchun turli burchaklardan kamida 3 ta surat oling
                        </p>
                    </div>
                </div>
                <div class="mt-4" id="faceRegisterProgress" style="display:none">
                    <div class="progress" style="height:22px;border-radius:999px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             style="background:var(--c-sky)"
                             role="progressbar" style="width:0%">
                            <span id="progressText">Saqlanmoqda...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--c-border)">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" id="saveFaceRegistration" disabled>
                    <i class="fas fa-save me-1"></i>Saqlash
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card.ring-active { outline: 2px solid var(--c-sky); outline-offset: 2px; }
.action-btn {
    display:inline-flex;align-items:center;justify-content:center;
    width:28px;height:28px;border-radius:6px;border:none;background:transparent;
    cursor:pointer;transition:all .15s;font-size:13px;padding:0;
}
.action-btn:hover { background:var(--c-bg); }
</style>

@endsection

@push('scripts')
<script>
const baseUrl = '{{ url('/') }}';
let faceStream = null;
let capturedFacePhotos = [];
let currentStudentHasFace = false;

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.face-register-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            document.getElementById('faceStudentId').value = this.dataset.studentId;
            document.getElementById('faceStudentName').textContent = this.dataset.studentName;
            currentStudentHasFace = this.dataset.hasFace === 'true';
            capturedFacePhotos = [];
            updateCapturedPhotosDisplay();
            document.getElementById('saveFaceRegistration').disabled = true;
            if (currentStudentHasFace) await loadExistingPhotos(this.dataset.studentId);
        });
    });

    document.getElementById('startFaceCamera').addEventListener('click', startFaceCamera);
    document.getElementById('captureFacePhoto').addEventListener('click', captureFacePhoto);
    document.getElementById('stopFaceCamera').addEventListener('click', stopFaceCamera);
    document.getElementById('saveFaceRegistration').addEventListener('click', saveFaceRegistration);
    document.getElementById('faceRegisterModal').addEventListener('hidden.bs.modal', stopFaceCamera);
});

async function startFaceCamera() {
    try {
        faceStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 }, audio: false });
        document.getElementById('faceVideo').srcObject = faceStream;
        document.getElementById('faceVideoOverlay').style.display = 'none';
        document.getElementById('startFaceCamera').disabled = true;
        document.getElementById('captureFacePhoto').disabled = false;
        document.getElementById('stopFaceCamera').disabled = false;
    } catch (error) { alert('Kamerani ochishda xatolik: ' + error.message); }
}

function stopFaceCamera() {
    if (faceStream) { faceStream.getTracks().forEach(t => t.stop()); faceStream = null; }
    document.getElementById('faceVideo').srcObject = null;
    document.getElementById('faceVideoOverlay').style.display = 'flex';
    document.getElementById('startFaceCamera').disabled = false;
    document.getElementById('captureFacePhoto').disabled = true;
    document.getElementById('stopFaceCamera').disabled = true;
}

function captureFacePhoto() {
    const video = document.getElementById('faceVideo');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth; canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    capturedFacePhotos.push(canvas.toDataURL('image/jpeg', 0.8));
    updateCapturedPhotosDisplay();
    if (capturedFacePhotos.length >= 3) document.getElementById('saveFaceRegistration').disabled = false;
}

function updateCapturedPhotosDisplay() {
    const container = document.getElementById('capturedPhotosContainer');
    if (!capturedFacePhotos.length) {
        container.innerHTML = '<div class="text-center w-100 d-flex align-items-center justify-content-center" style="color:var(--c-text-3)"><div><i class="fas fa-images fa-3x mb-2 opacity-25"></i><p class="mb-0">Hali suratlar olinmagan</p></div></div>';
        return;
    }
    container.innerHTML = capturedFacePhotos.map((photo, i) => `
        <div class="position-relative" style="width:100px;height:100px">
            <img src="${photo}" class="rounded" style="width:100%;height:100%;object-fit:cover;border:1px solid var(--c-border)">
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                    style="padding:2px 6px;font-size:10px" onclick="removeFacePhoto(${i})">
                <i class="fas fa-times"></i>
            </button>
            <span class="position-absolute bottom-0 start-0 badge bg-dark" style="font-size:10px">${i+1}</span>
        </div>`).join('');
}

function removeFacePhoto(index) {
    capturedFacePhotos.splice(index, 1);
    updateCapturedPhotosDisplay();
    if (capturedFacePhotos.length < 3) document.getElementById('saveFaceRegistration').disabled = true;
}

async function saveFaceRegistration() {
    const studentId = document.getElementById('faceStudentId').value;
    if (!studentId || capturedFacePhotos.length < 3) { alert('Kamida 3 ta surat kerak!'); return; }

    const progressDiv = document.getElementById('faceRegisterProgress');
    const progressBar = progressDiv.querySelector('.progress-bar');
    progressDiv.style.display = 'block';
    progressBar.style.width = '20%';
    document.getElementById('progressText').textContent = 'Serverga yuborilmoqda...';
    document.getElementById('saveFaceRegistration').disabled = true;

    try {
        const response = await fetch(baseUrl + '/api/face-attendance/student/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ student_id: studentId, images: capturedFacePhotos })
        });
        progressBar.style.width = '80%';
        document.getElementById('progressText').textContent = 'Saqlanmoqda...';
        const data = await response.json();
        if (data.success) {
            progressBar.style.width = '100%';
            document.getElementById('progressText').textContent = 'Muvaffaqiyatli!';
            setTimeout(() => {
                alert('Muvaffaqiyatli ro\'yxatdan o\'tkazildi!');
                bootstrap.Modal.getInstance(document.getElementById('faceRegisterModal')).hide();
                progressDiv.style.display = 'none';
                progressBar.style.width = '0%';
                capturedFacePhotos = [];
                updateCapturedPhotosDisplay();
            }, 500);
        } else throw new Error(data.message || 'Xatolik');
    } catch (error) {
        alert('Xatolik: ' + error.message);
        progressDiv.style.display = 'none';
        progressBar.style.width = '0%';
        document.getElementById('saveFaceRegistration').disabled = false;
    }
}

async function loadExistingPhotos(studentId) {
    try {
        const response = await fetch(baseUrl + `/api/face-attendance/student/${studentId}/photos`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await response.json();
        if (data.success && data.photos?.length) {
            const container = document.getElementById('capturedPhotosContainer');
            let html = '<div class="alert w-100 mb-2" style="background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);color:var(--c-text);font-size:12px"><i class="fas fa-exclamation-triangle me-1" style="color:var(--c-amber)"></i> Bu talaba allaqachon ro\'yxatdan o\'tgan. Qayta ro\'yxatdan o\'tkazish uchun yangi suratlar oling.</div>';
            html += '<div class="d-flex flex-wrap gap-2">';
            data.photos.forEach((url, i) => {
                html += `<div class="position-relative" style="width:100px;height:100px"><img src="${url}" class="rounded" style="width:100%;height:100%;object-fit:cover;border:1px solid var(--c-border)"><span class="position-absolute bottom-0 start-0 badge" style="background:var(--c-emerald);font-size:10px">Mavjud ${i+1}</span></div>`;
            });
            html += '</div>';
            container.innerHTML = html;
        }
    } catch(e) { console.error(e); }
}
</script>
@endpush
