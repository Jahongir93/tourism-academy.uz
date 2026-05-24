@extends('layouts.dashboard-new')

@section('title', 'Foydalanuvchi niknamlari')
@section('page-title', 'Foydalanuvchi niknamlari')

@section('content')
<div class="container-fluid">
    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('chat-admin.nicknames') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Qidirish</label>
                    <input type="text" name="search" class="form-control"
                           value="{{ $search }}" placeholder="Ism, nikname yoki email...">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Qidirish
                    </button>
                    <a href="{{ route('chat-admin.nicknames') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-at text-primary me-2"></i>Foydalanuvchi niknamlari</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Ism</th>
                            <th>Nikname</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th class="text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td>
                                <span class="nickname-display" id="nickname-display-{{ $user->id }}">
                                    @if($user->nickname)
                                        <span class="text-primary">@{{ $user->nickname }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </span>
                                <input type="text" class="form-control form-control-sm nickname-input d-none"
                                       id="nickname-input-{{ $user->id }}"
                                       value="{{ $user->nickname }}"
                                       placeholder="Nikname kiriting..."
                                       data-user-id="{{ $user->id }}">
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge bg-success">Faol</span>
                                @else
                                    <span class="badge bg-secondary">{{ $user->status ?? 'Noma\'lum' }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-nickname"
                                        data-user-id="{{ $user->id }}"
                                        id="edit-btn-{{ $user->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-success save-nickname d-none"
                                        data-user-id="{{ $user->id }}"
                                        id="save-btn-{{ $user->id }}">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary cancel-nickname d-none"
                                        data-user-id="{{ $user->id }}"
                                        id="cancel-btn-{{ $user->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-3x mb-3 d-block opacity-50"></i>
                                Foydalanuvchilar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer bg-white">{{ $users->withQueryString()->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit nickname
    document.querySelectorAll('.edit-nickname').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            showEditMode(userId);
        });
    });

    // Save nickname
    document.querySelectorAll('.save-nickname').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const nickname = document.getElementById(`nickname-input-${userId}`).value.trim();
            saveNickname(userId, nickname);
        });
    });

    // Cancel edit
    document.querySelectorAll('.cancel-nickname').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            hideEditMode(userId);
        });
    });

    // Handle Enter key in input
    document.querySelectorAll('.nickname-input').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const userId = this.dataset.userId;
                const nickname = this.value.trim();
                saveNickname(userId, nickname);
            }
        });
        input.addEventListener('keyup', function(e) {
            if (e.key === 'Escape') {
                const userId = this.dataset.userId;
                hideEditMode(userId);
            }
        });
    });

    function showEditMode(userId) {
        document.getElementById(`nickname-display-${userId}`).classList.add('d-none');
        document.getElementById(`nickname-input-${userId}`).classList.remove('d-none');
        document.getElementById(`edit-btn-${userId}`).classList.add('d-none');
        document.getElementById(`save-btn-${userId}`).classList.remove('d-none');
        document.getElementById(`cancel-btn-${userId}`).classList.remove('d-none');
        document.getElementById(`nickname-input-${userId}`).focus();
    }

    function hideEditMode(userId) {
        document.getElementById(`nickname-display-${userId}`).classList.remove('d-none');
        document.getElementById(`nickname-input-${userId}`).classList.add('d-none');
        document.getElementById(`edit-btn-${userId}`).classList.remove('d-none');
        document.getElementById(`save-btn-${userId}`).classList.add('d-none');
        document.getElementById(`cancel-btn-${userId}`).classList.add('d-none');
    }

    function saveNickname(userId, nickname) {
        const saveBtn = document.getElementById(`save-btn-${userId}`);
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        const formData = new FormData();
        formData.append('nickname', nickname);

        fetch(`{{ url('chat-admin/nicknames') }}/${userId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const displayEl = document.getElementById(`nickname-display-${userId}`);
                if (data.nickname) {
                    displayEl.innerHTML = `<span class="text-primary">@${data.nickname}</span>`;
                } else {
                    displayEl.innerHTML = '<span class="text-muted">-</span>';
                }
                hideEditMode(userId);
            } else {
                alert(data.message || 'Xatolik yuz berdi');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Xatolik yuz berdi');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-check"></i>';
        });
    }
});
</script>
@endpush
@endsection
