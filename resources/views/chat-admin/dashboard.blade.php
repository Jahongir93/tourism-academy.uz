@extends('layouts.dashboard-new')

@section('title', 'Chat Admin Dashboard')
@section('page-title', 'Chat Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Chat xonalari</h6>
                            <h2 class="mb-0">{{ $stats['total_rooms'] ?? 0 }}</h2>
                            <small class="opacity-75">{{ $stats['active_rooms'] ?? 0 }} faol</small>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-comments fa-3x"></i>
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
                            <h6 class="text-uppercase mb-1 opacity-75">Xabarlar</h6>
                            <h2 class="mb-0">{{ $stats['total_messages'] ?? 0 }}</h2>
                            <small class="opacity-75">{{ $stats['messages_today'] ?? 0 }} bugun</small>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-envelope fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Telegram</h6>
                            <h2 class="mb-0">{{ $stats['telegram_new'] ?? 0 }}</h2>
                            <small class="opacity-75">yangi xabarlar</small>
                        </div>
                        <div class="opacity-50">
                            <i class="fab fa-telegram fa-3x"></i>
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
                            <h6 class="text-uppercase mb-1 opacity-75">Murojaatlar</h6>
                            <h2 class="mb-0">{{ $stats['contact_requests_pending'] ?? 0 }}</h2>
                            <small class="opacity-75">kutilmoqda</small>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-user-clock fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3"><i class="fas fa-bolt text-warning me-2"></i>Tezkor harakatlar</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('chat-admin.support-chat') }}" class="btn btn-outline-danger">
                            <i class="fas fa-headset me-1"></i> Support Chat
                        </a>
                        <a href="{{ route('chat-admin.telegram') }}" class="btn btn-outline-primary">
                            <i class="fab fa-telegram me-1"></i> Telegram xabarlari
                        </a>
                        <a href="{{ route('chat-admin.contact-requests') }}" class="btn btn-outline-success">
                            <i class="fas fa-envelope me-1"></i> Murojaat so'rovlari
                        </a>
                        <a href="{{ route('chat-admin.telegram-settings') }}" class="btn btn-outline-info">
                            <i class="fas fa-cog me-1"></i> Telegram sozlamalari
                        </a>
                        <a href="{{ route('chat-admin.nicknames') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-at me-1"></i> Niknamlar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Telegram Messages -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fab fa-telegram text-primary me-2"></i>Oxirgi Telegram xabarlari</h5>
                    <a href="{{ route('chat-admin.telegram') }}" class="btn btn-sm btn-outline-primary">Hammasi</a>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentTelegramMessages) && count($recentTelegramMessages) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentTelegramMessages as $msg)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $msg->full_name }}</strong>
                                        @if($msg->telegram_username)
                                            <small class="text-muted">@{{ $msg->telegram_username }}</small>
                                        @endif
                                        <p class="mb-1 text-truncate" style="max-width: 300px;">{{ $msg->message }}</p>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                                        <br>
                                        @if($msg->status === 'new')
                                            <span class="badge bg-warning">Yangi</span>
                                        @elseif($msg->status === 'replied')
                                            <span class="badge bg-success">Javob berilgan</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($msg->status) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fab fa-telegram fa-2x mb-2"></i>
                            <p class="mb-0">Telegram xabarlari yo'q</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Contact Requests -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-clock text-success me-2"></i>Kutilayotgan murojaatlar</h5>
                    <a href="{{ route('chat-admin.contact-requests') }}" class="btn btn-sm btn-outline-success">Hammasi</a>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentContactRequests) && count($recentContactRequests) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentContactRequests as $req)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $req->fromUser?->name ?? 'Noma\'lum' }}</strong>
                                        <span class="badge bg-info ms-1">{{ $req->to_role ?? 'Umumiy' }}</span>
                                        <p class="mb-1 text-truncate" style="max-width: 300px;">{{ $req->message }}</p>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p class="mb-0">Kutilayotgan murojaatlar yo'q</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Most Active Rooms -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Eng faol xonalar</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($activeRooms) && count($activeRooms) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Xona</th>
                                        <th>Xabarlar</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeRooms as $room)
                                    <tr>
                                        <td>
                                            <strong>{{ $room->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $room->slug }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $room->messages_count }}</span>
                                        </td>
                                        <td>
                                            @if($room->is_active)
                                                <span class="badge bg-success">Faol</span>
                                            @else
                                                <span class="badge bg-secondary">Nofaol</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-comments fa-2x mb-2"></i>
                            <p class="mb-0">Chat xonalari yo'q</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Most Active Users -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-users text-success me-2"></i>Eng faol foydalanuvchilar</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($activeUsers) && count($activeUsers) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Foydalanuvchi</th>
                                        <th>Xabarlar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeUsers as $aUser)
                                    <tr>
                                        <td>
                                            <strong>{{ $aUser->name }}</strong>
                                            @if($aUser->nickname)
                                                <small class="text-muted">@{{ $aUser->nickname }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $aUser->chat_messages_count }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p class="mb-0">Faol foydalanuvchilar yo'q</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.bg-gradient-danger { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); }
</style>
@endsection
