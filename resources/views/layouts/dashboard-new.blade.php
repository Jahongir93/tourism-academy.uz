@php
// Detect which module is active and pick its accent color from the design system
$_r = request();
if ($_r->routeIs('students.*','student-contingent.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#0EA5E9','#0284C7','#BAE6FD','#F0F9FF','#BAE6FD'];
} elseif ($_r->routeIs('employees.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#7C3AED','#6D28D9','#C4B5FD','#F5F3FF','#DDD6FE'];
} elseif ($_r->routeIs('admin.vacancies.*','admin.vacancy-applications.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#F97316','#EA580C','#FDBA74','#FFF7ED','#FED7AA'];
} elseif ($_r->routeIs('structure.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#10B981','#059669','#6EE7B7','#ECFDF5','#A7F3D0'];
} elseif ($_r->routeIs('journal.*','schedule.*','attendance.*','grades.*','assignments.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#06B6D4','#0891B2','#67E8F9','#ECFEFF','#A5F3FC'];
} elseif ($_r->routeIs('lms.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#14B8A6','#0D9488','#5EEAD4','#F0FDFA','#99F6E4'];
} elseif ($_r->routeIs('hemis.*','gpa.*','vedomost.*','academic.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#6366F1','#4F46E5','#A5B4FC','#EEF2FF','#C7D2FE'];
} elseif ($_r->routeIs('cms.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#F97316','#EA580C','#FDBA74','#FFF7ED','#FED7AA'];
} elseif ($_r->routeIs('campus-tour.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#06B6D4','#0891B2','#67E8F9','#ECFEFF','#A5F3FC'];
} elseif ($_r->routeIs('finance.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#10B981','#059669','#6EE7B7','#ECFDF5','#A7F3D0'];
} elseif ($_r->routeIs('admission.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#F59E0B','#D97706','#FDE68A','#FFFBEB','#FDE68A'];
} elseif ($_r->routeIs('reports.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#F59E0B','#D97706','#FDE68A','#FFFBEB','#FDE68A'];
} elseif ($_r->routeIs('statistics.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#F43F5E','#E11D48','#FCA5A5','#FFF1F2','#FECDD3'];
} elseif ($_r->routeIs('settings.*','admin.settings.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#64748B','#475569','#CBD5E1','#F8FAFC','#E2E8F0'];
} elseif ($_r->routeIs('notifications.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#EC4899','#DB2777','#F9A8D4','#FDF2F8','#FBCFE8'];
} elseif ($_r->routeIs('chat-admin.*')) {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#EC4899','#DB2777','#F9A8D4','#FDF2F8','#FBCFE8'];
} else {
    [$_mc,$_mcd,$_mcl,$_mbg,$_mbr] = ['#4F46E5','#3730A3','#818CF8','#EEF2FF','#C7D2FE'];
}
@endphp
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>@yield('title', 'HEMIS — Tourism Academy Samarkand')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
    /* =====================================================
       HEMIS DESIGN SYSTEM — Global CSS
       ===================================================== */

    :root {
        /* Primary palette */
        --c-primary:        #4F46E5;
        --c-primary-dark:   #3730A3;
        --c-primary-light:  #818CF8;
        --c-primary-50:     #EEF2FF;
        --c-primary-glow:   rgba(79,70,229,.25);

        /* Accent / module colors */
        --c-violet:   #7C3AED;
        --c-sky:      #0EA5E9;
        --c-emerald:  #10B981;
        --c-amber:    #F59E0B;
        --c-rose:     #F43F5E;
        --c-orange:   #F97316;
        --c-teal:     #14B8A6;
        --c-pink:     #EC4899;
        --c-cyan:     #06B6D4;
        --c-lime:     #84CC16;

        /* Semantic */
        --c-success:  #10B981;
        --c-warning:  #F59E0B;
        --c-danger:   #EF4444;
        --c-info:     #0EA5E9;

        /* Neutrals */
        --c-text:       #0F172A;
        --c-text-2:     #475569;
        --c-text-3:     #94A3B8;
        --c-border:     #E2E8F0;
        --c-bg:         #F1F5F9;
        --c-bg-card:    #FFFFFF;
        --c-sidebar:    #0F172A;

        /* Radii */
        --r-sm:  8px;
        --r-md:  12px;
        --r-lg:  16px;
        --r-xl:  20px;
        --r-2xl: 24px;

        /* Shadows */
        --sh-xs: 0 1px 2px rgba(0,0,0,.05);
        --sh-sm: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
        --sh-md: 0 4px 6px -1px rgba(0,0,0,.08), 0 2px 4px -2px rgba(0,0,0,.04);
        --sh-lg: 0 10px 15px -3px rgba(0,0,0,.08), 0 4px 6px -4px rgba(0,0,0,.04);
        --sh-xl: 0 20px 25px -5px rgba(0,0,0,.10), 0 8px 10px -6px rgba(0,0,0,.04);

        /* Transitions */
        --tr-fast:   all .15s ease;
        --tr-normal: all .25s ease;
        --tr-slow:   all .4s cubic-bezier(.4,0,.2,1);
    }

    * { box-sizing: border-box; }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--c-bg);
        color: var(--c-text);
        font-size: 14px;
        line-height: 1.6;
    }

    /* ─── Scrollbar ─── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 999px; }
    ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

    /* ─── Typography ─── */
    h1,h2,h3,h4,h5,h6 { font-weight: 700; color: var(--c-text); }

    /* =====================================================
       LAYOUT SHELL
       ===================================================== */
    .layout-shell  { display: flex; height: 100vh; overflow: hidden; }
    .layout-sidebar { width: 272px; flex-shrink: 0; height: 100vh; overflow-y: auto; background: var(--c-sidebar); }
    .layout-main   { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
    .layout-topbar { height: 64px; flex-shrink: 0; background: #fff; border-bottom: 1px solid var(--c-border); box-shadow: var(--sh-sm); }
    .layout-content { flex: 1; overflow-y: auto; overflow-x: hidden; background: var(--c-bg); }
    .layout-inner  { padding: 28px 28px 40px; max-width: 1600px; }

    /* =====================================================
       TOPBAR
       ===================================================== */
    .topbar-inner {
        display: flex; align-items: center; justify-content: space-between;
        height: 64px; padding: 0 24px;
    }
    .topbar-left { display: flex; align-items: center; gap: 16px; }
    .topbar-right { display: flex; align-items: center; gap: 8px; }

    .topbar-toggle {
        width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
        border-radius: var(--r-sm); border: none; background: transparent; color: var(--c-text-2);
        cursor: pointer; transition: var(--tr-fast);
    }
    .topbar-toggle:hover { background: var(--c-bg); color: var(--c-primary); }

    .topbar-title {
        font-size: 18px; font-weight: 700; color: var(--c-text);
        background: linear-gradient(135deg, var(--c-primary), var(--c-violet));
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .topbar-icon-btn {
        position: relative; width: 38px; height: 38px; display: flex; align-items: center;
        justify-content: center; border-radius: var(--r-sm); border: none; background: transparent;
        color: var(--c-text-2); cursor: pointer; transition: var(--tr-fast);
    }
    .topbar-icon-btn:hover { background: var(--c-bg); color: var(--c-primary); }

    .topbar-badge {
        position: absolute; top: 4px; right: 4px; min-width: 16px; height: 16px;
        background: var(--c-rose); color: #fff; font-size: 10px; font-weight: 700;
        border-radius: 999px; display: flex; align-items: center; justify-content: center;
        padding: 0 4px; border: 2px solid #fff;
    }

    .topbar-user {
        display: flex; align-items: center; gap: 10px; padding: 6px 12px;
        border-radius: var(--r-md); border: none; background: transparent;
        cursor: pointer; transition: var(--tr-fast);
    }
    .topbar-user:hover { background: var(--c-bg); }

    .topbar-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        background: linear-gradient(135deg, var(--c-primary), var(--c-violet));
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 13px; font-weight: 700; flex-shrink: 0;
    }
    .topbar-user-name { font-size: 13px; font-weight: 600; color: var(--c-text); }
    .topbar-user-role { font-size: 11px; color: var(--c-text-3); }

    /* Notification dropdown */
    .notif-dropdown {
        position: absolute; right: 0; top: calc(100% + 8px); width: 340px;
        background: #fff; border-radius: var(--r-lg); box-shadow: var(--sh-xl);
        border: 1px solid var(--c-border); overflow: hidden; z-index: 100;
    }
    .notif-header {
        padding: 14px 16px; border-bottom: 1px solid var(--c-border);
        display: flex; align-items: center; justify-content: space-between;
        background: linear-gradient(135deg, var(--c-primary), var(--c-violet));
        color: #fff;
    }
    .notif-header h3 { font-size: 14px; font-weight: 700; color: #fff; margin: 0; }
    .notif-mark-all { font-size: 11px; color: rgba(255,255,255,.8); background: none;
        border: none; cursor: pointer; padding: 0; }
    .notif-mark-all:hover { color: #fff; }
    .notif-body { max-height: 340px; overflow-y: auto; }
    .notif-item {
        padding: 12px 16px; border-bottom: 1px solid #F8FAFC;
        cursor: pointer; transition: var(--tr-fast);
    }
    .notif-item:hover { background: var(--c-bg); }
    .notif-item.unread { background: #EEF2FF; }
    .notif-item-title { font-size: 13px; font-weight: 600; color: var(--c-text); }
    .notif-item-msg { font-size: 12px; color: var(--c-text-2); margin-top: 2px; }
    .notif-item-time { font-size: 11px; color: var(--c-text-3); margin-top: 3px; }
    .notif-dot { width: 7px; height: 7px; background: var(--c-primary); border-radius: 50%; flex-shrink: 0; }
    .notif-footer { padding: 10px 16px; border-top: 1px solid var(--c-border); background: var(--c-bg); }
    .notif-footer a { font-size: 12px; color: var(--c-primary); font-weight: 600; text-decoration: none; }

    /* User dropdown */
    .user-dropdown {
        position: absolute; right: 0; top: calc(100% + 8px); width: 200px;
        background: #fff; border-radius: var(--r-lg); box-shadow: var(--sh-xl);
        border: 1px solid var(--c-border); overflow: hidden; z-index: 100;
    }
    .user-dropdown-header { padding: 14px 16px; background: linear-gradient(135deg,var(--c-primary),var(--c-violet)); }
    .user-dropdown-name { font-size: 13px; font-weight: 700; color: #fff; }
    .user-dropdown-email { font-size: 11px; color: rgba(255,255,255,.75); }
    .user-dropdown-item {
        display: flex; align-items: center; gap: 10px; padding: 10px 16px;
        font-size: 13px; color: var(--c-text-2); text-decoration: none;
        border: none; background: none; width: 100%; cursor: pointer;
        transition: var(--tr-fast);
    }
    .user-dropdown-item:hover { background: var(--c-bg); color: var(--c-primary); }
    .user-dropdown-item.danger { color: var(--c-danger); }
    .user-dropdown-item.danger:hover { background: #FFF1F2; }
    .user-dropdown-divider { height: 1px; background: var(--c-border); margin: 4px 0; }

    /* =====================================================
       FLASH MESSAGES (toast-style)
       ===================================================== */
    .flash-wrap { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }

    .flash {
        display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px;
        border-radius: var(--r-md); font-size: 14px; font-weight: 500;
        box-shadow: var(--sh-sm); position: relative; overflow: hidden;
    }
    .flash::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; }
    .flash-icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
    .flash-text { flex: 1; }
    .flash-close {
        background: none; border: none; cursor: pointer; padding: 0; margin-left: 8px;
        opacity: .5; font-size: 16px; line-height: 1; transition: var(--tr-fast); flex-shrink: 0;
    }
    .flash-close:hover { opacity: 1; }

    .flash-success { background: #F0FDF9; color: #065F46; }
    .flash-success::before { background: var(--c-success); }
    .flash-success .flash-icon { color: var(--c-success); }

    .flash-error { background: #FFF1F2; color: #9F1239; }
    .flash-error::before { background: var(--c-danger); }
    .flash-error .flash-icon { color: var(--c-danger); }

    .flash-warning { background: #FFFBEB; color: #78350F; }
    .flash-warning::before { background: var(--c-warning); }
    .flash-warning .flash-icon { color: var(--c-warning); }

    .flash-info { background: #EFF6FF; color: #1E40AF; }
    .flash-info::before { background: var(--c-info); }
    .flash-info .flash-icon { color: var(--c-info); }

    /* =====================================================
       PAGE HEADER
       ===================================================== */
    .page-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 24px; gap: 16px; flex-wrap: wrap;
    }
    .page-header-left {}
    .page-header-title {
        font-size: 22px; font-weight: 800; color: var(--c-text); margin: 0;
        letter-spacing: -.3px;
    }
    .page-header-sub { font-size: 13px; color: var(--c-text-3); margin-top: 3px; }
    .page-header-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    /* =====================================================
       CARDS
       ===================================================== */
    .card {
        background: var(--c-bg-card); border: 1px solid var(--c-border);
        border-radius: var(--r-lg) !important; box-shadow: var(--sh-sm) !important;
        transition: var(--tr-normal); overflow: hidden;
    }
    .card:hover { box-shadow: var(--sh-md) !important; }

    .card-header {
        background: #fff !important; border-bottom: 1px solid var(--c-border) !important;
        padding: 16px 20px !important; font-weight: 700 !important;
        color: var(--c-text) !important;
    }
    .card-header.grad-primary { background: linear-gradient(135deg,var(--c-primary),var(--c-violet)) !important; color:#fff !important; border:none !important; }
    .card-header.grad-success { background: linear-gradient(135deg,#059669,#10B981) !important; color:#fff !important; border:none !important; }
    .card-header.grad-info    { background: linear-gradient(135deg,#0284C7,#0EA5E9) !important; color:#fff !important; border:none !important; }
    .card-header.grad-warning { background: linear-gradient(135deg,#D97706,#F59E0B) !important; color:#fff !important; border:none !important; }
    .card-header.grad-danger  { background: linear-gradient(135deg,#DC2626,#EF4444) !important; color:#fff !important; border:none !important; }
    .card-header.grad-dark    { background: linear-gradient(135deg,#1E293B,#334155) !important; color:#fff !important; border:none !important; }

    .card-body { padding: 20px !important; }
    .card-footer { background: #F8FAFC !important; border-top: 1px solid var(--c-border) !important; padding: 14px 20px !important; }

    /* Stat card */
    .stat-card {
        background: #fff; border-radius: var(--r-xl); padding: 22px 24px;
        border: 1px solid var(--c-border); box-shadow: var(--sh-sm);
        transition: var(--tr-slow); cursor: default; position: relative; overflow: hidden;
    }
    .stat-card::after {
        content:''; position:absolute; right:-20px; top:-20px; width:100px; height:100px;
        border-radius: 50%; background: currentColor; opacity: .04;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: var(--sh-lg); }
    .stat-card-icon {
        width: 48px; height: 48px; border-radius: var(--r-md);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; margin-bottom: 14px;
    }
    .stat-card-value { font-size: 28px; font-weight: 800; letter-spacing: -.5px; }
    .stat-card-label { font-size: 12px; font-weight: 500; color: var(--c-text-3); text-transform: uppercase; letter-spacing: .5px; margin-top: 2px; }
    .stat-card-delta { font-size: 12px; font-weight: 600; margin-top: 8px; }
    .stat-card-delta.up   { color: var(--c-success); }
    .stat-card-delta.down { color: var(--c-danger); }

    /* =====================================================
       BUTTONS — override Bootstrap
       ===================================================== */
    .btn {
        font-family: 'Inter', sans-serif !important; font-weight: 600 !important;
        border-radius: var(--r-sm) !important; padding: 8px 16px !important;
        font-size: 13px !important; line-height: 1.5 !important;
        transition: var(--tr-normal) !important; border: none !important;
        display: inline-flex !important; align-items: center !important;
        gap: 6px !important; cursor: pointer !important; text-decoration: none !important;
    }
    .btn:focus { box-shadow: none !important; }
    .btn:active { transform: scale(.97) !important; }

    .btn-primary {
        background: linear-gradient(135deg,var(--c-primary),var(--c-violet)) !important;
        color: #fff !important; box-shadow: 0 4px 12px var(--c-primary-glow) !important;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg,var(--c-primary-dark),#6D28D9) !important;
        color: #fff !important; box-shadow: 0 6px 16px var(--c-primary-glow) !important;
        transform: translateY(-1px) !important;
    }

    .btn-success {
        background: linear-gradient(135deg,#059669,var(--c-emerald)) !important;
        color: #fff !important; box-shadow: 0 4px 12px rgba(16,185,129,.25) !important;
    }
    .btn-success:hover { background: linear-gradient(135deg,#047857,#059669) !important; color:#fff !important; transform: translateY(-1px) !important; }

    .btn-danger {
        background: linear-gradient(135deg,#DC2626,var(--c-danger)) !important;
        color: #fff !important; box-shadow: 0 4px 12px rgba(239,68,68,.25) !important;
    }
    .btn-danger:hover { background: linear-gradient(135deg,#B91C1C,#DC2626) !important; color:#fff !important; transform: translateY(-1px) !important; }

    .btn-warning {
        background: linear-gradient(135deg,#D97706,var(--c-amber)) !important;
        color: #fff !important; box-shadow: 0 4px 12px rgba(245,158,11,.25) !important;
    }
    .btn-warning:hover { background: linear-gradient(135deg,#B45309,#D97706) !important; color:#fff !important; transform: translateY(-1px) !important; }

    .btn-info {
        background: linear-gradient(135deg,#0284C7,var(--c-sky)) !important;
        color: #fff !important;
    }
    .btn-info:hover { background: linear-gradient(135deg,#0369A1,#0284C7) !important; color:#fff !important; transform: translateY(-1px) !important; }

    .btn-secondary {
        background: #F1F5F9 !important; color: var(--c-text-2) !important;
        border: 1px solid var(--c-border) !important;
    }
    .btn-secondary:hover { background: #E2E8F0 !important; color: var(--c-text) !important; }

    .btn-outline-primary {
        background: transparent !important; color: var(--c-primary) !important;
        border: 1.5px solid var(--c-primary) !important;
    }
    .btn-outline-primary:hover { background: var(--c-primary) !important; color:#fff !important; }

    .btn-outline-secondary {
        background: transparent !important; color: var(--c-text-2) !important;
        border: 1.5px solid var(--c-border) !important;
    }
    .btn-outline-secondary:hover { background: var(--c-bg) !important; color: var(--c-text) !important; }

    .btn-outline-danger { background: transparent !important; color: var(--c-danger) !important; border: 1.5px solid var(--c-danger) !important; }
    .btn-outline-danger:hover { background: var(--c-danger) !important; color:#fff !important; }

    .btn-sm { padding: 5px 12px !important; font-size: 12px !important; }
    .btn-lg { padding: 12px 24px !important; font-size: 15px !important; border-radius: var(--r-md) !important; }

    /* =====================================================
       FORMS — override Bootstrap
       ===================================================== */
    .form-label {
        font-size: 13px !important; font-weight: 600 !important;
        color: var(--c-text-2) !important; margin-bottom: 6px !important;
    }
    .form-label .required, .text-danger { color: var(--c-danger) !important; }

    .form-control, .form-select {
        font-family: 'Inter', sans-serif !important;
        font-size: 14px !important; color: var(--c-text) !important;
        background: #fff !important; border: 1.5px solid var(--c-border) !important;
        border-radius: var(--r-sm) !important; padding: 9px 14px !important;
        transition: var(--tr-fast) !important; box-shadow: var(--sh-xs) !important;
        height: auto !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--c-primary) !important;
        box-shadow: 0 0 0 3px var(--c-primary-glow) !important;
        outline: none !important;
    }
    .form-control.is-invalid { border-color: var(--c-danger) !important; }
    .form-control.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.2) !important; }
    .invalid-feedback { font-size: 12px !important; color: var(--c-danger) !important; font-weight: 500 !important; }

    .form-check-input:checked { background-color: var(--c-primary) !important; border-color: var(--c-primary) !important; }
    .form-check-input:focus { box-shadow: 0 0 0 3px var(--c-primary-glow) !important; }

    .form-switch .form-check-input { width: 36px !important; height: 20px !important; }
    .form-check-label { font-size: 13px !important; font-weight: 500 !important; color: var(--c-text-2); }

    textarea.form-control { min-height: 100px; resize: vertical; }

    .input-group-text {
        background: var(--c-bg) !important; border: 1.5px solid var(--c-border) !important;
        color: var(--c-text-2) !important; font-size: 13px !important;
    }

    /* =====================================================
       TABLES — override Bootstrap
       ===================================================== */
    .table-responsive { border-radius: var(--r-lg); overflow: hidden; border: 1px solid var(--c-border); }

    .table {
        margin: 0 !important; font-size: 13px !important;
        color: var(--c-text) !important; border-color: var(--c-border) !important;
    }
    .table thead th {
        background: var(--c-bg) !important; color: var(--c-text-3) !important;
        font-size: 11px !important; font-weight: 700 !important;
        text-transform: uppercase !important; letter-spacing: .6px !important;
        padding: 12px 16px !important; border-bottom: 1px solid var(--c-border) !important;
        white-space: nowrap;
    }
    .table tbody td {
        padding: 13px 16px !important; vertical-align: middle !important;
        border-color: var(--c-border) !important; color: var(--c-text-2) !important;
        font-size: 13px !important;
    }
    .table tbody td strong { color: var(--c-text) !important; font-weight: 600 !important; }
    .table-hover tbody tr { transition: var(--tr-fast) !important; cursor: default; }
    .table-hover tbody tr:hover td { background: #F8FAFF !important; }
    .table-striped tbody tr:nth-of-type(odd) td { background: #FAFBFD !important; }

    /* =====================================================
       BADGES — override Bootstrap
       ===================================================== */
    .badge {
        font-family: 'Inter', sans-serif !important;
        font-size: 11px !important; font-weight: 600 !important;
        padding: 4px 10px !important; border-radius: 999px !important;
        letter-spacing: .2px !important;
    }
    .bg-primary   { background: linear-gradient(135deg,var(--c-primary),var(--c-violet)) !important; }
    .bg-success   { background: linear-gradient(135deg,#059669,#10B981) !important; }
    .bg-danger    { background: linear-gradient(135deg,#DC2626,#EF4444) !important; }
    .bg-warning   { background: linear-gradient(135deg,#D97706,#F59E0B) !important; color:#fff !important; }
    .bg-info      { background: linear-gradient(135deg,#0284C7,#0EA5E9) !important; }
    .bg-secondary { background: linear-gradient(135deg,#475569,#64748B) !important; }

    /* =====================================================
       MODALS — override Bootstrap
       ===================================================== */
    .modal-content {
        border: none !important; border-radius: var(--r-xl) !important;
        box-shadow: var(--sh-xl) !important; overflow: hidden !important;
    }
    .modal-header {
        background: linear-gradient(135deg,var(--c-primary),var(--c-violet)) !important;
        color: #fff !important; border: none !important; padding: 18px 24px !important;
    }
    .modal-header .modal-title { color: #fff !important; font-size: 16px !important; font-weight: 700 !important; }
    .btn-close { filter: brightness(0) invert(1) !important; opacity: .8 !important; }
    .btn-close:hover { opacity: 1 !important; }
    .modal-body { padding: 24px !important; }
    .modal-footer {
        background: var(--c-bg) !important; border-top: 1px solid var(--c-border) !important;
        padding: 14px 24px !important; gap: 8px !important;
    }
    .modal-backdrop.show { backdrop-filter: blur(3px); background: rgba(15,23,42,.5) !important; }

    /* =====================================================
       ALERTS — override Bootstrap
       ===================================================== */
    .alert {
        border-radius: var(--r-md) !important; border: none !important;
        padding: 14px 16px !important; font-size: 13px !important;
        font-weight: 500 !important; border-left: 4px solid !important;
        box-shadow: var(--sh-xs) !important;
    }
    .alert-success { background: #F0FDF9 !important; color: #065F46 !important; border-color: var(--c-success) !important; }
    .alert-danger  { background: #FFF1F2 !important; color: #9F1239 !important; border-color: var(--c-danger) !important; }
    .alert-warning { background: #FFFBEB !important; color: #78350F !important; border-color: var(--c-warning) !important; }
    .alert-info    { background: #EFF6FF !important; color: #1E40AF !important; border-color: var(--c-info) !important; }
    .alert-dismissible .btn-close { filter: none !important; opacity: .5 !important; }

    /* =====================================================
       PAGINATION — override Bootstrap
       ===================================================== */
    .pagination { gap: 4px !important; }
    .page-link {
        border-radius: var(--r-sm) !important; border: 1.5px solid var(--c-border) !important;
        color: var(--c-text-2) !important; font-size: 13px !important; font-weight: 600 !important;
        padding: 6px 12px !important; transition: var(--tr-fast) !important;
        font-family: 'Inter', sans-serif !important;
    }
    .page-link:hover { background: var(--c-bg) !important; border-color: var(--c-primary) !important; color: var(--c-primary) !important; }
    .page-item.active .page-link {
        background: linear-gradient(135deg,var(--c-primary),var(--c-violet)) !important;
        border-color: transparent !important; color: #fff !important;
        box-shadow: 0 4px 10px var(--c-primary-glow) !important;
    }
    .page-item.disabled .page-link { opacity: .4 !important; }

    /* =====================================================
       TABS — override Bootstrap
       ===================================================== */
    .nav-tabs {
        border-bottom: 2px solid var(--c-border) !important;
        gap: 4px !important; margin-bottom: 20px !important;
    }
    .nav-tabs .nav-link {
        font-size: 13px !important; font-weight: 600 !important; color: var(--c-text-3) !important;
        border: none !important; border-radius: var(--r-sm) var(--r-sm) 0 0 !important;
        padding: 10px 18px !important; transition: var(--tr-fast) !important;
    }
    .nav-tabs .nav-link:hover { color: var(--c-primary) !important; background: var(--c-primary-50) !important; }
    .nav-tabs .nav-link.active {
        color: var(--c-primary) !important; background: #fff !important;
        border-bottom: 2px solid var(--c-primary) !important; margin-bottom: -2px !important;
    }

    .nav-pills { gap: 6px !important; }
    .nav-pills .nav-link {
        font-size: 13px !important; font-weight: 600 !important; color: var(--c-text-2) !important;
        border-radius: var(--r-sm) !important; padding: 8px 16px !important; transition: var(--tr-fast) !important;
    }
    .nav-pills .nav-link:hover { background: var(--c-bg) !important; color: var(--c-primary) !important; }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg,var(--c-primary),var(--c-violet)) !important;
        color: #fff !important;
    }

    /* =====================================================
       DROPDOWN — override Bootstrap
       ===================================================== */
    .dropdown-menu {
        border-radius: var(--r-lg) !important; border: 1px solid var(--c-border) !important;
        box-shadow: var(--sh-xl) !important; padding: 6px !important;
        font-size: 13px !important; min-width: 180px !important;
    }
    .dropdown-item {
        border-radius: var(--r-sm) !important; padding: 8px 12px !important;
        font-size: 13px !important; font-weight: 500 !important; color: var(--c-text-2) !important;
        transition: var(--tr-fast) !important; display: flex; align-items: center; gap: 8px;
    }
    .dropdown-item:hover { background: var(--c-bg) !important; color: var(--c-primary) !important; }
    .dropdown-item.text-danger:hover { background: #FFF1F2 !important; color: var(--c-danger) !important; }
    .dropdown-divider { border-color: var(--c-border) !important; margin: 4px 0 !important; }

    /* =====================================================
       SELECT2 / Custom select
       ===================================================== */
    .select2-container--default .select2-selection--single {
        border: 1.5px solid var(--c-border) !important; border-radius: var(--r-sm) !important;
        height: 40px !important; padding: 5px 12px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--c-primary) !important;
        box-shadow: 0 0 0 3px var(--c-primary-glow) !important;
    }

    /* =====================================================
       BREADCRUMB
       ===================================================== */
    .breadcrumb { margin: 0; padding: 0; background: none; font-size: 12px; }
    .breadcrumb-item a { color: var(--c-text-3); text-decoration: none; font-weight: 500; }
    .breadcrumb-item a:hover { color: var(--c-primary); }
    .breadcrumb-item.active { color: var(--c-text-2); font-weight: 600; }
    .breadcrumb-item + .breadcrumb-item::before { color: var(--c-border); }

    /* =====================================================
       PROGRESS BAR
       ===================================================== */
    .progress { height: 6px !important; border-radius: 999px !important; background: var(--c-bg) !important; }
    .progress-bar {
        background: linear-gradient(90deg,var(--c-primary),var(--c-violet)) !important;
        border-radius: 999px !important;
    }
    .progress-bar.bg-success { background: linear-gradient(90deg,#059669,#10B981) !important; }
    .progress-bar.bg-warning { background: linear-gradient(90deg,#D97706,#F59E0B) !important; }
    .progress-bar.bg-danger  { background: linear-gradient(90deg,#DC2626,#EF4444) !important; }

    /* =====================================================
       ANIMATIONS
       ===================================================== */
    @keyframes fadeInUp {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity:0; }
        to   { opacity:1; }
    }
    @keyframes slideInRight {
        from { opacity:0; transform:translateX(20px); }
        to   { opacity:1; transform:translateX(0); }
    }
    @keyframes pulse-ring {
        0%   { transform:scale(1);   opacity:.8; }
        100% { transform:scale(1.4); opacity:0; }
    }

    .anim-fade-up  { animation: fadeInUp .35s ease both; }
    .anim-fade     { animation: fadeIn   .25s ease both; }
    .anim-slide-r  { animation: slideInRight .3s ease both; }

    /* stagger delay helpers */
    .delay-1 { animation-delay:.05s; }
    .delay-2 { animation-delay:.1s; }
    .delay-3 { animation-delay:.15s; }
    .delay-4 { animation-delay:.2s; }
    .delay-5 { animation-delay:.25s; }

    /* =====================================================
       MISCELLANEOUS
       ===================================================== */
    .text-primary { color: var(--c-primary) !important; }
    .text-success { color: var(--c-success) !important; }
    .text-danger  { color: var(--c-danger)  !important; }
    .text-warning { color: var(--c-warning) !important; }
    .text-info    { color: var(--c-info)    !important; }
    .text-muted   { color: var(--c-text-3)  !important; font-size: 12px; }

    /* ─── Chips / Tags ─── */
    .chip {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 11px; font-weight: 600; padding: 3px 10px;
        border-radius: 999px; border: 1.5px solid currentColor;
    }
    .chip-primary { color: var(--c-primary); background: var(--c-primary-50); border-color: var(--c-primary-light); }
    .chip-success { color: #059669; background: #F0FDF9; border-color: #A7F3D0; }
    .chip-danger  { color: var(--c-danger); background: #FFF1F2; border-color: #FECDD3; }
    .chip-warning { color: #D97706; background: #FFFBEB; border-color: #FDE68A; }

    /* ─── Empty state ─── */
    .empty-state {
        text-align: center; padding: 60px 20px; color: var(--c-text-3);
    }
    .empty-state-icon { font-size: 48px; margin-bottom: 16px; opacity: .5; }
    .empty-state-title { font-size: 16px; font-weight: 700; color: var(--c-text-2); margin-bottom: 6px; }
    .empty-state-sub { font-size: 13px; }

    /* ─── Skeleton loader ─── */
    @keyframes shimmer {
        0%   { background-position: -200% 0; }
        100% { background-position:  200% 0; }
    }
    .skeleton {
        background: linear-gradient(90deg, #F1F5F9 25%, #E2E8F0 50%, #F1F5F9 75%);
        background-size: 200% 100%; animation: shimmer 1.5s infinite;
        border-radius: var(--r-sm);
    }

    /* ─── Tailwind color normalization ─── */
    /* Maps Tailwind ad-hoc colors to design system tokens */
    .text-blue-500,.text-blue-600   { color: var(--c-sky)     !important; }
    .text-green-500,.text-green-600 { color: var(--c-emerald) !important; }
    .text-purple-500,.text-purple-600,.text-violet-500 { color: var(--c-violet) !important; }
    .text-yellow-500,.text-yellow-600 { color: var(--c-amber) !important; }
    .text-red-500,.text-red-600     { color: var(--c-danger)  !important; }
    .text-indigo-500,.text-indigo-600 { color: var(--c-primary) !important; }
    .text-pink-500,.text-pink-600   { color: var(--c-pink)    !important; }
    .text-teal-500,.text-teal-600   { color: var(--c-teal)    !important; }
    .text-cyan-500,.text-cyan-600   { color: var(--c-cyan)    !important; }
    .text-orange-500,.text-orange-600 { color: var(--c-orange) !important; }
    .text-gray-400  { color: var(--c-text-3) !important; }
    .text-gray-500,.text-gray-600   { color: var(--c-text-2)  !important; }
    .text-gray-700,.text-gray-800,.text-gray-900 { color: var(--c-text) !important; }

    /* Light bg tints used as icon containers */
    .bg-blue-50,.bg-blue-100    { background-color: rgba(14,165,233,.1)   !important; }
    .bg-green-50,.bg-green-100  { background-color: rgba(16,185,129,.1)   !important; }
    .bg-purple-50,.bg-purple-100 { background-color: rgba(124,58,237,.1) !important; }
    .bg-yellow-50,.bg-yellow-100 { background-color: rgba(245,158,11,.1) !important; }
    .bg-red-50,.bg-red-100      { background-color: rgba(239,68,68,.1)    !important; }
    .bg-indigo-50,.bg-indigo-100 { background-color: rgba(79,70,229,.1)  !important; }
    .bg-pink-50,.bg-pink-100    { background-color: rgba(236,72,153,.1)   !important; }
    .bg-teal-50,.bg-teal-100    { background-color: rgba(20,184,166,.1)   !important; }
    .bg-orange-50,.bg-orange-100 { background-color: rgba(249,115,22,.1) !important; }
    .bg-cyan-50,.bg-cyan-100    { background-color: rgba(6,182,212,.1)    !important; }

    /* Solid bg classes */
    .bg-blue-500   { background-color: var(--c-sky)     !important; }
    .bg-green-500  { background-color: var(--c-emerald) !important; }
    .bg-purple-500 { background-color: var(--c-violet)  !important; }
    .bg-yellow-500 { background-color: var(--c-amber)   !important; }
    .bg-red-500    { background-color: var(--c-danger)  !important; }

    /* Neutral bg normalization */
    .bg-white       { background-color: #fff !important; }
    .bg-gray-50,.bg-gray-100 { background-color: var(--c-bg) !important; }

    /* Shadow normalization */
    .shadow    { box-shadow: var(--sh-sm) !important; }
    .shadow-sm { box-shadow: var(--sh-xs) !important; }
    .shadow-lg { box-shadow: var(--sh-lg) !important; }
    .hover\:shadow-lg:hover { box-shadow: var(--sh-lg) !important; }

    /* Border normalization */
    .border-b,.border-t { border-color: var(--c-border) !important; }
    .border-b { border-bottom: 1px solid var(--c-border) !important; }
    .border-t { border-top:    1px solid var(--c-border) !important; }

    /* Rounded normalization */
    .rounded-lg  { border-radius: var(--r-md) !important; }
    .rounded-xl  { border-radius: var(--r-lg) !important; }
    .rounded-2xl { border-radius: var(--r-2xl) !important; }
    .rounded-full { border-radius: 999px !important; }

    /* Quick-action card tints */
    .hover\:bg-blue-100:hover   { background-color: rgba(14,165,233,.15)  !important; }
    .hover\:bg-green-100:hover  { background-color: rgba(16,185,129,.15)  !important; }
    .hover\:bg-purple-100:hover { background-color: rgba(124,58,237,.15)  !important; }
    .hover\:bg-yellow-100:hover { background-color: rgba(245,158,11,.15)  !important; }

    /* ─── Sidebar overrides ─── */
    .bg-primary { background: linear-gradient(135deg,var(--c-primary),var(--c-violet)) !important; }

    /* ─── Responsive ─── */
    @media (max-width: 768px) {
        .layout-inner { padding: 16px; }
        .page-header { flex-direction: column; }
    }
    </style>

    @yield('styles')

    {{-- Module-level CSS variable override (runs AFTER page styles, wins in cascade) --}}
    <style>
    :root {
        /* Module accent — set per route in layout @php block */
        --m-color:       {{ $_mc }};
        --m-color-dark:  {{ $_mcd }};
        --m-color-light: {{ $_mcl }};
        --m-bg:          {{ $_mbg }};
        --m-border:      {{ $_mbr }};

        /* Remap the "green theme" vars that many pages define in @section('styles')
           — this override wins because it appears AFTER the page's @section('styles') */
        --primary-dark-green: {{ $_mcd }};
        --secondary-green:    {{ $_mc }};
        --light-green:        {{ $_mbg }};
        --accent-green:       {{ $_mcl }};
        --border-green:       {{ $_mbr }};
        --text-dark:          #0F172A;
        --hover-green:        {{ $_mcd }};
        --very-light-green:   {{ $_mbg }};
    }
    </style>
</head>
<body style="background:var(--c-bg)">
<div class="layout-shell">

    <!-- ═══════════════════════════════════════
         SIDEBAR
         ═══════════════════════════════════════ -->
    <div class="layout-sidebar" id="appSidebar"
         x-data="{ open: true }"
         :style="open ? 'width:272px' : 'width:0; overflow:hidden'"
         style="transition:width .3s cubic-bezier(.4,0,.2,1)">
        @if(auth()->user()->hasRole(['Student','student']))
            @include('partials.sidebar-student')
        @elseif(auth()->user()->hasRole(['Teacher','teacher']))
            @include('partials.sidebar-teacher')
        @elseif(auth()->user()->hasRole(['HR','hr']))
            @include('partials.sidebar-hr')
        @elseif(auth()->user()->hasRole(['Dekan','dekan','Dean','dean']))
            @include('partials.sidebar-dean')
        @else
            @include('partials.sidebar')
        @endif
    </div>

    <!-- ═══════════════════════════════════════
         MAIN AREA
         ═══════════════════════════════════════ -->
    <div class="layout-main" x-data="{ sidebarOpen: true }">

        <!-- TOPBAR -->
        <div class="layout-topbar">
            <div class="topbar-inner">
                <!-- Left -->
                <div class="topbar-left">
                    <button class="topbar-toggle" onclick="toggleSidebar()" title="Menu">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                    </div>
                </div>

                <!-- Right -->
                <div class="topbar-right">

                    <!-- Notifications -->
                    <div style="position:relative" x-data="{
                        open: false,
                        notifications: [],
                        unread: 0,
                        load() {
                            fetch('{{ route('notifications.get') }}')
                                .then(r=>r.json())
                                .then(d=>{ this.notifications=d.notifications; this.unread=d.unread_count; })
                                .catch(()=>{});
                        },
                        read(id) {
                            fetch(`{{ url('notifications') }}/${id}/read`,{
                                method:'POST',
                                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}
                            }).then(()=>this.load());
                        },
                        readAll() {
                            fetch('{{ route('notifications.markAllRead') }}',{
                                method:'POST',
                                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}
                            }).then(()=>{ this.unread=0; this.load(); });
                        }
                    }" x-init="load(); setInterval(()=>load(),30000)">
                        <button class="topbar-icon-btn" @click="open=!open">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="topbar-badge" x-show="unread>0" x-text="unread"></span>
                        </button>
                        <div class="notif-dropdown" x-show="open" @click.away="open=false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95">
                            <div class="notif-header">
                                <h3>Bildirishnomalar <span x-show="unread>0" x-text="'('+unread+')'"></span></h3>
                                <button class="notif-mark-all" x-show="unread>0" @click="readAll()">Hammasini o'qilgan deb belgilash</button>
                            </div>
                            <div class="notif-body">
                                <template x-if="notifications.length===0">
                                    <div class="empty-state" style="padding:30px">
                                        <div class="empty-state-icon">🔔</div>
                                        <div class="empty-state-title">Bildirishnomalar yo'q</div>
                                    </div>
                                </template>
                                <template x-for="n in notifications" :key="n.id">
                                    <div class="notif-item" :class="n.is_read?'':'unread'" @click="!n.is_read&&read(n.id)">
                                        <div style="display:flex;justify-content:space-between;align-items:flex-start">
                                            <div style="flex:1">
                                                <div class="notif-item-title" x-text="n.title"></div>
                                                <div class="notif-item-msg"   x-text="n.message"></div>
                                                <div class="notif-item-time"  x-text="n.created_at"></div>
                                            </div>
                                            <div class="notif-dot" x-show="!n.is_read" style="margin-top:4px;margin-left:8px"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="notif-footer">
                                <a href="{{ route('notifications.index') }}">Barcha bildirishnomalar →</a>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div style="position:relative" x-data="{ open:false }">
                        <button class="topbar-user" @click="open=!open">
                            <div class="topbar-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                            <div style="text-align:left">
                                <div class="topbar-user-name">{{ Auth::user()->name ?? 'Foydalanuvchi' }}</div>
                                <div class="topbar-user-role">{{ Auth::user()->roles->first()->name ?? 'User' }}</div>
                            </div>
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#94A3B8">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="user-dropdown" x-show="open" @click.away="open=false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100">
                            <div class="user-dropdown-header">
                                <div class="user-dropdown-name">{{ Auth::user()->name ?? 'Foydalanuvchi' }}</div>
                                <div class="user-dropdown-email">{{ Auth::user()->email ?? '' }}</div>
                            </div>
                            <div style="padding:6px">
                                <a href="{{ route('profile.edit') }}" class="user-dropdown-item"><i class="fas fa-user" style="width:14px"></i> Profil</a>
                                <a href="{{ route('settings.index') }}" class="user-dropdown-item"><i class="fas fa-cog" style="width:14px"></i> Sozlamalar</a>
                                <div class="user-dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="user-dropdown-item danger w-100">
                                        <i class="fas fa-sign-out-alt" style="width:14px"></i> Chiqish
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /TOPBAR -->

        <!-- CONTENT -->
        <div class="layout-content">
            <div class="layout-inner anim-fade-up">

                <!-- Flash messages -->
                @if(session('success') || session('error') || session('warning') || session('info') || (isset($errors) && $errors->any()))
                <div class="flash-wrap">
                    @if(session('success'))
                    <div class="flash flash-success" id="flash-success">
                        <span class="flash-icon"><i class="fas fa-check-circle"></i></span>
                        <span class="flash-text">{{ session('success') }}</span>
                        <button class="flash-close" onclick="this.parentElement.remove()">×</button>
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="flash flash-error" id="flash-error">
                        <span class="flash-icon"><i class="fas fa-times-circle"></i></span>
                        <span class="flash-text">{{ session('error') }}</span>
                        <button class="flash-close" onclick="this.parentElement.remove()">×</button>
                    </div>
                    @endif
                    @if(session('warning'))
                    <div class="flash flash-warning" id="flash-warning">
                        <span class="flash-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <span class="flash-text">{{ session('warning') }}</span>
                        <button class="flash-close" onclick="this.parentElement.remove()">×</button>
                    </div>
                    @endif
                    @if(session('info'))
                    <div class="flash flash-info" id="flash-info">
                        <span class="flash-icon"><i class="fas fa-info-circle"></i></span>
                        <span class="flash-text">{{ session('info') }}</span>
                        <button class="flash-close" onclick="this.parentElement.remove()">×</button>
                    </div>
                    @endif
                    @if(isset($errors) && $errors->any())
                    <div class="flash flash-error">
                        <span class="flash-icon"><i class="fas fa-exclamation-circle"></i></span>
                        <div class="flash-text">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        <button class="flash-close" onclick="this.parentElement.remove()">×</button>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Page content -->
                @yield('content')
            </div>
        </div>
        <!-- /CONTENT -->

    </div>
    <!-- /MAIN -->

</div>
<!-- /SHELL -->

<!-- Mobile overlay -->
<div id="sidebarOverlay" onclick="toggleSidebar()"
     style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:29;backdrop-filter:blur(2px)"></div>

<!-- Chat Popup -->
@include('components.chat-popup')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ── Sidebar toggle ── */
function toggleSidebar() {
    const sb = document.getElementById('appSidebar');
    const ov = document.getElementById('sidebarOverlay');
    const isOpen = sb.style.width !== '0px' && sb.style.width !== '0';
    sb.style.width = isOpen ? '0' : '260px';
    sb.style.overflow = isOpen ? 'hidden' : '';
    if(window.innerWidth < 768) {
        ov.style.display = isOpen ? 'none' : 'block';
    }
}

/* ── Auto-dismiss flash messages ── */
setTimeout(()=>{
    document.querySelectorAll('.flash').forEach(el => {
        el.style.transition='opacity .4s ease';
        el.style.opacity='0';
        setTimeout(()=>el.remove(), 400);
    });
}, 5000);

/* ── Responsive sidebar ── */
window.addEventListener('resize', ()=>{
    const sb = document.getElementById('appSidebar');
    const ov = document.getElementById('sidebarOverlay');
    if(window.innerWidth >= 768) {
        ov.style.display = 'none';
        if(sb.style.width === '0px') sb.style.width = '260px';
    }
});
</script>

@yield('scripts')
@stack('scripts')
</body>
</html>
