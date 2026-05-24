@extends('layouts.dashboard-new')

@section('title', 'HEMIS Sinxronizatsiya')
@section('page-title', 'HEMIS Sinxronizatsiya')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <p class="text-muted">HEMIS tizimi bilan ma'lumotlarni sinxronlashtirish</p>
    </div>

    <!-- Last Sync Info -->
    @if($lastSync)
    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-info-circle me-3"></i>
        <div>
            <small class="d-block">Oxirgi sinxronlash</small>
            <strong>{{ $lastSync->format('d.m.Y H:i') }}</strong>
        </div>
    </div>
    @endif

    <!-- Sync Actions Grid -->
    <div class="row g-4">
        <!-- Sync Students -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-user-graduate fs-3 text-primary"></i>
                        </div>
                        <h5 class="card-title ms-3 mb-0">Talabalar</h5>
                    </div>
                    <p class="card-text text-muted small">HEMIS tizimidan talabalarni sinxronlashtirish</p>
                    <button onclick="syncStudents()" class="btn btn-primary w-100">
                        <i class="fas fa-sync-alt me-2"></i>Sinxronlashtirish
                    </button>
                </div>
            </div>
        </div>

        <!-- Sync Teachers -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chalkboard-teacher fs-3 text-success"></i>
                        </div>
                        <h5 class="card-title ms-3 mb-0">O'qituvchilar</h5>
                    </div>
                    <p class="card-text text-muted small">HEMIS tizimidan o'qituvchilarni sinxronlashtirish</p>
                    <button onclick="syncTeachers()" class="btn btn-success w-100">
                        <i class="fas fa-sync-alt me-2"></i>Sinxronlashtirish
                    </button>
                </div>
            </div>
        </div>

        <!-- Full Sync -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-purple bg-opacity-10 p-3 rounded">
                            <i class="fas fa-database fs-3 text-purple"></i>
                        </div>
                        <h5 class="card-title ms-3 mb-0">To'liq sinxronlash</h5>
                    </div>
                    <p class="card-text text-muted small">Barcha ma'lumotlarni sinxronlashtirish</p>
                    <button onclick="fullSync()" class="btn btn-purple w-100">
                        <i class="fas fa-sync-alt me-2"></i>Sinxronlashtirish
                    </button>
                </div>
            </div>
        </div>

        <!-- Test Connection -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-plug fs-3 text-warning"></i>
                        </div>
                        <h5 class="card-title ms-3 mb-0">Bog'lanishni tekshirish</h5>
                    </div>
                    <p class="card-text text-muted small">HEMIS tizimi bilan bog'lanishni tekshirish</p>
                    <button onclick="testConnection()" class="btn btn-warning w-100">
                        <i class="fas fa-plug me-2"></i>Tekshirish
                    </button>
                </div>
            </div>
        </div>

        <!-- HEMIS Settings -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-secondary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-cog fs-3 text-secondary"></i>
                        </div>
                        <h5 class="card-title ms-3 mb-0">Sozlamalar</h5>
                    </div>
                    <p class="card-text text-muted small">HEMIS integratsiya sozlamalarini boshqarish</p>
                    <a href="{{ route('hemis.settings') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-cog me-2"></i>Sozlamalar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sync Log -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Sinxronlash jurnali
            </h5>
        </div>
        <div class="card-body">
            <div id="syncLog" class="bg-light rounded p-3" style="max-height: 400px; overflow-y: auto;">
                <p class="text-muted small mb-0">Hali sinxronlash amalga oshirilmagan</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function addLog(message, type = 'info') {
    const log = document.getElementById('syncLog');
    const timestamp = new Date().toLocaleTimeString('uz-UZ');
    const colors = {
        success: 'text-success',
        error: 'text-danger',
        info: 'text-primary',
        warning: 'text-warning'
    };

    if (log.querySelector('.text-muted')) {
        log.innerHTML = '';
    }

    const entry = document.createElement('div');
    entry.className = `mb-2 small ${colors[type] || colors.info}`;
    entry.innerHTML = `<span class="font-monospace">[${timestamp}]</span> ${message}`;
    log.insertBefore(entry, log.firstChild);
}

function syncStudents() {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sinxronlanmoqda...';

    addLog('Talabalarni sinxronlash boshlandi...', 'info');

    fetch('{{ route("hemis.sync.students") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ limit: 100, offset: 0 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addLog(data.message, 'success');
        } else {
            addLog(data.message, 'error');
        }
    })
    .catch(error => {
        addLog('Xatolik: ' + error.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Sinxronlashtirish';
    });
}

function syncTeachers() {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sinxronlanmoqda...';

    addLog('O\'qituvchilarni sinxronlash boshlandi...', 'info');

    fetch('{{ route("hemis.sync.teachers") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ limit: 100, offset: 0 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addLog(data.message, 'success');
        } else {
            addLog(data.message, 'error');
        }
    })
    .catch(error => {
        addLog('Xatolik: ' + error.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Sinxronlashtirish';
    });
}

function fullSync() {
    if (!confirm('To\'liq sinxronlash uzoq vaqt davom etishi mumkin. Davom etishni xohlaysizmi?')) {
        return;
    }

    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sinxronlanmoqda...';

    addLog('To\'liq sinxronlash boshlandi...', 'info');

    fetch('{{ route("hemis.sync.full") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addLog(data.message, 'success');
        } else {
            addLog(data.message, 'error');
        }
    })
    .catch(error => {
        addLog('Xatolik: ' + error.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Sinxronlashtirish';
    });
}

function testConnection() {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Tekshirilmoqda...';

    addLog('HEMIS bilan bog\'lanish tekshirilmoqda...', 'info');

    fetch('{{ route("hemis.test") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addLog(data.message, 'success');
        } else {
            addLog(data.message, 'error');
        }
    })
    .catch(error => {
        addLog('Xatolik: ' + error.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plug mr-2"></i>Tekshirish';
    });
}
</script>
@endpush
@endsection
