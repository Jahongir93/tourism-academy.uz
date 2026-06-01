<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tourism Academy - Innovatsion ta\'lim markazi')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ asset('vendor/fonts/inter.css') }}" rel="stylesheet">

    <!-- TailwindCSS -->
    <script src="{{ asset('vendor/tailwind/tailwind.min.js') }}"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Alpine.js -->
    <script defer src="{{ asset('vendor/alpine/alpine.min.js') }}"></script>

    <!-- Custom Styles -->
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        /* Preloader Animation */
        .preloader-spinner {
            border: 4px solid #e5e7eb;
            border-top: 4px solid #0066CC;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #0066CC, #0052A3);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #0052A3, #003d7a);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(to right, #0066CC, #0052A3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Button animations */
        .btn-hover {
            transition: all 0.3s ease;
        }

        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Card animations */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* ============================================
           HEADER / NAVBAR STYLES
           ============================================ */

        /* === Main Header === */
        .site-header {
            background-color: #ffffff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        /* Scrolled state */
        .site-header.scrolled {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        /* === Container === */
        .site-header .header-container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* === Navbar === */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
            gap: 40px;
        }

        /* === Logo === */
        .navbar-brand .logo {
            display: flex;
            align-items: center;
            transition: opacity 0.3s ease;
            text-decoration: none;
        }

        .navbar-brand .logo:hover {
            opacity: 0.85;
        }

        .navbar-brand .logo img {
            height: 50px;
            width: auto;
            display: block;
        }

        /* === Navigation Menu === */
        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
            margin: 0;
            padding: 0;
            flex: 1;
            justify-content: center;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: block;
            font-size: 15px;
            font-weight: 500;
            color: #1a1a1a;
            text-decoration: none;
            padding: 8px 4px;
            transition: color 0.3s ease;
            white-space: nowrap;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #0066CC;
            transition: width 0.3s ease;
        }

        .nav-link:hover {
            color: #0066CC;
        }

        .nav-link:hover::after,
        .nav-item.active .nav-link::after {
            width: 100%;
        }

        .nav-item.active .nav-link {
            color: #0066CC;
            font-weight: 600;
        }

        /* === Right Actions === */
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* === Language Selector === */
        .language-selector {
            position: relative;
        }

        .lang-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: 1px solid rgba(0, 0, 0, 0.12);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 500;
            color: #1a1a1a;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .lang-btn:hover {
            border-color: #0066CC;
            background-color: rgba(0, 102, 204, 0.04);
        }

        .flag-icon {
            font-size: 18px;
            line-height: 1;
        }

        .chevron-icon {
            transition: transform 0.3s ease;
        }

        .language-selector:hover .chevron-icon {
            transform: rotate(180deg);
        }

        /* Language Dropdown */
        .lang-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            padding: 8px;
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 100;
        }

        .language-selector:hover .lang-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .lang-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            font-size: 14px;
            color: #1a1a1a;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.2s ease;
        }

        .lang-option:hover {
            background-color: rgba(0, 102, 204, 0.06);
        }

        /* === Login Button === */
        .btn-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #0066CC;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-login:hover {
            background-color: #0052A3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
            color: #ffffff;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* === Mobile Menu Toggle === */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 28px;
            height: 22px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .hamburger-line {
            width: 100%;
            height: 3px;
            background-color: #1a1a1a;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translateY(10px);
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translateY(-10px);
        }

        /* === Mobile Menu Overlay === */
        .mobile-menu-overlay {
            position: fixed;
            top: 80px;
            left: 0;
            width: 100%;
            height: calc(100vh - 80px);
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
        }

        .mobile-menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .mobile-menu {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            max-width: 400px;
            height: 100%;
            background-color: #ffffff;
            padding: 32px 24px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .mobile-menu-overlay.active .mobile-menu {
            transform: translateX(0);
        }

        .mobile-nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .mobile-nav-list li {
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .mobile-nav-list li:last-child {
            border-bottom: none;
        }

        .mobile-nav-list a {
            display: block;
            padding: 16px 0;
            font-size: 16px;
            font-weight: 500;
            color: #1a1a1a;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .mobile-nav-list a:hover {
            color: #0066CC;
        }

        .mobile-menu-footer {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
        }

        .btn-login.mobile {
            width: 100%;
        }

        /* Mobile Language Selector */
        .mobile-lang-selector {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .mobile-lang-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            background: #f5f5f5;
            border: 2px solid transparent;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #1a1a1a;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .mobile-lang-btn:hover,
        .mobile-lang-btn.active {
            border-color: #0066CC;
            background: rgba(0, 102, 204, 0.06);
            color: #0066CC;
        }

        /* ============================================
           RESPONSIVE DESIGN
           ============================================ */

        /* Large Desktop (1400px+) */
        @media (min-width: 1400px) {
            .site-header .header-container {
                max-width: 1400px;
            }
        }

        /* Desktop (1200px - 1399px) */
        @media (max-width: 1399px) {
            .navbar-menu {
                gap: 24px;
            }
        }

        /* Tablet (992px - 1199px) */
        @media (max-width: 1199px) {
            .navbar-menu {
                gap: 20px;
            }

            .nav-link {
                font-size: 14px;
            }
        }

        /* Tablet & Mobile (below 992px) */
        @media (max-width: 991px) {
            .navbar {
                height: 70px;
            }

            .navbar-brand .logo img {
                height: 42px;
            }

            .navbar-menu {
                display: none;
            }

            .navbar-actions {
                gap: 12px;
            }

            .language-selector {
                display: none;
            }

            .btn-login:not(.mobile) {
                padding: 8px 16px;
                font-size: 13px;
            }

            .mobile-menu-toggle {
                display: flex;
            }

            .mobile-menu-overlay {
                top: 70px;
                height: calc(100vh - 70px);
            }
        }

        /* Mobile (below 576px) */
        @media (max-width: 575px) {
            .site-header .header-container {
                padding: 0 16px;
            }

            .navbar {
                height: 64px;
            }

            .navbar-brand .logo img {
                height: 36px;
            }

            .btn-login:not(.mobile) {
                display: none;
            }

            .mobile-menu {
                max-width: 100%;
            }

            .mobile-menu-overlay {
                top: 64px;
                height: calc(100vh - 64px);
            }
        }

        /* ============================================
           ACCESSIBILITY STYLES
           ============================================ */
        .accessibility-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #0066CC, #0052a3);
            border: none;
            border-radius: 10px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 18px;
        }

        .accessibility-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }

        .accessibility-panel {
            position: fixed;
            top: 90px;
            right: 20px;
            width: 320px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .accessibility-panel.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .accessibility-header {
            padding: 16px 20px;
            background: linear-gradient(135deg, #0066CC, #0052a3);
            color: white;
            border-radius: 16px 16px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .accessibility-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .accessibility-close {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .accessibility-close:hover {
            background: rgba(255,255,255,0.3);
        }

        .accessibility-body {
            padding: 16px;
            max-height: 400px;
            overflow-y: auto;
        }

        .accessibility-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            margin-bottom: 8px;
            background: #f8fafc;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .accessibility-item:hover {
            background: #f1f5f9;
        }

        .accessibility-item-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .accessibility-item-icon {
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0066CC;
            font-size: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .accessibility-item-text {
            font-size: 14px;
            font-weight: 500;
            color: #1a1a1a;
        }

        .accessibility-toggle {
            position: relative;
            width: 44px;
            height: 24px;
            background: #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .accessibility-toggle.active {
            background: #0066CC;
        }

        .accessibility-toggle::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .accessibility-toggle.active::after {
            transform: translateX(20px);
        }

        .font-size-controls {
            display: flex;
            gap: 8px;
        }

        .font-size-btn {
            width: 36px;
            height: 36px;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: #374151;
            transition: all 0.2s;
        }

        .font-size-btn:hover {
            border-color: #0066CC;
            color: #0066CC;
        }

        .font-size-btn.active {
            background: #0066CC;
            border-color: #0066CC;
            color: white;
        }

        .accessibility-reset {
            width: 100%;
            padding: 12px;
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 8px;
        }

        .accessibility-reset:hover {
            background: #fecaca;
        }

        /* Accessibility Active States */
        body.high-contrast {
            filter: contrast(1.5);
        }

        body.grayscale {
            filter: grayscale(1);
        }

        body.large-cursor * {
            cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Cpath d='M0 0L20 20L12 20L16 28L12 30L8 22L0 30Z' fill='black' stroke='white' stroke-width='2'/%3E%3C/svg%3E"), auto !important;
        }

        body.underline-links a {
            text-decoration: underline !important;
        }

        body.readable-font * {
            font-family: 'OpenDyslexic', Arial, sans-serif !important;
        }

        body.highlight-focus *:focus {
            outline: 3px solid #ff6600 !important;
            outline-offset: 2px !important;
        }

        body.font-size-large {
            font-size: 120% !important;
        }

        body.font-size-xlarge {
            font-size: 140% !important;
        }

        @media (max-width: 768px) {
            .accessibility-panel {
                right: 10px;
                left: 10px;
                width: auto;
            }
        }

        /* Accessibility button always visible */
        .accessibility-btn {
            flex-shrink: 0;
        }

        @media (max-width: 575px) {
            .accessibility-btn {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 bg-white z-[9999] flex items-center justify-center">
        <div class="preloader-spinner"></div>
    </div>

    <!-- Header -->
    <header class="site-header">
        <div class="header-container">
            <nav class="navbar">
                <!-- Logo -->
                <div class="navbar-brand">
                    <a href="{{ route('home') }}" class="logo">
                        <img src="{{ asset('images/logo.png') }}" alt="International Academy of Tourism and Hospitality">
                    </a>
                </div>

                <!-- Navigation Menu (Desktop) -->
                @php
                    // CMS menyu elementlarini olish (Biz haqimizda uchun)
                    $headerMenu = \App\Models\CmsMenu::where('location', 'header')->where('is_active', true)->first();
                    $cmsAboutSubmenus = collect();
                    $frontendLang = app()->getLocale();
                    $titleField = 'title_' . $frontendLang;
                    if ($headerMenu) {
                        $aboutParent = $headerMenu->menuItems()->whereNull('parent_id')->where('is_active', true)->first();
                        if ($aboutParent) {
                            $cmsAboutSubmenus = $aboutParent->children()->where('is_active', true)->orderBy('order_position')->get();
                        }
                    }
                    $hasAboutSubmenus = $cmsAboutSubmenus->count() > 0;
                @endphp
                <ul class="navbar-menu">
                    <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                        <a href="{{ route('home') }}" class="nav-link">Bosh sahifa</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('about') ? 'active' : '' }} {{ $hasAboutSubmenus ? 'has-dropdown' : '' }}">
                        <a href="{{ $hasAboutSubmenus ? '#' : route('about') }}" class="nav-link">Biz haqimizda</a>
                        @if($hasAboutSubmenus)
                            <ul class="dropdown-menu-custom">
                                @foreach($cmsAboutSubmenus as $cmsChild)
                                    @php
                                        $childUrl = $cmsChild->url;
                                        $isChildExternal = str_starts_with($childUrl, 'http://') || str_starts_with($childUrl, 'https://');
                                        $finalChildUrl = ($childUrl === '#' || $isChildExternal) ? $childUrl : url($childUrl);
                                    @endphp
                                    <li>
                                        <a href="{{ $finalChildUrl }}" @if($isChildExternal) target="_blank" rel="noopener" @endif>
                                            <i class="fas fa-chevron-right"></i>
                                            {{ $cmsChild->$titleField ?? $cmsChild->title_uz }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                    <li class="nav-item {{ request()->routeIs('frontend.faculties') ? 'active' : '' }}">
                        <a href="{{ route('frontend.faculties') }}" class="nav-link">Fakultetlar</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('public.library') ? 'active' : '' }}">
                        <a href="{{ route('public.library') }}" class="nav-link">Kutubxona</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('frontend.news') ? 'active' : '' }}">
                        <a href="{{ route('frontend.news') }}" class="nav-link">Yangiliklar</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('frontend.events') ? 'active' : '' }}">
                        <a href="{{ route('frontend.events') }}" class="nav-link">Tadbirlar</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('campus-tour.public.*') ? 'active' : '' }}">
                        <a href="{{ route('campus-tour.public.index') }}" class="nav-link">Virtual Tur</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('frontend.contacts') ? 'active' : '' }}">
                        <a href="{{ route('frontend.contacts') }}" class="nav-link">Kontaktlar</a>
                    </li>
                </ul>

                <!-- Right Side Actions -->
                <div class="navbar-actions">
                    <!-- Accessibility Button -->
                    <button class="accessibility-btn" id="accessibilityToggle" title="Maxsus imkoniyatlar">
                        <i class="fas fa-universal-access"></i>
                    </button>

                    <!-- Language Selector -->
                    <div class="language-selector">
                        <button class="lang-btn" type="button">
                            <span class="flag-icon">🇺🇿</span>
                            <span>UZB</span>
                            <svg class="chevron-icon" width="12" height="12" viewBox="0 0 12 12">
                                <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" fill="none"/>
                            </svg>
                        </button>
                        <div class="lang-dropdown">
                            <a href="#" class="lang-option">
                                <span class="flag-icon">🇺🇿</span>
                                <span>O'zbekcha</span>
                            </a>
                            <a href="#" class="lang-option">
                                <span class="flag-icon">🇬🇧</span>
                                <span>English</span>
                            </a>
                            <a href="#" class="lang-option">
                                <span class="flag-icon">🇷🇺</span>
                                <span>Русский</span>
                            </a>
                        </div>
                    </div>

                    <!-- Login/Dashboard Button -->
                    @guest
                        <a href="{{ route('login') }}" class="btn-login">
                            <i class="fas fa-sign-in-alt mr-2"></i>Kirish
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn-login">
                            <i class="fas fa-user mr-2"></i>Dashboard
                        </a>
                    @endguest
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" aria-label="Toggle Menu">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
            </nav>
        </div>

        <!-- Mobile Menu Overlay -->
        <div class="mobile-menu-overlay">
            <div class="mobile-menu">
                <!-- Mobile Language Selector -->
                <div class="mobile-lang-selector">
                    <a href="#" class="mobile-lang-btn active">🇺🇿 UZB</a>
                    <a href="#" class="mobile-lang-btn">🇬🇧 ENG</a>
                    <a href="#" class="mobile-lang-btn">🇷🇺 RUS</a>
                </div>

                <ul class="mobile-nav-list">
                    <li><a href="{{ route('home') }}">Bosh sahifa</a></li>
                    <li><a href="{{ route('about') }}">Biz haqimizda</a></li>
                    <li><a href="{{ route('frontend.faculties') }}">Fakultetlar</a></li>
                    <li><a href="{{ route('public.library') }}">Kutubxona</a></li>
                    <li><a href="{{ route('frontend.news') }}">Yangiliklar</a></li>
                    <li><a href="{{ route('frontend.events') }}">Tadbirlar</a></li>
                    <li><a href="{{ route('campus-tour.public.index') }}">Virtual Tur</a></li>
                    <li><a href="{{ route('frontend.contacts') }}">Kontaktlar</a></li>
                </ul>
                <div class="mobile-menu-footer">
                    @guest
                        <a href="{{ route('login') }}" class="btn-login mobile">
                            <i class="fas fa-sign-in-alt mr-2"></i>Kirish
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn-login mobile">
                            <i class="fas fa-user mr-2"></i>Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <!-- Accessibility Panel -->
    <div class="accessibility-panel" id="accessibilityPanel">
        <div class="accessibility-header">
            <h3><i class="fas fa-universal-access"></i> Maxsus imkoniyatlar</h3>
            <button class="accessibility-close" id="accessibilityClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="accessibility-body">
            <!-- High Contrast -->
            <div class="accessibility-item">
                <div class="accessibility-item-info">
                    <div class="accessibility-item-icon">
                        <i class="fas fa-adjust"></i>
                    </div>
                    <span class="accessibility-item-text">Yuqori kontrast</span>
                </div>
                <div class="accessibility-toggle" data-feature="high-contrast"></div>
            </div>

            <!-- Grayscale -->
            <div class="accessibility-item">
                <div class="accessibility-item-info">
                    <div class="accessibility-item-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <span class="accessibility-item-text">Oq-qora rejim</span>
                </div>
                <div class="accessibility-toggle" data-feature="grayscale"></div>
            </div>

            <!-- Large Cursor -->
            <div class="accessibility-item">
                <div class="accessibility-item-info">
                    <div class="accessibility-item-icon">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <span class="accessibility-item-text">Katta kursor</span>
                </div>
                <div class="accessibility-toggle" data-feature="large-cursor"></div>
            </div>

            <!-- Underline Links -->
            <div class="accessibility-item">
                <div class="accessibility-item-info">
                    <div class="accessibility-item-icon">
                        <i class="fas fa-underline"></i>
                    </div>
                    <span class="accessibility-item-text">Havolalarni chizish</span>
                </div>
                <div class="accessibility-toggle" data-feature="underline-links"></div>
            </div>

            <!-- Highlight Focus -->
            <div class="accessibility-item">
                <div class="accessibility-item-info">
                    <div class="accessibility-item-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <span class="accessibility-item-text">Fokusni belgilash</span>
                </div>
                <div class="accessibility-toggle" data-feature="highlight-focus"></div>
            </div>

            <!-- Font Size -->
            <div class="accessibility-item">
                <div class="accessibility-item-info">
                    <div class="accessibility-item-icon">
                        <i class="fas fa-text-height"></i>
                    </div>
                    <span class="accessibility-item-text">Shrift o'lchami</span>
                </div>
                <div class="font-size-controls">
                    <button class="font-size-btn" data-size="normal" title="Normal">A</button>
                    <button class="font-size-btn" data-size="large" title="Katta">A+</button>
                    <button class="font-size-btn" data-size="xlarge" title="Juda katta">A++</button>
                </div>
            </div>

            <!-- Reset Button -->
            <button class="accessibility-reset" id="accessibilityReset">
                <i class="fas fa-undo mr-2"></i> Hammasi qaytarish
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-emerald-800 via-teal-700 to-green-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Contact Info -->
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-map-marker-alt mr-3 text-emerald-300"></i>
                        Manzil
                    </h3>
                    <p class="mb-2 text-sm">
                        <i class="fas fa-building mr-2 text-emerald-200"></i>
                        Samarqand sh., Universitet xiyoboni 15
                    </p>
                    <p class="mb-2 text-sm">
                        <i class="fas fa-phone mr-2 text-emerald-200"></i>
                        <a href="tel:+998901234567" class="hover:text-emerald-300 transition">+998 90 123-45-67</a>
                    </p>
                    <p class="text-sm">
                        <i class="fas fa-envelope mr-2 text-emerald-200"></i>
                        <a href="mailto:info@tourism.uz" class="hover:text-emerald-300 transition">info@tourism.uz</a>
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-link mr-3 text-emerald-300"></i>
                        Tezkor havolalar
                    </h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('about') }}" class="hover:text-emerald-300 transition flex items-center">
                            <i class="fas fa-chevron-right mr-2 text-xs"></i>Biz haqimizda
                        </a></li>
                        <li><a href="{{ route('admission.apply') }}" class="hover:text-emerald-300 transition flex items-center">
                            <i class="fas fa-chevron-right mr-2 text-xs"></i>Qabul 2025
                        </a></li>
                        <li><a href="{{ route('forum.index') }}" class="hover:text-emerald-300 transition flex items-center">
                            <i class="fas fa-chevron-right mr-2 text-xs"></i>Forum
                        </a></li>
                        <li><a href="{{ route('frontend.contacts') }}" class="hover:text-emerald-300 transition flex items-center">
                            <i class="fas fa-chevron-right mr-2 text-xs"></i>Bog'lanish
                        </a></li>
                    </ul>
                </div>

                <!-- Xizmatlar -->
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-concierge-bell mr-3 text-emerald-300"></i>
                        Xizmatlar
                    </h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('vacancies.index') }}" class="hover:text-emerald-300 transition flex items-center">
                            <i class="fas fa-chevron-right mr-2 text-xs"></i>Vakansiyalar
                        </a></li>
                        <li><a href="{{ route('state-symbols') }}" class="hover:text-emerald-300 transition flex items-center">
                            <i class="fas fa-chevron-right mr-2 text-xs"></i>Davlat ramzlari
                        </a></li>
                        <li><a href="{{ route('chat.index') }}" class="hover:text-emerald-300 transition flex items-center">
                            <i class="fas fa-chevron-right mr-2 text-xs"></i>Chat
                        </a></li>
                        <li><a href="{{ route('public.library') }}" class="hover:text-emerald-300 transition flex items-center">
                            <i class="fas fa-chevron-right mr-2 text-xs"></i>Kutubxona
                        </a></li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-share-alt mr-3 text-emerald-300"></i>
                        Ijtimoiy tarmoqlar
                    </h3>
                    <p class="mb-4 text-emerald-100">Bizni ijtimoiy tarmoqlarda kuzating</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition btn-hover">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition btn-hover">
                            <i class="fab fa-telegram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition btn-hover">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition btn-hover">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-8 pt-8 border-t border-emerald-600 text-center">
                <p class="text-emerald-100">
                    &copy; {{ date('Y') }} Tourism Academy. Barcha huquqlar himoyalangan.
                </p>
                <p class="text-sm text-emerald-200 mt-2">
                    Made with <i class="fas fa-heart text-red-400"></i> in Samarkand
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Remove preloader when page loads - OPTIMIZED
        window.addEventListener('DOMContentLoaded', function() {
            // Hide immediately when DOM is ready (much faster)
            document.getElementById('preloader').style.display = 'none';
        });

        // Header Scroll Effect
        const header = document.querySelector('.site-header');
        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
        });

        // Mobile Menu Toggle
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
        const body = document.body;

        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', () => {
                mobileMenuToggle.classList.toggle('active');
                mobileMenuOverlay.classList.toggle('active');
                body.style.overflow = mobileMenuOverlay.classList.contains('active') ? 'hidden' : '';
            });

            // Close menu when clicking overlay
            mobileMenuOverlay.addEventListener('click', (e) => {
                if (e.target === mobileMenuOverlay) {
                    mobileMenuToggle.classList.remove('active');
                    mobileMenuOverlay.classList.remove('active');
                    body.style.overflow = '';
                }
            });
        }

        // Close mobile menu on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                if (mobileMenuToggle) mobileMenuToggle.classList.remove('active');
                if (mobileMenuOverlay) mobileMenuOverlay.classList.remove('active');
                body.style.overflow = '';
            }
        });

        // Close mobile menu when clicking a link
        document.querySelectorAll('.mobile-nav-list a').forEach(link => {
            link.addEventListener('click', () => {
                if (mobileMenuToggle) mobileMenuToggle.classList.remove('active');
                if (mobileMenuOverlay) mobileMenuOverlay.classList.remove('active');
                body.style.overflow = '';
            });
        });

        // ============================================
        // ACCESSIBILITY FUNCTIONS
        // ============================================
        (function() {
            const accessibilityToggle = document.getElementById('accessibilityToggle');
            const accessibilityPanel = document.getElementById('accessibilityPanel');
            const accessibilityClose = document.getElementById('accessibilityClose');
            const accessibilityReset = document.getElementById('accessibilityReset');
            const toggles = document.querySelectorAll('.accessibility-toggle');
            const fontSizeBtns = document.querySelectorAll('.font-size-btn');

            // Load saved settings from localStorage
            function loadSettings() {
                const settings = JSON.parse(localStorage.getItem('accessibilitySettings') || '{}');

                // Apply saved toggles
                toggles.forEach(toggle => {
                    const feature = toggle.dataset.feature;
                    if (settings[feature]) {
                        toggle.classList.add('active');
                        document.body.classList.add(feature);
                    }
                });

                // Apply saved font size
                if (settings.fontSize) {
                    fontSizeBtns.forEach(btn => {
                        btn.classList.remove('active');
                        if (btn.dataset.size === settings.fontSize) {
                            btn.classList.add('active');
                        }
                    });
                    applyFontSize(settings.fontSize);
                } else {
                    // Set normal as default active
                    const normalBtn = document.querySelector('.font-size-btn[data-size="normal"]');
                    if (normalBtn) normalBtn.classList.add('active');
                }
            }

            // Save settings to localStorage
            function saveSettings() {
                const settings = {};

                toggles.forEach(toggle => {
                    const feature = toggle.dataset.feature;
                    settings[feature] = toggle.classList.contains('active');
                });

                // Save font size
                fontSizeBtns.forEach(btn => {
                    if (btn.classList.contains('active')) {
                        settings.fontSize = btn.dataset.size;
                    }
                });

                localStorage.setItem('accessibilitySettings', JSON.stringify(settings));
            }

            // Apply font size
            function applyFontSize(size) {
                document.body.classList.remove('font-size-large', 'font-size-xlarge');
                if (size === 'large') {
                    document.body.classList.add('font-size-large');
                } else if (size === 'xlarge') {
                    document.body.classList.add('font-size-xlarge');
                }
            }

            // Toggle panel visibility
            if (accessibilityToggle) {
                accessibilityToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    accessibilityPanel.classList.toggle('active');
                });
            }

            // Close panel
            if (accessibilityClose) {
                accessibilityClose.addEventListener('click', () => {
                    accessibilityPanel.classList.remove('active');
                });
            }

            // Close panel on outside click
            document.addEventListener('click', (e) => {
                if (!accessibilityPanel.contains(e.target) && !accessibilityToggle.contains(e.target)) {
                    accessibilityPanel.classList.remove('active');
                }
            });

            // Toggle features
            toggles.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const feature = toggle.dataset.feature;
                    toggle.classList.toggle('active');
                    document.body.classList.toggle(feature);
                    saveSettings();
                });
            });

            // Font size buttons
            fontSizeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    fontSizeBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    applyFontSize(btn.dataset.size);
                    saveSettings();
                });
            });

            // Reset all settings
            if (accessibilityReset) {
                accessibilityReset.addEventListener('click', () => {
                    // Remove all body classes
                    toggles.forEach(toggle => {
                        const feature = toggle.dataset.feature;
                        toggle.classList.remove('active');
                        document.body.classList.remove(feature);
                    });

                    // Reset font size
                    document.body.classList.remove('font-size-large', 'font-size-xlarge');
                    fontSizeBtns.forEach(btn => {
                        btn.classList.remove('active');
                        if (btn.dataset.size === 'normal') {
                            btn.classList.add('active');
                        }
                    });

                    // Clear localStorage
                    localStorage.removeItem('accessibilitySettings');
                });
            }

            // Load settings on page load
            loadSettings();
        })();
    </script>

    @stack('scripts')
</body>
</html>