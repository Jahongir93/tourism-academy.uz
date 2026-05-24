<!-- HR Manager Sidebar -->
<nav class="navbar navbar-vertical navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand pt-0" href="{{ route('hr.dashboard') }}">
            <h3 class="text-primary">HR Portal</h3>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Navigation -->
            <ul class="navbar-nav">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}"
                       href="{{ route('hr.dashboard') }}">
                        <i class="fas fa-home text-primary"></i>
                        <span class="nav-link-text">Dashboard</span>
                    </a>
                </li>

                <!-- Xodimlar moduli -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('hr/employees*') ? 'active' : '' }}"
                       href="#navbar-employees" data-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('hr/employees*') ? 'true' : 'false' }}"
                       aria-controls="navbar-employees">
                        <i class="fas fa-users text-info"></i>
                        <span class="nav-link-text">Xodimlar</span>
                    </a>
                    <div class="collapse {{ request()->is('hr/employees*') ? 'show' : '' }}" id="navbar-employees">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.employees.index') }}">
                                    <i class="fas fa-list mr-2"></i> Barcha xodimlar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.employees.create') }}">
                                    <i class="fas fa-user-plus mr-2"></i> Yangi xodim
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.employees.export') }}">
                                    <i class="fas fa-file-export mr-2"></i> Eksport
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Talabalar moduli -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('hr/students*') ? 'active' : '' }}"
                       href="#navbar-students" data-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('hr/students*') ? 'true' : 'false' }}"
                       aria-controls="navbar-students">
                        <i class="fas fa-user-graduate text-success"></i>
                        <span class="nav-link-text">Talabalar</span>
                    </a>
                    <div class="collapse {{ request()->is('hr/students*') ? 'show' : '' }}" id="navbar-students">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.students.index') }}">
                                    <i class="fas fa-list mr-2"></i> Barcha talabalar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.students.create') }}">
                                    <i class="fas fa-user-plus mr-2"></i> Yangi talaba
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.students.export') }}">
                                    <i class="fas fa-file-export mr-2"></i> Eksport
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Davomat moduli -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('hr/attendance*') ? 'active' : '' }}"
                       href="#navbar-attendance" data-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('hr/attendance*') ? 'true' : 'false' }}"
                       aria-controls="navbar-attendance">
                        <i class="fas fa-clipboard-list text-warning"></i>
                        <span class="nav-link-text">Davomat</span>
                    </a>
                    <div class="collapse {{ request()->is('hr/attendance*') ? 'show' : '' }}" id="navbar-attendance">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.attendance.today') }}">
                                    <i class="fas fa-calendar-day mr-2"></i> Bugungi davomat
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.attendance.mark') }}">
                                    <i class="fas fa-check-circle mr-2"></i> Belgilash
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.attendance.calendar') }}">
                                    <i class="fas fa-calendar-alt mr-2"></i> Kalendar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.attendance.report') }}">
                                    <i class="fas fa-chart-bar mr-2"></i> Hisobotlar
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Hisobotlar -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('hr/reports*') ? 'active' : '' }}"
                       href="#navbar-reports" data-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('hr/reports*') ? 'true' : 'false' }}"
                       aria-controls="navbar-reports">
                        <i class="fas fa-file-alt text-danger"></i>
                        <span class="nav-link-text">Hisobotlar</span>
                    </a>
                    <div class="collapse {{ request()->is('hr/reports*') ? 'show' : '' }}" id="navbar-reports">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.reports.employee-summary') }}">
                                    <i class="fas fa-users mr-2"></i> Xodimlar hisoboti
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.reports.student-summary') }}">
                                    <i class="fas fa-graduation-cap mr-2"></i> Talabalar hisoboti
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.reports.attendance-summary') }}">
                                    <i class="fas fa-clipboard-check mr-2"></i> Davomat hisoboti
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.reports.monthly-report') }}">
                                    <i class="fas fa-calendar mr-2"></i> Oylik hisobot
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hr.reports.generate') }}">
                                    <i class="fas fa-file-pdf mr-2"></i> Hisobot yaratish
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Separator -->
                <hr class="my-3">

                <!-- Heading -->
                <h6 class="navbar-heading text-muted">Qo'shimcha</h6>

                <!-- Settings (agar kerak bo'lsa) -->
                @if(auth()->user()->hasRole('superadmin'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-cog text-default"></i>
                        <span class="nav-link-text">Admin Panel</span>
                    </a>
                </li>
                @endif

                <!-- Logout -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt text-danger"></i>
                        <span class="nav-link-text">Chiqish</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
/* HR Sidebar custom styles */
#sidenav-main {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 1030;
    width: 250px;
    padding-top: 1rem;
    overflow-x: hidden;
    overflow-y: auto;
    background: #fff;
    box-shadow: 0 0 2rem 0 rgba(136, 152, 170, .15);
}

#sidenav-main .navbar-nav .nav-link {
    padding: .75rem 1.5rem;
    color: #32325d;
    transition: all .15s ease;
}

#sidenav-main .navbar-nav .nav-link:hover {
    color: #5e72e4;
    background-color: #f6f9fc;
}

#sidenav-main .navbar-nav .nav-link.active {
    color: #5e72e4;
    background-color: #e9ecef;
}

#sidenav-main .navbar-nav .nav-link i {
    min-width: 2.25rem;
    font-size: .9375rem;
    line-height: 1.5rem;
}

#sidenav-main .navbar-heading {
    padding: 0 1.5rem;
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .04em;
}

/* Submenu styles */
#sidenav-main .nav-sm .nav-link {
    padding: .5rem 1rem .5rem 3.5rem;
    font-size: .875rem;
}

#sidenav-main .collapse {
    transition: all .15s ease;
}

/* Mobile responsive */
@media (max-width: 768px) {
    #sidenav-main {
        transform: translateX(-250px);
        transition: transform .3s ease-in-out;
    }

    #sidenav-main.show {
        transform: translateX(0);
    }

    .navbar-toggler {
        display: block !important;
    }
}
</style>