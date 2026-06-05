@php
    use App\Models\CmsContent;

    // Header content
    $headerContents = CmsContent::where('section', 'header')->get()->keyBy('key');
    $logoUrl = $headerContents->get('logo_url');
    $siteName = $headerContents->get('site_name');
    $menuHome = $headerContents->get('menu_home');
    $menuAbout = $headerContents->get('menu_about');
    $menuPrograms = $headerContents->get('menu_programs');
    $menuTeachers = $headerContents->get('menu_teachers');
    $menuStatistics = $headerContents->get('menu_statistics');
    $menuBlog = $headerContents->get('menu_blog');
    $menuContact = $headerContents->get('menu_contact');
    $loginButton = $headerContents->get('login_button');
    $dashboardButton = $headerContents->get('dashboard_button');

    // Custom menu items (only active ones)
    $customMenus = $headerContents->filter(function($item, $key) {
        return str_starts_with($key, 'menu_custom_') && !str_ends_with($key, '_url') && $item->is_active;
    })->sortBy('order');

    // Helper function to get submenus for a menu key
    $getSubmenus = function($menuKey) use ($headerContents) {
        return $headerContents->filter(function($item, $key) use ($menuKey) {
            return str_starts_with($key, $menuKey . '_sub_') && !str_ends_with($key, '_url');
        })->sortBy('order');
    };

    // Get URLs for default menus
    $menuUrls = [
        'menu_home' => $headerContents->get('menu_home_url'),
        'menu_about' => $headerContents->get('menu_about_url'),
        'menu_programs' => $headerContents->get('menu_programs_url'),
        'menu_teachers' => $headerContents->get('menu_teachers_url'),
        'menu_statistics' => $headerContents->get('menu_statistics_url'),
        'menu_blog' => $headerContents->get('menu_blog_url'),
        'menu_contact' => $headerContents->get('menu_contact_url'),
    ];

    // Default URLs if not set in CMS
    $defaultUrls = [
        'menu_home' => '/',
        'menu_about' => '/about',
        'menu_programs' => '/programs',
        'menu_teachers' => '/teachers',
        'menu_statistics' => '/statistics',
        'menu_blog' => '/blog',
        'menu_contact' => '/aloqa',
    ];

    // Footer content
    $footerContents = CmsContent::where('section', 'footer')->get()->keyBy('key');
    $footerLogo = $footerContents->get('footer_logo');
    $footerTitle = $footerContents->get('footer_title');
    $footerDesc = $footerContents->get('footer_description');
    $col2Title = $footerContents->get('col2_title');
    $col3Title = $footerContents->get('col3_title');
    $col4Title = $footerContents->get('col4_title');
    $contactAddress = $footerContents->get('contact_address');
    $contactPhone = $footerContents->get('contact_phone');
    $contactEmail = $footerContents->get('contact_email');
    $newsletterPlaceholder = $footerContents->get('newsletter_placeholder');
    $copyrightText = $footerContents->get('copyright_text');

    // Social media
    $socialFacebook = $footerContents->get('social_facebook');
    $socialTwitter = $footerContents->get('social_twitter');
    $socialInstagram = $footerContents->get('social_instagram');
    $socialYoutube = $footerContents->get('social_youtube');
    $socialLinkedin = $footerContents->get('social_linkedin');
    $socialTelegram = $footerContents->get('social_telegram');

    // Footer links - Column 2
    $col2Links = $footerContents->filter(function($item, $key) {
        return str_starts_with($key, 'col2_link') && str_ends_with($key, '_text');
    })->sortBy('order');

    // Footer links - Column 3
    $col3Links = $footerContents->filter(function($item, $key) {
        return str_starts_with($key, 'col3_link') && str_ends_with($key, '_text');
    })->sortBy('order');

    // Get current language (default to 'uz')
    $lang = app()->getLocale() ?? 'uz';
    $langField = 'value_' . $lang;
