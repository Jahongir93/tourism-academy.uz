@extends('layouts.dashboard-new')

@section('title', 'Foydalanuvchilar boshqaruvi')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Foydalanuvchilar boshqaruvi</h1>
        <a href="{{ route('admin.settings.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yangi foydalanuvchi
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ism</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Yaratilgan</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->roles->count())
                                        <span class="badge bg-primary">{{ $user->roles->first()->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Rol yo'q</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.settings.users.edit', $user) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Tahrirlash
                                    </a>
                                    {{-- Face Registration button --}}
                                    <button type="button"
                                            class="btn btn-sm btn-info face-register-btn"
                                            title="Face Attendance ro'yxatga olish"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#faceRegisterModal">
                                        <i class="fas fa-user-circle"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.settings.users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                                                <i class="fas fa-trash"></i> O'chirish
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Foydalanuvchilar topilmadi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Face Registration Modal --}}
<div class="modal fade" id="faceRegisterModal" tabindex="-1" aria-labelledby="faceRegisterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="faceRegisterModalLabel">
                    <i class="fas fa-user-circle me-2"></i> Face Attendance ro'yxatga olish
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="faceUserId">

                {{-- User Info --}}
                <div class="alert alert-info mb-4">
                    <i class="fas fa-user me-2"></i>
                    <strong>Xodim:</strong> <span id="faceUserName"></span>
                </div>

                {{-- Camera Section --}}
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3"><i class="fas fa-camera me-2 text-success"></i>Kamera</h6>
                        <div class="position-relative bg-dark rounded overflow-hidden" style="min-height: 300px;">
                            <video id="faceVideo" autoplay muted playsinline class="w-100" style="height: 300px; object-fit: cover;"></video>
                            <div id="faceVideoOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.8);">
                                <div class="text-center text-white">
                                    <i class="fas fa-camera fa-3x mb-3 opacity-50"></i>
                                    <p>Kamerani yoqish uchun tugmani bosing</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-success btn-sm" id="startFaceCamera">
                                <i class="fas fa-play"></i> Boshlash
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="captureFacePhoto" disabled>
                                <i class="fas fa-camera"></i> Suratga olish
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="stopFaceCamera" disabled>
                                <i class="fas fa-stop"></i> To'xtatish
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3"><i class="fas fa-images me-2 text-primary"></i>Olingan suratlar (kamida 3 ta)</h6>
                        <div id="capturedPhotosContainer" class="d-flex flex-wrap gap-2 p-3 border rounded" style="min-height: 300px; background: #f8f9fa;">
                            <div class="text-center text-muted w-100 d-flex align-items-center justify-content-center">
                                <div>
                                    <i class="fas fa-images fa-3x mb-2 opacity-25"></i>
                                    <p class="mb-0">Hali suratlar olinmagan</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Yaxshi natija uchun turli burchaklardan kamida 3 ta surat oling
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Progress --}}
                <div class="mt-4" id="faceRegisterProgress" style="display: none;">
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 0%">
                            <span id="progressText">Saqlanmoqda...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-info" id="saveFaceRegistration" disabled>
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

// Face Registration Variables
let faceStream = null;
let capturedFacePhotos = [];

// Face Registration Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Face register button click
    document.querySelectorAll('.face-register-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            document.getElementById('faceUserId').value = userId;
            document.getElementById('faceUserName').textContent = userName;

            // Reset
            capturedFacePhotos = [];
            updateCapturedPhotosDisplay();
            document.getElementById('saveFaceRegistration').disabled = true;
        });
    });

    // Start camera
    document.getElementById('startFaceCamera').addEventListener('click', startFaceCamera);

    // Capture photo
    document.getElementById('captureFacePhoto').addEventListener('click', captureFacePhoto);

    // Stop camera
    document.getElementById('stopFaceCamera').addEventListener('click', stopFaceCamera);

    // Save registration
    document.getElementById('saveFaceRegistration').addEventListener('click', saveFaceRegistration);

    // Modal close - stop camera
    document.getElementById('faceRegisterModal').addEventListener('hidden.bs.modal', function() {
        stopFaceCamera();
    });
});

