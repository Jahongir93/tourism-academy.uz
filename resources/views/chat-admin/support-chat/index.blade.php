@extends('layouts.dashboard-new')

@section('title', 'Support Chat')
@section('page-title', 'Qo\'llab-quvvatlash Xabarlari')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Jami sessiyalar</h6>
                            <h2 class="mb-0">{{ $stats['total_sessions'] ?? 0 }}</h2>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Jami xabarlar</h6>
                            <h2 class="mb-0">{{ $stats['total_messages'] ?? 0 }}</h2>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-envelope fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-gradient-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">O'qilmagan</h6>
                            <h2 class="mb-0">{{ $stats['unread_messages'] ?? 0 }}</h2>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-bell fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Bugun</h6>
                            <h2 class="mb-0">{{ $stats['messages_today'] ?? 0 }}</h2>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-calendar-day fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-headset me-2"></i>Qo'llab-quvvatlash so'rovlari</h5>
        </div>
        <div class="card-body p-0">
            @if($sessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Mijoz</th>
                                <th>Oxirgi xabar</th>
                                <th>Xabarlar</th>
                                <th>O'qilmagan</th>
                                <th>Vaqt</th>
                                <th>Amal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                <tr class="{{ $session->unread_count > 0 ? 'table-warning' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2">
                                                {{ strtoupper(substr($session->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $session->name }}</div>
                                                <small class="text-muted"><i class="fas fa-phone me-1"></i>{{ $session->phone ?: 'Telefon yo\'q' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                            {{ $session->last_message }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $session->message_count }}</span>
                                    </td>
                                    <td>
                                        @if($session->unread_count > 0)
                                            <span class="badge bg-danger">{{ $session->unread_count }}</span>
                                        @else
                                            <span class="badge bg-success">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $session->last_message_time }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('chat-admin.support-chat.session', $session->session_id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Ko'rish
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $sessions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Hozircha xabarlar yo'q</h5>
                    <p class="text-muted">Sayt tashrif buyuruvchilari xabar yuborganda bu yerda ko'rinadi.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.bg-gradient-danger { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
.bg-gradient-info { background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%); }
</style>
@endsection
