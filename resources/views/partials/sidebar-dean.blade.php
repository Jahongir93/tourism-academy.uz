<!-- Dekan Sidebar -->
<div class="sidebar-header p-3 border-bottom">
    <div class="d-flex align-items-center">
        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
            <i class="fas fa-university text-success fa-lg"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold">Dekanat</h6>
            <small class="text-muted">Boshqaruv paneli</small>
        </div>
    </div>
</div>

<nav class="sidebar-nav p-2">
    <!-- Dashboard -->
    <a href="{{ route('dean.dashboard') }}" class="nav-link {{ request()->routeIs('dean.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt me-2"></i>
        <span>Dashboard</span>
    </a>

    <!-- Talabalar -->
    <div class="nav-item" x-data="{ open: {{ request()->routeIs('dean.students.*') ? 'true' : 'false' }} }">
        <a href="#" class="nav-link d-flex justify-content-between align-items-center" @click.prevent="open = !open">
            <span><i class="fas fa-user-graduate me-2"></i> Talabalar</span>
            <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
        </a>
        <div class="submenu ms-3" x-show="open" x-collapse>
            <a href="{{ route('dean.students.index') }}" class="nav-link {{ request()->routeIs('dean.students.index') ? 'active' : '' }}">
                <i class="fas fa-list me-2"></i> Talabalar ro'yxati
            </a>
            <a href="{{ route('dean.students.transfers') }}" class="nav-link {{ request()->routeIs('dean.students.transfers') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt me-2"></i> Ko'chirishlar
            </a>
            <a href="{{ route('dean.students.graduates') }}" class="nav-link {{ request()->routeIs('dean.students.graduates') ? 'active' : '' }}">
                <i class="fas fa-graduation-cap me-2"></i> Bitiruvchilar
            </a>
        </div>
    </div>

    <!-- Guruhlar -->
    <div class="nav-item" x-data="{ open: {{ request()->routeIs('dean.groups.*') ? 'true' : 'false' }} }">
        <a href="#" class="nav-link d-flex justify-content-between align-items-center" @click.prevent="open = !open">
            <span><i class="fas fa-users me-2"></i> Guruhlar</span>
            <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
        </a>
        <div class="submenu ms-3" x-show="open" x-collapse>
            <a href="{{ route('dean.groups.index') }}" class="nav-link {{ request()->routeIs('dean.groups.index') ? 'active' : '' }}">
                <i class="fas fa-list me-2"></i> Guruhlar ro'yxati
            </a>
            <a href="{{ route('dean.groups.curators') }}" class="nav-link {{ request()->routeIs('dean.groups.curators') ? 'active' : '' }}">
                <i class="fas fa-user-tie me-2"></i> Kuratorlar
            </a>
        </div>
    </div>

    <!-- O'qituvchilar -->
    <a href="{{ route('dean.teachers.index') }}" class="nav-link {{ request()->routeIs('dean.teachers.*') ? 'active' : '' }}">
        <i class="fas fa-chalkboard-teacher me-2"></i>
        <span>O'qituvchilar</span>
    </a>

    <!-- Kafedralar -->
    <a href="{{ route('dean.departments.index') }}" class="nav-link {{ request()->routeIs('dean.departments.*') ? 'active' : '' }}">
        <i class="fas fa-building me-2"></i>
        <span>Kafedralar</span>
    </a>

    <!-- Dars jadvali -->
    <div class="nav-item" x-data="{ open: {{ request()->routeIs('dean.schedule.*') ? 'true' : 'false' }} }">
        <a href="#" class="nav-link d-flex justify-content-between align-items-center" @click.prevent="open = !open">
            <span><i class="fas fa-calendar-alt me-2"></i> Dars jadvali</span>
            <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
        </a>
        <div class="submenu ms-3" x-show="open" x-collapse>
            <a href="{{ route('dean.schedule.index') }}" class="nav-link {{ request()->routeIs('dean.schedule.index') ? 'active' : '' }}">
                <i class="fas fa-table me-2"></i> Jadval ko'rish
            </a>
            <a href="{{ route('dean.schedule.exams') }}" class="nav-link {{ request()->routeIs('dean.schedule.exams') ? 'active' : '' }}">
                <i class="fas fa-file-alt me-2"></i> Imtihon jadvali
            </a>
        </div>
    </div>

    <!-- O'zlashtirish -->
    <div class="nav-item" x-data="{ open: {{ request()->routeIs('dean.grades.*') ? 'true' : 'false' }} }">
        <a href="#" class="nav-link d-flex justify-content-between align-items-center" @click.prevent="open = !open">
            <span><i class="fas fa-chart-line me-2"></i> O'zlashtirish</span>
            <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
        </a>
        <div class="submenu ms-3" x-show="open" x-collapse>
            <a href="{{ route('dean.grades.index') }}" class="nav-link {{ request()->routeIs('dean.grades.index') ? 'active' : '' }}">
                <i class="fas fa-star me-2"></i> Baholar
            </a>
            <a href="{{ route('dean.grades.gpa') }}" class="nav-link {{ request()->routeIs('dean.grades.gpa') ? 'active' : '' }}">
                <i class="fas fa-chart-bar me-2"></i> GPA statistikasi
            </a>
            <a href="{{ route('dean.grades.retakes') }}" class="nav-link {{ request()->routeIs('dean.grades.retakes') ? 'active' : '' }}">
                <i class="fas fa-redo me-2"></i> Qayta topshirish
            </a>
        </div>
    </div>

    <!-- Davomat -->
    <a href="{{ route('dean.attendance.index') }}" class="nav-link {{ request()->routeIs('dean.attendance.*') ? 'active' : '' }}">
        <i class="fas fa-clipboard-check me-2"></i>
        <span>Davomat</span>
    </a>

    <!-- Stipendiya -->
    <div class="nav-item" x-data="{ open: {{ request()->routeIs('dean.scholarship.*') ? 'true' : 'false' }} }">
        <a href="#" class="nav-link d-flex justify-content-between align-items-center" @click.prevent="open = !open">
            <span><i class="fas fa-award me-2"></i> Stipendiya</span>
            <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
        </a>
        <div class="submenu ms-3" x-show="open" x-collapse>
            <a href="{{ route('dean.scholarship.index') }}" class="nav-link {{ request()->routeIs('dean.scholarship.index') ? 'active' : '' }}">
                <i class="fas fa-list me-2"></i> Stipendiatlar
            </a>
            <a href="{{ route('dean.scholarship.applications') }}" class="nav-link {{ request()->routeIs('dean.scholarship.applications') ? 'active' : '' }}">
                <i class="fas fa-file-signature me-2"></i> Arizalar
            </a>
        </div>
    </div>

    <!-- Hisobotlar -->
    <div class="nav-item" x-data="{ open: {{ request()->routeIs('dean.reports.*') ? 'true' : 'false' }} }">
        <a href="#" class="nav-link d-flex justify-content-between align-items-center" @click.prevent="open = !open">
            <span><i class="fas fa-chart-pie me-2"></i> Hisobotlar</span>
            <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
        </a>
        <div class="submenu ms-3" x-show="open" x-collapse>
            <a href="{{ route('dean.reports.students') }}" class="nav-link {{ request()->routeIs('dean.reports.students') ? 'active' : '' }}">
                <i class="fas fa-users me-2"></i> Talabalar hisoboti
            </a>
            <a href="{{ route('dean.reports.grades') }}" class="nav-link {{ request()->routeIs('dean.reports.grades') ? 'active' : '' }}">
                <i class="fas fa-chart-bar me-2"></i> O'zlashtirish hisoboti
            </a>
            <a href="{{ route('dean.reports.attendance') }}" class="nav-link {{ request()->routeIs('dean.reports.attendance') ? 'active' : '' }}">
                <i class="fas fa-calendar-check me-2"></i> Davomat hisoboti
            </a>
        </div>
    </div>

    <!-- E'lonlar -->
    <a href="{{ route('dean.announcements.index') }}" class="nav-link {{ request()->routeIs('dean.announcements.*') ? 'active' : '' }}">
        <i class="fas fa-bullhorn me-2"></i>
        <span>E'lonlar</span>
    </a>

    <hr class="my-3">

    <!-- Sozlamalar -->
    <a href="{{ route('dean.settings') }}" class="nav-link {{ request()->routeIs('dean.settings') ? 'active' : '' }}">
        <i class="fas fa-cog me-2"></i>
        <span>Sozlamalar</span>
    </a>
</nav>

<style>
.sidebar-nav .nav-link {
    padding: 0.75rem 1rem;
    color: #6c757d;
    border-radius: 0.5rem;
    margin-bottom: 0.25rem;
    transition: all 0.2s ease;
}

.sidebar-nav .nav-link:hover {
    background-color: rgba(25, 135, 84, 0.1);
    color: #198754;
}

.sidebar-nav .nav-link.active {
    background-color: #198754;
    color: white;
}

.sidebar-nav .submenu .nav-link {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

.sidebar-nav .submenu .nav-link.active {
    background-color: rgba(25, 135, 84, 0.15);
    color: #198754;
}

.transition-transform {
    transition: transform 0.2s ease;
}

.rotate-180 {
    transform: rotate(180deg);
}
</style>