// Start Face Camera
async function startFaceCamera() {
    try {
        faceStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: 640, height: 480 },
            audio: false
        });

        const video = document.getElementById('faceVideo');
        video.srcObject = faceStream;

        document.getElementById('faceVideoOverlay').style.display = 'none';
        document.getElementById('startFaceCamera').disabled = true;
        document.getElementById('captureFacePhoto').disabled = false;
        document.getElementById('stopFaceCamera').disabled = false;

    } catch (error) {
        console.error('Kamera xatoligi:', error);
        alert('Kamerani ochishda xatolik: ' + error.message);
    }
}

// Stop Face Camera
function stopFaceCamera() {
    if (faceStream) {
        faceStream.getTracks().forEach(track => track.stop());
        faceStream = null;
    }

    document.getElementById('faceVideo').srcObject = null;
    document.getElementById('faceVideoOverlay').style.display = 'flex';
    document.getElementById('startFaceCamera').disabled = false;
    document.getElementById('captureFacePhoto').disabled = true;
    document.getElementById('stopFaceCamera').disabled = true;
}

// Capture Face Photo
function captureFacePhoto() {
    const video = document.getElementById('faceVideo');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);

    const imageData = canvas.toDataURL('image/jpeg', 0.8);
    capturedFacePhotos.push(imageData);

    updateCapturedPhotosDisplay();

    // Enable save button if enough photos
    if (capturedFacePhotos.length >= 3) {
        document.getElementById('saveFaceRegistration').disabled = false;
    }
}

// Update captured photos display
function updateCapturedPhotosDisplay() {
    const container = document.getElementById('capturedPhotosContainer');

    if (capturedFacePhotos.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted w-100 d-flex align-items-center justify-content-center">
                <div>
                    <i class="fas fa-images fa-3x mb-2 opacity-25"></i>
                    <p class="mb-0">Hali suratlar olinmagan</p>
                </div>
            </div>
        `;
        return;
    }

    let html = '';
    capturedFacePhotos.forEach((photo, index) => {
        html += `
            <div class="position-relative" style="width: 100px; height: 100px;">
                <img src="${photo}" class="rounded border" style="width: 100%; height: 100%; object-fit: cover;">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                        style="padding: 2px 6px; font-size: 10px;"
                        onclick="removeFacePhoto(${index})">
                    <i class="fas fa-times"></i>
                </button>
                <span class="position-absolute bottom-0 start-0 badge bg-dark" style="font-size: 10px;">
                    ${index + 1}
                </span>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Remove captured photo
function removeFacePhoto(index) {
    capturedFacePhotos.splice(index, 1);
    updateCapturedPhotosDisplay();

    // Disable save button if not enough photos
    if (capturedFacePhotos.length < 3) {
        document.getElementById('saveFaceRegistration').disabled = true;
    }
}

// Save Face Registration
async function saveFaceRegistration() {
    const userId = document.getElementById('faceUserId').value;

    if (!userId) {
        alert('Xodim tanlanmagan!');
        return;
    }

    if (capturedFacePhotos.length < 3) {
        alert('Kamida 3 ta surat kerak!');
        return;
    }

    // Show progress
    const progressDiv = document.getElementById('faceRegisterProgress');
    const progressBar = progressDiv.querySelector('.progress-bar');
    const progressText = document.getElementById('progressText');

    progressDiv.style.display = 'block';
    progressBar.style.width = '20%';
    progressText.textContent = 'Serverga yuborilmoqda...';

    document.getElementById('saveFaceRegistration').disabled = true;

    try {
        const response = await fetch(baseUrl + '/api/face-attendance/staff/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                staff_id: userId,
                images: capturedFacePhotos
            })
        });

        progressBar.style.width = '80%';
        progressText.textContent = 'Saqlanmoqda...';

        const data = await response.json();

        if (data.success) {
            progressBar.style.width = '100%';
            progressText.textContent = 'Muvaffaqiyatli!';

            setTimeout(() => {
                alert('Xodim Face Attendance tizimiga muvaffaqiyatli ro\'yxatdan o\'tkazildi!');

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('faceRegisterModal'));
                modal.hide();

                // Reset
                progressDiv.style.display = 'none';
                progressBar.style.width = '0%';
                capturedFacePhotos = [];
                updateCapturedPhotosDisplay();

            }, 500);

        } else {
            throw new Error(data.message || 'Xatolik yuz berdi');
        }

    } catch (error) {
        console.error('Face registration xatoligi:', error);
        alert('Xatolik: ' + error.message);

        progressDiv.style.display = 'none';
        progressBar.style.width = '0%';
        document.getElementById('saveFaceRegistration').disabled = false;
    }
}
</script>
@endpush
