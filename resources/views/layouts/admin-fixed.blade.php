<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Admin Dashboard') - HEMIS</title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    
    <style>
        :root {
            --sidebar-bg: #1e3a8a;
            --sidebar-hover: #2563eb;
            --sidebar-active: #3b82f6;
            --sidebar-text: #e0e7ff;
            --sidebar-text-hover: #ffffff;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #1e40af 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-link:hover {
            background-color: rgba(255,255,255,0.05);
            color: var(--sidebar-text-hover);
            border-left-color: var(--sidebar-hover);
        }
        
        .sidebar-link.active {
            background-color: rgba(255,255,255,0.1);
            color: var(--sidebar-text-hover);
            border-left-color: var(--sidebar-active);
        }
        
        .sidebar-dropdown {
            background-color: rgba(0,0,0,0.1);
        }
        
        .sidebar-dropdown .sidebar-link {
            padding-left: 3rem;
            font-size: 0.9rem;
        }
        
        .main-content {
            flex: 1;
            min-height: 100vh;
        }
        
        .navbar-top {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 1rem 1.5rem;
        }
        
        .content-wrapper {
            padding: 2rem;
        }
        
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .dropdown-toggle::after {
            transition: transform 0.3s ease;
        }
        
        .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <h4 class="text-white mb-0">HEMIS</h4>
                <small class="text-light">Admin Panel</small>
            </div>
            
            <nav class="mt-3">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home me-3"></i>
                    <span>Bosh sahifa</span>
                </a>
                
                <!-- O'quv jarayoni -->
                <div class="mt-2">
                    <a class="sidebar-link dropdown-toggle {{ request()->routeIs('journal.*', 'attendance.*', 'grades.*', 'schedule.*') ? 'active' : '' }}" 
                       data-bs-toggle="collapse" href="#educationMenu" role="button" 
                       aria-expanded="{{ request()->routeIs('journal.*', 'attendance.*', 'grades.*', 'schedule.*') ? 'true' : 'false' }}">
                        <i class="fas fa-graduation-cap me-3"></i>
                        <span>O'quv jarayoni</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('journal.*', 'attendance.*', 'grades.*', 'schedule.*') ? 'show' : '' }}" id="educationMenu">
                        <div class="sidebar-dropdown">
                            <a href="{{ route('journal.index') }}" class="sidebar-link {{ request()->routeIs('journal.*') ? 'active' : '' }}">
                                <i class="fas fa-book me-2"></i> Elektron jurnal
                            </a>
                            <a href="{{ route('attendance.all') }}" class="sidebar-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                                <i class="fas fa-user-check me-2"></i> Davomat
                            </a>
                            <a href="{{ route('grades.all') }}" class="sidebar-link {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                                <i class="fas fa-chart-line me-2"></i> Baholar
                            </a>
                            <a href="{{ route('schedule.index') }}" class="sidebar-link {{ request()->routeIs('schedule.*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt me-2"></i> Dars jadvali
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Tuzilma -->
                <div class="mt-2">
                    <a class="sidebar-link dropdown-toggle {{ request()->routeIs('structure.*') ? 'active' : '' }}" 
                       data-bs-toggle="collapse" href="#structureMenu" role="button" 
                       aria-expanded="{{ request()->routeIs('structure.*') ? 'true' : 'false' }}">
                        <i class="fas fa-sitemap me-3"></i>
                        <span>Tuzilma</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('structure.*') ? 'show' : '' }}" id="structureMenu">
                        <div class="sidebar-dropdown">
                            <a href="{{ route('structure.faculties.index') }}" class="sidebar-link {{ request()->routeIs('structure.faculties.*') ? 'active' : '' }}">
                                <i class="fas fa-university me-2"></i> Fakultetlar
                            </a>
                            <a href="{{ route('structure.departments.index') }}" class="sidebar-link {{ request()->routeIs('structure.departments.*') ? 'active' : '' }}">
                                <i class="fas fa-building me-2"></i> Kafedralar
                            </a>
                            <a href="{{ route('structure.positions.index') }}" class="sidebar-link {{ request()->routeIs('structure.positions.*') ? 'active' : '' }}">
                                <i class="fas fa-user-tie me-2"></i> Lavozimlar
                            </a>
                            <a href="{{ route('structure.chart.index') }}" class="sidebar-link {{ request()->routeIs('structure.chart.*') ? 'active' : '' }}">
                                <i class="fas fa-project-diagram me-2"></i> Tashkiliy tuzilma
                            </a>
                            <hr class="my-2 mx-3 border-light opacity-25">
                            <small class="text-light px-3 text-uppercase" style="font-size: 0.7rem;">Akademik</small>
                            <a href="{{ route('structure.academic.programs.index') }}" class="sidebar-link {{ request()->routeIs('structure.academic.programs.*') ? 'active' : '' }}">
                                <i class="fas fa-graduation-cap me-2"></i> Ta'lim yo'nalishlari
                            </a>
                            <a href="{{ route('structure.academic.subjects.index') }}" class="sidebar-link {{ request()->routeIs('structure.academic.subjects.*') ? 'active' : '' }}">
                                <i class="fas fa-book me-2"></i> Fanlar
                            </a>
                            <a href="{{ route('structure.academic.curriculum.index') }}" class="sidebar-link {{ request()->routeIs('structure.academic.curriculum.*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt me-2"></i> O'quv rejalar
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Talabalar -->
                <a href="{{ route('students.index') }}" class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }} mt-2">
                    <i class="fas fa-users me-3"></i>
                    <span>Talabalar</span>
                </a>
                
                <!-- Xodimlar -->
                <a href="{{ route('employees.index') }}" class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie me-3"></i>
                    <span>Xodimlar</span>
                </a>
                
                <!-- Hisobotlar -->
                <a href="#" class="sidebar-link">
                    <i class="fas fa-chart-bar me-3"></i>
                    <span>Hisobotlar</span>
                </a>
                
                <!-- Sozlamalar -->
                <a href="#" class="sidebar-link">
                    <i class="fas fa-cog me-3"></i>
                    <span>Sozlamalar</span>
                </a>

                <!-- CMS Boshqaruvi -->
                <a href="{{ route('admin.cms.index') }}" class="sidebar-link {{ request()->routeIs('admin.cms.*') ? 'active' : '' }} mt-2">
                    <i class="fas fa-edit me-3"></i>
                    <span>CMS Boshqaruvi</span>
                </a>

                <!-- Kampus Virtual Turi -->
                <a href="{{ route('campus-tour.dashboard') }}" class="sidebar-link {{ request()->routeIs('campus-tour.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marked-alt me-3" style="color: #17a2b8;"></i>
                    <span>Kampus Turi</span>
                </a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <nav class="navbar-top d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-link text-dark d-md-none" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 d-inline">@yield('page-title', 'Dashboard')</h5>
                </div>
                
                <div class="d-flex align-items-center">
                    <!-- Notifications -->
                    <div class="dropdown me-3">
                        <button class="btn btn-link text-dark position-relative" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Bildirishnomalar</h6></li>
                            <li><a class="dropdown-item" href="#">Yangi topshiriq yuklandi</a></li>
                            <li><a class="dropdown-item" href="#">Dars jadvali o'zgardi</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Barchasini ko'rish</a></li>
                        </ul>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="dropdown">
                        <button class="btn btn-link text-dark d-flex align-items-center" data-bs-toggle="dropdown">
                            <img src="{{ url('avatar') }}?name=Admin&background=3b82f6&color=fff" 
                                 class="rounded-circle me-2" width="32" height="32" alt="User">
                            <span>{{ Auth::user()->name ?? 'Admin' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Sozlamalar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i> Chiqish
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Page Content -->
            <div class="content-wrapper">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- jQuery -->
    <script src="{{ asset('vendor/jquery/jquery-3.7.0.min.js') }}"></script>
    
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('d-none');
        }
    </script>
    
    @stack('scripts')
</body>
</html>