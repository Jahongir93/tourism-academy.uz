<!-- Teacher Sidebar -->
<nav class="navbar navbar-vertical navbar-expand-md navbar-light bg-white" id="teacher-sidenav">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand pt-0" href="{{ route('teacher.dashboard') }}">
            <h3 class="text-primary">O'qituvchi Kabineti</h3>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#teacher-sidenav-collapse">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="teacher-sidenav-collapse">
            <!-- Navigation -->
            <ul class="navbar-nav">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}"
                       href="{{ route('teacher.dashboard') }}">
                        <i class="fas fa-home text-primary"></i>
                        <span class="nav-link-text">Bosh sahifa</span>
                    </a>
                </li>

                <!-- LMS Moduli -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('lms*') || request()->is('teacher/lms*') ? 'active' : '' }}"
                       href="#navbar-lms" data-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('lms*') || request()->is('teacher/lms*') ? 'true' : 'false' }}">
                        <i class="fas fa-graduation-cap text-purple"></i>
                        <span class="nav-link-text">LMS Kurslar</span>
                    </a>
                    <div class="collapse {{ request()->is('lms*') || request()->is('teacher/lms*') ? 'show' : '' }}" id="navbar-lms">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('lms.courses.index') }}">
                                    <i class="fas fa-book-open mr-2"></i> Mening kurslarim
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('lms.courses.create') }}">
                                    <i class="fas fa-plus-circle mr-2"></i> Yangi kurs yaratish
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('lms.dashboard') }}">
                                    <i class="fas fa-tachometer-alt mr-2"></i> LMS Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.materials.index') }}">
                                    <i class="fas fa-file-alt mr-2"></i> Materiallar
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Guruhlar -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('teacher/groups*') ? 'active' : '' }}"
                       href="#navbar-groups" data-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('teacher/groups*') ? 'true' : 'false' }}">
                        <i class="fas fa-users text-success"></i>
                        <span class="nav-link-text">Guruhlar</span>
                    </a>
                    <div class="collapse {{ request()->is('teacher/groups*') ? 'show' : '' }}" id="navbar-groups">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.groups.index') }}">
                                    <i class="fas fa-list mr-2"></i> Mening guruhlarim
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.groups.students') }}">
                                    <i class="fas fa-user-friends mr-2"></i> Talabalar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.groups.messages') }}">
                                    <i class="fas fa-envelope mr-2"></i> Xabarlar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.groups.statistics') }}">
                                    <i class="fas fa-chart-pie mr-2"></i> Statistika
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Elektron Jurnal -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('teacher/journal*') ? 'active' : '' }}"
                       href="#navbar-journal" data-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('teacher/journal*') ? 'true' : 'false' }}">
                        <i class="fas fa-book-open text-warning"></i>
                        <span class="nav-link-text">Jurnal</span>
                    </a>
                    <div class="collapse {{ request()->is('teacher/journal*') ? 'show' : '' }}" id="navbar-journal">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.journal.index') }}">
                                    <i class="fas fa-book mr-2"></i> Jurnal ko'rish
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.journal.grades') }}">
                                    <i class="fas fa-pen mr-2"></i> Baho qo'yish
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.journal.topics') }}">
                                    <i class="fas fa-list-alt mr-2"></i> Mavzular
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.journal.export') }}">
                                    <i class="fas fa-file-export mr-2"></i> Eksport
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Davomat -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('teacher/attendance*') ? 'active' : '' }}"
                       href="#navbar-attendance" data-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('teacher/attendance*') ? 'true' : 'false' }}">
                        <i class="fas fa-clipboard-list text-danger"></i>
                        <span class="nav-link-text">Davomat</span>
                    </a>
                    <div class="collapse {{ request()->is('teacher/attendance*') ? 'show' : '' }}" id="navbar-attendance">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.attendance.mark') }}">
                                    <i class="fas fa-check-circle mr-2"></i> Belgilash
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.attendance.today') }}">
                                    <i class="fas fa-calendar-day mr-2"></i> Bugungi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.attendance.history') }}">
                                    <i class="fas fa-history mr-2"></i> Tarix
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.attendance.report') }}">
                                    <i class="fas fa-chart-bar mr-2"></i> Hisobot
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- O'quv reja -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('teacher/curriculum*') ? 'active' : '' }}"
                       href="#navbar-curriculum" data-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('teacher/curriculum*') ? 'true' : 'false' }}">
                        <i class="fas fa-graduation-cap text-secondary"></i>
                        <span class="nav-link-text">O'quv reja</span>
                    </a>
                    <div class="collapse {{ request()->is('teacher/curriculum*') ? 'show' : '' }}" id="navbar-curriculum">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.curriculum.view') }}">
                                    <i class="fas fa-eye mr-2"></i> Ko'rish
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.curriculum.create') }}">
                                    <i class="fas fa-plus mr-2"></i> Yaratish
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.curriculum.edit') }}">
                                    <i class="fas fa-edit mr-2"></i> Tahrirlash
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.curriculum.materials') }}">
                                    <i class="fas fa-file-upload mr-2"></i> Materiallar
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Vedmost -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('teacher/vedomost*') ? 'active' : '' }}"
                       href="#navbar-vedomost" data-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('teacher/vedomost*') ? 'true' : 'false' }}">
                        <i class="fas fa-file-invoice text-primary"></i>
                        <span class="nav-link-text">Vedmost</span>
                    </a>
                    <div class="collapse {{ request()->is('teacher/vedomost*') ? 'show' : '' }}" id="navbar-vedomost">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.vedomost.create') }}">
                                    <i class="fas fa-plus-circle mr-2"></i> Yaratish
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.vedomost.list') }}">
                                    <i class="fas fa-list mr-2"></i> Ro'yxat
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.vedomost.fill', 0) }}">
                                    <i class="fas fa-pen-alt mr-2"></i> To'ldirish
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.vedomost.submit', 0) }}">
                                    <i class="fas fa-paper-plane mr-2"></i> Topshirish
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Dars jadvali -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('teacher/schedule*') ? 'active' : '' }}"
                       href="{{ route('teacher.schedule.index') }}">
                        <i class="fas fa-calendar-alt text-info"></i>
                        <span class="nav-link-text">Dars jadvali</span>
                    </a>
                </li>

                <!-- Baholar va Topshiriqlar -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('teacher/grades*') || request()->is('teacher/assignments*') ? 'active' : '' }}"
                       href="#navbar-grades" data-toggle="collapse" role="button"
                       aria-expanded="{{ request()->is('teacher/grades*') || request()->is('teacher/assignments*') ? 'true' : 'false' }}">
                        <i class="fas fa-star text-warning"></i>
                        <span class="nav-link-text">Baholash</span>
                    </a>
                    <div class="collapse {{ request()->is('teacher/grades*') || request()->is('teacher/assignments*') ? 'show' : '' }}" id="navbar-grades">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.grades.index') }}">
                                    <i class="fas fa-star-half-alt mr-2"></i> Baholar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.assignments.index') }}">
                                    <i class="fas fa-tasks mr-2"></i> Topshiriqlar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.assignments.create') }}">
                                    <i class="fas fa-plus mr-2"></i> Topshiriq berish
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.grades.statistics') }}">
                                    <i class="fas fa-chart-line mr-2"></i> Statistika
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Separator -->
                <hr class="my-3">

                <!-- Heading -->
                <h6 class="navbar-heading text-muted">Qo'shimcha</h6>

                <!-- Profil -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('teacher.profile') }}">
                        <i class="fas fa-user-circle text-default"></i>
                        <span class="nav-link-text">Mening profilim</span>
                    </a>
                </li>

                <!-- SuperAdmin panel (agar kerak bo'lsa) -->
                @if(auth()->user()->hasRole('superadmin'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-cog text-default"></i>
                        <span class="nav-link-text">Admin Panel</span>
                    </a>
                </li>
                @endif

                <!-- Chiqish -->
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
/* Purple color for LMS */
.text-purple {
    color: #8b5cf6 !important;
}

/* Teacher Sidebar custom styles */
#teacher-sidenav {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 1030;
    width: 260px;
    padding-top: 1rem;
    overflow-x: hidden;
    overflow-y: auto;
    background: linear-gradient(180deg, #fff 0%, #f8f9fa 100%);
    box-shadow: 0 0 2rem 0 rgba(136, 152, 170, .15);
}

#teacher-sidenav .navbar-brand {
    font-weight: 600;
    color: #5e72e4;
}

#teacher-sidenav .navbar-nav .nav-link {
    padding: .8rem 1.5rem;
    color: #525f7f;
    font-size: .9rem;
    transition: all .15s ease;
    border-radius: .375rem;
    margin: .125rem 1rem;
}

#teacher-sidenav .navbar-nav .nav-link:hover {
    color: #5e72e4;
    background-color: #f0f3ff;
}

