@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Ariza topshirish - ' . $vacancy->title)

@section('content')
<!-- Hero Section -->
<section class="page-hero apply-hero" style="padding-top: 140px; margin-top: -80px;">
    <div class="container">
        <nav class="hero-breadcrumb">
            <a href="{{ route('home') }}">Bosh sahifa</a>
            <span>/</span>
            <a href="{{ route('vacancies.index') }}">Vakansiyalar</a>
            <span>/</span>
            <a href="{{ route('vacancies.show', $vacancy) }}">{{ $vacancy->title }}</a>
            <span>/</span>
            <span>Ariza</span>
        </nav>
        <h1 class="hero-title">
            <i class="fas fa-paper-plane me-3"></i>Ariza topshirish
        </h1>
        <p class="hero-subtitle">
            {{ $vacancy->title }} vakansiyasi uchun ariza to'ldiring
        </p>
    </div>
</section>

<!-- Form -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="row g-4">
            <!-- Form -->
            <div class="col-lg-8">
                <form action="{{ route('vacancies.storeApplication', $vacancy) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="apply-form-card">
                        <!-- Personal Info -->
                        <div class="apply-section">
                            <h4><i class="fas fa-user"></i>Shaxsiy ma'lumotlar</h4>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Familiya <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                           value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ism <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                           value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Otasining ismi</label>
                                    <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telefon <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone') }}" placeholder="+998 90 123-45-67" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tug'ilgan sana</label>
                                    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jinsi</label>
                                    <select name="gender" class="form-select">
                                        <option value="">Tanlang</option>
                                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Erkak</option>
                                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Ayol</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Viloyat</label>
                                    <input type="text" name="region" class="form-control" value="{{ old('region') }}" placeholder="Samarqand">
                                </div>
                            </div>
                        </div>

                        <!-- Education -->
                        <div class="apply-section">
                            <h4><i class="fas fa-graduation-cap"></i>Ma'lumot</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Ma'lumot darajasi</label>
                                    <select name="education_level" class="form-select">
                                        <option value="">Tanlang</option>
                                        @foreach($educationLevels as $key => $label)
                                            <option value="{{ $key }}" {{ old('education_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Bitirgan yili</label>
                                    <input type="number" name="graduation_year" class="form-control" value="{{ old('graduation_year') }}"
                                           min="1950" max="{{ date('Y') + 5 }}" placeholder="{{ date('Y') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">O'quv yurti</label>
                                    <input type="text" name="education_institution" class="form-control" value="{{ old('education_institution') }}"
                                           placeholder="Universitet, kollej nomi">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mutaxassislik</label>
                                    <input type="text" name="education_specialty" class="form-control" value="{{ old('education_specialty') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Experience -->
                        <div class="apply-section">
                            <h4><i class="fas fa-briefcase"></i>Ish tajribasi</h4>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tajriba (yil)</label>
                                    <input type="number" name="experience_years" class="form-control" value="{{ old('experience_years') }}" min="0" max="60">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Ko'nikmalar</label>
                                    <input type="text" name="skills" class="form-control" value="{{ old('skills') }}"
                                           placeholder="MS Office, Python, English...">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Ish tajribasi tavsifi</label>
                                    <textarea name="work_experience" class="form-control" rows="3"
                                              placeholder="Oldingi ish joylari va vazifalar haqida...">{{ old('work_experience') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tillar</label>
                                    <input type="text" name="languages" class="form-control" value="{{ old('languages') }}"
                                           placeholder="O'zbek (ona tili), Rus (yaxshi), Ingliz (o'rta)...">
                                </div>
                            </div>
                        </div>

                        <!-- Cover Letter -->
                        <div class="apply-section">
                            <h4><i class="fas fa-envelope-open-text"></i>Motivatsiya xati</h4>
                            <textarea name="cover_letter" class="form-control" rows="5"
                                      placeholder="Nima uchun ushbu lavozim uchun ariza berayapsiz? O'zingiz haqingizda qisqacha yozing...">{{ old('cover_letter') }}</textarea>
                        </div>

                        <!-- Files -->
                        <div class="apply-section">
                            <h4><i class="fas fa-paperclip"></i>Hujjatlar</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Rezyume (CV)</label>
                                    <input type="file" name="resume" class="form-control @error('resume') is-invalid @enderror"
                                           accept=".pdf,.doc,.docx">
                                    <div class="form-text">PDF, DOC yoki DOCX (max 5MB)</div>
                                    @error('resume')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Rasm</label>
                                    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror"
                                           accept=".jpg,.jpeg,.png">
                                    <div class="form-text">JPG yoki PNG (max 2MB)</div>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="apply-section submit-section">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agree" required>
                                <label class="form-check-label" for="agree">
                                    Shaxsiy ma'lumotlarimni qayta ishlashga rozilik bildiraman
                                </label>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Ariza yuborish
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar-sticky">
                    <div class="vacancy-info-card mb-4">
                        <h5 class="mb-3">{{ $vacancy->title }}</h5>
                        @if($vacancy->department)
                            <p class="vacancy-info-item"><i class="fas fa-building me-2"></i>{{ $vacancy->department }}</p>
                        @endif
                        <p class="vacancy-info-item mb-0"><i class="fas fa-briefcase me-2"></i>{{ $vacancy->employment_type_label }}</p>
                    </div>

                    <div class="tips-card">
                        <h6 class="mb-3"><i class="fas fa-lightbulb me-2 text-warning"></i>Maslahat</h6>
                        <ul class="tips-list">
                            <li><i class="fas fa-check text-success me-2"></i>Barcha maydonlarni to'liq to'ldiring</li>
                            <li><i class="fas fa-check text-success me-2"></i>CV faylni PDF formatda yuklang</li>
                            <li><i class="fas fa-check text-success me-2"></i>Telefon raqamni to'g'ri kiriting</li>
                            <li><i class="fas fa-check text-success me-2"></i>Motivatsiya xatini yozing</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    /* Hero Section */
    .page-hero {
        background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
        color: white;
        padding: 60px 0;
        position: relative;
    }
    .apply-hero {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }
    .page-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.5;
    }
    .hero-breadcrumb {
        margin-bottom: 20px;
        font-size: 14px;
        position: relative;
        z-index: 1;
    }
    .hero-breadcrumb a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
    }
    .hero-breadcrumb a:hover {
        color: white;
    }
    .hero-breadcrumb span {
        color: rgba(255,255,255,0.6);
        margin: 0 8px;
    }
    .hero-breadcrumb span:last-child {
        color: white;
        margin: 0;
    }
    .hero-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }
    .hero-subtitle {
        font-size: 1.1rem;
        color: rgba(255,255,255,0.9);
        max-width: 600px;
        position: relative;
        z-index: 1;
        margin: 0;
    }

    /* Apply Form Card */
    .apply-form-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .apply-section {
        padding: 24px;
        border-bottom: 1px solid #f0f0f0;
    }
    .apply-section:last-child {
        border-bottom: none;
    }
    .apply-section h4 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #1a1a1a;
        display: flex;
        align-items: center;
    }
    .apply-section h4 i {
        width: 32px;
        height: 32px;
        background: #f0f7ff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        color: #0066CC;
    }
    .submit-section {
        background: #f8fafc;
    }

    /* Sidebar */
    .sidebar-sticky {
        position: sticky;
        top: 100px;
    }
    .vacancy-info-card {
        background: linear-gradient(135deg, #0066CC, #0052a3);
        color: white;
        border-radius: 16px;
        padding: 24px;
    }
    .vacancy-info-card h5 {
        font-weight: 600;
    }
    .vacancy-info-item {
        opacity: 0.9;
        margin-bottom: 8px;
        font-size: 14px;
    }
    .tips-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .tips-card h6 {
        font-weight: 600;
    }
    .tips-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }
    .tips-list li {
        color: #666;
        font-size: 14px;
        margin-bottom: 10px;
    }
    .tips-list li:last-child {
        margin-bottom: 0;
    }

    @media (max-width: 991px) {
        .hero-title {
            font-size: 2rem;
        }
        .sidebar-sticky {
            position: static;
        }
    }
    @media (max-width: 576px) {
        .hero-title {
            font-size: 1.75rem;
        }
    }
</style>
@endsection
