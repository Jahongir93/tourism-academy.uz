@extends('layouts.dashboard-new')

@section('title', 'Modul ruxsatlari sozlamalari')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0">Modul ruxsatlari sozlamalari</h1>
        <p class="text-muted">Har bir rol uchun qaysi modullar va qanday ruxsatlar berilishini sozlang</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Access Levels Legend -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Ruxsat darajalari</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($accessLevels as $levelKey => $levelData)
                <div class="col-md-3 mb-2">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $levelData['color'] }} me-2">
                            <i class="{{ $levelData['icon'] }}"></i>
                        </span>
                        <div>
                            <strong>{{ $levelData['name'] }}</strong>
                            <br><small class="text-muted">{{ $levelData['description'] }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Roles and Modules Grid -->
    @foreach($roles as $role)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-user-tag me-2"></i>{{ $role->name }}
                <small class="text-muted">({{ $role->permissions->count() }} ta ruxsat)</small>
            </h5>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAllModules({{ $role->id }})">
                <i class="fas fa-check-double"></i> Barchasini belgilash
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($modules as $moduleKey => $moduleData)
                @php
                    $currentAccess = $roleModuleAccess[$role->id][$moduleKey] ?? 'none';
                    $badgeColor = $accessLevels[$currentAccess]['color'] ?? 'secondary';
                    $badgeIcon = $accessLevels[$currentAccess]['icon'] ?? 'fas fa-ban';
                @endphp
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <div class="module-card border rounded p-3 h-100 {{ $currentAccess !== 'none' ? 'border-'.$badgeColor : '' }}"
                         id="module-card-{{ $role->id }}-{{ $moduleKey }}"
                         style="cursor: pointer; transition: all 0.3s ease;"
                         onclick="openAccessModal({{ $role->id }}, '{{ $moduleKey }}', '{{ addslashes($moduleData['name']) }}', '{{ $currentAccess }}')">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center">
                                <i class="{{ $moduleData['icon'] }} text-primary me-2 fa-lg"></i>
                                <div>
                                    <strong>{{ $moduleData['name'] }}</strong>
                                    <br><small class="text-muted">{{ $moduleData['description'] }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-{{ $badgeColor }} access-badge" id="badge-{{ $role->id }}-{{ $moduleKey }}">
                                <i class="{{ $badgeIcon }} me-1"></i>
                                {{ $accessLevels[$currentAccess]['name'] ?? 'Noma\'lum' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    <!-- Module Summary Table -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Modullar jadvali</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Modul</th>
                            @foreach($roles as $role)
                            <th class="text-center">{{ $role->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $moduleKey => $moduleData)
                        <tr>
                            <td>
                                <i class="{{ $moduleData['icon'] }} me-2 text-primary"></i>
                                <strong>{{ $moduleData['name'] }}</strong>
                            </td>
                            @foreach($roles as $role)
                            @php
                                $access = $roleModuleAccess[$role->id][$moduleKey] ?? 'none';
                                $color = $accessLevels[$access]['color'] ?? 'secondary';
                            @endphp
                            <td class="text-center">
                                <span class="badge bg-{{ $color }}" id="table-badge-{{ $role->id }}-{{ $moduleKey }}">
                                    {{ $accessLevels[$access]['name'] ?? '-' }}
                                </span>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Access Level Modal -->
<div class="modal fade" id="accessModal" tabindex="-1" aria-labelledby="accessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accessModalLabel">
                    <i class="fas fa-key me-2"></i>Ruxsat darajasini tanlang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 id="modalModuleName" class="text-primary"></h6>
                    <p class="text-muted small mb-0" id="modalRoleName"></p>
                </div>
                <hr>
                <div class="access-options">
                    @foreach($accessLevels as $levelKey => $levelData)
                    <div class="form-check mb-3 p-3 border rounded access-option" data-level="{{ $levelKey }}" onclick="selectAccessLevel('{{ $levelKey }}')">
                        <input class="form-check-input" type="radio" name="accessLevel" id="access_{{ $levelKey }}" value="{{ $levelKey }}">
                        <label class="form-check-label w-100" for="access_{{ $levelKey }}" style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $levelData['color'] }} me-3 p-2">
                                    <i class="{{ $levelData['icon'] }} fa-lg"></i>
                                </span>
                                <div>
                                    <strong>{{ $levelData['name'] }}</strong>
                                    <br><small class="text-muted">{{ $levelData['description'] }}</small>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" id="saveAccessBtn" onclick="saveAccess()">
                    <i class="fas fa-save me-1"></i>Saqlash
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.module-card:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.module-card.border-success {
    background-color: rgba(25, 135, 84, 0.05);
}

.module-card.border-warning {
    background-color: rgba(255, 193, 7, 0.05);
}

.module-card.border-info {
    background-color: rgba(13, 202, 240, 0.05);
}

.access-option {
    cursor: pointer;
    transition: all 0.2s ease;
}

.access-option:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd !important;
}

.access-option.selected {
    background-color: #e7f1ff;
    border-color: #0d6efd !important;
}

.access-badge {
    font-size: 0.75rem;
}
</style>

<script>
let currentRoleId = null;
let currentModule = null;
let accessModal = null;

document.addEventListener('DOMContentLoaded', function() {
    accessModal = new bootstrap.Modal(document.getElementById('accessModal'));
});

function openAccessModal(roleId, moduleKey, moduleName, currentAccess) {
    currentRoleId = roleId;
    currentModule = moduleKey;

    document.getElementById('modalModuleName').innerHTML = '<i class="fas fa-cube me-2"></i>' + moduleName;
    document.getElementById('modalRoleName').textContent = 'Rol ID: ' + roleId;

    // Reset all options
    document.querySelectorAll('.access-option').forEach(opt => {
        opt.classList.remove('selected');
        opt.querySelector('input').checked = false;
    });

    // Select current access level
    const currentOption = document.querySelector(`.access-option[data-level="${currentAccess}"]`);
    if (currentOption) {
        currentOption.classList.add('selected');
        currentOption.querySelector('input').checked = true;
    }

    accessModal.show();
}

function selectAccessLevel(level) {
    document.querySelectorAll('.access-option').forEach(opt => {
        opt.classList.remove('selected');
        opt.querySelector('input').checked = false;
    });

    const option = document.querySelector(`.access-option[data-level="${level}"]`);
    if (option) {
        option.classList.add('selected');
        option.querySelector('input').checked = true;
    }
}

function saveAccess() {
    const selectedLevel = document.querySelector('input[name="accessLevel"]:checked');
    if (!selectedLevel) {
        alert('Iltimos, ruxsat darajasini tanlang!');
        return;
    }

    const saveBtn = document.getElementById('saveAccessBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saqlanmoqda...';

    fetch('{{ route("admin.settings.modules.update-access") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            role_id: currentRoleId,
            module: currentModule,
            access_level: selectedLevel.value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            updateModuleUI(currentRoleId, currentModule, selectedLevel.value);
            accessModal.hide();

            // Show success message
            showToast('success', data.message);
        } else {
            showToast('error', 'Xatolik yuz berdi!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Server xatosi!');
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save me-1"></i>Saqlash';
    });
}

function updateModuleUI(roleId, moduleKey, accessLevel) {
    const accessLevels = @json($accessLevels);
    const levelData = accessLevels[accessLevel];

    // Update card badge
    const badge = document.getElementById(`badge-${roleId}-${moduleKey}`);
    if (badge) {
        badge.className = `badge bg-${levelData.color} access-badge`;
        badge.innerHTML = `<i class="${levelData.icon} me-1"></i>${levelData.name}`;
    }

    // Update card border
    const card = document.getElementById(`module-card-${roleId}-${moduleKey}`);
    if (card) {
        card.className = 'module-card border rounded p-3 h-100';
        if (accessLevel !== 'none') {
            card.classList.add(`border-${levelData.color}`);
        }
    }

    // Update table badge
    const tableBadge = document.getElementById(`table-badge-${roleId}-${moduleKey}`);
    if (tableBadge) {
        tableBadge.className = `badge bg-${levelData.color}`;
        tableBadge.textContent = levelData.name;
    }
}

function toggleAllModules(roleId) {
    // This could be expanded to set all modules to a specific level
    alert('Bu funksiya hali ishlab chiqilmoqda');
}

function showToast(type, message) {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close float-end" onclick="this.parentElement.remove()"></button>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endsection
