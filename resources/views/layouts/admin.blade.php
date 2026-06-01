<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Admin Panel') - Tourism Academy</title>

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap5.min.css') }}">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}">

    <!-- Select2 CSS -->
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet" />

    @stack('styles')

    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #16a085;
            --light-bg: #ecf0f1;
            --white: #ffffff;
            --text-dark: #2c3e50;
            --text-muted: #7f8c8d;
            --border-color: #dee2e6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light-bg);
            color: var(--text-dark);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-header {
            padding: 25px 20px;
            background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            color: var(--white);
            text-decoration: none;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .sidebar-logo i {
            font-size: 1.8rem;
            margin-right: 12px;
            color: var(--accent-color);
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-item {
            margin-bottom: 5px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.1);
            color: var(--white);
            border-left-color: var(--accent-color);
        }

        .sidebar-link.active {
            background: rgba(52,152,219,0.2);
            color: var(--white);
            border-left-color: var(--accent-color);
        }

        .sidebar-link i {
            width: 25px;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        /* Submenu styles */
        .sidebar-item.has-submenu .sidebar-link {
            justify-content: space-between;
        }
        .sidebar-item.has-submenu .sidebar-link .submenu-arrow {
            width: auto;
            margin-right: 0;
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }
        .sidebar-item.has-submenu.open .submenu-arrow {
            transform: rotate(90deg);
        }
        .submenu {
            display: none;
            list-style: none;
            padding: 0;
            margin: 0;
            background: rgba(0,0,0,0.15);
        }
        .submenu li a {
            display: flex;
            align-items: center;
            padding: 10px 20px 10px 55px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .submenu li a:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }
        .submenu li a.active {
            background: rgba(52,152,219,0.3);
            color: white;
        }
        .submenu li a i {
            width: 20px;
            margin-right: 10px;
            font-size: 0.85rem;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* Top Navbar */
        .top-navbar {
            background: var(--white);
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .navbar-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* User Badge */
        .user-badge {
            background: var(--light-bg);
            padding: 8px 16px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-badge:hover {
            background: var(--accent-color);
            color: var(--white);
        }

        /* Page Content */
        .page-content {
            padding: 30px;
        }

        /* Cards */
        .card-custom {
            background: var(--white);
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 30px;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .top-navbar {
                padding: 15px;
            }

            .page-content {
                padding: 20px;
            }
        }

        /* Additional Styles */
        .table th {
            background: var(--light-bg);
            font-weight: 600;
        }

        .btn-primary {
            background: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background: #2980b9;
            border-color: #2980b9;
        }

        .btn-success {
            background: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-success:hover {
            background: #229954;
            border-color: #229954;
        }

        .btn-danger {
            background: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-danger:hover {
            background: #c0392b;
            border-color: #c0392b;
        }

        .btn-warning {
            background: var(--warning-color);
            border-color: var(--warning-color);
        }

        .btn-warning:hover {
            background: #e67e22;
            border-color: #e67e22;
        }

        .btn-info {
            background: var(--info-color);
            border-color: var(--info-color);
        }

        .btn-info:hover {
            background: #138d75;
            border-color: #138d75;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
                <i class="fas fa-graduation-cap"></i>
                <span>Tourism Academy</span>
            </a>
        </div>

        <div class="sidebar-menu">
            <!-- Bosh sahifa -->
            <div class="sidebar-item">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Bosh sahifa</span>
                </a>
            </div>

            <!-- Talabalar -->
            <div class="sidebar-item">
                <a href="{{ route('students.index') }}" class="sidebar-link {{ request()->routeIs('students.*') || request()->routeIs('student-contingent.*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>Talabalar</span>
                </a>
            </div>

            <!-- Xodimlar -->
            <div class="sidebar-item">
                <a href="{{ route('employees.index') }}" class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>Xodimlar</span>
                </a>
            </div>

            <!-- Elektron jurnal -->
            <div class="sidebar-item">
                <a href="{{ route('journal.index') }}" class="sidebar-link {{ request()->routeIs('journal.*') || request()->routeIs('attendance.*') || request()->routeIs('grades.*') || request()->routeIs('assignments.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Elektron jurnal</span>
                </a>
            </div>

            <!-- Dars jadvali -->
            <div class="sidebar-item">
                <a href="{{ route('schedule.index') }}" class="sidebar-link {{ request()->routeIs('schedule.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Dars jadvali</span>
                </a>
            </div>

            <!-- Tuzilma -->
            <div class="sidebar-item">
                <a href="{{ route('structure.faculties.index') }}" class="sidebar-link {{ request()->routeIs('structure.*') ? 'active' : '' }}">
                    <i class="fas fa-sitemap"></i>
                    <span>Tuzilma</span>
                </a>
            </div>

            <!-- Online ta'lim -->
            <div class="sidebar-item">
                <a href="{{ route('lms.dashboard') }}" class="sidebar-link {{ request()->routeIs('lms.dashboard') || request()->routeIs('lms.courses.*') ? 'active' : '' }}">
                    <i class="fas fa-laptop"></i>
                    <span>Online ta'lim</span>
                </a>
            </div>

            <!-- Imtihonlar -->
            <div class="sidebar-item">
                <a href="{{ route('lms.exams.index') }}" class="sidebar-link {{ request()->routeIs('lms.exams.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt" style="color: #27ae60;"></i>
                    <span>Imtihonlar</span>
                </a>
            </div>

            <!-- Kutubxona -->
            <div class="sidebar-item">
                <a href="{{ route('lms.library.index') }}" class="sidebar-link {{ request()->routeIs('lms.library.*') ? 'active' : '' }}">
                    <i class="fas fa-book-open" style="color: #9b59b6;"></i>
                    <span>E-kutubxona</span>
                </a>
            </div>

            <!-- CMS -->
            <div class="sidebar-item">
                <a href="{{ route('admin.cms.index') }}" class="sidebar-link {{ request()->routeIs('admin.cms.*') || request()->routeIs('admin.menu.*') ? 'active' : '' }}">
                    <i class="fas fa-edit"></i>
                    <span>CMS Boshqaruvi</span>
                </a>
            </div>

            <!-- Kampus Virtual Turi -->
            <div class="sidebar-item has-submenu {{ request()->routeIs('campus-tour.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="sidebar-link {{ request()->routeIs('campus-tour.*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
                    <span style="display: flex; align-items: center;">
                        <i class="fas fa-map-marked-alt" style="color: #17a2b8;"></i>
                        <span>Kampus Turi</span>
                    </span>
                    <i class="fas fa-chevron-right submenu-arrow"></i>
                </a>
                <ul class="submenu" style="{{ request()->routeIs('campus-tour.*') ? 'display: block;' : '' }}">
                    <li>
                        <a href="{{ route('campus-tour.dashboard') }}" class="{{ request()->routeIs('campus-tour.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('campus-tour.panoramas.index') }}" class="{{ request()->routeIs('campus-tour.panoramas.*') ? 'active' : '' }}">
                            <i class="fas fa-vr-cardboard"></i> 360° Panoramalar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('campus-tour.buildings.index') }}" class="{{ request()->routeIs('campus-tour.buildings.*') ? 'active' : '' }}">
                            <i class="fas fa-building"></i> Binolar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('campus-tour.routes.index') }}" class="{{ request()->routeIs('campus-tour.routes.*') ? 'active' : '' }}">
                            <i class="fas fa-route"></i> Yo'nalishlar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('campus-tour.map.index') }}" class="{{ request()->routeIs('campus-tour.map.*') ? 'active' : '' }}">
                            <i class="fas fa-map"></i> Xarita sozlamalari
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Davomat -->
            <div class="sidebar-item">
                <a href="{{ route('attendance.face-recognition') }}" class="sidebar-link {{ request()->routeIs('attendance.face-recognition') ? 'active' : '' }}">
                    <i class="fas fa-user-check"></i>
                    <span>Davomat (Yuz tanish)</span>
                </a>
            </div>

            <!-- Page Builder -->
            <div class="sidebar-item">
                <a href="{{ route('page-builder.index') }}" class="sidebar-link {{ request()->routeIs('page-builder.*') ? 'active' : '' }}">
                    <i class="fas fa-paint-brush"></i>
                    <span>Page Builder</span>
                </a>
            </div>

            <!-- Bildirishnomalar -->
            <div class="sidebar-item">
                <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i>
                    <span>Bildirishnomalar</span>
                </a>
            </div>

            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">

            <!-- Chiqish -->
            <div class="sidebar-item">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <button type="submit" class="sidebar-link" style="background: none; border: none; width: 100%; text-align: left;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Chiqish</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="mobile-menu-toggle me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="navbar-title mb-0">@yield('page-title', 'Admin Panel')</h1>
            </div>
            <div class="navbar-user">
                <div class="user-badge">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('vendor/jquery/jquery-3.7.0.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- DataTables JS -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>

    <!-- Chart.js -->
    <script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>

    <script>
        // Toggle Sidebar for Mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Toggle Submenu
        function toggleSubmenu(element) {
            var parent = element.parentElement;
            var submenu = parent.querySelector('.submenu');

            parent.classList.toggle('open');

            if (submenu.style.display === 'block') {
                submenu.style.display = 'none';
            } else {
                submenu.style.display = 'block';
            }
        }

        // Initialize DataTables
        $(document).ready(function() {
            if ($('.datatable').length) {
                $('.datatable').DataTable({
                    language: {
                        "sEmptyTable":     "Ma'lumot mavjud emas",
                        "sInfo":           "_TOTAL_ ta yozuvdan _START_ dan _END_ gacha ko'rsatilmoqda",
                        "sInfoEmpty":      "0 ta yozuvdan 0 dan 0 gacha ko'rsatilmoqda",
                        "sInfoFiltered":   "(_MAX_ ta yozuvdan saralandi)",
                        "sLengthMenu":     "_MENU_ ta yozuvni ko'rsat",
                        "sLoadingRecords": "Yuklanmoqda...",
                        "sProcessing":     "Qayta ishlanmoqda...",
                        "sSearch":         "Qidirish:",
                        "sZeroRecords":    "Hech qanday mos yozuv topilmadi",
                        "oPaginate": {
                            "sFirst":    "Birinchi",
                            "sLast":     "Oxirgi",
                            "sNext":     "Keyingi",
                            "sPrevious": "Oldingi"
                        }
                    },
                    responsive: true,
                    pageLength: 25
                });
            }

            // Initialize Select2
            if ($('.select2').length) {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            }
        });

        // Delete Confirmation
        function confirmDelete(form) {
            event.preventDefault();
            Swal.fire({
                title: 'Ishonchingiz komilmi?',
                text: "Bu amalni ortga qaytarib bo'lmaydi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Ha, o\'chirish!',
                cancelButtonText: 'Bekor qilish'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

    <!-- Chat Popup Component -->
    @include('components.chat-popup')

    @stack('scripts')
</body>
</html>