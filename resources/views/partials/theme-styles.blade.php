@php
    use App\Models\CmsSetting;
    $currentTheme = CmsSetting::getCurrentTheme();
@endphp

<style>
/* ========================================
   THEME 1: Classic Blue (Default)
   Professional indigo blue theme
   ======================================== */
@if($currentTheme == 'theme1')
:root {
    /* Primary Colors */
    --theme-primary: #4338CA;
    --theme-primary-dark: #3730A3;
    --theme-primary-light: #6366F1;
    --theme-secondary: #6366F1;
    --theme-accent: #CCFF00;
    --theme-accent-hover: #B8E600;

    /* Gradients */
    --theme-gradient: linear-gradient(135deg, #4338CA 0%, #6366F1 100%);
    --theme-gradient-dark: linear-gradient(135deg, #3730A3 0%, #4338CA 100%);
    --theme-hero-gradient: linear-gradient(135deg, rgba(67, 56, 202, 0.95) 0%, rgba(99, 102, 241, 0.9) 100%);

    /* Header */
    --header-bg: rgba(255, 255, 255, 0.95);
    --header-bg-scrolled: rgba(255, 255, 255, 0.98);
    --header-text: #1a1a2e;
    --header-link-hover: #4338CA;

    /* Footer */
    --footer-bg: linear-gradient(180deg, #0D0D0D 0%, #1a1a2e 100%);
    --footer-text: #ffffff;
    --footer-accent: #CCFF00;

    /* Buttons */
    --btn-primary-bg: var(--theme-gradient);
    --btn-primary-text: #ffffff;
    --btn-secondary-bg: transparent;
    --btn-secondary-border: #4338CA;
    --btn-secondary-text: #4338CA;

    /* Cards */
    --card-bg: #ffffff;
    --card-border: rgba(67, 56, 202, 0.1);
    --card-shadow: 0 4px 20px rgba(67, 56, 202, 0.1);
    --card-hover-shadow: 0 8px 30px rgba(67, 56, 202, 0.15);
}
@endif

/* ========================================
   THEME 2: Ocean Green
   Fresh emerald green theme
   ======================================== */
@if($currentTheme == 'theme2')
:root {
    /* Primary Colors */
    --theme-primary: #059669;
    --theme-primary-dark: #047857;
    --theme-primary-light: #10B981;
    --theme-secondary: #10B981;
    --theme-accent: #34D399;
    --theme-accent-hover: #6EE7B7;

    /* Gradients */
    --theme-gradient: linear-gradient(135deg, #059669 0%, #10B981 100%);
    --theme-gradient-dark: linear-gradient(135deg, #047857 0%, #059669 100%);
    --theme-hero-gradient: linear-gradient(135deg, rgba(5, 150, 105, 0.95) 0%, rgba(16, 185, 129, 0.9) 100%);

    /* Header */
    --header-bg: rgba(255, 255, 255, 0.95);
    --header-bg-scrolled: rgba(255, 255, 255, 0.98);
    --header-text: #064E3B;
    --header-link-hover: #059669;

    /* Footer */
    --footer-bg: linear-gradient(180deg, #064E3B 0%, #022C22 100%);
    --footer-text: #ffffff;
    --footer-accent: #34D399;

    /* Buttons */
    --btn-primary-bg: var(--theme-gradient);
    --btn-primary-text: #ffffff;
    --btn-secondary-bg: transparent;
    --btn-secondary-border: #059669;
    --btn-secondary-text: #059669;

    /* Cards */
    --card-bg: #ffffff;
    --card-border: rgba(5, 150, 105, 0.1);
    --card-shadow: 0 4px 20px rgba(5, 150, 105, 0.1);
    --card-hover-shadow: 0 8px 30px rgba(5, 150, 105, 0.15);
}

/* Green Theme Specific Overrides */
.neon-lime, .hero-badge {
    background: linear-gradient(135deg, #34D399 0%, #6EE7B7 100%) !important;
    color: #064E3B !important;
}
.site-header.scrolled {
    border-bottom-color: rgba(5, 150, 105, 0.2) !important;
}
.nav-link:hover, .nav-link.active {
    color: #059669 !important;
}
.btn-login {
    background: var(--theme-gradient) !important;
}
@endif

/* ========================================
   THEME 3: Sunset Orange
   Warm sunset orange theme
   ======================================== */
@if($currentTheme == 'theme3')
:root {
    /* Primary Colors */
    --theme-primary: #EA580C;
    --theme-primary-dark: #C2410C;
    --theme-primary-light: #F97316;
    --theme-secondary: #F97316;
    --theme-accent: #FBBF24;
    --theme-accent-hover: #FCD34D;

    /* Gradients */
    --theme-gradient: linear-gradient(135deg, #EA580C 0%, #F97316 100%);
    --theme-gradient-dark: linear-gradient(135deg, #C2410C 0%, #EA580C 100%);
    --theme-hero-gradient: linear-gradient(135deg, rgba(234, 88, 12, 0.95) 0%, rgba(249, 115, 22, 0.9) 100%);

    /* Header */
    --header-bg: rgba(255, 255, 255, 0.95);
    --header-bg-scrolled: rgba(255, 255, 255, 0.98);
    --header-text: #7C2D12;
    --header-link-hover: #EA580C;

    /* Footer */
    --footer-bg: linear-gradient(180deg, #7C2D12 0%, #431407 100%);
    --footer-text: #ffffff;
    --footer-accent: #FBBF24;

    /* Buttons */
    --btn-primary-bg: var(--theme-gradient);
    --btn-primary-text: #ffffff;
    --btn-secondary-bg: transparent;
    --btn-secondary-border: #EA580C;
    --btn-secondary-text: #EA580C;

    /* Cards */
    --card-bg: #ffffff;
    --card-border: rgba(234, 88, 12, 0.1);
    --card-shadow: 0 4px 20px rgba(234, 88, 12, 0.1);
    --card-hover-shadow: 0 8px 30px rgba(234, 88, 12, 0.15);
}

/* Orange Theme Specific Overrides */
.neon-lime, .hero-badge {
    background: linear-gradient(135deg, #FBBF24 0%, #FCD34D 100%) !important;
    color: #7C2D12 !important;
}
.site-header.scrolled {
    border-bottom-color: rgba(234, 88, 12, 0.2) !important;
}
.nav-link:hover, .nav-link.active {
    color: #EA580C !important;
}
.btn-login {
    background: var(--theme-gradient) !important;
}
.footer-section {
    background: var(--footer-bg) !important;
}
@endif

/* ========================================
   THEME 4: Royal Purple
   Elegant purple theme
   ======================================== */
@if($currentTheme == 'theme4')
:root {
    /* Primary Colors */
    --theme-primary: #7C3AED;
    --theme-primary-dark: #6D28D9;
    --theme-primary-light: #8B5CF6;
    --theme-secondary: #8B5CF6;
    --theme-accent: #A78BFA;
    --theme-accent-hover: #C4B5FD;

    /* Gradients */
    --theme-gradient: linear-gradient(135deg, #7C3AED 0%, #8B5CF6 100%);
    --theme-gradient-dark: linear-gradient(135deg, #6D28D9 0%, #7C3AED 100%);
    --theme-hero-gradient: linear-gradient(135deg, rgba(124, 58, 237, 0.95) 0%, rgba(139, 92, 246, 0.9) 100%);

    /* Header */
    --header-bg: rgba(255, 255, 255, 0.95);
    --header-bg-scrolled: rgba(255, 255, 255, 0.98);
    --header-text: #4C1D95;
    --header-link-hover: #7C3AED;

    /* Footer */
    --footer-bg: linear-gradient(180deg, #4C1D95 0%, #2E1065 100%);
    --footer-text: #ffffff;
    --footer-accent: #A78BFA;

    /* Buttons */
    --btn-primary-bg: var(--theme-gradient);
    --btn-primary-text: #ffffff;
    --btn-secondary-bg: transparent;
    --btn-secondary-border: #7C3AED;
    --btn-secondary-text: #7C3AED;

    /* Cards */
    --card-bg: #ffffff;
    --card-border: rgba(124, 58, 237, 0.1);
    --card-shadow: 0 4px 20px rgba(124, 58, 237, 0.1);
    --card-hover-shadow: 0 8px 30px rgba(124, 58, 237, 0.15);
}

/* Purple Theme Specific Overrides */
.neon-lime, .hero-badge {
    background: linear-gradient(135deg, #A78BFA 0%, #C4B5FD 100%) !important;
    color: #4C1D95 !important;
}
.site-header.scrolled {
    border-bottom-color: rgba(124, 58, 237, 0.2) !important;
}
.nav-link:hover, .nav-link.active {
    color: #7C3AED !important;
}
.btn-login {
    background: var(--theme-gradient) !important;
}
.footer-section {
    background: var(--footer-bg) !important;
}
@endif

/* ========================================
   THEME 5: Midnight Dark
   Modern dark mode theme
   ======================================== */
@if($currentTheme == 'theme5')
:root {
    /* Primary Colors */
    --theme-primary: #1E293B;
    --theme-primary-dark: #0F172A;
    --theme-primary-light: #334155;
    --theme-secondary: #334155;
    --theme-accent: #38BDF8;
    --theme-accent-hover: #7DD3FC;

    /* Gradients */
    --theme-gradient: linear-gradient(135deg, #1E293B 0%, #334155 100%);
    --theme-gradient-dark: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
    --theme-hero-gradient: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(51, 65, 85, 0.95) 100%);

    /* Header - Dark Mode */
    --header-bg: rgba(15, 23, 42, 0.95);
    --header-bg-scrolled: rgba(15, 23, 42, 0.98);
    --header-text: #F1F5F9;
    --header-link-hover: #38BDF8;

    /* Footer */
    --footer-bg: linear-gradient(180deg, #0F172A 0%, #020617 100%);
    --footer-text: #F1F5F9;
    --footer-accent: #38BDF8;

    /* Buttons */
    --btn-primary-bg: linear-gradient(135deg, #38BDF8 0%, #7DD3FC 100%);
    --btn-primary-text: #0F172A;
    --btn-secondary-bg: transparent;
    --btn-secondary-border: #38BDF8;
    --btn-secondary-text: #38BDF8;

    /* Cards */
    --card-bg: #1E293B;
    --card-border: rgba(56, 189, 248, 0.1);
    --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    --card-hover-shadow: 0 8px 30px rgba(56, 189, 248, 0.15);
}

/* Dark Theme Complete Override */
body {
    background-color: #0F172A !important;
    color: #F1F5F9 !important;
}

.site-header {
    background: var(--header-bg) !important;
    border-bottom-color: rgba(56, 189, 248, 0.1) !important;
}

.site-header.scrolled {
    background: var(--header-bg-scrolled) !important;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3) !important;
}

.navbar-menu .nav-link {
    color: #CBD5E1 !important;
}

.navbar-menu .nav-link:hover,
.navbar-menu .nav-link.active {
    color: #38BDF8 !important;
}

.neon-lime, .hero-badge {
    background: linear-gradient(135deg, #38BDF8 0%, #7DD3FC 100%) !important;
    color: #0F172A !important;
}

.btn-login {
    background: linear-gradient(135deg, #38BDF8 0%, #7DD3FC 100%) !important;
    color: #0F172A !important;
}

/* Hero Section Dark */
.hero-section::before {
    background: var(--theme-hero-gradient) !important;
}

/* Footer Dark */
.footer-section {
    background: var(--footer-bg) !important;
}

.footer-title, .footer-column h5 {
    color: #38BDF8 !important;
}

/* Cards Dark */
.card, .quick-link-card, .news-card {
    background: #1E293B !important;
    border-color: rgba(56, 189, 248, 0.1) !important;
    color: #F1F5F9 !important;
}

.card:hover, .quick-link-card:hover, .news-card:hover {
    border-color: rgba(56, 189, 248, 0.3) !important;
    box-shadow: var(--card-hover-shadow) !important;
}

/* Text colors for dark mode */
h1, h2, h3, h4, h5, h6 {
    color: #F1F5F9 !important;
}

p, .text-muted {
    color: #94A3B8 !important;
}

/* Mobile menu dark */
.mobile-menu-overlay {
    background: rgba(15, 23, 42, 0.98) !important;
}

.mobile-nav-list a {
    color: #CBD5E1 !important;
}

.mobile-nav-list a:hover {
    color: #38BDF8 !important;
}

/* Language selector dark */
.lang-btn {
    color: #F1F5F9 !important;
}

.lang-dropdown {
    background: #1E293B !important;
    border-color: rgba(56, 189, 248, 0.2) !important;
}

.lang-option {
    color: #CBD5E1 !important;
}

.lang-option:hover, .lang-option.active {
    background: rgba(56, 189, 248, 0.1) !important;
    color: #38BDF8 !important;
}
@endif

/* ========================================
   COMMON THEME STYLES
   Applied to all themes
   ======================================== */

/* Apply theme gradient to primary buttons */
.btn-primary, .btn-hero-primary {
    background: var(--btn-primary-bg) !important;
    color: var(--btn-primary-text) !important;
    border: none !important;
}

.btn-primary:hover, .btn-hero-primary:hover {
    filter: brightness(1.1);
    transform: translateY(-2px);
}

/* Apply theme colors to links */
a.theme-link {
    color: var(--theme-primary);
}

a.theme-link:hover {
    color: var(--theme-primary-dark);
}

/* Theme accent badges */
.theme-badge {
    background: var(--theme-accent);
    color: var(--theme-primary-dark);
}

/* Card hover effects */
.theme-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
}

.theme-card:hover {
    box-shadow: var(--card-hover-shadow);
    transform: translateY(-5px);
}
</style>
