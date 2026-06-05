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

    // Helper function to format menu URL
    $formatMenuUrl = function($menuUrl, $defaultUrl) use ($langField) {
        $rawUrl = null;
        if ($menuUrl && $menuUrl->$langField) {
            $rawUrl = $menuUrl->$langField;
        } else {
            $rawUrl = $defaultUrl;
        }

        // If URL is empty or just '#', return as is
        if (empty($rawUrl) || $rawUrl === '#') {
            return $rawUrl ?: '#';
        }

        // If URL is external (starts with http), return as is
        if (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://')) {
            return $rawUrl;
        }

        // Otherwise, wrap with url() helper for proper absolute URL
        return url($rawUrl);
    };
@endphp
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(!empty($siteFavicon))
    <link rel="icon" href="{{ $siteFavicon }}">
    <link rel="shortcut icon" href="{{ $siteFavicon }}">
    @endif
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

        /* ===== MODERN ACADEMIC DESIGN SYSTEM ===== */
        :root {
            /* Primary Colors */
            --primary-dark: #0A1F44;
            --primary-blue: #0D2B5A;
            --accent-blue: #2F80ED;
            --accent-light: #56CCF2;

            /* Neutral Colors */
            --white: #FFFFFF;
            --gray-50: #F9FAFB;
            --gray-100: #F2F4F7;
            --gray-200: #E4E7EC;
            --gray-300: #D0D5DD;
            --gray-500: #667085;
            --gray-700: #344054;
            --gray-900: #101828;

            /* Semantic Colors */
            --success: #12B76A;
            --warning: #F79009;
            --error: #F04438;

            /* Typography */
            --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-heading: 'Poppins', 'Inter', sans-serif;

            /* Shadows */
            --shadow-sm: 0 1px 2px rgba(16, 24, 40, 0.05);
            --shadow-md: 0 4px 8px -2px rgba(16, 24, 40, 0.1), 0 2px 4px -2px rgba(16, 24, 40, 0.06);
            --shadow-lg: 0 12px 16px -4px rgba(16, 24, 40, 0.08), 0 4px 6px -2px rgba(16, 24, 40, 0.03);
            --shadow-xl: 0 20px 24px -4px rgba(16, 24, 40, 0.08), 0 8px 8px -4px rgba(16, 24, 40, 0.03);

            /* Border Radius */
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-full: 9999px;

            /* Transitions */
            --transition-fast: 150ms ease;
            --transition-normal: 250ms ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-primary);
            background: var(--white);
            color: var(--gray-700);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ===== TOP INFO BAR (Blue Panel) ===== */
        .top-info-bar {
            background: #2F5BFF;
            height: 38px;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .top-info-bar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-info-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .top-info-left .info-icon {
            color: #FFFFFF;
            font-size: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.95;
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .top-info-left .info-icon:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        .top-info-left .info-icon img {
            height: 18px;
            width: auto;
            border-radius: 50%;
        }

        .top-info-left .accessibility-icon {
            position: relative;
        }

        .top-info-left .accessibility-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #FFFFFF;
            transform: translateY(-50%) rotate(-45deg);
        }

        .top-info-right {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .top-info-right .social-icon {
            color: #FFFFFF;
            font-size: 14px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: var(--transition-fast);
        }

        .top-info-right .social-icon:hover {
            opacity: 0.75;
        }

        /* ===== MAIN HEADER (White Panel) ===== */
        .main-header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
        }

        /* Logo Section - Left */
        .header-logos {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-logos a {
            display: flex;
            align-items: center;
        }

        .header-logos img {
            height: 42px;
            width: auto;
        }

        .header-logos .logo-separator {
            width: 1px;
            height: 32px;
            background: var(--gray-200);
        }

        /* Main Navigation - Center */
        .main-nav {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .main-nav .nav-link {
            font-weight: 500;
            font-size: 14px;
            color: #333333;
            padding: 10px 14px;
            transition: var(--transition-fast);
            text-decoration: none;
            white-space: nowrap;
        }

        .main-nav .nav-link:hover {
            color: #2F5BFF;
        }

        .main-nav .nav-link.active {
            color: #2F5BFF;
        }

        /* Header Actions - Right */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-search-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: #667085;
            cursor: pointer;
            transition: var(--transition-fast);
            font-size: 16px;
        }

        .header-search-btn:hover {
            color: #2F5BFF;
        }

        .lang-selector-new {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 14px;
            color: #333333;
            font-weight: 500;
        }

        .lang-selector-new img {
            width: 20px;
            height: 14px;
            object-fit: cover;
            border-radius: 2px;
        }

        .btn-login {
            background: #2F5BFF;
            color: var(--white);
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition-fast);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-login:hover {
            background: #2548D9;
            color: var(--white);
            transform: translateY(-1px);
        }

        /* Dropdown */
        .nav-dropdown {
            position: relative;
        }

        .nav-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: var(--white);
            min-width: 220px;
            padding: 8px;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--gray-200);
            border-top: none;
            opacity: 0;
            visibility: hidden;
            transform: translateY(0);
            transition: var(--transition-normal);
            z-index: 100;
        }

        .nav-dropdown:hover .nav-dropdown-menu {
            opacity: 1;
            visibility: visible;
        }

        .nav-dropdown-menu a {
            display: block;
            padding: 10px 14px;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 14px;
            border-radius: var(--radius-md);
            transition: var(--transition-fast);
        }

        .nav-dropdown-menu a:hover {
            background: var(--gray-100);
            color: #2F5BFF;
        }

        /* Language Dropdown */
        .lang-dropdown {
            position: relative;
        }

        .lang-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--white);
            min-width: 120px;
            padding: 6px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            opacity: 0;
            visibility: hidden;
            transition: var(--transition-fast);
            z-index: 100;
        }

        .lang-dropdown:hover .lang-dropdown-menu {
            opacity: 1;
            visibility: visible;
        }

        .lang-dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 14px;
            border-radius: var(--radius-sm);
            transition: var(--transition-fast);
        }

        .lang-dropdown-menu a:hover {
            background: var(--gray-100);
        }

        .lang-dropdown-menu a.active {
            background: #2F5BFF;
            color: #FFFFFF;
        }

        .lang-dropdown-menu img {
            width: 20px;
            height: 14px;
            object-fit: cover;
            border-radius: 2px;
        }

        /* Hide old utility bar */
        .utility-bar {
            display: none;
        }

        /* ===== HEADER TOP ROW (Logo + Actions) ===== */
        .header-top-row {
            padding: 10px 0;
            background: var(--white);
        }

        .header-top-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ===== HEADER NAV ROW (Menu) ===== */
        .header-nav-row {
            background: var(--white);
        }

        .header-nav-row .main-nav {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0;
        }

        .header-nav-row .nav-link {
            padding: 10px 16px;
            font-size: 14px;
            color: #333333;
        }

        .header-nav-row .nav-link:hover,
        .header-nav-row .nav-link.active {
            color: #2F5BFF;
            background: transparent;
        }

        /* Header Right Actions */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-search-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: #667085;
            cursor: pointer;
            transition: var(--transition-fast);
            font-size: 16px;
        }

        .header-search-btn:hover {
            color: #2F5BFF;
        }

        .lang-selector {
            display: flex;
            background: var(--gray-100);
            border-radius: var(--radius-full);
            padding: 3px;
        }

        .lang-selector button {
            padding: 5px 10px;
            border: none;
            background: transparent;
            font-size: 11px;
            font-weight: 600;
            color: var(--gray-500);
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .lang-selector button.active {
            background: var(--white);
            color: var(--primary-dark);
            box-shadow: var(--shadow-sm);
        }

        .btn-login {
            background: #2F5BFF;
            color: var(--white);
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition-fast);
            text-decoration: none;
        }

        .btn-login:hover {
            background: #2548D9;
            color: var(--white);
        }

        /* ===== HERO SECTION ===== */
        .hero-academic {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-blue) 100%);
            position: relative;
            overflow: hidden;
            min-height: 600px;
            display: flex;
            align-items: center;
        }

        .hero-academic::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }

        .hero-academic::after {
            content: '';
            position: absolute;
            right: -10%;
            bottom: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(47, 128, 237, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(47, 128, 237, 0.2);
            border: 1px solid rgba(47, 128, 237, 0.4);
            padding: 8px 18px;
            border-radius: var(--radius-full);
            color: var(--accent-light);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .hero-title {
            font-family: var(--font-heading);
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--white);
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .hero-subtitle {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.8);
            max-width: 540px;
            margin-bottom: 32px;
            line-height: 1.7;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: var(--accent-blue);
            color: var(--white);
            border: none;
            padding: 14px 32px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: var(--transition-normal);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-hero-primary:hover {
            background: #1a6fd1;
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(47, 128, 237, 0.3);
        }

        .btn-hero-secondary {
            background: transparent;
            color: var(--white);
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 14px 32px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: var(--transition-normal);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            color: var(--white);
        }

        /* ===== HALF HERO (Inner Pages) ===== */
        .half-hero {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-blue) 100%);
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 80px 0 60px;
        }

        .half-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }

        .half-hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .half-hero-title {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 16px;
        }

        .breadcrumb-academic {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .breadcrumb-academic a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: var(--transition-fast);
        }

        .breadcrumb-academic a:hover {
            color: var(--white);
        }

        .breadcrumb-academic .separator {
            color: rgba(255, 255, 255, 0.4);
        }

        /* ===== SECTION STYLES ===== */
        .section {
            padding: 80px 0;
        }

        .section-light {
            background: var(--gray-50);
        }

        .section-title {
            font-family: var(--font-heading);
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 16px;
        }

        .section-subtitle {
            font-size: 1rem;
            color: var(--gray-500);
            max-width: 600px;
        }

        /* ===== CARDS ===== */
        .card-academic {
            background: var(--white);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: var(--transition-normal);
        }

        .card-academic:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
        }

        .card-academic-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-academic-body {
            padding: 24px;
        }

        .card-academic-title {
            font-family: var(--font-heading);
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 8px;
        }

        .card-academic-text {
            font-size: 14px;
            color: var(--gray-500);
            line-height: 1.6;
        }

        /* ===== BUTTONS ===== */
        .btn-academic {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: var(--transition-normal);
            cursor: pointer;
            border: none;
        }

        .btn-academic-primary {
            background: var(--accent-blue);
            color: var(--white);
        }

        .btn-academic-primary:hover {
            background: #1a6fd1;
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-academic-outline {
            background: transparent;
            color: var(--accent-blue);
            border: 2px solid var(--accent-blue);
        }

        .btn-academic-outline:hover {
            background: var(--accent-blue);
            color: var(--white);
        }

        /* ===== FOOTER ===== */
        .footer-academic {
            background: var(--white);
            border-top: 1px solid var(--gray-200);
        }

        .footer-main {
            padding: 60px 0 40px;
        }

        .footer-logo {
            height: 45px;
            margin-bottom: 20px;
        }

        .footer-desc {
            font-size: 14px;
            color: var(--gray-500);
            line-height: 1.7;
            max-width: 300px;
        }

        .footer-title {
            font-family: var(--font-heading);
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            font-size: 14px;
            color: var(--gray-500);
            text-decoration: none;
            transition: var(--transition-fast);
        }

        .footer-links a:hover {
            color: var(--accent-blue);
        }

        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
        }

        .footer-contact-item i {
            width: 20px;
            color: var(--accent-blue);
            margin-top: 3px;
        }

        .footer-contact-item span {
            font-size: 14px;
            color: var(--gray-500);
        }

        .footer-social {
            display: flex;
            gap: 10px;
            margin-top: 24px;
        }

        .footer-social a {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gray-100);
            border-radius: var(--radius-full);
            color: var(--gray-500);
            transition: var(--transition-fast);
        }

        .footer-social a:hover {
            background: var(--accent-blue);
            color: var(--white);
        }

        .footer-bottom {
            padding: 20px 0;
            border-top: 1px solid var(--gray-200);
        }

        .footer-copyright {
            font-size: 14px;
            color: var(--gray-500);
        }

        /* ===== MOBILE MENU ===== */
        .mobile-menu-toggle {
            display: none;
            width: 36px;
            height: 36px;
            align-items: center;
            justify-content: center;
            background: var(--gray-100);
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-size: 16px;
            color: var(--gray-700);
        }

        /* Mobile Nav */
        .mobile-nav {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--white);
            border-top: 1px solid var(--gray-200);
            box-shadow: var(--shadow-lg);
            padding: 16px;
            z-index: 999;
        }

        .mobile-nav.active {
            display: block;
        }

        .mobile-nav a {
            display: block;
            padding: 12px 16px;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: var(--radius-md);
            transition: var(--transition-fast);
        }

        .mobile-nav a:hover,
        .mobile-nav a.active {
            background: var(--gray-100);
            color: var(--accent-blue);
        }

        @media (max-width: 991.98px) {
            .header-nav-row {
                display: none;
            }

            .mobile-menu-toggle {
                display: flex !important;
            }

            .header-logos img {
                height: 36px;
            }

            .hero-title {
                font-size: 2.25rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .section {
                padding: 60px 0;
            }
        }

        @media (max-width: 767.98px) {
            .top-info-bar {
                display: none;
            }

            .header-logos .logo-separator,
            .header-logos a:last-child {
                display: none;
            }

            .header-logos img {
                height: 32px;
            }

            .lang-dropdown {
                display: none;
            }

            .hero-academic {
                min-height: 500px;
            }

            .hero-title {
                font-size: 1.875rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .hero-buttons .btn-hero-primary,
            .hero-buttons .btn-hero-secondary {
                width: 100%;
                justify-content: center;
            }

            .half-hero {
                min-height: 250px;
                padding: 60px 0 40px;
            }

            .half-hero-title {
                font-size: 1.75rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .footer-main {
                padding: 40px 0 30px;
            }
        }

        @media (max-width: 575.98px) {
            .btn-login span,
            .btn-login i:not(:first-child) {
                display: none;
            }

            .btn-login {
                padding: 8px 12px;
            }

            .header-search-btn {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .hero-title {
                font-size: 1.5rem;
            }
        }

        /* ===== ADDITIONAL UTILITIES ===== */
        .text-primary-dark { color: var(--primary-dark) !important; }
        .text-accent-blue { color: var(--accent-blue) !important; }
        .bg-gray-50 { background-color: var(--gray-50) !important; }
        .bg-gray-100 { background-color: var(--gray-100) !important; }

        /* ===== ACCESSIBILITY STYLES ===== */
        .accessibility-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: var(--accent-blue);
            border: none;
            border-radius: var(--radius-md);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            flex-shrink: 0;
        }

        .accessibility-btn:hover {
            transform: scale(1.05);
            background: #1a6fd1;
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
            background: var(--accent-blue);
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
            color: var(--accent-blue);
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
            background: var(--accent-blue);
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
            border-color: var(--accent-blue);
            color: var(--accent-blue);
        }

        .font-size-btn.active {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
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
                width: 32px;
                height: 32px;
                font-size: 14px;
            }
        }

        /* ===== CONTACT PAGE PRESERVE STYLES ===== */
        /* Ensure contact page keeps its original design */
        .academic-template main .contact-hero {
            min-height: 55vh !important;
            padding-top: 140px !important;
            padding-bottom: 80px !important;
            background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 50%, #1b263b 100%) !important;
            display: flex !important;
            align-items: center !important;
        }

        .academic-template main .contact-hero h1 {
            color: #ffffff !important;
            font-size: 3rem !important;
            font-weight: 800 !important;
        }

        .academic-template main .contact-hero p {
            color: #cbd5e1 !important;
            font-size: 1.2rem !important;
        }

        .academic-template main .contact-hero .hero-badge {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6) !important;
            color: white !important;
        }

        .academic-template main .hero-breadcrumb a {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .academic-template main .hero-breadcrumb .current {
            color: #3b82f6 !important;
        }

        /* Contact Section */
        .academic-template main .contact-section {
            padding: 80px 0 !important;
            background: #fff !important;
        }

        .academic-template main .contact-grid {
            display: grid !important;
            grid-template-columns: 1fr 2fr !important;
            gap: 40px !important;
        }

        .academic-template main .contact-info-card {
            background: #fff !important;
            border-radius: 16px !important;
            padding: 30px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .academic-template main .info-item {
            display: flex !important;
            align-items: flex-start !important;
            gap: 15px !important;
            padding: 20px 0 !important;
            border-bottom: 1px solid #f0f0f0 !important;
        }

        .academic-template main .info-icon {
            width: 50px !important;
            height: 50px !important;
            border-radius: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .academic-template main .info-icon.green { background: #E8F5E9 !important; }
        .academic-template main .info-icon.blue { background: #E3F2FD !important; }
        .academic-template main .info-icon.orange { background: #FFE0B2 !important; }
        .academic-template main .info-icon.purple { background: #F3E5F5 !important; }

        .academic-template main .info-content h4 {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: #1a1a2e !important;
        }

        .academic-template main .info-content p {
            color: #6b7280 !important;
            font-size: 0.9rem !important;
        }

        .academic-template main .working-hours {
            background: #f8f9fa !important;
            border-radius: 12px !important;
            padding: 20px !important;
        }

        .academic-template main .working-hours h5 {
            color: #1a1a2e !important;
        }

        .academic-template main .hours-list li {
            color: #6b7280 !important;
        }

        .academic-template main .contact-form-card {
            background: #fff !important;
            border-radius: 16px !important;
            padding: 40px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .academic-template main .form-header h3 {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #1a1a2e !important;
        }

        .academic-template main .form-header p {
            color: #6b7280 !important;
        }

        .academic-template main .form-label {
            color: #1a1a2e !important;
            font-weight: 500 !important;
        }

        .academic-template main .contact-section .form-control,
        .academic-template main .contact-form-card .form-control {
            border: 2px solid #e5e7eb !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            background: #fff !important;
            color: #1a1a2e !important;
        }

        .academic-template main .contact-section .form-select,
        .academic-template main .contact-form-card .form-select {
            border: 2px solid #e5e7eb !important;
            border-radius: 10px !important;
            background: #fff !important;
        }

        .academic-template main .contact-section .submit-btn,
        .academic-template main .contact-form-card .submit-btn {
            background: #1a1a2e !important;
            color: #fff !important;
            padding: 14px 30px !important;
            border-radius: 10px !important;
        }

        .academic-template main .contact-section .submit-btn:hover {
            background: #C8E637 !important;
            color: #1a1a2e !important;
        }

        /* Map Section */
        .academic-template main .map-section {
            padding: 0 0 80px !important;
            background: #fff !important;
        }

        .academic-template main .map-container {
            background: #fff !important;
            border-radius: 16px !important;
            height: 400px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        }

        /* FAQ Section - Contact Page */
        .academic-template main .faq-section {
            background: #f8f9fa !important;
            padding: 80px 0 !important;
        }

        .academic-template main .section-header {
            text-align: center !important;
            margin-bottom: 50px !important;
        }

        .academic-template main .section-badge {
            display: inline-block !important;
            background: rgba(200, 230, 55, 0.15) !important;
            color: #1a1a2e !important;
            padding: 6px 16px !important;
            border-radius: 20px !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
        }

        .academic-template main .section-title {
            font-size: 2.2rem !important;
            font-weight: 700 !important;
            color: #1a1a2e !important;
        }

        .academic-template main .faq-grid {
            max-width: 820px !important;
            margin: 0 auto !important;
        }

        .academic-template main .faq-item {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 16px !important;
            margin-bottom: 14px !important;
            overflow: hidden !important;
            box-shadow: none !important;
            transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease !important;
        }

        .academic-template main .faq-item:hover {
            border-color: #C8E637 !important;
            box-shadow: 0 6px 18px rgba(200, 230, 55, 0.18) !important;
            transform: translateY(-1px) !important;
        }

        .academic-template main .faq-item.active {
            border-color: #C8E637 !important;
            box-shadow: 0 10px 28px rgba(200, 230, 55, 0.22) !important;
        }

        .academic-template main .faq-question {
            width: 100% !important;
            padding: 24px 28px !important;
            background: transparent !important;
            border: none !important;
            text-align: left !important;
            font-size: 1.02rem !important;
            font-weight: 600 !important;
            color: #1a1a2e !important;
            cursor: pointer !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 18px !important;
            transition: background 0.25s ease !important;
        }

        .academic-template main .faq-question:hover {
            background: rgba(200, 230, 55, 0.06) !important;
        }

        .academic-template main .faq-question span {
            flex: 1 !important;
            line-height: 1.55 !important;
            text-align: left !important;
            padding-right: 4px !important;
        }

        .academic-template main .faq-question i {
            width: 38px !important;
            height: 38px !important;
            border-radius: 50% !important;
            background: rgba(200, 230, 55, 0.18) !important;
            color: #1a1a2e !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.85rem !important;
            flex-shrink: 0 !important;
            transition: background 0.3s ease, color 0.3s ease, transform 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .academic-template main .faq-item.active .faq-question i,
        .academic-template main .faq-question.active i {
            background: #C8E637 !important;
            color: #1a1a2e !important;
            transform: rotate(180deg) !important;
        }

        .academic-template main .faq-answer {
            max-height: 0;
            overflow: hidden !important;
            padding: 0 !important;
            color: #4b5563 !important;
            line-height: 1.75 !important;
            font-size: 0.95rem !important;
            display: block !important;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .academic-template main .faq-item.active .faq-answer {
            max-height: 1500px;
        }

        .academic-template main .faq-answer-inner {
            padding: 4px 28px 26px !important;
        }

        .academic-template main .social-section {
            margin-top: 25px !important;
            padding-top: 20px !important;
            border-top: 1px solid #f0f0f0 !important;
            background: transparent !important;
        }

        .academic-template main .social-section h5 {
            color: #1a1a2e !important;
        }

        .academic-template main .social-link {
            width: 40px !important;
            height: 40px !important;
            border-radius: 10px !important;
            background: rgba(200, 230, 55, 0.15) !important;
            color: #1a1a2e !important;
        }

        .academic-template main .social-link:hover {
            background: #C8E637 !important;
        }

        @media (max-width: 991px) {
            .academic-template main .contact-grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* ===== VACANCIES PAGE STYLES FOR ACADEMIC TEMPLATE ===== */
        .academic-template main .page-hero {
            background: linear-gradient(135deg, #0A1F44 0%, #0D2B5A 100%) !important;
            padding: 80px 0 60px !important;
            padding-top: 140px !important;
            margin-top: -80px !important;
        }

        .academic-template main .page-hero::before {
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") !important;
            opacity: 0.5 !important;
        }

        .academic-template main .hero-breadcrumb {
            margin-bottom: 20px !important;
        }

        .academic-template main .hero-breadcrumb a {
            color: rgba(255,255,255,0.8) !important;
        }

        .academic-template main .hero-breadcrumb a:hover {
            color: #fff !important;
        }

        .academic-template main .hero-title {
            font-size: 2.5rem !important;
            font-weight: 700 !important;
            color: #FFFFFF !important;
            margin-bottom: 16px !important;
        }

        .academic-template main .hero-subtitle {
            font-size: 1.1rem !important;
            color: rgba(255,255,255,0.9) !important;
            max-width: 600px !important;
        }

        /* Filter Card */
        .academic-template main .filter-card {
            background: #FFFFFF !important;
            border-radius: 16px !important;
            padding: 24px !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;
            border: 1px solid #E4E7EC !important;
        }

        .academic-template main .filter-title {
            font-weight: 600 !important;
            color: #101828 !important;
            margin-bottom: 20px !important;
            padding-bottom: 16px !important;
            border-bottom: 1px solid #E4E7EC !important;
        }

        .academic-template main .filter-card .form-control,
        .academic-template main .filter-card .form-select {
            border: 1px solid #E4E7EC !important;
            border-radius: 8px !important;
            padding: 10px 14px !important;
        }

        .academic-template main .filter-card .form-control:focus,
        .academic-template main .filter-card .form-select:focus {
            border-color: #2F80ED !important;
            box-shadow: 0 0 0 3px rgba(47, 128, 237, 0.1) !important;
        }

        .academic-template main .filter-card .btn-primary {
            background: #2F80ED !important;
            border-color: #2F80ED !important;
            border-radius: 8px !important;
        }

        .academic-template main .filter-card .btn-primary:hover {
            background: #1a6fd1 !important;
            border-color: #1a6fd1 !important;
        }

        /* Vacancy Cards */
        .academic-template main .vacancy-card {
            background: #FFFFFF !important;
            border-radius: 16px !important;
            border: 1px solid #E4E7EC !important;
            overflow: hidden !important;
            transition: all 0.3s ease !important;
            height: 100% !important;
        }

        .academic-template main .vacancy-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12) !important;
            border-color: #2F80ED !important;
        }

        .academic-template main .vacancy-card-body {
            padding: 24px !important;
        }

        .academic-template main .featured-badge {
            position: absolute !important;
            top: 16px !important;
            right: 16px !important;
            background: linear-gradient(135deg, #2F80ED, #0A1F44) !important;
            color: #FFFFFF !important;
            padding: 6px 12px !important;
            border-radius: 50px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
        }

        .academic-template main .vacancy-type-badge {
            display: inline-flex !important;
            align-items: center !important;
            padding: 6px 14px !important;
            border-radius: 50px !important;
            font-size: 12px !important;
            font-weight: 500 !important;
        }

        .academic-template main .vacancy-type-badge.full_time {
            background: #DCFCE7 !important;
            color: #166534 !important;
        }

        .academic-template main .vacancy-type-badge.part_time {
            background: #FEF3C7 !important;
            color: #92400E !important;
        }

        .academic-template main .vacancy-type-badge.contract {
            background: #DBEAFE !important;
            color: #1E40AF !important;
        }

        .academic-template main .vacancy-type-badge.internship {
            background: #F3E8FF !important;
            color: #7C3AED !important;
        }

        .academic-template main .vacancy-title {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            margin: 16px 0 8px !important;
        }

        .academic-template main .vacancy-title a {
            color: #101828 !important;
            text-decoration: none !important;
        }

        .academic-template main .vacancy-title a:hover {
            color: #2F80ED !important;
        }

        .academic-template main .vacancy-department {
            color: #667085 !important;
            font-size: 14px !important;
            margin-bottom: 12px !important;
        }

        .academic-template main .vacancy-excerpt {
            color: #667085 !important;
            font-size: 14px !important;
            line-height: 1.6 !important;
        }

        .academic-template main .vacancy-meta {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 16px !important;
            margin-top: 16px !important;
            padding-top: 16px !important;
            border-top: 1px solid #E4E7EC !important;
        }

        .academic-template main .vacancy-meta-item {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 13px !important;
            color: #667085 !important;
        }

        .academic-template main .vacancy-card .btn-outline-primary {
            color: #2F80ED !important;
            border-color: #2F80ED !important;
            border-radius: 8px !important;
        }

        .academic-template main .vacancy-card .btn-outline-primary:hover {
            background: #2F80ED !important;
            color: #FFFFFF !important;
        }

        /* Empty State */
        .academic-template main .empty-state {
            text-align: center !important;
            padding: 80px 20px !important;
            background: #FFFFFF !important;
            border-radius: 16px !important;
            border: 1px solid #E4E7EC !important;
        }

        .academic-template main .empty-state i {
            font-size: 4rem !important;
            color: #D1D5DB !important;
            margin-bottom: 24px !important;
        }

        .academic-template main .empty-state h3 {
            color: #101828 !important;
        }

        .academic-template main .empty-state p {
            color: #667085 !important;
        }

        /* CTA Section */
        .academic-template main .cta-section {
            background: linear-gradient(135deg, #2F80ED 0%, #0A1F44 100%) !important;
            color: #FFFFFF !important;
            padding: 60px 0 !important;
        }

        .academic-template main .cta-section h2 {
            color: #FFFFFF !important;
            font-size: 1.75rem !important;
            font-weight: 700 !important;
        }

        .academic-template main .cta-section p {
            color: rgba(255,255,255,0.9) !important;
        }

        .academic-template main .cta-section .btn-light {
            background: #FFFFFF !important;
            color: #2F80ED !important;
            border: none !important;
            font-weight: 600 !important;
        }

        .academic-template main .cta-section .btn-light:hover {
            background: #F0F9FF !important;
        }

        /* Vacancy Show Page */
        .academic-template main .vacancy-detail-card {
            background: #FFFFFF !important;
            border-radius: 16px !important;
            overflow: hidden !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;
            border: 1px solid #E4E7EC !important;
        }

        .academic-template main .vacancy-section {
            padding: 24px !important;
            border-bottom: 1px solid #E4E7EC !important;
        }

        .academic-template main .vacancy-section:last-child {
            border-bottom: none !important;
        }

        .academic-template main .vacancy-section h3 {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: #101828 !important;
            margin-bottom: 16px !important;
        }

        .academic-template main .vacancy-list {
            padding-left: 0 !important;
            list-style: none !important;
        }

        .academic-template main .vacancy-list li {
            position: relative !important;
            padding-left: 24px !important;
            margin-bottom: 10px !important;
            line-height: 1.6 !important;
            color: #667085 !important;
        }

        .academic-template main .vacancy-list li::before {
            content: '' !important;
            position: absolute !important;
            left: 0 !important;
            top: 10px !important;
            width: 8px !important;
            height: 8px !important;
            background: #2F80ED !important;
            border-radius: 50% !important;
        }

        .academic-template main .vacancy-dept-badge {
            background: rgba(255,255,255,0.15) !important;
            color: #FFFFFF !important;
            padding: 6px 14px !important;
            border-radius: 50px !important;
        }

        .academic-template main .info-card {
            background: #F9FAFB !important;
            border-radius: 16px !important;
            padding: 24px !important;
            border: 1px solid #E4E7EC !important;
        }

        .academic-template main .info-item {
            display: flex !important;
            align-items: flex-start !important;
            gap: 12px !important;
            margin-bottom: 16px !important;
        }

        .academic-template main .info-icon {
            width: 40px !important;
            height: 40px !important;
            background: #FFFFFF !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #2F80ED !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
        }

        .academic-template main .related-card {
            background: #FFFFFF !important;
            border-radius: 12px !important;
            padding: 16px !important;
            border: 1px solid #E4E7EC !important;
            transition: all 0.3s ease !important;
        }

        .academic-template main .related-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
            transform: translateY(-2px) !important;
            border-color: #2F80ED !important;
        }

        .academic-template main .apply-sidebar .btn-primary {
            background: #2F80ED !important;
            border-color: #2F80ED !important;
            border-radius: 10px !important;
        }

        .academic-template main .apply-sidebar .btn-primary:hover {
            background: #1a6fd1 !important;
            border-color: #1a6fd1 !important;
        }

        .academic-template main .apply-sidebar .btn-outline-primary {
            color: #2F80ED !important;
            border-color: #2F80ED !important;
        }

        .academic-template main .apply-sidebar .btn-outline-primary:hover {
            background: #2F80ED !important;
            color: #FFFFFF !important;
        }

        /* ===== CHAT WIDGET BLUE THEME FOR ACADEMIC TEMPLATE ===== */
        .academic-template .support-chat-toggle {
            background: linear-gradient(135deg, #2F80ED 0%, #0A1F44 100%) !important;
            box-shadow: 0 4px 20px rgba(47, 128, 237, 0.4) !important;
        }

        .academic-template .support-chat-toggle:hover {
            box-shadow: 0 6px 25px rgba(47, 128, 237, 0.5) !important;
        }

        .academic-template .support-chat-header {
            background: linear-gradient(135deg, #2F80ED 0%, #0A1F44 100%) !important;
        }

        .academic-template .support-chat-input-field:focus {
            border-color: #2F80ED !important;
            box-shadow: 0 0 0 3px rgba(47, 128, 237, 0.1) !important;
        }

        .academic-template .support-chat-start-btn {
            background: linear-gradient(135deg, #2F80ED 0%, #0A1F44 100%) !important;
        }

        .academic-template .support-message.user .support-message-bubble {
            background: linear-gradient(135deg, #2F80ED 0%, #0A1F44 100%) !important;
        }

        .academic-template .support-chat-send {
            background: linear-gradient(135deg, #2F80ED 0%, #0A1F44 100%) !important;
        }

        .academic-template .support-chat-welcome-icon {
            background: linear-gradient(135deg, rgba(47, 128, 237, 0.1) 0%, rgba(10, 31, 68, 0.1) 100%) !important;
            color: #2F80ED !important;
        }

        .academic-template .support-chat-input:focus {
            border-color: #2F80ED !important;
        }

        /* ===== CSS ISOLATION - Override page-specific styles ===== */

        /* Force override of :root variables from page styles */
        body.academic-template,
        body.academic-template *,
        .academic-template main,
        .academic-template main * {
            --primary-lime: #2F80ED;
            --primary-dark: #0A1F44;
            --text-dark: #101828;
            --text-gray: #667085;
            --bg-light: #F9FAFB;
            --card-green: #F2F4F7;
            --card-yellow: #F2F4F7;
            --card-orange: #F2F4F7;
            --card-blue: #F2F4F7;
        }

        /* ===== HERO SLIDER COMPLETE OVERRIDE ===== */
        .academic-template main .hero-slider {
            position: relative !important;
            min-height: 550px !important;
            height: 550px !important;
            margin-top: 0 !important;
            overflow: hidden !important;
            display: block !important;
        }

        .academic-template main .hero-slide {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            padding-top: 80px !important;
            display: flex !important;
            align-items: center !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: opacity 1s ease-in-out !important;
        }

        .academic-template main .hero-slide.active {
            opacity: 1 !important;
            visibility: visible !important;
            z-index: 1 !important;
        }

        .academic-template main .hero-slide-bg {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background-size: cover !important;
            background-position: center !important;
            z-index: -1 !important;
        }

        .academic-template main .hero-slide-bg::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: linear-gradient(135deg, rgba(10, 31, 68, 0.92) 0%, rgba(13, 43, 90, 0.85) 100%) !important;
        }

        .academic-template main .hero-slide-content {
            position: relative !important;
            z-index: 2 !important;
        }

        .academic-template main .hero-title {
            font-family: var(--font-heading) !important;
            font-size: 2.75rem !important;
            font-weight: 700 !important;
            color: #FFFFFF !important;
            line-height: 1.2 !important;
            margin-bottom: 16px !important;
            max-width: 580px !important;
        }

        .academic-template main .hero-subtitle {
            font-size: 1rem !important;
            color: rgba(255, 255, 255, 0.85) !important;
            line-height: 1.7 !important;
            margin-bottom: 28px !important;
            max-width: 480px !important;
        }

        /* Hero badge */
        .academic-template main .hero-badge {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            background: rgba(47, 128, 237, 0.25) !important;
            border: 1px solid rgba(47, 128, 237, 0.4) !important;
            padding: 6px 14px !important;
            border-radius: 20px !important;
            margin-bottom: 16px !important;
            backdrop-filter: blur(10px) !important;
        }

        .academic-template main .hero-badge-icon {
            width: 18px !important;
            height: 18px !important;
            background: #2F80ED !important;
            border-radius: 4px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .academic-template main .hero-badge-icon i {
            font-size: 9px !important;
            color: #FFFFFF !important;
        }

        .academic-template main .hero-badge span {
            color: #FFFFFF !important;
            font-size: 0.7rem !important;
            font-weight: 600 !important;
            letter-spacing: 0.5px !important;
        }

        /* Hero buttons */
        .academic-template main .hero-buttons {
            display: flex !important;
            gap: 12px !important;
            flex-wrap: wrap !important;
        }

        .academic-template main .btn-lime {
            background: #2F80ED !important;
            color: #FFFFFF !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
            border-radius: 50px !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            border: 2px solid #2F80ED !important;
            font-size: 14px !important;
            transition: all 0.3s ease !important;
        }

        .academic-template main .btn-lime:hover {
            background: #1a6fd1 !important;
            border-color: #1a6fd1 !important;
            color: #FFFFFF !important;
            transform: translateY(-2px) !important;
        }

        .academic-template main .btn-lime i {
            font-size: 14px !important;
        }

        .academic-template main .btn-outline-white {
            background: transparent !important;
            color: #FFFFFF !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
            border-radius: 50px !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            border: 2px solid rgba(255, 255, 255, 0.4) !important;
            font-size: 14px !important;
            transition: all 0.3s ease !important;
        }

        .academic-template main .btn-outline-white:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.7) !important;
            color: #FFFFFF !important;
        }

        .academic-template main .btn-outline-white i {
            font-size: 14px !important;
        }

        .academic-template main .btn-dark {
            background: #0A1F44 !important;
            color: #FFFFFF !important;
            border: 2px solid #0A1F44 !important;
            padding: 12px 24px !important;
            border-radius: 50px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
        }

        .academic-template main .btn-dark:hover {
            background: #0d2b5a !important;
            border-color: #0d2b5a !important;
        }

        /* Slider arrows */
        .academic-template main .slider-arrow {
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            width: 44px !important;
            height: 44px !important;
            background: rgba(47, 128, 237, 0.3) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(47, 128, 237, 0.5) !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #FFFFFF !important;
            font-size: 1rem !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            z-index: 10 !important;
        }

        .academic-template main .slider-arrow:hover {
            background: #2F80ED !important;
            border-color: #2F80ED !important;
        }

        .academic-template main .slider-arrow.prev {
            left: 20px !important;
        }

        .academic-template main .slider-arrow.next {
            right: 20px !important;
        }

        .academic-template main .slider-arrow i {
            font-size: 16px !important;
        }

        /* Slider dots */
        .academic-template main .slider-nav {
            position: absolute !important;
            bottom: 30px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            display: flex !important;
            gap: 10px !important;
            z-index: 10 !important;
        }

        .academic-template main .slider-dot {
            width: 10px !important;
            height: 10px !important;
            border-radius: 50% !important;
            background: rgba(255, 255, 255, 0.4) !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            border: none !important;
        }

        .academic-template main .slider-dot.active {
            background: #2F80ED !important;
            transform: scale(1.2) !important;
        }

        .academic-template main .slider-dot:hover {
            background: rgba(255, 255, 255, 0.7) !important;
        }

        /* ===== STATISTICS SECTION ===== */
        .academic-template main .statistics-section {
            padding: 60px 0 !important;
            background: #FFFFFF !important;
        }

        .academic-template main .stat-cards {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 20px !important;
        }

        .academic-template main .stat-card {
            padding: 28px 20px !important;
            border-radius: 12px !important;
            text-align: center !important;
            background: #FFFFFF !important;
            border: 1px solid #E4E7EC !important;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.1) !important;
            transition: all 0.3s ease !important;
        }

        .academic-template main .stat-card:nth-child(1),
        .academic-template main .stat-card:nth-child(2),
        .academic-template main .stat-card:nth-child(3),
        .academic-template main .stat-card:nth-child(4) {
            background: #FFFFFF !important;
        }

        .academic-template main .stat-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 12px 24px rgba(16, 24, 40, 0.12) !important;
            border-color: #2F80ED !important;
        }

        .academic-template main .stat-number {
            font-size: 2.5rem !important;
            font-weight: 800 !important;
            color: #2F80ED !important;
            margin-bottom: 6px !important;
            line-height: 1 !important;
        }

        .academic-template main .stat-label {
            font-size: 0.85rem !important;
            color: #667085 !important;
            line-height: 1.4 !important;
        }

        /* ===== SECTION TITLES ===== */
        .academic-template main .section-label {
            color: #667085 !important;
            font-size: 0.8rem !important;
            font-weight: 500 !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            margin-bottom: 6px !important;
        }

        .academic-template main .section-title {
            color: #101828 !important;
            font-size: 1.75rem !important;
            font-weight: 700 !important;
            margin-bottom: 12px !important;
            font-family: var(--font-heading) !important;
        }

        .academic-template main .section-subtitle {
            color: #667085 !important;
            font-size: 0.95rem !important;
            margin-bottom: 32px !important;
        }

        /* ===== QUICK LINKS ===== */
        .academic-template main .quick-links-section {
            padding: 0 0 60px !important;
            background: #FFFFFF !important;
        }

        .academic-template main .quick-links-grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
        }

        .academic-template main .quick-link-card {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
            padding: 20px !important;
            background: #FFFFFF !important;
            border: 1px solid #E4E7EC !important;
            border-radius: 12px !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
        }

        .academic-template main .quick-link-card:hover {
            border-color: #2F80ED !important;
            box-shadow: 0 8px 20px rgba(16, 24, 40, 0.08) !important;
            transform: translateY(-2px) !important;
            background: linear-gradient(135deg, #FFFFFF 0%, #F9FAFB 100%) !important;
        }

        .academic-template main .quick-link-icon {
            width: 44px !important;
            height: 44px !important;
            background: #F2F4F7 !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        .academic-template main .quick-link-icon i {
            font-size: 1.1rem !important;
            color: #0A1F44 !important;
        }

        .academic-template main .quick-link-card:hover .quick-link-icon {
            background: #2F80ED !important;
        }

        .academic-template main .quick-link-card:hover .quick-link-icon i {
            color: #FFFFFF !important;
        }

        .academic-template main .quick-link-content h4 {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: #101828 !important;
            margin-bottom: 4px !important;
        }

        .academic-template main .quick-link-content p {
            font-size: 0.85rem !important;
            color: #667085 !important;
            line-height: 1.4 !important;
            margin: 0 !important;
        }

        .academic-template main .quick-link-arrow {
            width: 32px !important;
            height: 32px !important;
            background: #2F80ED !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-left: auto !important;
            opacity: 0 !important;
            transform: translateX(-10px) !important;
            transition: all 0.3s ease !important;
        }

        .academic-template main .quick-link-arrow i {
            font-size: 0.8rem !important;
            color: #FFFFFF !important;
        }

        .academic-template main .quick-link-card:hover .quick-link-arrow {
            opacity: 1 !important;
            transform: translateX(0) !important;
        }

        /* ===== NEWS SECTION ===== */
        .academic-template main .news-section {
            padding: 60px 0 !important;
            background: #F9FAFB !important;
        }

        .academic-template main .news-grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
        }

        .academic-template main .news-card {
            background: #FFFFFF !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            transition: all 0.3s ease !important;
            border: 1px solid #E4E7EC !important;
        }

        .academic-template main .news-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 12px 24px rgba(16, 24, 40, 0.1) !important;
            border-color: #2F80ED !important;
        }

        .academic-template main .news-card-image {
            height: 180px !important;
            overflow: hidden !important;
        }

        .academic-template main .news-card-image img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .academic-template main .news-card-content {
            padding: 18px !important;
        }

        .academic-template main .news-card-badges {
            display: flex !important;
            gap: 8px !important;
            margin-bottom: 10px !important;
        }

        .academic-template main .badge-news {
            background: #2F80ED !important;
            color: #FFFFFF !important;
            font-size: 0.65rem !important;
            font-weight: 600 !important;
            padding: 3px 8px !important;
            border-radius: 4px !important;
        }

        .academic-template main .badge-category {
            background: #F2F4F7 !important;
            color: #667085 !important;
            font-size: 0.65rem !important;
            font-weight: 500 !important;
            padding: 3px 8px !important;
            border-radius: 4px !important;
        }

        .academic-template main .news-card-title {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: #101828 !important;
            margin-bottom: 6px !important;
            line-height: 1.4 !important;
        }

        .academic-template main .news-card-excerpt {
            font-size: 0.85rem !important;
            color: #667085 !important;
            margin-bottom: 12px !important;
            line-height: 1.5 !important;
        }

        .academic-template main .news-card-link {
            color: #2F80ED !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            text-decoration: none !important;
        }

        .academic-template main .news-card-link i {
            font-size: 0.75rem !important;
        }

        /* ===== EVENTS SECTION ===== */
        .academic-template main .events-section {
            padding: 60px 0 !important;
            background: #FFFFFF !important;
        }

        .academic-template main .events-grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
        }

        .academic-template main .event-card {
            background: #FFFFFF !important;
            border: 1px solid #E4E7EC !important;
            border-radius: 12px !important;
            overflow: hidden !important;
        }

        .academic-template main .event-card:hover {
            border-color: #2F80ED !important;
            box-shadow: 0 8px 20px rgba(16, 24, 40, 0.08) !important;
        }

        .academic-template main .event-card-image {
            height: 160px !important;
            overflow: hidden !important;
            position: relative !important;
        }

        .academic-template main .event-card-image img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .academic-template main .event-badge {
            position: absolute !important;
            top: 10px !important;
            left: 10px !important;
            background: #2F80ED !important;
            color: #FFFFFF !important;
            font-size: 0.65rem !important;
            font-weight: 600 !important;
            padding: 3px 8px !important;
            border-radius: 4px !important;
        }

        .academic-template main .event-card-content {
            padding: 16px !important;
        }

        .academic-template main .event-card-title {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: #101828 !important;
            margin-bottom: 6px !important;
        }

        .academic-template main .event-card-excerpt {
            font-size: 0.8rem !important;
            color: #667085 !important;
            margin-bottom: 12px !important;
            line-height: 1.5 !important;
        }

        .academic-template main .event-card-link {
            color: #2F80ED !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            text-decoration: none !important;
        }

        /* ===== ACADEMY LIFE ===== */
        .academic-template main .academy-life-section {
            padding: 60px 0 !important;
            background: #F9FAFB !important;
        }

        .academic-template main .academy-grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
        }

        .academic-template main .academy-card {
            position: relative !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            height: 240px !important;
        }

        .academic-template main .academy-card img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .academic-template main .academy-card-overlay {
            position: absolute !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: linear-gradient(to top, rgba(10, 31, 68, 0.9), transparent) !important;
            padding: 20px !important;
            color: #FFFFFF !important;
        }

        .academic-template main .academy-card-title {
            font-size: 1rem !important;
            font-weight: 600 !important;
            margin-bottom: 6px !important;
        }

        .academic-template main .academy-card-text {
            font-size: 0.8rem !important;
            opacity: 0.9 !important;
            margin-bottom: 10px !important;
            line-height: 1.5 !important;
        }

        .academic-template main .academy-card-link {
            color: #56CCF2 !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            text-decoration: none !important;
        }

        /* ===== FAQ SECTION (homepage style) ===== */
        .academic-template main .faq-section {
            padding: 80px 0 !important;
            background: #F9FAFB !important;
        }

        .academic-template main .faq-container {
            max-width: 820px !important;
            margin: 0 auto !important;
        }

        .academic-template main .faq-container .faq-item {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            border-bottom: 1px solid #e5e7eb !important;
            border-radius: 16px !important;
            margin-bottom: 14px !important;
            overflow: hidden !important;
            transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease !important;
        }

        .academic-template main .faq-container .faq-item:hover {
            border-color: #C8E637 !important;
            box-shadow: 0 6px 18px rgba(200, 230, 55, 0.18) !important;
            transform: translateY(-1px) !important;
        }

        .academic-template main .faq-container .faq-item.active {
            border-color: #C8E637 !important;
            box-shadow: 0 10px 28px rgba(200, 230, 55, 0.22) !important;
        }

        .academic-template main .faq-container .faq-question {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 18px !important;
            padding: 22px 26px !important;
            cursor: pointer !important;
            transition: background 0.25s ease !important;
        }

        .academic-template main .faq-container .faq-question:hover {
            background: rgba(200, 230, 55, 0.06) !important;
        }

        .academic-template main .faq-container .faq-question h4 {
            flex: 1 !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: #101828 !important;
            margin: 0 !important;
            line-height: 1.5 !important;
        }

        .academic-template main .faq-container .faq-question i {
            width: 38px !important;
            height: 38px !important;
            border-radius: 50% !important;
            background: rgba(200, 230, 55, 0.18) !important;
            color: #1a1a2e !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.85rem !important;
            flex-shrink: 0 !important;
            transition: background 0.3s ease, color 0.3s ease, transform 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .academic-template main .faq-container .faq-item.active .faq-question i {
            background: #C8E637 !important;
            color: #1a1a2e !important;
            transform: rotate(180deg) !important;
        }

        .academic-template main .faq-container .faq-answer {
            max-height: 0;
            overflow: hidden !important;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .academic-template main .faq-container .faq-item.active .faq-answer {
            max-height: 1500px;
        }

        .academic-template main .faq-container .faq-answer p {
            padding: 4px 26px 24px !important;
            color: #667085 !important;
            font-size: 0.95rem !important;
            line-height: 1.75 !important;
            margin: 0 !important;
        }

        /* ===== PARTNERS ===== */
        .academic-template main .partners-section {
            padding: 50px 0 !important;
            background: #F9FAFB !important;
        }

        .academic-template main .partners-grid {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 40px !important;
            flex-wrap: wrap !important;
        }

        .academic-template main .partner-logo {
            height: 36px !important;
            opacity: 0.6 !important;
            filter: grayscale(100%) !important;
            transition: all 0.3s ease !important;
        }

        .academic-template main .partner-logo:hover {
            opacity: 1 !important;
            filter: grayscale(0%) !important;
        }

        /* ===== SECTION BUTTON CONTAINERS ===== */
        /* Fix for "Barcha yangiliklar" and "Ko'proq o'qish" buttons */
        .academic-template main .news-section > .container > .text-center,
        .academic-template main .events-section > .container > .text-center {
            display: block !important;
            position: relative !important;
            margin-top: 32px !important;
            padding: 0 !important;
            clear: both !important;
            width: 100% !important;
        }

        .academic-template main .news-section .text-center .btn-dark,
        .academic-template main .events-section .text-center .btn-lime {
            display: inline-flex !important;
            position: relative !important;
            top: auto !important;
            left: auto !important;
            right: auto !important;
            bottom: auto !important;
            transform: none !important;
            margin: 0 auto !important;
        }

        /* Ensure news-grid and events-grid don't affect buttons */
        .academic-template main .news-grid,
        .academic-template main .events-grid {
            margin-bottom: 0 !important;
        }

        /* ===== ENSURE PROPER IMAGE SIZING ===== */
        .academic-template main img {
            max-width: 100% !important;
            height: auto !important;
        }

        .academic-template main .news-card-image img,
        .academic-template main .event-card-image img,
        .academic-template main .academy-card img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        /* ===== RESPONSIVE OVERRIDES ===== */
        @media (max-width: 991.98px) {
            .academic-template main .hero-slider {
                min-height: 450px !important;
                height: 450px !important;
            }

            .academic-template main .hero-title {
                font-size: 2rem !important;
            }

            .academic-template main .stat-cards {
                grid-template-columns: repeat(2, 1fr) !important;
            }

            .academic-template main .quick-links-grid,
            .academic-template main .news-grid,
            .academic-template main .events-grid,
            .academic-template main .academy-grid {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 767.98px) {
            .academic-template main .hero-slider {
                min-height: 420px !important;
                height: 420px !important;
            }

            .academic-template main .hero-slide {
                padding-top: 50px !important;
            }

            .academic-template main .hero-title {
                font-size: 1.5rem !important;
            }

            .academic-template main .hero-subtitle {
                font-size: 0.9rem !important;
            }

            .academic-template main .hero-buttons {
                flex-direction: column !important;
            }

            .academic-template main .btn-lime,
            .academic-template main .btn-outline-white {
                width: 100% !important;
                justify-content: center !important;
            }

            .academic-template main .stat-cards {
                grid-template-columns: 1fr !important;
            }

            .academic-template main .stat-number {
                font-size: 2rem !important;
            }

            .academic-template main .section-title {
                font-size: 1.5rem !important;
            }

            .academic-template main .slider-arrow {
                width: 36px !important;
                height: 36px !important;
            }

            .academic-template main .slider-arrow.prev {
                left: 10px !important;
            }

            .academic-template main .slider-arrow.next {
                right: 10px !important;
            }
        }

        @stack('styles')
    </style>
    @stack('styles')
</head>
<body class="academic-template">
    <!-- Top Info Bar (Blue Panel) -->
    <div class="top-info-bar">
        <div class="container">
            <!-- Left Side: Icons -->
            <div class="top-info-left">
                <!-- Coat of Arms / Gerb -->
                <span class="info-icon" title="O'zbekiston gerbi">
                    <img src="{{ asset('images/gerb.png') }}" alt="Gerb">
                </span>
                <!-- Flag -->
                <span class="info-icon" title="O'zbekiston bayrog'i">
                    <img src="{{ asset('images/flag.png') }}" alt="UZ Flag">
                </span>
                <!-- Media/Sound -->
                <span class="info-icon" title="Media">
                    <i class="fas fa-music"></i>
                </span>
                <!-- Accessibility (eye with line) -->
                <span class="info-icon accessibility-icon" id="accessibilityToggle" title="Maxsus imkoniyatlar">
                    <i class="fas fa-eye"></i>
                </span>
            </div>

            <!-- Right Side: Social Media -->
            <div class="top-info-right">
                @if($socialFacebook && $socialFacebook->$langField)
                    <a href="{{ $socialFacebook->$langField }}" target="_blank" class="social-icon" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                @endif
                @if($socialInstagram && $socialInstagram->$langField)
                    <a href="{{ $socialInstagram->$langField }}" target="_blank" class="social-icon" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                @endif
                @if($socialYoutube && $socialYoutube->$langField)
                    <a href="{{ $socialYoutube->$langField }}" target="_blank" class="social-icon" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                @endif
                @if($socialLinkedin && $socialLinkedin->$langField)
                    <a href="{{ $socialLinkedin->$langField }}" target="_blank" class="social-icon" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Header (White Panel) -->
    <header class="main-header">
        <!-- First Row: Logos + Actions -->
        <div class="header-top-row">
            <div class="container">
                <div class="header-top-content">
                    <!-- Left: Logos -->
                    <div class="header-logos">
                        <a href="{{ route('home') }}" title="Bosh sahifa">
                            @if(!empty($siteLogo))
                                <img src="{{ $siteLogo }}" alt="Academy Logo">
                            @elseif($logoUrl && $logoUrl->$langField)
                                <img src="{{ \App\Support\CmsHeaderFooter::assetUrl($logoUrl->$langField) }}" alt="Academy Logo">
                            @else
                                <img src="{{ asset('images/logo.png') }}" alt="Academy Logo" onerror="this.style.display='none'">
                            @endif
                        </a>
                        @if(!empty($siteLogoSecondary))
                        <div class="logo-separator"></div>
                        <a href="#" title="Hamkor logosi">
                            <img src="{{ $siteLogoSecondary }}" alt="Hamkor logo" onerror="this.style.display='none'">
                        </a>
                        @endif
                    </div>

                    <!-- Right: Actions -->
                    <div class="header-actions">
                        <!-- Search Icon -->
                        <button class="header-search-btn" onclick="window.location.href='{{ route('search') }}'" title="Qidirish">
                            <i class="fas fa-search"></i>
                        </button>

                        <!-- Language Selector -->
                        <div class="lang-dropdown">
                            <button class="lang-selector-new">
                                @if($lang === 'uz')
                                    <img src="{{ asset('vendor/flags/uz.png') }}" alt="UZ">
                                    <span>Uz</span>
                                @elseif($lang === 'en')
                                    <img src="{{ asset('vendor/flags/gb.png') }}" alt="EN">
                                    <span>En</span>
                                @else
                                    <img src="{{ asset('vendor/flags/ru.png') }}" alt="RU">
                                    <span>Ru</span>
                                @endif
                                <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 4px;"></i>
                            </button>
                            <div class="lang-dropdown-menu">
                                <a href="?lang=uz" class="{{ $lang === 'uz' ? 'active' : '' }}">
                                    <img src="{{ asset('vendor/flags/uz.png') }}" alt="UZ"> O'zbekcha
                                </a>
                                <a href="?lang=en" class="{{ $lang === 'en' ? 'active' : '' }}">
                                    <img src="{{ asset('vendor/flags/gb.png') }}" alt="EN"> English
                                </a>
                                <a href="?lang=ru" class="{{ $lang === 'ru' ? 'active' : '' }}">
                                    <img src="{{ asset('vendor/flags/ru.png') }}" alt="RU"> Русский
                                </a>
                            </div>
                        </div>

                        <!-- Login Button -->
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-login">
                                <i class="fas fa-th-large"></i>
                                {{ $dashboardButton && $dashboardButton->$langField ? $dashboardButton->$langField : 'Dashboard' }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-login">
                                {{ $loginButton && $loginButton->$langField ? $loginButton->$langField : 'Kirish' }}
                            </a>
                        @endauth

                        <!-- Mobile Menu Toggle -->
                        <button class="mobile-menu-toggle d-lg-none" id="mobileMenuToggle" onclick="toggleMobileMenu()">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row: Navigation Menu -->
        <div class="header-nav-row">
            <div class="container">
                <nav class="main-nav">
                    @php
                        // CmsMenuItem submenyularni olish
                        $headerMenuCms = \App\Models\CmsMenu::where('location', 'header')->where('is_active', true)->first();
                        $titleField = 'title_' . $lang;
                        $cmsMenuItemsByParent = collect();
                        if ($headerMenuCms) {
                            $allCmsItems = $headerMenuCms->menuItems()->whereNotNull('parent_id')->where('is_active', true)->orderBy('order_position')->get();
                            $cmsMenuItemsByParent = $allCmsItems->groupBy('parent_id');
                        }

                        // Menyu konfiguratsiyasi
                        $navMenus = [
                            ['var' => $menuHome, 'key' => 'menu_home', 'default' => 'Bosh sahifa', 'route' => '/'],
                            ['var' => $menuAbout, 'key' => 'menu_about', 'default' => 'Biz haqimizda', 'route' => 'about*'],
                            ['var' => $menuPrograms, 'key' => 'menu_programs', 'default' => 'Dasturlar', 'route' => 'programs*'],
                            ['var' => $menuTeachers, 'key' => 'menu_teachers', 'default' => "O'qituvchilar", 'route' => 'teachers*'],
                            ['var' => $menuBlog, 'key' => 'menu_blog', 'default' => 'Yangiliklar', 'route' => 'blog*||news*'],
                            ['var' => $menuContact, 'key' => 'menu_contact', 'default' => 'Aloqa', 'route' => 'aloqa*||contact*'],
                        ];
                    @endphp

                    @foreach($navMenus as $navItem)
                        @if($navItem['var'] && $navItem['var']->is_active)
                            @php
                                $submenus = $getSubmenus($navItem['key']);
                                // CmsMenuItem submenyularni topish (parent nomi bo'yicha)
                                $cmsChildItems = collect();
                                if ($headerMenuCms) {
                                    $parentCmsItem = $headerMenuCms->menuItems()
                                        ->whereNull('parent_id')
                                        ->where('is_active', true)
                                        ->get()
                                        ->first(function($item) use ($navItem) {
                                            // Menyu kalitiga mos parent ni topish
                                            $keyMap = [
                                                'menu_about' => ['Akademiya haqida', 'Biz haqimizda', 'About'],
                                                'menu_programs' => ["O'quv dasturlari", "Yo'nalishlar", 'Programs'],
                                                'menu_teachers' => ["O'qituvchilar", 'Teachers'],
                                                'menu_blog' => ['Blog', 'Yangiliklar', 'News'],
                                                'menu_contact' => ['Aloqa', "Bog'lanish", 'Contact'],
                                            ];
                                            $searchNames = $keyMap[$navItem['key']] ?? [];
                                            return in_array($item->title_uz, $searchNames);
                                        });
                                    if ($parentCmsItem) {
                                        $cmsChildItems = $cmsMenuItemsByParent->get($parentCmsItem->id, collect());
                                    }
                                }
                                $hasDropdown = $submenus->count() > 0 || $cmsChildItems->count() > 0;
                                $routeParts = explode('||', $navItem['route']);
                                $isActive = false;
                                foreach ($routeParts as $rp) {
                                    if (request()->is(trim($rp))) { $isActive = true; break; }
                                }
                            @endphp

                            @if($hasDropdown)
                                <div class="nav-dropdown">
                                    <a href="{{ $formatMenuUrl($menuUrls[$navItem['key']] ?? null, $defaultUrls[$navItem['key']] ?? '#') }}"
                                       class="nav-link {{ $isActive ? 'active' : '' }}">
                                        {{ $navItem['var']->$langField ?? $navItem['default'] }}
                                        <i class="fas fa-chevron-down ms-1" style="font-size: 10px;"></i>
                                    </a>
                                    <div class="nav-dropdown-menu">
                                        @foreach($submenus as $key => $submenu)
                                            @php
                                                $subUrlKey = $key . '_url';
                                                $subUrl = $headerContents->get($subUrlKey);
                                            @endphp
                                            <a href="{{ $formatMenuUrl($subUrl, '#') }}">
                                                {{ $submenu->$langField ?? $submenu->value_uz }}
                                            </a>
                                        @endforeach
                                        @foreach($cmsChildItems as $cmsChild)
                                            @php
                                                $childUrl = $cmsChild->getRawOriginal('url');
                                                $isExt = str_starts_with($childUrl, 'http');
                                                $finalUrl = ($childUrl === '#' || $isExt) ? $childUrl : url($childUrl);
                                            @endphp
                                            <a href="{{ $finalUrl }}" @if($isExt) target="_blank" @endif>
                                                {{ $cmsChild->$titleField ?? $cmsChild->title_uz }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ $formatMenuUrl($menuUrls[$navItem['key']] ?? null, $defaultUrls[$navItem['key']] ?? '#') }}"
                                   class="nav-link {{ $isActive ? 'active' : '' }}">
                                    {{ $navItem['var']->$langField ?? $navItem['default'] }}
                                </a>
                            @endif
                        @endif
                    @endforeach

                    @foreach($customMenus as $key => $customMenu)
                        @php
                            $customUrlKey = $key . '_url';
                            $customUrl = $headerContents->get($customUrlKey);
                        @endphp
                        <a href="{{ $formatMenuUrl($customUrl, '#') }}" class="nav-link">
                            {{ $customMenu->$langField }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="mobile-nav" id="mobileNav">
            @foreach($navMenus as $navItem)
                @if($navItem['var'] && $navItem['var']->is_active)
                    @php
                        $mSubmenus = $getSubmenus($navItem['key']);
                        $mCmsChildItems = collect();
                        if ($headerMenuCms) {
                            $mParentCmsItem = $headerMenuCms->menuItems()
                                ->whereNull('parent_id')
                                ->where('is_active', true)
                                ->get()
                                ->first(function($item) use ($navItem) {
                                    $keyMap = [
                                        'menu_about' => ['Akademiya haqida', 'Biz haqimizda', 'About'],
                                        'menu_programs' => ["O'quv dasturlari", "Yo'nalishlar", 'Programs'],
                                        'menu_teachers' => ["O'qituvchilar", 'Teachers'],
                                        'menu_blog' => ['Blog', 'Yangiliklar', 'News'],
                                        'menu_contact' => ['Aloqa', "Bog'lanish", 'Contact'],
                                    ];
                                    $searchNames = $keyMap[$navItem['key']] ?? [];
                                    return in_array($item->title_uz, $searchNames);
                                });
                            if ($mParentCmsItem) {
                                $mCmsChildItems = $cmsMenuItemsByParent->get($mParentCmsItem->id, collect());
                            }
                        }
                        $mHasDropdown = $mSubmenus->count() > 0 || $mCmsChildItems->count() > 0;
                        $mRouteParts = explode('||', $navItem['route']);
                        $mIsActive = false;
                        foreach ($mRouteParts as $rp) {
                            if (request()->is(trim($rp))) { $mIsActive = true; break; }
                        }
                    @endphp
                    <a href="{{ $formatMenuUrl($menuUrls[$navItem['key']] ?? null, $defaultUrls[$navItem['key']] ?? '#') }}"
                       class="{{ $mIsActive ? 'active' : '' }}">
                        {{ $navItem['var']->$langField ?? $navItem['default'] }}
                    </a>
                    @if($mHasDropdown)
                        @foreach($mSubmenus as $key => $submenu)
                            @php
                                $mSubUrlKey = $key . '_url';
                                $mSubUrl = $headerContents->get($mSubUrlKey);
                            @endphp
                            <a href="{{ $formatMenuUrl($mSubUrl, '#') }}" style="padding-left: 2rem; font-size: 0.9rem;">
                                <i class="fas fa-angle-right me-1" style="font-size: 10px;"></i>{{ $submenu->$langField ?? $submenu->value_uz }}
                            </a>
                        @endforeach
                        @foreach($mCmsChildItems as $cmsChild)
                            @php
                                $mChildUrl = $cmsChild->getRawOriginal('url');
                                $mIsExt = str_starts_with($mChildUrl, 'http');
                                $mFinalUrl = ($mChildUrl === '#' || $mIsExt) ? $mChildUrl : url($mChildUrl);
                            @endphp
                            <a href="{{ $mFinalUrl }}" style="padding-left: 2rem; font-size: 0.9rem;" @if($mIsExt) target="_blank" rel="noopener" @endif>
                                <i class="fas fa-angle-right me-1" style="font-size: 10px;"></i>{{ $cmsChild->$titleField ?? $cmsChild->title_uz }}
                            </a>
                        @endforeach
                    @endif
                @endif
            @endforeach
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
    <footer class="footer-academic">
        <div class="footer-main">
            <div class="container">
                <div class="row g-4">
                    <!-- Column 1: About -->
                    <div class="col-lg-4 col-md-6">
                        @if($footerLogo && $footerLogo->$langField)
                            <img src="{{ \App\Support\CmsHeaderFooter::assetUrl($footerLogo->$langField) }}" alt="Logo" class="footer-logo">
                        @elseif($logoUrl && $logoUrl->$langField)
                            <img src="{{ \App\Support\CmsHeaderFooter::assetUrl($logoUrl->$langField) }}" alt="Logo" class="footer-logo">
                        @endif
                        <p class="footer-desc">
                            {{ $footerDesc && $footerDesc->$langField ? $footerDesc->$langField : 'Xalqaro standartlarga mos ta\'lim va professional rivojlanish platformasi.' }}
                        </p>
                        <div class="footer-social">
                            @if($socialFacebook && $socialFacebook->$langField)
                                <a href="{{ $socialFacebook->$langField }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if($socialInstagram && $socialInstagram->$langField)
                                <a href="{{ $socialInstagram->$langField }}" target="_blank"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if($socialTelegram && $socialTelegram->$langField)
                                <a href="{{ $socialTelegram->$langField }}" target="_blank"><i class="fab fa-telegram-plane"></i></a>
                            @endif
                            @if($socialYoutube && $socialYoutube->$langField)
                                <a href="{{ $socialYoutube->$langField }}" target="_blank"><i class="fab fa-youtube"></i></a>
                            @endif
                            @if($socialLinkedin && $socialLinkedin->$langField)
                                <a href="{{ $socialLinkedin->$langField }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                            @endif
                        </div>
                    </div>

                    <!-- Column 2: Quick Links -->
                    <div class="col-lg-2 col-md-6">
                        <h4 class="footer-title">{{ $col2Title && $col2Title->$langField ? $col2Title->$langField : 'Tezkor havolalar' }}</h4>
                        <ul class="footer-links">
                            @if($col2Links->count() > 0)
                                @foreach($col2Links as $key => $link)
                                    @php
                                        $linkUrlKey = str_replace('_text', '_url', $key);
                                        $linkUrl = $footerContents->get($linkUrlKey);
                                    @endphp
                                    <li><a href="{{ $linkUrl && $linkUrl->$langField ? $linkUrl->$langField : '#' }}">{{ $link->$langField }}</a></li>
                                @endforeach
                            @else
                                <li><a href="{{ route('home') }}">Bosh sahifa</a></li>
                                <li><a href="/about">Biz haqimizda</a></li>
                                <li><a href="/programs">Dasturlar</a></li>
                                <li><a href="/teachers">O'qituvchilar</a></li>
                            @endif
                        </ul>
                    </div>

                    <!-- Column 3: Resources -->
                    <div class="col-lg-2 col-md-6">
                        <h4 class="footer-title">{{ $col3Title && $col3Title->$langField ? $col3Title->$langField : 'Resurslar' }}</h4>
                        <ul class="footer-links">
                            @if($col3Links->count() > 0)
                                @foreach($col3Links as $key => $link)
                                    @php
                                        $linkUrlKey = str_replace('_text', '_url', $key);
                                        $linkUrl = $footerContents->get($linkUrlKey);
                                    @endphp
                                    <li><a href="{{ $linkUrl && $linkUrl->$langField ? $linkUrl->$langField : '#' }}">{{ $link->$langField }}</a></li>
                                @endforeach
                            @else
                                <li><a href="/blog">Yangiliklar</a></li>
                                <li><a href="/events">Tadbirlar</a></li>
                                <li><a href="/library">Kutubxona</a></li>
                                <li><a href="/faq">FAQ</a></li>
                            @endif
                        </ul>
                    </div>

                    <!-- Column 4: Contact -->
                    <div class="col-lg-4 col-md-6">
                        <h4 class="footer-title">{{ $col4Title && $col4Title->$langField ? $col4Title->$langField : 'Bog\'lanish' }}</h4>
                        <div class="footer-contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $contactAddress && $contactAddress->$langField ? $contactAddress->$langField : 'Toshkent shahri, Universitet ko\'chasi 1' }}</span>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-phone-alt"></i>
                            <span>{{ $contactPhone && $contactPhone->$langField ? $contactPhone->$langField : '+998 71 234 56 78' }}</span>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>{{ $contactEmail && $contactEmail->$langField ? $contactEmail->$langField : 'info@academy.uz' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <p class="footer-copyright mb-0">
                        {{ $copyrightText && $copyrightText->$langField ? $copyrightText->$langField : '© ' . date('Y') . ' Tourism Academy. Barcha huquqlar himoyalangan.' }}
                    </p>
                    <div class="d-flex gap-4">
                        <a href="/privacy" class="text-muted text-decoration-none small">Maxfiylik siyosati</a>
                        <a href="/terms" class="text-muted text-decoration-none small">Foydalanish shartlari</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AOS -->
    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true
        });

        // Mobile menu toggle
        function toggleMobileMenu() {
            var mobileNav = document.getElementById('mobileNav');
            var toggleBtn = document.getElementById('mobileMenuToggle');
            if (mobileNav) {
                mobileNav.classList.toggle('active');
                if (toggleBtn) {
                    var icon = toggleBtn.querySelector('i');
                    if (mobileNav.classList.contains('active')) {
                        icon.className = 'fas fa-times';
                    } else {
                        icon.className = 'fas fa-bars';
                    }
                }
            }
        }

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

    @stack('scripts')
</body>
</html>