@endphp
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    @if(!empty($siteFavicon))
    <link rel="icon" href="{{ $siteFavicon }}">
    <link rel="shortcut icon" href="{{ $siteFavicon }}">
    @endif
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Tourism va Service fakulteti')</title>
    <meta name="description" content="@yield('description', 'Tourism va Service fakultetining rasmiy veb-sayti')">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- AOS Animation -->
    <link href="{{ asset('vendor/aos/aos.css') }}" rel="stylesheet">
    
    <style>
        @import url('{{ asset('vendor/fonts/inter.css') }}');

        :root {
            --neon-lime: #D9FF5F;
            --neon-yellow: #C7FD45;
            --neon-purple: #655CFF;
            --neon-blue: #6F00FF;
            --gradient-primary: linear-gradient(135deg, #655CFF 0%, #6F00FF 100%);
            --white: #ffffff;
            --light-gray: #F5F6FA;
            --very-light: #FAFBFC;
            --border-light: #E8EAED;
            --text-dark: #1a1a2e;
            --text-gray: #6B7280;

            /* Indigo Blue */
            --indigo-blue: #4338CA;
            --indigo-gradient: linear-gradient(135deg, #4338CA 0%, #3730A3 100%);

            /* Footer Colors */
            --footer-black: #0D0D0D;
            --footer-gradient-start: #16022C;
            --footer-gradient-mid: #1C0F75;
            --footer-gradient-end: #2500B5;
            --neon-lime-bright: #D7FF37;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--white);
            color: var(--text-dark);
            overflow-x: hidden;
            padding-top: 0;
        }

        /* Hero section that extends behind navbar */
        .hero-full {
            margin-top: 0;
            padding-top: 0;
        }

        /* Half Hero Section for inner pages */
        .half-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding-top: 110px;
            padding-bottom: 60px;
        }

        .half-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(204, 255, 0, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(204, 255, 0, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 60% 80%, rgba(255, 255, 255, 0.03) 0%, transparent 40%);
            pointer-events: none;
        }

        .half-hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .half-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(204, 255, 0, 0.15);
            border: 1px solid rgba(204, 255, 0, 0.3);
            padding: 8px 20px;
            border-radius: 50px;
            color: #CCFF00;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .half-hero h1 {
            color: #ffffff;
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .half-hero h1 span {
            color: #CCFF00;
        }

        .half-hero p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.15rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* Breadcrumb in half-hero */
        .half-hero-breadcrumb {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 25px;
            font-size: 0.9rem;
        }

        .half-hero-breadcrumb a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .half-hero-breadcrumb a:hover {
            color: #CCFF00;
        }

        .half-hero-breadcrumb span {
            color: rgba(255, 255, 255, 0.5);
        }

        .half-hero-breadcrumb .current {
            color: #CCFF00;
        }

        @media (max-width: 991px) {
            .half-hero {
                min-height: 300px;
                padding-top: 100px;
                padding-bottom: 50px;
            }
            .half-hero h1 {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 575px) {
            .half-hero {
                min-height: 280px;
                padding-top: 90px;
                padding-bottom: 40px;
            }
            .half-hero h1 {
                font-size: 1.8rem;
            }
            .half-hero p {
                font-size: 1rem;
            }
        }

        /* Pages without hero need padding */
        .page-content {
            padding-top: 130px;
        }

        @media (max-width: 991px) {
            .page-content {
                padding-top: 100px;
            }
        }

        @media (max-width: 575px) {
            .page-content {
                padding-top: 90px;
            }
        }

        /* ============================================
           HEADER / NAVBAR STYLES - GLASSMORPHISM PILL
           ============================================ */

        /* === Header Wrapper (for floating effect) === */
        .header-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            padding: 20px 24px;
            transition: all 0.3s ease;
        }

        .header-wrapper.scrolled {
            padding: 12px 24px;
        }

        /* === Main Header (Glassmorphism Pill) === */
        .site-header {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            max-width: 1400px;
            margin: 0 auto;
            transition: all 0.3s ease;
        }

        .site-header:hover {
            background: rgba(15, 23, 42, 0.85);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .site-header.scrolled {
            background: rgba(15, 23, 42, 0.9);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .site-header .header-container {
            padding: 0 32px;
        }

        .site-header .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
            gap: 24px;
            flex-wrap: nowrap;
        }

        .navbar-brand {
            flex-shrink: 0;
        }

        .navbar-brand .logo {
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .navbar-brand .logo:hover {
            transform: scale(1.02);
        }

        .navbar-brand .logo img {
            height: 45px;
            width: auto;
            display: block;
            filter: brightness(1.1);
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85) !important;
            text-decoration: none;
            padding: 8px 12px;
            transition: all 0.3s ease;
            white-space: nowrap;
            position: relative;
            border-radius: 25px;
        }

        .nav-link:hover {
            color: #CCFF00 !important;
            background-color: rgba(204, 255, 0, 0.1);
        }

        .nav-item.active .nav-link {
            color: #0f172a !important;
            background-color: #CCFF00;
            font-weight: 600;
        }

        .nav-item.active .nav-link:hover {
            background-color: #b8e600;
        }

        /* Dropdown Submenu Styles - Minimalist Design */
        .nav-item.has-dropdown {
            position: relative;
        }

        .nav-item.has-dropdown > .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-item.has-dropdown > .nav-link::after {
            content: '';
            border: solid rgba(255,255,255,0.5);
            border-width: 0 1.5px 1.5px 0;
            display: inline-block;
            padding: 2.5px;
            transform: rotate(45deg);
            transition: transform 0.25s ease;
            margin-left: 5px;
            margin-top: -2px;
        }

        .nav-item.has-dropdown:hover > .nav-link::after {
            transform: rotate(-135deg);
        }

        .dropdown-menu-custom {
            position: absolute;
            top: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%) translateY(8px);
            min-width: 200px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0, 0, 0, 0.08);
            padding: 8px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 1000;
            list-style: none;
            margin: 0;
        }

        .dropdown-menu-custom::before {
            content: '';
            position: absolute;
            top: -6px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-bottom: 7px solid rgba(255, 255, 255, 0.98);
        }

        .nav-item.has-dropdown:hover .dropdown-menu-custom {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        .dropdown-menu-custom li {
            padding: 0;
        }

        .dropdown-menu-custom li + li {
            margin-top: 2px;
        }

        .dropdown-menu-custom a {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            color: #374151;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            border-radius: 8px;
            gap: 10px;
        }

        .dropdown-menu-custom a:hover {
            background: linear-gradient(135deg, rgba(204, 255, 0, 0.15) 0%, rgba(180, 230, 0, 0.1) 100%);
            color: #1a1a2e;
        }

        .dropdown-menu-custom a i {
            font-size: 10px;
            color: #CCFF00;
            opacity: 0;
            transform: translateX(-5px);
            transition: all 0.2s ease;
        }

        .dropdown-menu-custom a:hover i {
            opacity: 1;
            transform: translateX(0);
        }

        /* Mobile submenu styles - Minimalist */
        .mobile-nav-list .has-submenu > a {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-nav-list .has-submenu > a .submenu-arrow {
            transition: transform 0.25s ease;
            font-size: 12px;
            opacity: 0.6;
        }

        .mobile-nav-list .has-submenu.open > a .submenu-arrow {
            transform: rotate(180deg);
        }

        .mobile-submenu {
            list-style: none;
            padding: 0;
            margin: 8px 0 0 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .mobile-nav-list .has-submenu.open .mobile-submenu {
            max-height: 500px;
            padding: 8px 0;
        }

        .mobile-submenu li {
            border-bottom: none !important;
        }

        .mobile-submenu a {
            padding: 10px 16px 10px 24px !important;
            font-size: 13px !important;
            color: rgba(255, 255, 255, 0.65) !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            transition: all 0.2s ease !important;
            border-radius: 6px !important;
            margin: 2px 8px !important;
        }

        .mobile-submenu a::before {
            content: '';
            width: 4px;
            height: 4px;
            background: rgba(204, 255, 0, 0.5);
            border-radius: 50%;
            flex-shrink: 0;
        }

        .mobile-submenu a:hover {
            color: #CCFF00 !important;
            background: rgba(204, 255, 0, 0.08) !important;
        }

        .mobile-submenu a:hover::before {
            background: #CCFF00;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        /* Language Selector - Transparent with White Border */
        .language-selector {
            position: relative;
        }

        .lang-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            border-radius: 25px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .lang-btn:hover {
            border-color: rgba(255, 255, 255, 0.7);
            background: rgba(255, 255, 255, 0.1);
        }

        .flag-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            background-size: cover;
            background-position: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            flex-shrink: 0;
        }

        .flag-uz {
            background: linear-gradient(180deg, #0099B5 0%, #0099B5 33%, #CE1126 33%, #CE1126 50%, #fff 50%, #fff 67%, #1EB53A 67%, #1EB53A 100%);
        }

        .flag-en {
            background: linear-gradient(180deg, #012169 0%, #012169 100%);
            position: relative;
        }

        .flag-en::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                linear-gradient(45deg, transparent 35%, #fff 35%, #fff 40%, #C8102E 40%, #C8102E 45%, transparent 45%),
                linear-gradient(-45deg, transparent 35%, #fff 35%, #fff 40%, #C8102E 40%, #C8102E 45%, transparent 45%),
                linear-gradient(0deg, transparent 40%, #fff 40%, #fff 48%, #C8102E 48%, #C8102E 52%, #fff 52%, #fff 60%, transparent 60%),
                linear-gradient(90deg, transparent 40%, #fff 40%, #fff 48%, #C8102E 48%, #C8102E 52%, #fff 52%, #fff 60%, transparent 60%);
            border-radius: 50%;
        }

        .flag-ru {
            background: linear-gradient(180deg, #fff 0%, #fff 33%, #0039A6 33%, #0039A6 66%, #D52B1E 66%, #D52B1E 100%);
        }

        .chevron-icon {
            transition: transform 0.3s ease;
            color: rgba(255, 255, 255, 0.7);
        }

        .language-selector:hover .chevron-icon {
            transform: rotate(180deg);
        }

        .lang-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            padding: 8px;
            min-width: 160px;
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
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .lang-option:hover {
            background-color: rgba(204, 255, 0, 0.15);
            color: #CCFF00;
        }

        .lang-option.active {
            background-color: rgba(204, 255, 0, 0.2);
            color: #CCFF00;
            font-weight: 600;
        }

        /* Login Button - Neon Lime */
        .btn-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #CCFF00;
            color: #0f172a !important;
            font-size: 14px;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(204, 255, 0, 0.3);
        }

        .btn-login:hover {
            background-color: #b8e600;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(204, 255, 0, 0.4);
            color: #0f172a !important;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* UN Tourism Logo */
        .un-tourism-logo {
            display: flex;
            align-items: center;
            padding-left: 16px;
            border-left: 1px solid rgba(255, 255, 255, 0.2);
            margin-left: 8px;
        }

        .un-tourism-logo img {
            height: 36px;
            width: auto;
            transition: all 0.3s ease;
            filter: brightness(1.1);
        }

        .un-tourism-logo:hover img {
            opacity: 0.8;
            transform: scale(1.02);
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 26px;
            height: 20px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .hamburger-line {
            width: 100%;
            height: 2.5px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translateY(9px);
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translateY(-9px);
        }

        /* Mobile Menu Overlay */
        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.7);
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
            max-width: 380px;
            height: 100%;
            background: rgba(15, 23, 42, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 100px 24px 32px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
            box-shadow: -4px 0 30px rgba(0, 0, 0, 0.3);
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-nav-list li:last-child {
            border-bottom: none;
        }

        .mobile-nav-list a {
            display: block;
            padding: 16px 0;
            font-size: 16px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .mobile-nav-list a:hover {
            color: #CCFF00;
            padding-left: 8px;
        }

        .mobile-menu-footer {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-login.mobile {
            width: 100%;
        }

        .mobile-lang-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }

        .mobile-lang-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            background: transparent;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .mobile-lang-btn:hover,
        .mobile-lang-btn.active {
            border-color: #CCFF00;
            background: rgba(204, 255, 0, 0.15);
            color: #CCFF00;
        }

        /* Header Responsive */
        @media (max-width: 1399px) {
            .navbar-menu {
                gap: 2px;
            }
            .nav-link {
                padding: 6px 10px;
                font-size: 12px;
            }
            .navbar-actions {
                gap: 8px;
            }
            .accessibility-btn {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }
        }

        @media (max-width: 1199px) {
            .navbar-menu {
                gap: 1px;
            }
            .nav-link {
                font-size: 11px;
                padding: 6px 8px;
            }
            .header-wrapper {
                padding: 16px 20px;
            }
            .navbar-actions {
                gap: 6px;
            }
            .lang-btn {
                padding: 6px 10px;
                font-size: 11px;
            }
            .btn-login {
                padding: 6px 12px !important;
                font-size: 11px !important;
            }
        }

        @media (max-width: 991px) {
            .header-wrapper {
                padding: 12px 16px;
            }
            .site-header {
                border-radius: 35px;
            }
            .site-header .header-container {
                padding: 0 20px;
            }
            .site-header .navbar {
                height: 60px;
            }
            .navbar-brand .logo img {
                height: 38px;
            }
            .navbar-menu {
                display: none;
            }
            .navbar-actions {
                gap: 10px;
            }
            .language-selector {
                display: none;
            }
            .btn-login:not(.mobile) {
                padding: 8px 18px;
                font-size: 13px;
            }
            .mobile-menu-toggle {
                display: flex;
            }
            .un-tourism-logo {
                display: none;
            }
            body {
                padding-top: 100px;
            }
        }

        @media (max-width: 575px) {
            .header-wrapper {
                padding: 10px 12px;
            }
            .site-header {
                border-radius: 30px;
            }
            .site-header .header-container {
                padding: 0 16px;
            }
            .site-header .navbar {
                height: 54px;
            }
            .navbar-brand .logo img {
                height: 32px;
            }
            .btn-login:not(.mobile) {
                display: none;
            }
            .mobile-menu {
                max-width: 100%;
            }
            body {
                padding-top: 90px;
            }
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            background: linear-gradient(125deg, #ffffff 0%, #F5F6FA 50%, #FAFBFC 100%);
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(101, 92, 255, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(217, 255, 95, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(199, 253, 69, 0.04) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(1deg); }
            66% { transform: translate(-20px, 20px) rotate(-1deg); }
        }
        
        /* Navbar */
        .navbar-custom {
            background: var(--footer-black) !important;
            backdrop-filter: blur(20px);
            border-bottom: 3px solid var(--neon-lime-bright);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            transition: all 0.3s;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-custom .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--white);
            transition: all 0.3s;
        }

        .navbar-custom .navbar-brand:hover {
            color: var(--neon-lime-bright);
        }

        .navbar-custom .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .navbar-custom .navbar-brand img {
            transition: transform 0.3s ease;
        }

        .navbar-custom .navbar-brand i {
            color: var(--neon-lime-bright);
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
            padding: 8px 16px !important;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: var(--neon-lime-bright) !important;
            transform: translateY(-2px);
        }

        .navbar-custom .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--neon-lime-bright);
            transition: all 0.3s;
            transform: translateX(-50%);
        }

        .navbar-custom .nav-link:hover::after,
        .navbar-custom .nav-link.active::after {
            width: 80%;
        }

        .navbar-custom .navbar-toggler {
            border-color: var(--neon-lime-bright);
            background: rgba(215, 255, 55, 0.1);
        }

        .navbar-custom .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23D7FF37' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        /* Buttons */
        .btn-neon {
            padding: 12px 28px;
            font-weight: 600;
            border: 2px solid var(--neon-lime-bright);
            background: var(--neon-lime-bright);
            color: #000;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 16px rgba(215, 255, 55, 0.3);
        }

        .btn-neon:hover {
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(215, 255, 55, 0.5);
            background: var(--neon-yellow);
            border-color: var(--neon-yellow);
        }

        .navbar-custom .btn-neon {
            padding: 10px 24px;
            font-size: 0.9rem;
        }

        .btn-neon-secondary {
            border: 2px solid var(--neon-purple);
            background: var(--white);
            color: var(--neon-purple);
            box-shadow: 0 4px 12px rgba(101, 92, 255, 0.15);
        }

        .btn-neon-secondary:hover {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 8px 24px rgba(101, 92, 255, 0.3);
        }
        
        /* Stats */
        .stat-card {
            background: var(--white);
            border: 1px solid var(--border-light);
            border-radius: 20px;
            padding: 35px;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 32px rgba(101, 92, 255, 0.12);
            border-color: var(--neon-lime);
        }
        
        /* Footer - PDF Design */
        .footer {
            background: #1a1a2e;
            padding: 0;
            margin-top: 0;
            position: relative;
        }

        .footer-main {
            padding: 60px 0 40px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            gap: 30px;
        }

        .footer-brand {
            padding-right: 40px;
        }

        .footer-logo {
            height: 50px;
            width: auto;
            margin-bottom: 20px;
        }

        .footer-desc {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
            line-height: 1.7;
            margin: 0;
        }

        .footer h5 {
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: block;
            padding: 6px 0;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .footer-link:hover {
            color: #C8E637;
            padding-left: 5px;
        }

        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        .footer-contact-icon {
            width: 20px;
            margin-right: 10px;
            color: #C8E637;
            flex-shrink: 0;
        }

        .footer-social-icons {
            display: flex;
            gap: 12px;
            margin-top: 15px;
        }

        .footer-social-icons a {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .footer-social-icons a:hover {
            background: #C8E637;
            color: #1a1a2e;
            transform: translateY(-3px);
        }

        .footer-bottom {
            background: rgba(0,0,0,0.2);
            padding: 20px 0;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .footer-bottom-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.6);
            margin: 0;
            font-size: 0.85rem;
        }

        .footer-legal-links {
            display: flex;
            gap: 20px;
        }

        .footer-legal-links a {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-legal-links a:hover {
            color: #C8E637;
        }

        .footer-social {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .footer-social a {
            color: #fff;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .footer-social a:hover {
            color: #C8E637;
            transform: translateY(-3px);
        }

        @media (max-width: 1199px) {
            .footer-grid {
                grid-template-columns: 2fr 1fr 1fr 1fr;
                gap: 25px;
            }
            .footer-column:last-child {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 991px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr 1fr;
                gap: 25px;
            }
            .footer-brand {
                padding-right: 0;
                grid-column: 1 / -1;
            }
            .footer-column:last-child {
                grid-column: auto;
            }
        }

        @media (max-width: 576px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }
            .footer-bottom-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .footer-legal-links {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .hover-purple {
            transition: all 0.3s;
            color: var(--text-gray) !important;
        }

        .hover-purple:hover {
            color: var(--neon-purple) !important;
            padding-left: 10px;
        }

        /* ============================================
           SEARCH STYLES
           ============================================ */
        .nav-search-wrapper {
            position: relative;
        }

        .search-toggle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: transparent;
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            color: rgba(255, 255, 255, 0.9);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            flex-shrink: 0;
        }

        .search-toggle-btn:hover {
            border-color: #CCFF00;
            color: #CCFF00;
            background: rgba(204, 255, 0, 0.1);
        }

        .search-toggle-btn.active {
            background: #CCFF00;
            border-color: #CCFF00;
            color: #0f172a;
        }

        .search-dropdown {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: 360px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            padding: 16px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1001;
        }

        .search-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .search-form {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        .search-input {
            flex: 1;
            padding: 12px 16px;
            border: none;
            background: transparent;
            color: white;
            font-size: 14px;
            outline: none;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .search-submit {
            padding: 12px 16px;
            background: #CCFF00;
            border: none;
            color: #0f172a;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-submit:hover {
            background: #b8e600;
        }

        .search-quick-results {
            margin-top: 12px;
            max-height: 300px;
            overflow-y: auto;
        }

        .search-quick-results:empty {
            display: none;
        }

        .quick-result-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .quick-result-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .quick-result-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(204, 255, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #CCFF00;
            font-size: 14px;
            flex-shrink: 0;
        }

        .quick-result-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .quick-result-content {
            flex: 1;
            min-width: 0;
        }

        .quick-result-title {
            color: white;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .quick-result-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            margin-top: 2px;
        }

        .search-loading {
            text-align: center;
            padding: 20px;
            color: rgba(255, 255, 255, 0.6);
        }

        .search-no-results {
            text-align: center;
            padding: 20px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .search-view-all {
            display: block;
            text-align: center;
            padding: 12px;
            margin-top: 8px;
            background: rgba(204, 255, 0, 0.1);
            border-radius: 10px;
            color: #CCFF00;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .search-view-all:hover {
            background: rgba(204, 255, 0, 0.2);
        }

        @media (max-width: 576px) {
            .search-dropdown {
                position: fixed;
                top: 80px;
                left: 16px;
                right: 16px;
                width: auto;
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
            flex-shrink: 0;
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

        @media (max-width: 575px) {
            .accessibility-btn {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }
        }
    </style>

    <!-- Dynamic Theme Styles -->
    @include('partials.theme-styles')

    @yield('styles')
    @stack('styles')
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg"></div>
    
    <!-- Header Wrapper (Floating) -->
    <div class="header-wrapper">
        <header class="site-header">
            <div class="header-container">
                <nav class="navbar">
                    <!-- Logo -->
                    <div class="navbar-brand">
                        <a href="{{ route('home') }}" class="logo">
                            @if(!empty($siteLogo))
                                <img src="{{ $siteLogo }}" alt="{{ $siteName->$langField ?? 'Tourism Academy' }}">
                            @elseif($logoUrl && $logoUrl->value_uz)
                                <img src="{{ \App\Support\CmsHeaderFooter::assetUrl($logoUrl->value_uz) }}" alt="{{ $siteName->$langField ?? 'Tourism Academy' }}">
                            @else
                                <img src="{{ asset('images/logo.png') }}" alt="Tourism Academy Logo">
                            @endif
                        </a>
                    </div>

                <!-- Navigation Menu (Desktop) -->
                <ul class="navbar-menu">
                    @php
                        $menuItemsConfig = [
                            ['key' => 'menu_home', 'label' => $menuHome, 'default' => 'Bosh sahifa', 'route' => 'home'],
                            ['key' => 'menu_about', 'label' => $menuAbout, 'default' => 'Biz haqimizda', 'route' => 'about'],
                            ['key' => 'menu_programs', 'label' => $menuPrograms, 'default' => "Yo'nalishlar", 'route' => 'programs'],
                            ['key' => 'menu_teachers', 'label' => $menuTeachers, 'default' => "O'qituvchilar", 'route' => 'teachers'],
                            ['key' => 'menu_statistics', 'label' => $menuStatistics, 'default' => 'Statistika', 'route' => 'statistics'],
                            ['key' => 'menu_blog', 'label' => $menuBlog, 'default' => 'Blog', 'route' => 'blog*'],
                            ['key' => 'menu_contact', 'label' => $menuContact, 'default' => "Bog'lanish", 'route' => 'contact'],
                        ];
                    @endphp

                    @php
                        // CMS menyu elementlarini olish (Biz haqimizda uchun)
                        $headerMenu = \App\Models\CmsMenu::where('location', 'header')->where('is_active', true)->first();
                        $cmsAboutSubmenus = collect();
                        $titleField = 'title_' . $lang;
                        if ($headerMenu) {
                            $aboutParent = $headerMenu->menuItems()->whereNull('parent_id')->where('is_active', true)->first();
                            if ($aboutParent) {
                                $cmsAboutSubmenus = $aboutParent->children()->where('is_active', true)->orderBy('order_position')->get();
                            }
                        }
                    @endphp

                    @foreach($menuItemsConfig as $menuItem)
                        @php
                            $submenus = $getSubmenus($menuItem['key']);
                            // Biz haqimizda uchun CMS submenyularni ham hisoblash
                            $cmsSubCount = ($menuItem['key'] === 'menu_about') ? $cmsAboutSubmenus->count() : 0;
                            $hasSubmenu = $submenus->count() > 0 || $cmsSubCount > 0;
                            $menuUrl = $menuUrls[$menuItem['key']] ?? null;
                            $rawUrl = $menuUrl ? $menuUrl->value_uz : ($defaultUrls[$menuItem['key']] ?? '#');
                            $isExternal = str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://');
                            $url = ($rawUrl === '#' || $isExternal) ? $rawUrl : url($rawUrl);
                            $isActive = request()->routeIs($menuItem['route']);
                        @endphp
                        <li class="nav-item {{ $isActive ? 'active' : '' }} {{ $hasSubmenu ? 'has-dropdown' : '' }}">
                            <a href="{{ $hasSubmenu ? '#' : $url }}" class="nav-link">
                                {{ $menuItem['label']->$langField ?? $menuItem['default'] }}
                            </a>
                            @if($hasSubmenu)
                                <ul class="dropdown-menu-custom">
                                    {{-- CmsContent submenular --}}
                                    @foreach($submenus as $submenu)
                                        @php
                                            $submenuUrl = $headerContents->get($submenu->key . '_url');
                                            $subRawUrl = $submenuUrl ? $submenuUrl->value_uz : '#';
                                            $isSubExternal = str_starts_with($subRawUrl, 'http://') || str_starts_with($subRawUrl, 'https://');
                                            $subUrl = ($subRawUrl === '#' || $isSubExternal) ? $subRawUrl : url($subRawUrl);
                                        @endphp
                                        <li>
                                            <a href="{{ $subUrl }}" @if($isSubExternal) target="_blank" rel="noopener" @endif>
                                                <i class="fas fa-chevron-right"></i>
                                                {{ $submenu->$langField ?? $submenu->value_uz }}
                                            </a>
                                        </li>
                                    @endforeach

                                    {{-- CmsMenuItem submenular (faqat menu_about uchun) --}}
                                    @if($menuItem['key'] === 'menu_about')
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
                                    @endif
                                </ul>
                            @endif
                        </li>
                    @endforeach

                    @foreach($customMenus as $menu)
                        @php
                            $menuUrl = $headerContents->get($menu->key . '_url');
                            $rawUrl = $menuUrl ? $menuUrl->value_uz : '#';
                            $isExternal = str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://');
                            $url = ($rawUrl === '#' || $isExternal) ? $rawUrl : url($rawUrl);
                        @endphp
                        <li class="nav-item">
                            <a href="{{ $url }}" class="nav-link" @if($isExternal) target="_blank" rel="noopener" @endif>
                                {{ $menu->$langField ?? $menu->value_uz }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Right Side Actions -->
                <div class="navbar-actions">
                    <!-- Search Button -->
                    <div class="nav-search-wrapper">
                        <button class="search-toggle-btn" id="searchToggle" title="Qidiruv">
                            <i class="fas fa-search"></i>
                        </button>
                        <div class="search-dropdown" id="searchDropdown">
                            <form action="{{ route('search') }}" method="GET" class="search-form">
                                <input type="text" name="q" class="search-input" placeholder="Qidirish..." autocomplete="off" id="navSearchInput">
                                <button type="submit" class="search-submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                            <div class="search-quick-results" id="searchQuickResults"></div>
                        </div>
                    </div>

                    <!-- Accessibility Button -->
                    <button class="accessibility-btn" id="accessibilityToggle" title="Maxsus imkoniyatlar">
                        <i class="fas fa-universal-access"></i>
                    </button>

                    <!-- Language Selector -->
                    <div class="language-selector">
                        <button class="lang-btn" type="button">
                            @if($lang == 'uz')
                                <span class="flag-icon flag-uz"></span>
                                <span>UZB</span>
                            @elseif($lang == 'ru')
                                <span class="flag-icon flag-ru"></span>
                                <span>RUS</span>
                            @else
                                <span class="flag-icon flag-en"></span>
                                <span>ENG</span>
                            @endif
                            <svg class="chevron-icon" width="12" height="12" viewBox="0 0 12 12">
                                <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" fill="none"/>
                            </svg>
                        </button>
                        <div class="lang-dropdown">
                            <a href="{{ route('lang.switch', 'uz') }}" class="lang-option {{ $lang == 'uz' ? 'active' : '' }}">
                                <span class="flag-icon flag-uz"></span>
                                <span>UZB</span>
                            </a>
                            <a href="{{ route('lang.switch', 'en') }}" class="lang-option {{ $lang == 'en' ? 'active' : '' }}">
                                <span class="flag-icon flag-en"></span>
                                <span>ENG</span>
                            </a>
                            <a href="{{ route('lang.switch', 'ru') }}" class="lang-option {{ $lang == 'ru' ? 'active' : '' }}">
                                <span class="flag-icon flag-ru"></span>
                                <span>RUS</span>
                            </a>
                        </div>
                    </div>

                    <!-- Login/Dashboard Button -->
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-login">
                            {{ $dashboardButton->$langField ?? 'Dashboard' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-login">
                            {{ $loginButton->$langField ?? 'Login' }}
                        </a>
                    @endauth

                    <!-- Hamkor logosi (Settings'dan boshqariladi) -->
                    @if(!empty($siteLogoSecondary))
                    <a href="#" class="un-tourism-logo" title="Hamkor">
                        <img src="{{ $siteLogoSecondary }}" alt="Hamkor logo" onerror="this.style.display='none'">
                    </a>
                    @endif
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
                    <a href="{{ route('lang.switch', 'uz') }}" class="mobile-lang-btn {{ $lang == 'uz' ? 'active' : '' }}">
                        <span class="flag-icon flag-uz"></span> UZB
                    </a>
                    <a href="{{ route('lang.switch', 'en') }}" class="mobile-lang-btn {{ $lang == 'en' ? 'active' : '' }}">
                        <span class="flag-icon flag-en"></span> ENG
                    </a>
                    <a href="{{ route('lang.switch', 'ru') }}" class="mobile-lang-btn {{ $lang == 'ru' ? 'active' : '' }}">
                        <span class="flag-icon flag-ru"></span> RUS
                    </a>
                </div>

                <ul class="mobile-nav-list">
                    @foreach($menuItemsConfig as $menuItem)
                        @php
                            $submenus = $getSubmenus($menuItem['key']);
                            $hasSubmenu = $submenus->count() > 0;
                            $menuUrl = $menuUrls[$menuItem['key']] ?? null;
                            $rawUrl = $menuUrl ? $menuUrl->value_uz : ($defaultUrls[$menuItem['key']] ?? '#');
                            $isExternal = str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://');
                            $url = ($rawUrl === '#' || $isExternal) ? $rawUrl : url($rawUrl);
                        @endphp
                        @if($hasSubmenu)
                            <li class="has-submenu">
                                <a href="#" onclick="toggleMobileSubmenu(this); return false;">
                                    {{ $menuItem['label']->$langField ?? $menuItem['default'] }}
                                    <i class="fas fa-chevron-down submenu-arrow"></i>
                                </a>
                                <ul class="mobile-submenu">
                                    @foreach($submenus as $submenu)
                                        @php
                                            $submenuUrl = $headerContents->get($submenu->key . '_url');
                                            $subRawUrl = $submenuUrl ? $submenuUrl->value_uz : '#';
                                            $isSubExternal = str_starts_with($subRawUrl, 'http://') || str_starts_with($subRawUrl, 'https://');
                                            $subUrl = ($subRawUrl === '#' || $isSubExternal) ? $subRawUrl : url($subRawUrl);
                                        @endphp
                                        <li>
                                            <a href="{{ $subUrl }}">{{ $submenu->$langField ?? $submenu->value_uz }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li><a href="{{ $url }}">{{ $menuItem['label']->$langField ?? $menuItem['default'] }}</a></li>
                        @endif
                    @endforeach
                    @foreach($customMenus as $menu)
                        @php
                            $menuUrl = $headerContents->get($menu->key . '_url');
                            $rawUrl = $menuUrl ? $menuUrl->value_uz : '#';
                            $isExternal = str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://');
                            $url = ($rawUrl === '#' || $isExternal) ? $rawUrl : url($rawUrl);
                        @endphp
                        <li><a href="{{ $url }}">{{ $menu->$langField ?? $menu->value_uz }}</a></li>
                    @endforeach
                </ul>
                <div class="mobile-menu-footer">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-login mobile">
                            <i class="fas fa-user-circle me-1"></i>{{ $dashboardButton->$langField ?? 'Dashboard' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-login mobile">
                            <i class="fas fa-sign-in-alt me-1"></i>{{ $loginButton->$langField ?? 'Kirish' }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>
    </div>
    <!-- End Header Wrapper -->

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
    @yield('content')

    <!-- Footer - PDF Design -->
    <footer class="footer">
        <div class="footer-main">
            <div class="container">
                <div class="footer-grid">
                    <!-- Column 1: Logo & Description -->
                    <div class="footer-brand">
                        @if($footerLogo && $footerLogo->value_uz)
                            <img src="{{ \App\Support\CmsHeaderFooter::assetUrl($footerLogo->value_uz) }}" alt="{{ $footerTitle->$langField ?? 'Tourism Academy' }}" class="footer-logo">
                        @else
                            <img src="{{ asset('images/logo.png') }}" alt="Tourism Academy Logo" class="footer-logo">
                        @endif
                        <p class="footer-desc">
                            {{ $footerDesc->$langField ?? "UN Tourism bilan hamkorlikdagi Samarqand Xalqaro Turizm va Mehmondo'stlik Akademiyasi – zamonaviy ta'lim va yuqori malakali kadrlar tayyorlash markazi." }}
                        </p>
                    </div>

                    <!-- Column 2: Ma'lumot -->
                    <div class="footer-column">
                        <h5>{{ $col2Title->$langField ?? "Ma'lumot" }}</h5>
                        @forelse($col2Links as $link)
                            @php
                                $urlKey = str_replace('_text', '_url', $link->key ?? '');
                                $linkUrl = $footerContents->get($urlKey);
                            @endphp
                            <a href="{{ $linkUrl->$langField ?? '#' }}" class="footer-link">{{ $link->$langField ?? '' }}</a>
                        @empty
                            <a href="{{ route('about') }}" class="footer-link">Biz haqimizda</a>
                            <a href="{{ route('programs') }}" class="footer-link">Ta'lim yo'nalishlari</a>
                            <a href="{{ route('teachers') }}" class="footer-link">O'qituvchilar</a>
                            <a href="{{ route('statistics') }}" class="footer-link">Statistika</a>
                            <a href="{{ route('blog') }}" class="footer-link">Yangiliklar</a>
                        @endforelse
                    </div>

                    <!-- Column 3: Xizmatlar -->
                    <div class="footer-column">
                        <h5>{{ $col3Title->$langField ?? "Xizmatlar" }}</h5>
                        @forelse($col3Links as $link)
                            @php
                                $urlKey = str_replace('_text', '_url', $link->key ?? '');
                                $linkUrl = $footerContents->get($urlKey);
                            @endphp
                            <a href="{{ $linkUrl->$langField ?? '#' }}" class="footer-link">{{ $link->$langField ?? '' }}</a>
                        @empty
                            <a href="{{ route('public.library') }}" class="footer-link">Elektron kutubxona</a>
                            <a href="{{ route('virtual-tour') }}" class="footer-link">Virtual sayohat</a>
                            <a href="{{ route('faq') }}" class="footer-link">FAQ</a>
                        @endforelse
                    </div>

                    <!-- Column 4: Contact -->
                    <div class="footer-column">
                        <h5>{{ $col4Title->$langField ?? "Bog'lanish" }}</h5>
                        <div class="footer-contact-item">
                            <i class="fas fa-map-marker-alt footer-contact-icon"></i>
                            <span>{{ $contactAddress->$langField ?? "Samarqand shahar, Istiqlol ko'chasi, 47" }}</span>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-phone footer-contact-icon"></i>
                            <span>{{ $contactPhone->$langField ?? '+998 66 233 XX XX' }}</span>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-envelope footer-contact-icon"></i>
                            <span>{{ $contactEmail->$langField ?? 'info@tourism.uz' }}</span>
                        </div>
                    </div>

                    <!-- Column 4: Social -->
                    <div class="footer-column">
                        <h5>{{ $footerContents->get('social_title')->$langField ?? 'Ijtimoiy tarmoqlar' }}</h5>
                        <div class="footer-social-icons">
                            @if($socialFacebook && $socialFacebook->value_uz && $socialFacebook->value_uz !== '#')
                            <a href="{{ $socialFacebook->value_uz }}" aria-label="Facebook" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            @endif
                            @if($socialInstagram && $socialInstagram->value_uz && $socialInstagram->value_uz !== '#')
                            <a href="{{ $socialInstagram->value_uz }}" aria-label="Instagram" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                            @endif
                            @if($socialYoutube && $socialYoutube->value_uz && $socialYoutube->value_uz !== '#')
                            <a href="{{ $socialYoutube->value_uz }}" aria-label="YouTube" target="_blank">
                                <i class="fab fa-youtube"></i>
                            </a>
                            @endif
                            @if($socialTelegram && $socialTelegram->value_uz && $socialTelegram->value_uz !== '#')
                            <a href="{{ $socialTelegram->value_uz }}" aria-label="Telegram" target="_blank">
                                <i class="fab fa-telegram-plane"></i>
                            </a>
                            @endif
                            @if($socialLinkedin && $socialLinkedin->value_uz && $socialLinkedin->value_uz !== '#')
                            <a href="{{ $socialLinkedin->value_uz }}" aria-label="LinkedIn" target="_blank">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright Section -->
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <p>{{ $copyrightText->$langField ?? '© 2025 Tourism Academy. Barcha huquqlar himoyalangan.' }}</p>
                    <div class="footer-legal-links">
                        <a href="#">{{ $footerContents->get('privacy_policy')->$langField ?? 'Maxfiylik siyosati' }}</a>
                        <a href="#">{{ $footerContents->get('terms_of_use')->$langField ?? 'Foydalanish shartlari' }}</a>
                        <a href="#">{{ $footerContents->get('cookie_settings')->$langField ?? 'Cookie sozlamalari' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        // Header Scroll Effect
        const headerWrapper = document.querySelector('.header-wrapper');
        const header = document.querySelector('.site-header');
        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 50) {
                headerWrapper.classList.add('scrolled');
                header.classList.add('scrolled');
            } else {
                headerWrapper.classList.remove('scrolled');
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

        // Close mobile menu when clicking a link (except submenu toggles)
        document.querySelectorAll('.mobile-nav-list a').forEach(link => {
            link.addEventListener('click', (e) => {
                // Don't close if it's a submenu toggle
                if (link.closest('.has-submenu') && link.parentElement.classList.contains('has-submenu')) {
                    return;
                }
                if (mobileMenuToggle) mobileMenuToggle.classList.remove('active');
                if (mobileMenuOverlay) mobileMenuOverlay.classList.remove('active');
                body.style.overflow = '';
            });
        });

        // Toggle mobile submenu
        function toggleMobileSubmenu(element) {
            const parent = element.closest('.has-submenu');
            if (parent) {
                parent.classList.toggle('open');
            }
        }

        // ============================================
        // SEARCH FUNCTIONS
        // ============================================
        (function() {
            const searchToggle = document.getElementById('searchToggle');
            const searchDropdown = document.getElementById('searchDropdown');
            const searchInput = document.getElementById('navSearchInput');
            const searchResults = document.getElementById('searchQuickResults');
            let searchTimeout = null;

            if (searchToggle && searchDropdown) {
                // Toggle search dropdown
                searchToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    searchToggle.classList.toggle('active');
                    searchDropdown.classList.toggle('active');
                    if (searchDropdown.classList.contains('active')) {
                        setTimeout(() => searchInput.focus(), 100);
                    }
                });

                // Close when clicking outside
                document.addEventListener('click', (e) => {
                    if (!searchDropdown.contains(e.target) && !searchToggle.contains(e.target)) {
                        searchToggle.classList.remove('active');
                        searchDropdown.classList.remove('active');
                    }
                });

                // Live search
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const query = this.value.trim();

                        clearTimeout(searchTimeout);

                        if (query.length < 2) {
                            searchResults.innerHTML = '';
                            return;
                        }

                        searchResults.innerHTML = '<div class="search-loading"><i class="fas fa-spinner fa-spin"></i> Qidirilmoqda...</div>';

                        searchTimeout = setTimeout(() => {
                            fetch(`/api/search/quick?q=${encodeURIComponent(query)}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.results && data.results.length > 0) {
                                        let html = '';
                                        data.results.forEach(item => {
                                            html += `
                                                <a href="${item.url}" class="quick-result-item">
                                                    <div class="quick-result-icon">
                                                        ${item.image ? `<img src="${item.image}" alt="">` : `<i class="fas ${item.icon}"></i>`}
                                                    </div>
                                                    <div class="quick-result-content">
                                                        <div class="quick-result-title">${item.title}</div>
                                                        ${item.subtitle ? `<div class="quick-result-subtitle">${item.subtitle}</div>` : ''}
                                                    </div>
                                                </a>
                                            `;
                                        });
                                        html += `<a href="/qidiruv?q=${encodeURIComponent(query)}" class="search-view-all">Barcha natijalarni ko'rish <i class="fas fa-arrow-right"></i></a>`;
                                        searchResults.innerHTML = html;
                                    } else {
                                        searchResults.innerHTML = '<div class="search-no-results">Natija topilmadi</div>';
                                    }
                                })
                                .catch(() => {
                                    searchResults.innerHTML = '<div class="search-no-results">Xatolik yuz berdi</div>';
                                });
                        }, 300);
                    });

                    // Handle Enter key
                    searchInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            // Form will submit naturally
                        }
                        if (e.key === 'Escape') {
                            searchToggle.classList.remove('active');
                            searchDropdown.classList.remove('active');
                        }
                    });
                }
            }
        })();

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
                if (accessibilityPanel && accessibilityToggle && !accessibilityPanel.contains(e.target) && !accessibilityToggle.contains(e.target)) {
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

    <!-- Support Chat Widget -->
    @include('components.support-chat-widget')

    @yield('scripts')
    @stack('scripts')
</body>
</html>