#teacher-sidenav .navbar-nav .nav-link.active {
    color: #fff;
    background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%);
    box-shadow: 0 4px 6px rgba(50, 50, 93, .11), 0 1px 3px rgba(0, 0, 0, .08);
}

#teacher-sidenav .navbar-nav .nav-link i {
    min-width: 2rem;
    font-size: .9375rem;
    line-height: 1.5rem;
}

#teacher-sidenav .navbar-heading {
    padding: 0 1.5rem;
    font-size: .75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
}

/* Submenu styles */
#teacher-sidenav .nav-sm .nav-link {
    padding: .5rem 1rem .5rem 3.5rem;
    font-size: .875rem;
    background: transparent;
}

#teacher-sidenav .nav-sm .nav-link:hover {
    background-color: #f0f3ff;
}

#teacher-sidenav .collapse {
    transition: all .15s ease;
}

/* Mobile responsive */
@media (max-width: 768px) {
    #teacher-sidenav {
        transform: translateX(-260px);
        transition: transform .3s ease-in-out;
    }

    #teacher-sidenav.show {
        transform: translateX(0);
    }

    .navbar-toggler {
        display: block !important;
    }
}

/* Main content adjustment */
.main-content {
    margin-left: 260px;
    transition: margin-left .3s ease;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
    }
}
</style>