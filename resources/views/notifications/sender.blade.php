@extends('layouts.dashboard-new')

@section('title', 'Xabar yuborish')
@section('page-title', 'Talabalarga xabar yuborish')

@section('content')
<style>
    .sender-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .target-option {
        padding: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .target-option:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    .target-option.selected {
        border-color: #3b82f6;
        background: #dbeafe;
    }
    .target-option i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .history-item {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .history-item:last-child {
        border-bottom: none;
    }
</style>

<div class="row">
    <div class="col-lg-8">
        <div class="sender-card p-4 mb-4">
            <h5 class="mb-4"><i class="fas fa-paper-plane text-primary me-2"></i> Yangi xabar yuborish</h5>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('notifications.send') }}" method="POST" id="notificationForm">
                @csrf

                <!-- Target Selection -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Qabul qiluvchilar</label>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="target-option text-center selected" data-target="all" onclick="selectTarget('all')">
                                <i class="fas fa-users text-primary"></i>
                                <div class="fw-medium">Barcha talabalar</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="target-option text-center" data-target="faculty" onclick="selectTarget('faculty')">
                                <i class="fas fa-building text-success"></i>
                                <div class="fw-medium">Fakultet</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="target-option text-center" data-target="group" onclick="selectTarget('group')">
                                <i class="fas fa-user-friends text-warning"></i>
                                <div class="fw-medium">Guruh</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="target-option text-center" data-target="custom" onclick="selectTarget('custom')">
                                <i class="fas fa-user-check text-info"></i>
                                <div class="fw-medium">Tanlangan</div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="target" id="targetInput" value="all">
                </div>

                <!-- Faculty Selection -->
                <div class="mb-4" id="facultySection" style="display: none;">
                    <label class="form-label fw-semibold">Fakultet tanlang</label>
                    <select name="faculty_id" id="facultySelect" class="form-select">
                        <option value="">-- Fakultet tanlang --</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}">{{ $faculty->name_uz }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Group Selection -->
                <div class="mb-4" id="groupSection" style="display: none;">
                    <label class="form-label fw-semibold">Guruh tanlang</label>
                    <select name="group_id" id="groupSelect" class="form-select">
                        <option value="">-- Guruh tanlang --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->specialty->faculty->name_uz ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Custom Selection (simplified) -->
                <div class="mb-4" id="customSection" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Maxsus tanlash uchun avval fakultet yoki guruh tanlang
                    </div>
                </div>

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sarlavha <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Xabar sarlavhasi" required maxlength="255">
                    @error('title')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Message -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Xabar matni <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-control" rows="4" placeholder="Xabar matnini kiriting..." required maxlength="1000"></textarea>
                    <div class="text-muted small mt-1">Maksimal 1000 belgi</div>
                    @error('message')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Preview -->
                <div class="mb-4 p-3 bg-light rounded" id="preview" style="display: none;">
                    <h6 class="text-muted mb-2"><i class="fas fa-eye me-1"></i> Ko'rinish</h6>
                    <div class="bg-white p-3 rounded border">
                        <div class="d-flex align-items-start">
                            <div class="bg-primary rounded-circle p-2 text-white me-3">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <h6 class="mb-1" id="previewTitle">Sarlavha</h6>
                                <p class="mb-1 text-muted small" id="previewMessage">Xabar matni</p>
                                <small class="text-muted">Hozir</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i> Yuborish
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-2"></i> Tozalash
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="sender-card p-4 mb-4">
            <h6 class="mb-3"><i class="fas fa-chart-bar text-primary me-2"></i> Statistika</h6>
            <div class="row g-3">
                <div class="col-6">
                    <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                        <div class="fs-4 fw-bold text-primary">{{ \App\Models\Student::whereNotNull('user_id')->count() }}</div>
                        <div class="small text-muted">Jami talabalar</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                        <div class="fs-4 fw-bold text-success">{{ \App\Models\Notification::where('type', 'marketing')->count() }}</div>
                        <div class="small text-muted">Yuborilgan xabarlar</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="sender-card p-4">
            <h6 class="mb-3"><i class="fas fa-history text-primary me-2"></i> So'nggi yuborilgan xabarlar</h6>

            @if($sentNotifications->count() > 0)
                <div style="max-height: 400px; overflow-y: auto;">
                    @foreach($sentNotifications as $notification)
                        <div class="history-item">
                            <div class="fw-medium text-truncate">{{ $notification->title }}</div>
                            <div class="small text-muted text-truncate">{{ Str::limit($notification->message, 50) }}</div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                <span class="badge bg-primary">{{ $notification->recipient_count }} kishi</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                    <p class="mb-0">Hali xabar yuborilmagan</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function selectTarget(target) {
    // Update UI
    document.querySelectorAll('.target-option').forEach(el => {
        el.classList.remove('selected');
    });
    document.querySelector(`[data-target="${target}"]`).classList.add('selected');

    // Update hidden input
    document.getElementById('targetInput').value = target;

    // Show/hide sections
    document.getElementById('facultySection').style.display = target === 'faculty' ? 'block' : 'none';
    document.getElementById('groupSection').style.display = target === 'group' ? 'block' : 'none';
    document.getElementById('customSection').style.display = target === 'custom' ? 'block' : 'none';
}

// Live preview
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.querySelector('input[name="title"]');
    const messageInput = document.querySelector('textarea[name="message"]');
    const preview = document.getElementById('preview');
    const previewTitle = document.getElementById('previewTitle');
    const previewMessage = document.getElementById('previewMessage');

    function updatePreview() {
        const title = titleInput.value.trim();
        const message = messageInput.value.trim();

        if (title || message) {
            preview.style.display = 'block';
            previewTitle.textContent = title || 'Sarlavha';
            previewMessage.textContent = message || 'Xabar matni';
        } else {
            preview.style.display = 'none';
        }
    }

    titleInput.addEventListener('input', updatePreview);
    messageInput.addEventListener('input', updatePreview);
});
</script>
@endsection
