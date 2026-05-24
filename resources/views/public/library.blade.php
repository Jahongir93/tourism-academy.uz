@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Elektron Kutubxona - Tourism Academy Samarkand')
@section('page-title', 'Elektron Kutubxona')
@section('breadcrumb', 'Kutubxona')

@section('content')
<style>
    .library-hero {
        background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);
        padding: 4rem 0;
        margin-bottom: 3rem;
        border-radius: 0 0 2rem 2rem;
    }

    .library-hero h1 {
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .library-hero p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
    }

    .library-hero i {
        color: rgba(255, 255, 255, 0.2);
        font-size: 5rem;
        margin-bottom: 1.5rem;
    }

    .search-box {
        max-width: 600px;
        margin: 2rem auto 0;
    }

    .search-input {
        border: 2px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.95);
        color: #333;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem 0 0 0.75rem;
        font-size: 1rem;
    }

    .search-input:focus {
        outline: none;
        border-color: white;
        background: white;
    }

    .search-btn {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 1rem 2rem;
        border-radius: 0 0.75rem 0.75rem 0;
        transition: all 0.3s;
        font-weight: 600;
    }

    .search-btn:hover {
        background: white;
        color: #0066CC;
        border-color: white;
    }

    .category-card {
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.05) 0%, rgba(0, 82, 163, 0.05) 100%);
        border: 2px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .category-card:hover {
        border-color: #0066CC;
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.1) 0%, rgba(0, 82, 163, 0.1) 100%);
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 102, 204, 0.15);
    }

    .category-card i {
        color: #0066CC;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .category-card h4 {
        font-weight: 700;
        color: #1f2937;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .category-card p {
        color: #6b7280;
        font-size: 0.9rem;
    }

    .book-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.25rem;
        transition: all 0.3s;
        display: flex;
        gap: 1rem;
    }

    .book-card:hover {
        border-color: #0066CC;
        box-shadow: 0 10px 25px rgba(0, 102, 204, 0.1);
        transform: translateY(-2px);
    }

    .book-cover {
        width: 80px;
        height: 110px;
        background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.2);
    }

    .book-cover i {
        color: white;
        font-size: 2rem;
    }

    .book-info h4 {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .book-info p {
        color: #6b7280;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
    }

    .book-info a {
        color: #0066CC;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .book-info a:hover {
        color: #0052A3;
        text-decoration: underline;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2rem;
        position: relative;
        padding-bottom: 1rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #0066CC 0%, #0052A3 100%);
        border-radius: 2px;
    }
</style>

<!-- Hero Section -->
<div class="library-hero">
    <div class="container mx-auto px-4">
        <div class="text-center">
            <i class="fas fa-book-open"></i>
            <h1>Elektron Kutubxona</h1>
            <p>10,000+ elektron kitob va ilmiy maqolalar</p>

            <!-- Search Bar -->
            <div class="search-box">
                <div class="flex">
                    <input type="text" placeholder="Kitob yoki muallifni qidiring..." class="search-input flex-1">
                    <button class="search-btn">
                        <i class="fas fa-search me-2"></i>Qidirish
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 pb-12">
    <!-- Categories -->
    <h3 class="section-title">Kategoriyalar</h3>
    <div class="grid md:grid-cols-4 gap-6 mb-12">
        <div class="category-card">
            <i class="fas fa-globe"></i>
            <h4>Turizm</h4>
            <p>2,500 kitob</p>
        </div>
        <div class="category-card">
            <i class="fas fa-hotel"></i>
            <h4>Mehmonxona</h4>
            <p>1,800 kitob</p>
        </div>
        <div class="category-card">
            <i class="fas fa-utensils"></i>
            <h4>Restoran</h4>
            <p>1,200 kitob</p>
        </div>
        <div class="category-card">
            <i class="fas fa-map-signs"></i>
            <h4>Gidlik</h4>
            <p>900 kitob</p>
        </div>
    </div>

    <!-- Featured Books -->
    <h3 class="section-title">Tavsiya etilgan kitoblar</h3>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @for($i = 1; $i <= 6; $i++)
        <div class="book-card">
            <div class="book-cover">
                <i class="fas fa-book"></i>
            </div>
            <div class="book-info flex-1">
                <h4>Turizm asoslari {{ $i }}</h4>
                <p>Muallif: A. Karimov</p>
                <a href="#">
                    <i class="fas fa-download"></i>
                    Yuklab olish
                </a>
            </div>
        </div>
        @endfor
    </div>
</div>
@endsection