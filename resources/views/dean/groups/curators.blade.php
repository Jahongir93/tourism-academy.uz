@extends('layouts.dashboard-new')

@section('title', 'Kuratorlar')
@section('page-title', 'Guruh kuratorlari')

@section('content')
<div class="container-fluid">
    <!-- Xabarlar -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-user-tie me-2"></i>Guruh kuratorlari</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }} - Kuratorlarni boshqarish</p>
                        </div>
                        <a href="{{ route('dean.groups.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Guruhlar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistika -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-success">{{ $groups->filter(fn($g) => $g->curator_id)->count() }}</h3>
                    <p class="text-muted mb-0">Kuratori bor</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-warning">{{ $groups->filter(fn($g) => !$g->curator_id)->count() }}</h3>
                    <p class="text-muted mb-0">Kuratori yo'q</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-primary">{{ $teachers->count() }}</h3>
                    <p class="text-muted mb-0">Mavjud o'qituvchilar</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-users text-primary me-2"></i>Guruhlar va Kuratorlar</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Guruh</th>
                            <th class="border-0">Kurator</th>
                            <th class="border-0">Yo'nalish</th>
                            <th class="border-0">Kurs</th>
                            <th class="border-0">Talabalar</th>
                            <th class="border-0 text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                        <tr>
                            <td>
                                <span class="badge bg-primary fs-6">{{ $group->name }}</span>
                            </td>
                            <td>
                                @php $curatorEmployee = $group->curatorEmployee(); @endphp
                                @if($curatorEmployee)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-success bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user-tie text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $curatorEmployee->last_name }} {{ $curatorEmployee->first_name }}</h6>
                                        <small class="text-muted">{{ $curatorEmployee->phone ?? '' }}</small>
                                    </div>
                                </div>
                                @elseif($group->curator)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-info bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $group->curator->name }}</h6>
                                        <small class="text-muted">{{ $group->curator->email ?? '' }}</small>
                                    </div>
                                </div>
                                @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Tayinlanmagan
                                </span>
                                @endif
                            </td>
                            <td>{{ $group->specialty?->name_uz ?? '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $group->course }}-kurs</span></td>
                            <td><span class="badge bg-info">{{ $group->students_count }} ta</span></td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#curatorModal"
                                        data-group-id="{{ $group->id }}"
                                        data-group-name="{{ $group->name }}"
                                        data-curator-id="{{ $curatorEmployee?->id ?? '' }}">
                                    <i class="fas fa-edit me-1"></i>
                                    {{ $group->curator_id ? 'O\'zgartirish' : 'Tayinlash' }}
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-3x mb-3 d-block opacity-50"></i>
                                Guruhlar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($groups->hasPages())
        <div class="card-footer bg-white">{{ $groups->links() }}</div>
        @endif
    </div>
</div>

<!-- Kurator Modal -->
<div class="modal fade" id="curatorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-tie text-primary me-2"></i>Kurator tayinlash
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="curatorForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Guruh</label>
                        <input type="text" class="form-control" id="modalGroupName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kurator</label>
                        <select name="curator_id" id="curatorSelect" class="form-select">
                            <option value="">-- Kuratorni tanlang --</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">
                                {{ $teacher->last_name }} {{ $teacher->first_name }} {{ $teacher->middle_name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>Kuratorni olib tashlash uchun bo'sh qoldiring
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Bekor qilish
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-gradient-info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.avatar-sm { width: 40px; height: 40px; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const curatorModal = document.getElementById('curatorModal');

    curatorModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const groupId = button.getAttribute('data-group-id');
        const groupName = button.getAttribute('data-group-name');
        const curatorId = button.getAttribute('data-curator-id');

        // Form action-ni yangilash
        const form = document.getElementById('curatorForm');
        form.action = "{{ url('dean/groups') }}/" + groupId + "/curator";

        // Guruh nomini ko'rsatish
        document.getElementById('modalGroupName').value = groupName;

        // Joriy kuratorni tanlash
        const curatorSelect = document.getElementById('curatorSelect');
        curatorSelect.value = curatorId || '';
    });
});
</script>
@endpush
@endsection
