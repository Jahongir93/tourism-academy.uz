<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourism va Service fakulteti - Zamonaviy ta'lim markazi</title>
    <meta name="description" content="Tourism va Service fakultetining rasmiy veb-sayti - Kelajak kasblari uchun zamonaviy ta'lim">
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- AOS Animation -->
    <link href="{{ asset('vendor/aos/aos.css') }}" rel="stylesheet">
    
    <style>
        @import url('{{ asset('vendor/fonts/inter.css') }}');
        
        :root {
            --neon-purple: #a855f7;
            --neon-blue: #3b82f6;
            --neon-pink: #ec4899;
            --neon-cyan: #06b6d4;
            --dark-bg: #0f0f23;
            --dark-secondary: #1a1a2e;
            --dark-card: #16213e;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: #fff;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        .animated-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            background: linear-gradient(125deg, #0f0f23 0%, #1a1a2e 50%, #0f172a 100%);
        }
        
        .animated-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(168, 85, 247, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(236, 72, 153, 0.1) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(1deg); }
            66% { transform: translate(-20px, 20px) rotate(-1deg); }
        }
        
        /* Navbar */
        .navbar-custom {
            background: rgba(15, 15, 35, 0.8) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(168, 85, 247, 0.2);
            transition: all 0.3s;
        }
        
        .navbar-custom .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: linear-gradient(45deg, var(--neon-purple), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: glow 2s ease-in-out infinite;
        }
        
        @keyframes glow {
            0%, 100% { filter: brightness(1); }
            50% { filter: brightness(1.2); }
        }
        
        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }
        
        .navbar-custom .nav-link:hover {
            color: var(--neon-purple) !important;
            transform: translateY(-2px);
        }
        
        .navbar-custom .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--neon-purple), var(--neon-blue));
            transition: all 0.3s;
            transform: translateX(-50%);
        }
        
        .navbar-custom .nav-link:hover::after {
            width: 80%;
        }
        
        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            padding: 100px 0;
        }
        
        .hero-title {
            font-size: clamp(2.5rem, 8vw, 5rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #fff 0%, var(--neon-purple) 50%, var(--neon-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 3s ease infinite;
        }
        
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Neon Buttons */
        .btn-neon {
            padding: 12px 30px;
            font-weight: 600;
            border: 2px solid var(--neon-purple);
            background: transparent;
            color: var(--neon-purple);
            border-radius: 50px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-neon:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 40px rgba(168, 85, 247, 0.4);
        }
        
        .btn-neon::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--neon-purple);
            transition: left 0.3s;
            z-index: -1;
        }
        
        .btn-neon:hover::before {
            left: 0;
        }
        
        .btn-neon-secondary {
            border-color: var(--neon-blue);
            color: var(--neon-blue);
        }
        
        .btn-neon-secondary::before {
            background: var(--neon-blue);
        }
        
        .btn-neon-secondary:hover {
            box-shadow: 0 10px 40px rgba(59, 130, 246, 0.4);
        }
        
        /* Floating Cards */
        .floating-card {
            position: absolute;
            background: rgba(168, 85, 247, 0.1);
            border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            animation: float-random 15s ease-in-out infinite;
        }
        
        @keyframes float-random {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(50px, -30px) rotate(5deg); }
            50% { transform: translate(-30px, 40px) rotate(-5deg); }
            75% { transform: translate(40px, 20px) rotate(3deg); }
        }
        
        /* Stats Section */
        .stats-section {
            padding: 100px 0;
            background: var(--dark-secondary);
            position: relative;
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.1), rgba(59, 130, 246, 0.1));
            border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.3) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .stat-card:hover::before {
            opacity: 1;
        }
        
        .stat-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 40px rgba(168, 85, 247, 0.3);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(45deg, var(--neon-purple), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        /* Feature Cards */
        .feature-card {
            background: var(--dark-card);
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 20px;
            padding: 40px;
            height: 100%;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--neon-purple), var(--neon-blue), var(--neon-pink));
            animation: gradient-move 3s linear infinite;
        }
        
        @keyframes gradient-move {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(168, 85, 247, 0.2);
            border-color: var(--neon-purple);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--neon-purple), var(--neon-blue));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 2rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* Department Cards */
        .dept-card {
            background: linear-gradient(135deg, var(--dark-card), rgba(168, 85, 247, 0.1));
            border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 20px;
            padding: 30px;
            height: 100%;
            transition: all 0.4s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .dept-card::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.5) 0%, transparent 70%);
            transition: all 0.5s;
            transform: translate(-50%, -50%);
        }
        
        .dept-card:hover::after {
            width: 300px;
            height: 300px;
        }
        
        .dept-card:hover {
            transform: scale(1.05) rotate(1deg);
            box-shadow: 0 15px 40px rgba(168, 85, 247, 0.4);
        }
        
        /* News Cards */
        .news-card {
            background: var(--dark-card);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
        }
        
        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.3);
            border-color: var(--neon-blue);
        }
        
        .news-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s;
        }
        
        .news-card:hover img {
            transform: scale(1.1);
        }
        
        .news-meta {
            color: var(--neon-cyan);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        /* Contact Section */
        .contact-section {
            padding: 100px 0;
            background: var(--dark-secondary);
            position: relative;
            overflow: hidden;
        }
        
        .contact-info {
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.1), rgba(6, 182, 212, 0.1));
            border: 1px solid rgba(236, 72, 153, 0.3);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .contact-info:hover {
            transform: translateX(10px);
            box-shadow: 0 10px 30px rgba(236, 72, 153, 0.3);
        }
        
        /* Social Links */
        .social-link {
            width: 50px;
            height: 50px;
            border: 2px solid rgba(168, 85, 247, 0.5);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            transition: all 0.3s;
            color: white;
            text-decoration: none;
        }
        
        .social-link:hover {
            background: var(--neon-purple);
            transform: translateY(-5px) rotate(360deg);
            box-shadow: 0 10px 20px rgba(168, 85, 247, 0.4);
            color: white;
        }
        
        /* Footer */
        .footer {
            background: var(--dark-bg);
            border-top: 1px solid rgba(168, 85, 247, 0.2);
            padding: 50px 0 30px;
            position: relative;
        }
        
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--neon-purple), var(--neon-blue), transparent);
            animation: gradient-move 3s linear infinite;
        }
        
        /* Scroll animations */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s;
        }
        
        .scroll-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Particle effect */
        .particle {
            position: fixed;
            pointer-events: none;
            opacity: 0;
            animation: particle-float 3s linear;
        }
        
        @keyframes particle-float {
            0% {
                opacity: 1;
                transform: translate(0, 0) scale(0);
            }
            50% {
                opacity: 1;
                transform: translate(var(--x), var(--y)) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(calc(var(--x) * 2), calc(var(--y) * 2)) scale(0);
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg"></div>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-graduation-cap me-2"></i>
                Tourism & Service
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Bosh sahifa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('faculties') }}">Fakultetlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('departments') }}">Kafedralar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('news') }}">Yangiliklar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">Aloqa</a>
                    </li>
                    @auth
                        <li class="nav-item ms-3">
                            <a class="btn btn-neon" href="{{ url('/dashboard') }}">
                                <i class="fas fa-user-circle me-2"></i>Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item ms-3">
                            <a class="btn btn-neon" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-2"></i>Kirish
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="hero-title" data-aos="fade-up">
                        Kelajak kasblarini<br>biz bilan o'rganing
                    </h1>
                    <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">
                        Tourism va Service fakulteti - zamonaviy ta'lim, xalqaro standartlar
                        va cheksiz imkoniyatlar dunyosi
                    </p>
                    <div class="d-flex gap-3 flex-wrap" data-aos="fade-up" data-aos-delay="200">
                        <a href="#about" class="btn btn-neon">
                            <i class="fas fa-rocket me-2"></i>Boshlash
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-neon-secondary">
                            <i class="fas fa-play-circle me-2"></i>Online ta'lim
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 position-relative">
                    <div class="floating-card" style="width: 150px; height: 150px; top: 50px; right: 20px;">
                        <div class="p-3">
                            <i class="fas fa-globe fa-3x text-primary"></i>
                        </div>
                    </div>
                    <div class="floating-card" style="width: 120px; height: 120px; bottom: 50px; left: 20px;">
                        <div class="p-3">
                            <i class="fas fa-hotel fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3" data-aos="zoom-in">
                    <div class="stat-card">
                        <div class="stat-number" data-count="{{ App\Models\Student::count() ?? 1500 }}">0</div>
                        <div class="stat-label">Talabalar</div>
                    </div>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
                    <div class="stat-card">
                        <div class="stat-number" data-count="{{ App\Models\Employee::where('employee_type', 'teacher')->count() ?? 120 }}">0</div>
                        <div class="stat-label">O'qituvchilar</div>
                    </div>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                    <div class="stat-card">
                        <div class="stat-number" data-count="{{ App\Models\Department::count() ?? 8 }}">0</div>
                        <div class="stat-label">Kafedralar</div>
                    </div>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
                    <div class="stat-card">
                        <div class="stat-number" data-count="{{ App\Models\LmsCourse::count() ?? 50 }}">0</div>
                        <div class="stat-label">Online kurslar</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h2 class="display-4 fw-bold mb-4">
                        <span style="background: linear-gradient(45deg, var(--neon-purple), var(--neon-pink)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            Zamonaviy ta'lim
                        </span>
                    </h2>
                    <p class="lead text-muted mb-4">
                        Tourism va Service fakulteti - bu nafaqat bilim, balki amaliy ko'nikmalar, 
                        xalqaro tajriba va karyera imkoniyatlari markazi.
                    </p>
                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <h5>Xalqaro sertifikatlar</h5>
                                <p class="text-muted">Dunyo tan olgan diplomlar va sertifikatlar</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <h5>Kasbiy amaliyot</h5>
                                <p class="text-muted">Yetakchi kompaniyalarda tajriba orttirish</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600" 
                             alt="Faculty" class="img-fluid rounded-4 shadow-lg">
                        <div class="floating-card" style="position: absolute; bottom: -20px; right: -20px; width: 150px; height: 100px;">
                            <div class="p-3 text-center">
                                <h4 class="mb-0 text-warning">TOP 10</h4>
                                <small>O'zbekistonda</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Departments Section -->
    <section id="departments" class="py-5 stats-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3">
                    <span style="background: linear-gradient(45deg, var(--neon-blue), var(--neon-cyan)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        Bizning kafedralar
                    </span>
                </h2>
                <p class="lead text-muted">Fakultetimizdagi kafedralar va yo'nalishlar</p>
            </div>
            <div class="row g-4">
                @php
                    $departments = App\Models\Department::where('is_active', true)->orderBy('order_number')->take(6)->get();
                @endphp
                @forelse($departments as $dept)
                <div class="col-md-4" data-aos="flip-left" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="dept-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="feature-icon me-3">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $dept->name_uz }}</h5>
                                <small class="text-muted">{{ $dept->short_name }}</small>
                            </div>
                        </div>
                        <p class="text-muted small">Zamonaviy ta'lim dasturlari va malakali o'qituvchilar</p>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Ma'lumotlar yuklanmoqda...</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section id="news" class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="display-4 fw-bold">
                        <span style="background: linear-gradient(45deg, var(--neon-pink), var(--neon-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            Yangiliklar
                        </span>
                    </h2>
                </div>
                <a href="#" class="btn btn-neon">Barcha yangiliklar</a>
            </div>
            <div class="row g-4">
                @php
                    $news = App\Models\CmsNews::where('status', 'published')
                        ->orderBy('published_at', 'desc')
                        ->take(3)
                        ->get();
                @endphp
                @forelse($news as $item)
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="news-card">
                        @if($item->featured_image)
                        <img src="{{ asset('storage/' . $item->featured_image) }}" alt="{{ $item->title_uz }}">
                        @else
                        <div style="height: 200px; background: linear-gradient(135deg, var(--neon-purple), var(--neon-blue));"></div>
                        @endif
                        <div class="p-4">
                            <div class="news-meta">
                                <i class="far fa-calendar me-2"></i>{{ $item->published_at?->format('d.m.Y') }}
                            </div>
                            <h5 class="mb-3">{{ $item->title_uz }}</h5>
                            <p class="text-muted">{{ Str::limit($item->excerpt_uz, 100) }}</p>
                            <a href="#" class="btn btn-neon-secondary btn-sm">Batafsil</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Yangiliklar tez orada...</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6" data-aos="fade-right">
                    <h2 class="display-4 fw-bold mb-4">
                        <span style="background: linear-gradient(45deg, var(--neon-cyan), var(--neon-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            Biz bilan bog'laning
                        </span>
                    </h2>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt me-3 text-danger"></i>
                        Toshkent sh., Amir Temur ko'chasi, 100
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone me-3 text-success"></i>
                        +998 71 123 45 67
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-envelope me-3 text-info"></i>
                        info@tourism.uz
                    </div>
                    <div class="mt-4">
                        <a href="#" class="social-link">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-telegram"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="feature-card">
                        <h4 class="mb-4">Savollaringiz bormi?</h4>
                        <form>
                            <div class="mb-3">
                                <input type="text" class="form-control bg-dark border-secondary text-white" placeholder="Ismingiz">
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control bg-dark border-secondary text-white" placeholder="Email">
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control bg-dark border-secondary text-white" rows="4" placeholder="Xabaringiz"></textarea>
                            </div>
                            <button type="submit" class="btn btn-neon w-100">Yuborish</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2025 Tourism va Service fakulteti. Barcha huquqlar himoyalangan.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <span style="color: var(--neon-purple);">♥</span> bilan HEMIS tizimi asosida yaratilgan
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Counter animation
        const counters = document.querySelectorAll('.stat-number');
        const speed = 200;
        
        const animateCounter = (counter) => {
            const target = +counter.getAttribute('data-count');
            const increment = target / speed;
            
            const updateCount = () => {
                const count = +counter.innerText;
                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(updateCount, 10);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        };
        
        // Intersection Observer for counters
        const observerOptions = {
            threshold: 0.5
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        counters.forEach(counter => {
            observer.observe(counter);
        });
        
        // Particle effect on mouse move
        document.addEventListener('mousemove', (e) => {
            if (Math.random() > 0.98) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = e.pageX + 'px';
                particle.style.top = e.pageY + 'px';
                particle.style.width = '10px';
                particle.style.height = '10px';
                particle.style.background = `linear-gradient(45deg, var(--neon-purple), var(--neon-blue))`;
                particle.style.borderRadius = '50%';
                particle.style.setProperty('--x', (Math.random() - 0.5) * 100 + 'px');
                particle.style.setProperty('--y', (Math.random() - 0.5) * 100 + 'px');
                document.body.appendChild(particle);
                
                setTimeout(() => {
                    particle.remove();
                }, 3000);
            }
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar-custom');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(15, 15, 35, 0.95)';
                navbar.style.backdropFilter = 'blur(20px)';
            } else {
                navbar.style.background = 'rgba(15, 15, 35, 0.8)';
                navbar.style.backdropFilter = 'blur(10px)';
            }
        });
        
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>