@extends('layouts.dashboard-new')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('styles')
<style>
    /* Professional Green Theme */
    :root {
        --primary-dark-green: #0d4f3c;
        --secondary-green: #16a085;
        --light-green: #e8f5f0;
        --accent-green: #48c9b0;
        --pale-green: #f0f9f6;
        --soft-mint: #d1f2eb;
        --text-dark: #2c3e50;
        --text-light: #5a6c7d;
        --border-light: #e1e8ed;
        --white: #ffffff;
        --background: #f8fafb;
    }

    body {
        background: var(--background) !important;
    }

    .green-gradient {
        background: linear-gradient(135deg, var(--primary-dark-green) 0%, var(--secondary-green) 100%);
    }

    .card-modern {
        border-radius: 12px;
        border: 1px solid var(--border-light);
        background: var(--white);
        transition: all 0.3s ease;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(13, 79, 60, 0.12);
    }

    .stat-card {
        background: var(--white);
        border-left: 4px solid var(--secondary-green);
    }

    .btn-primary-green {
        background: var(--primary-dark-green);
        color: white;
        border-radius: 8px;
        padding: 10px 24px;
        transition: all 0.3s;
        border: none;
        font-weight: 500;
    }

    .btn-primary-green:hover {
        background: var(--secondary-green);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 79, 60, 0.25);
        color: white;
    }

    .btn-light-green {
        background: var(--light-green);
        color: var(--primary-dark-green);
        border-radius: 8px;
        padding: 10px 24px;
        transition: all 0.3s;
        border: none;
        font-weight: 500;
    }

    .btn-light-green:hover {
        background: var(--soft-mint);
        color: var(--primary-dark-green);
    }

    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
        position: relative;
        padding-left: 16px;
    }

    .section-title:before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 20px;
        background: var(--secondary-green);
        border-radius: 2px;
    }

    .module-card {
        background: var(--pale-green);
        border: 1px solid var(--light-green);
        transition: all 0.3s;
        border-radius: 10px;
    }

    .module-card:hover {
        background: var(--light-green);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 79, 60, 0.1);
    }

    .badge-success {
        background: var(--light-green);
        color: var(--primary-dark-green);
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 500;
    }

    .table thead {
        background: var(--pale-green);
    }

    .table-hover tbody tr:hover {
        background: var(--pale-green);
    }

    .progress {
        background: var(--light-green);
        border-radius: 8px;
        overflow: hidden;
    }

    .progress-bar {
        background: linear-gradient(90deg, var(--secondary-green) 0%, var(--accent-green) 100%);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Welcome Banner -->
    <div class="green-gradient rounded-3 p-4 mb-4 text-white shadow">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-2">Xush kelibsiz, {{ Auth::user()->name }}!</h2>
                <p class="mb-0 opacity-90">Boshqaruv paneli - barcha ma'lumotlar bir joyda</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex gap-2 justify-content-end flex-wrap">
                    <a href="{{ route('attendance.face-recognition') }}" class="btn btn-light fw-semibold">
                        <i class="fas fa-camera me-2"></i>Yuz davomat
                    </a>
                    <a href="{{ route('journal.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-book me-2"></i>Jurnallar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Face Attendance Integration -->
    {{-- Attendance components vaqtincha o'chirildi
    <div class="row mb-4">
        <div class="col-lg-6">
            @includeIf('components.attendance-stats', [
                'todayAttendance' => $todayAttendance ?? ['present' => 0, 'late' => 0, 'absent' => 0, 'total' => 0],
                'recentCheckIns' => $recentCheckIns ?? []
            ])
        </div>
        <div class="col-lg-6">
            @includeIf('components.attendance-widget', [
                'hasRegisteredFace' => $hasRegisteredFace ?? false,
                'todayAttendance' => null,
                'attendanceHistory' => null
            ])
        </div>
    </div>
    --}}

    <!-- Main Statistics -->
    <h2 class="section-title">Asosiy ko'rsatkichlar</h2>
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6">
            <div class="card card-modern stat-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box" style="background: var(--light-green);">
                            <i class="fas fa-users fs-5" style="color: var(--primary-dark-green);"></i>
                        </div>
                        <span class="badge badge-success">+12%</span>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $stats['total_users'] }}</h3>
                    <p class="text-muted small mb-0">Jami foydalanuvchilar</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <a href="{{ route('students.index') }}" class="text-decoration-none">
                <div class="card card-modern stat-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-box" style="background: var(--soft-mint);">
                                <i class="fas fa-user-graduate fs-5" style="color: var(--secondary-green);"></i>
                            </div>
                            <i class="fas fa-arrow-up-right" style="color: var(--accent-green); font-size: 12px;"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $stats['total_students'] }}</h3>
                        <p class="text-muted small mb-0">Talabalar</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="card card-modern stat-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box" style="background: var(--pale-green);">
                            <i class="fas fa-chalkboard-teacher fs-5" style="color: var(--primary-dark-green);"></i>
                        </div>
                        <span class="text-muted small">Faol</span>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $stats['total_teachers'] }}</h3>
                    <p class="text-muted small mb-0">O'qituvchilar</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="card card-modern stat-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box" style="background: var(--light-green);">
                            <i class="fas fa-book-open fs-5" style="color: var(--secondary-green);"></i>
                        </div>
                        <span class="text-muted small">Jami</span>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--text-dark);">{{ class_exists('\App\Models\Subject') ? \App\Models\Subject::count() : 0 }}</h3>
                    <p class="text-muted small mb-0">Fanlar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- O'quv jarayoni statistikasi -->
    <div class="card card-modern green-gradient text-white mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center mb-3">
                <div class="col">
                    <h3 class="fw-semibold mb-1">Elektron jurnal tizimi</h3>
                    <p class="mb-0 opacity-90">O'quv jarayonini monitoring qilish</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('schedule.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-calendar-alt me-1"></i> Jadval
                    </a>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-6 col-lg-3">
                    <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center">
                        <h4 class="fw-bold mb-1">{{ class_exists('\App\Models\JournalEntry') ? \App\Models\JournalEntry::count() : 0 }}</h4>
                        <small class="opacity-90">Jurnallar</small>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center">
                        <h4 class="fw-bold mb-1">{{ class_exists('\App\Models\AttendanceRecord') ? \App\Models\AttendanceRecord::whereDate('lesson_date', today())->count() : 0 }}</h4>
                        <small class="opacity-90">Bugungi darslar</small>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center">
                        <h4 class="fw-bold mb-1">{{ class_exists('\App\Models\Assignment') ? \App\Models\Assignment::where('deadline', '>=', now())->count() : 0 }}</h4>
                        <small class="opacity-90">Topshiriqlar</small>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center">
                        <h4 class="fw-bold mb-1">{{ class_exists('\App\Models\Schedule') ? \App\Models\Schedule::where('status', 'active')->count() : 0 }}</h4>
                        <small class="opacity-90">Faol jadvallar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tashkiliy tuzilma -->
        <div class="col-lg-6 mb-4">
            <div class="card card-modern h-100">
                <div class="card-header bg-white border-0 pb-0 pt-3">
                    <h5 class="section-title">Tashkiliy tuzilma</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('structure.faculties.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center module-card">
                                    <div class="icon-box mx-auto mb-2" style="background: var(--white);">
                                        <i class="fas fa-university" style="color: var(--secondary-green);"></i>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-1">Fakultetlar</h6>
                                    <span class="badge badge-success">{{ class_exists('\App\Models\Faculty') ? \App\Models\Faculty::count() : 0 }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('structure.departments.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center module-card">
                                    <div class="icon-box mx-auto mb-2" style="background: var(--white);">
                                        <i class="fas fa-sitemap" style="color: var(--primary-dark-green);"></i>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-1">Kafedralar</h6>
                                    <span class="badge badge-success">{{ class_exists('\App\Models\Department') ? \App\Models\Department::count() : 0 }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('structure.positions.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center module-card">
                                    <div class="icon-box mx-auto mb-2" style="background: var(--white);">
                                        <i class="fas fa-briefcase" style="color: var(--secondary-green);"></i>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-1">Lavozimlar</h6>
                                    <span class="badge badge-success">{{ class_exists('\App\Models\Position') ? \App\Models\Position::count() : 0 }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('structure.chart.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center module-card">
                                    <div class="icon-box mx-auto mb-2" style="background: var(--white);">
                                        <i class="fas fa-project-diagram" style="color: var(--accent-green);"></i>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-1">Tuzilma</h6>
                                    <span class="badge badge-success">Sxema</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- O'quv jarayoni -->
        <div class="col-lg-6 mb-4">
            <div class="card card-modern h-100">
                <div class="card-header bg-white border-0 pb-0 pt-3">
                    <h5 class="section-title">O'quv jarayoni</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('structure.academic.programs.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center module-card">
                                    <div class="icon-box mx-auto mb-2" style="background: var(--white);">
                                        <i class="fas fa-graduation-cap" style="color: var(--secondary-green);"></i>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-1">Dasturlar</h6>
                                    <span class="badge badge-success">{{ class_exists('\App\Models\EducationalProgram') ? \App\Models\EducationalProgram::count() : 0 }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('structure.academic.subjects.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center module-card">
                                    <div class="icon-box mx-auto mb-2" style="background: var(--white);">
                                        <i class="fas fa-book" style="color: var(--primary-dark-green);"></i>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-1">Fanlar</h6>
                                    <span class="badge badge-success">{{ class_exists('\App\Models\Subject') ? \App\Models\Subject::count() : 0 }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('structure.academic.curriculum.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center module-card">
                                    <div class="icon-box mx-auto mb-2" style="background: var(--white);">
                                        <i class="fas fa-clipboard-list" style="color: var(--secondary-green);"></i>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-1">O'quv reja</h6>
                                    <span class="badge badge-success">Rejalar</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('structure.academic.hours.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center module-card">
                                    <div class="icon-box mx-auto mb-2" style="background: var(--white);">
                                        <i class="fas fa-clock" style="color: var(--accent-green);"></i>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-1">Soatlar</h6>
                                    <span class="badge badge-success">Taqsimot</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card card-modern">
        <div class="card-header bg-white border-0">
            <h5 class="section-title">So'nggi ro'yxatdan o'tganlar</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="border-0 px-4 py-3" style="color: var(--text-dark);">Ism</th>
                            <th class="border-0 px-4 py-3" style="color: var(--text-dark);">Kontakt</th>
                            <th class="border-0 px-4 py-3" style="color: var(--text-dark);">Rol</th>
                            <th class="border-0 px-4 py-3" style="color: var(--text-dark);">Sana</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['recent_registrations'] as $user)
                            <tr>
                                <td class="px-4 py-3 align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px; background: var(--light-green);">
                                            <i class="fas fa-user" style="color: var(--primary-dark-green);"></i>
                                        </div>
                                        <span class="fw-medium">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle text-muted">{{ $user->email ?? $user->phone }}</td>
                                <td class="px-4 py-3 align-middle">
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-success">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 align-middle text-muted">
                                    {{ $user->created_at->format('d.m.Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fs-1 mb-3 d-block opacity-25"></i>
                                    Hozircha foydalanuvchilar yo'q
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions Button -->
    <div class="position-fixed" style="bottom: 30px; right: 30px;">
        <button class="btn btn-primary-green shadow-lg" data-bs-toggle="dropdown">
            <i class="fas fa-plus me-2"></i> Tezkor qo'shish
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('students.index') }}"><i class="fas fa-user-graduate me-2"></i> Talabalar</a></li>
            <li><a class="dropdown-item" href="{{ route('journal.index') }}"><i class="fas fa-book me-2"></i> Jurnallar</a></li>
            <li><a class="dropdown-item" href="{{ route('schedule.index') }}"><i class="fas fa-calendar me-2"></i> Dars jadvali</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('attendance.face-recognition') }}"><i class="fas fa-camera me-2"></i> Yuz davomat</a></li>
        </ul>
    </div>

    <!-- Chat Widget - Bottom Right -->
    <div class="position-fixed" style="bottom: 20px; right: 20px; z-index: 9999;">
        <!-- Chat Widget Panel -->
        <div class="card shadow-lg" id="chatWidget" style="display: none; width: 350px; margin-bottom: 10px;">
            <div class="card-header text-white d-flex justify-content-between align-items-center py-2" style="background: #0d4f3c;">
                <h6 class="mb-0">
                    <i class="fas fa-comments"></i> Chat
                </h6>
                <div>
                    <span id="chatUnreadBadge" class="badge bg-danger rounded-pill me-2" style="display:none;">0</span>
                    <button type="button" class="btn-close btn-close-white btn-sm" onclick="toggleChatWidget()"></button>
                </div>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto; background: white;">
                <div id="recentConversations">
                    <div class="text-center py-3">
                        <i class="fas fa-comments text-muted" style="font-size: 32px;"></i>
                        <p class="text-muted mt-2">Yuklanmoqda...</p>
                    </div>
                </div>
            </div>
            <div class="card-footer p-2 bg-white">
                <a href="{{ route('chat.index') }}" class="btn btn-sm w-100" style="background: #16a085; color: white;">
                    <i class="fas fa-external-link-alt"></i> Barcha chatlar
                </a>
            </div>
        </div>

        <!-- Chat Toggle Button -->
        <button id="chatToggleBtn" class="btn rounded-circle shadow-lg"
                style="width: 60px; height: 60px; background: #16a085; color: white; border: none;"
                onclick="toggleChatWidget()">
            <i class="fas fa-comment-dots" style="font-size: 24px;"></i>
            <span id="chatFloatingBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">
                0
            </span>
        </button>
    </div>
</div>

<script>
    // Load recent conversations for chat widget
    function loadRecentConversations() {
        fetch('/chat')
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const conversations = doc.querySelectorAll('.conversation-item');
                const container = document.getElementById('recentConversations');

                if (conversations.length > 0) {
                    container.innerHTML = '';
                    let count = 0;
                    conversations.forEach(conv => {
                        if (count < 5) { // Show only 5 recent conversations
                            const convItem = document.createElement('div');
                            convItem.className = 'p-2 border-bottom conversation-mini';
                            convItem.style.cursor = 'pointer';

                            const name = conv.querySelector('.font-semibold').textContent;
                            const lastMsg = conv.querySelector('.text-gray-600');
                            const unreadBadge = conv.querySelector('.bg-green-600');

                            const convId = conv.getAttribute('data-conversation-id');

                            convItem.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0" style="font-size: 14px;">${name}</h6>
                                        ${lastMsg ? `<small class="text-muted">${lastMsg.textContent}</small>` : ''}
                                    </div>
                                    ${unreadBadge ? `<span class="badge bg-danger rounded-pill">${unreadBadge.textContent}</span>` : ''}
                                </div>
                            `;

                            convItem.onclick = () => {
                                window.location.href = '/chat/' + convId;
                            };

                            container.appendChild(convItem);
                            count++;
                        }
                    });
                } else {
                    container.innerHTML = `
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-comments mb-2" style="font-size: 32px;"></i>
                            <p class="mb-0">Hali chatlar yo'q</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading conversations:', error);
                document.getElementById('recentConversations').innerHTML = `
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-exclamation-circle mb-2"></i>
                        <p class="mb-0">Yuklanmadi</p>
                    </div>
                `;
            });
    }

    // Update unread count
    function updateUnreadCount() {
        fetch('/chat/unread')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('chatUnreadBadge');
                const floatingBadge = document.getElementById('chatFloatingBadge');
                if (data.unread > 0) {
                    badge.textContent = data.unread;
                    badge.style.display = 'inline-block';
                    floatingBadge.textContent = data.unread;
                    floatingBadge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                    floatingBadge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Toggle chat widget
    function toggleChatWidget() {
        const widget = document.getElementById('chatWidget');
        const toggleBtn = document.getElementById('chatToggleBtn');

        if (widget.style.display === 'none') {
            widget.style.display = 'block';
            toggleBtn.style.display = 'none';
            loadRecentConversations();
        } else {
            widget.style.display = 'none';
            toggleBtn.style.display = 'block';
        }
    }

    // Load on page load
    document.addEventListener('DOMContentLoaded', () => {
        updateUnreadCount();

        // Refresh every 30 seconds
        setInterval(() => {
            updateUnreadCount();
        }, 30000);
    });
</script>
@endsection