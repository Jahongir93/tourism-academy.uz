@extends('layouts.dashboard-new')

@section('title', 'Murojaat so\'rovlari')
@section('page-title', 'Murojaat so\'rovlari')

@section('content')
<div class="container-fluid">
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small>Jami so'rovlar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                    <small>Kutilmoqda</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['accepted'] }}</h3>
                    <small>Qabul qilingan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['rejected'] }}</h3>
                    <small>Rad etilgan</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('chat-admin.contact-requests') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                        <option value="accepted" {{ $status == 'accepted' ? 'selected' : '' }}>Qabul qilingan</option>
                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rad etilgan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rol</label>
                    <select name="role" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="dean" {{ $role == 'dean' ? 'selected' : '' }}>Dekan</option>
                        <option value="prorector" {{ $role == 'prorector' ? 'selected' : '' }}>Prorektor</option>
                        <option value="teacher" {{ $role == 'teacher' ? 'selected' : '' }}>O'qituvchi</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filtrlash
                    </button>
                    <a href="{{ route('chat-admin.contact-requests') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-user-clock text-primary me-2"></i>Murojaat so'rovlari</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Yuboruvchi</th>
                            <th>Qabul qiluvchi</th>
                            <th>Xabar</th>
                            <th>Status</th>
                            <th>Sana</th>
                            <th class="text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr class="{{ $request->status === 'pending' ? 'table-warning' : '' }}">
                            <td>
                                <strong>{{ $request->fromUser?->name ?? 'Noma\'lum' }}</strong>
                                <br>
                                <small class="text-muted">{{ $request->fromUser?->email }}</small>
                            </td>
                            <td>
                                @if($request->to_role)
                                    <span class="badge bg-info">{{ ucfirst($request->to_role) }}</span>
                                @elseif($request->toUser)
                                    {{ $request->toUser->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="max-width: 250px;">
                                <div class="text-truncate">{{ $request->message }}</div>
                                @if($request->response)
                                    <div class="mt-1 p-2 bg-light rounded small">
                                        <strong class="text-success">Javob:</strong> {{ Str::limit($request->response, 80) }}
                                        <br><small class="text-muted">{{ $request->handledBy?->name }}</small>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @switch($request->status)
                                    @case('pending')
                                        <span class="badge bg-warning">Kutilmoqda</span>
                                        @break
                                    @case('accepted')
                                        <span class="badge bg-success">Qabul qilingan</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">Rad etilgan</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                {{ $request->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary view-request"
                                        data-bs-toggle="modal" data-bs-target="#requestModal"
                                        data-id="{{ $request->id }}"
                                        data-from="{{ $request->fromUser?->name }}"
                                        data-email="{{ $request->fromUser?->email }}"
                                        data-role="{{ $request->to_role }}"
                                        data-message="{{ $request->message }}"
                                        data-response="{{ $request->response }}"
                                        data-status="{{ $request->status }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($request->status === 'pending')
                                <button type="button" class="btn btn-sm btn-outline-success respond-request"
                                        data-bs-toggle="modal" data-bs-target="#respondModal"
                                        data-id="{{ $request->id }}"
                                        data-from="{{ $request->fromUser?->name }}"
                                        data-message="{{ $request->message }}">
                                    <i class="fas fa-reply"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                Murojaat so'rovlari topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($requests->hasPages())
        <div class="card-footer bg-white">{{ $requests->withQueryString()->links() }}</div>
        @endif
    </div>
</div>

<!-- View Request Modal -->
<div class="modal fade" id="requestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Murojaat tafsilotlari</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Yuboruvchi:</strong> <span id="modal-from"></span></p>
                <p><strong>Email:</strong> <span id="modal-email"></span></p>
                <p><strong>Qabul qiluvchi rol:</strong> <span id="modal-role"></span></p>
                <p><strong>Status:</strong> <span id="modal-status"></span></p>
                <hr>
                <p><strong>Xabar:</strong></p>
                <div class="bg-light p-3 rounded" id="modal-message"></div>
                <div id="modal-response-section" style="display: none;">
                    <hr>
                    <p><strong>Javob:</strong></p>
                    <div class="bg-success bg-opacity-10 p-3 rounded" id="modal-response"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
            </div>
        </div>
    </div>
</div>

<!-- Respond Modal -->
<div class="modal fade" id="respondModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Murojaatga javob berish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="respondForm">
                <div class="modal-body">
                    <p><strong>Yuboruvchi:</strong> <span id="respond-from"></span></p>
                    <div class="bg-light p-3 rounded mb-3">
                        <small class="text-muted">Xabar:</small>
                        <div id="respond-message"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Javob</label>
                        <textarea name="response" class="form-control" rows="4" placeholder="Javob xabarini yozing..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Qaror</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input type="radio" name="status" value="accepted" class="form-check-input" id="status_accept" checked>
                                <label class="form-check-label text-success" for="status_accept">
                                    <i class="fas fa-check me-1"></i> Qabul qilish
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="status" value="rejected" class="form-check-input" id="status_reject">
                                <label class="form-check-label text-danger" for="status_reject">
                                    <i class="fas fa-times me-1"></i> Rad etish
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Yuborish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-gradient-info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // View request modal
    document.querySelectorAll('.view-request').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal-from').textContent = this.dataset.from;
            document.getElementById('modal-email').textContent = this.dataset.email;
            document.getElementById('modal-role').textContent = this.dataset.role || '-';
            document.getElementById('modal-message').textContent = this.dataset.message;
            document.getElementById('modal-status').innerHTML = getStatusBadge(this.dataset.status);

            const responseSection = document.getElementById('modal-response-section');
            const responseEl = document.getElementById('modal-response');
            if (this.dataset.response) {
                responseSection.style.display = 'block';
                responseEl.textContent = this.dataset.response;
            } else {
                responseSection.style.display = 'none';
            }
        });
    });

    // Respond modal
    let currentRequestId = null;
    document.querySelectorAll('.respond-request').forEach(btn => {
        btn.addEventListener('click', function() {
            currentRequestId = this.dataset.id;
            document.getElementById('respond-from').textContent = this.dataset.from;
            document.getElementById('respond-message').textContent = this.dataset.message;
        });
    });

    // Respond form submit
    document.getElementById('respondForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(`{{ url('chat-admin/contact-requests') }}/${currentRequestId}/respond`, {
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
                location.reload();
            } else {
                alert(data.message || 'Xatolik yuz berdi');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Xatolik yuz berdi');
        });
    });

    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge bg-warning">Kutilmoqda</span>',
            'accepted': '<span class="badge bg-success">Qabul qilingan</span>',
            'rejected': '<span class="badge bg-danger">Rad etilgan</span>',
        };
        return badges[status] || status;
    }
});
</script>
@endpush
@endsection
