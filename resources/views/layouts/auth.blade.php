<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'HEMIS - Tourism Academy Samarkand')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap CSS (fallback) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Prevent cache */
        html { -webkit-font-smoothing: antialiased; }
    </style>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #4338CA 0%, #3730A3 50%, #312e81 100%);
            background-attachment: fixed;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background gradient */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(67, 56, 202, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(55, 48, 163, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(49, 46, 129, 0.2) 0%, transparent 50%);
            animation: gradientShift 15s ease infinite;
            pointer-events: none;
        }

        @keyframes gradientShift {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Modern card container */
        .auth-container {
            min-height: 100vh;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            z-index: 10;
            overflow: hidden;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.15),
                0 0 1px rgba(0, 0, 0, 0.05);
            max-width: 950px;
            width: 100%;
            display: grid;
            grid-template-columns: 40% 60%;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
            max-height: 90vh;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left panel - Branding */
        .auth-brand {
            background: linear-gradient(135deg, #16022C 0%, #1C0F75 50%, #2500B5 100%);
            padding: 1.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .auth-brand::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(215, 255, 55, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .brand-logo {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .brand-logo img {
            max-width: 180px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3));
        }

        .brand-content {
            position: relative;
            z-index: 2;
            color: #e0e7ff;
        }

        .brand-content h1 {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 0.4rem;
            letter-spacing: -0.02em;
            color: #ffffff;
        }

        .brand-content p {
            font-size: 0.8rem;
            opacity: 0.9;
            font-weight: 500;
            line-height: 1.4;
            color: #c7d2fe;
        }

        .brand-illustration {
            position: relative;
            z-index: 2;
            margin-top: 1rem;
            opacity: 0.8;
        }

        .illustration-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(99, 102, 241, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .illustration-circle i {
            font-size: 2.5rem;
            color: #818cf8;
        }

        /* Right panel - Form */
        .auth-form-panel {
            padding: 1.5rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }

        .form-header {
            margin-bottom: 1.2rem;
        }

        .form-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.375rem;
            letter-spacing: -0.02em;
        }

        .form-header p {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 400;
        }

        /* Input fields */
        .form-input {
            position: relative;
            margin-bottom: 0.75rem;
        }

        .form-input input {
            width: 100%;
            padding: 0.65rem 0.875rem 0.65rem 2.75rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.875rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
            color: #1f2937;
            font-weight: 400;
        }

        .form-input input::placeholder {
            color: #9ca3af;
        }

        .form-input input:focus {
            outline: none;
            border-color: #4338CA;
            box-shadow: 0 0 0 4px rgba(67, 56, 202, 0.08);
        }

        .form-input i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.9375rem;
            transition: color 0.3s ease;
        }

        .form-input input:focus + i {
            color: #4338CA;
        }

        /* Button styles */
        .btn-primary {
            background: #D7FF37;
            color: #000;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            border: 2px solid #D7FF37;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 16px rgba(215, 255, 55, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(215, 255, 55, 0.5);
            background: #C7FD45;
            border-color: #C7FD45;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Checkbox */
        .custom-checkbox {
            position: relative;
            padding-left: 1.75rem;
            cursor: pointer;
            user-select: none;
            font-size: 0.875rem;
            color: #4b5563;
            font-weight: 500;
        }

        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .checkmark {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            height: 20px;
            width: 20px;
            background-color: #ffffff;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            transition: all 0.25s ease;
        }

        .custom-checkbox:hover input ~ .checkmark {
            border-color: #4338CA;
            background-color: #f9fafb;
        }

        .custom-checkbox input:checked ~ .checkmark {
            background: linear-gradient(135deg, #4338CA, #3730A3);
            border-color: #4338CA;
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 3px;
            width: 5px;
            height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .custom-checkbox input:checked ~ .checkmark:after {
            display: block;
        }

        /* Links */
        .link-hover {
            color: #4338CA;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .link-hover:hover {
            color: #655CFF;
        }

        /* Dividers */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 0.85rem 0;
        }

        .divider-solid {
            height: 1px;
            background: #f3f4f6;
            margin: 0.75rem 0;
        }

        /* Service buttons */
        .service-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            gap: 0.375rem;
        }

        .service-btn:hover {
            background: #ffffff;
            border-color: #4338CA;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 56, 202, 0.1);
        }

        .service-btn i {
            font-size: 1.125rem;
            color: #6b7280;
            transition: color 0.3s ease;
        }

        .service-btn:hover i {
            color: #4338CA;
        }

        .service-btn p {
            font-size: 0.75rem;
            color: #374151;
            font-weight: 500;
            margin: 0;
        }

        /* Student registration button */
        .btn-student {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.8125rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .btn-student:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
            color: white;
        }

        /* Alert styles */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            animation: slideIn 0.4s ease-out;
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            font-size: 0.8125rem;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert i {
            margin-top: 0.125rem;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            color: #166534;
        }

        .alert-error {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            color: #991b1b;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .auth-card {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .auth-brand {
                padding: 2.5rem 2rem;
            }

            .brand-illustration {
                display: none;
            }

            .auth-form-panel {
                padding: 2.5rem 2rem;
            }
        }

        /* Footer text */
        .footer-text {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8125rem;
            position: fixed;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 5;
        }

        .footer-text-small {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.6875rem;
            margin-top: 0.25rem;
        }

        /* Language Switcher */
        .lang-switcher {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 100;
            display: flex;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 0.5rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .lang-btn {
            padding: 0.5rem 0.875rem;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .lang-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-1px);
        }

        .lang-btn.active {
            background: white;
            color: #6366f1;
            border-color: white;
        }
    </style>
</head>
<body>
    <!-- Language Switcher -->
    <div class="lang-switcher">
        <a href="{{ url()->current() }}?lang=uz" class="lang-btn {{ session('locale', 'uz') == 'uz' ? 'active' : '' }}">O'Z</a>
        <a href="{{ url()->current() }}?lang=ru" class="lang-btn {{ session('locale') == 'ru' ? 'active' : '' }}">РУ</a>
        <a href="{{ url()->current() }}?lang=en" class="lang-btn {{ session('locale') == 'en' ? 'active' : '' }}">EN</a>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Left Panel - Branding -->
            <div class="auth-brand">
                <div class="brand-logo">
                    <img src="{{ asset('images/oqlogo.png') }}" alt="Tourism Academy Samarkand">
                </div>
                <div class="brand-content">
                    <h1>{{ __('auth.tourism_academy') }}</h1>
                    <p>{{ __('auth.academy_name') }}</p>
                </div>
                <div class="brand-illustration">
                    <div class="illustration-circle">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Form -->
            <div class="auth-form-panel">
                <div class="form-header">
                    <h2>@yield('title', __('auth.login'))</h2>
                    <p>{{ __('auth.welcome') }}</p>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-text">
        © {{ date('Y') }} {{ __('auth.academy_name') }}
        <div class="footer-text-small">{{ __('auth.all_rights_reserved') }}</div>
    </div>
</body>
</html>