@extends('layouts.dashboard-new')

@section('title', 'Nomzod ma\'lumotlari')
@section('page-title', 'Nomzod ma\'lumotlari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.vacancies.index') }}">Vakansiyalar</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vacancy-applications.index') }}">Nomzodlar</a></li>
                    <li class="breadcrumb-item active">Ko'rish</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ $application->full_name }}</h1>
        </div>
        <div class="d-flex gap-2">
            @if($application->resume_url)
                <a href="{{ $application->resume_url }}" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-file-pdf me-2"></i>CV yuklab olish
                </a>
            @endif
            <a href="{{ route('admin.vacancy-applications.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Orqaga
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Personal Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Shaxsiy ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="150" class="text-muted">F.I.O:</th>
                                    <td><strong>{{ $application->full_name }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Email:</th>
                                    <td><a href="mailto:{{ $application->email }}">{{ $application->email }}</a></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Telefon:</th>
                                    <td><a href="tel:{{ $application->phone }}">{{ $application->phone }}</a></td>
                                </tr>
                                @if($application->birth_date)
                                <tr>
                                    <th class="text-muted">Tug'ilgan sana:</th>
                                    <td>{{ $application->birth_date->format('d.m.Y') }} ({{ $application->age }} yosh)</td>
                                </tr>
                                @endif
                                @if($application->gender)
                                <tr>
                                    <th class="text-muted">Jinsi:</th>
                                    <td>{{ $application->gender === 'male' ? 'Erkak' : 'Ayol' }}</td>
                                </tr>
                                @endif
                                @if($application->region || $application->city)
                                <tr>
                                    <th class="text-muted">Manzil:</th>
                                    <td>{{ implode(', ', array_filter([$application->region, $application->city, $application->address])) }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-4 text-center">
                            @if($application->photo_url)
                                <img src="{{ $application->photo_url }}" alt="{{ $application->full_name }}"
                                     class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                     style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-4x text-muted"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Education & Experience -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Ma'lumot va tajriba</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Ma'lumoti</h6>
                            @if($application->education_level)
                                <p><strong>Daraja:</strong> {{ $application->education_level_label }}</p>
                            @endif
                            @if($application->education_institution)
                                <p><strong>O'quv yurti:</strong> {{ $application->education_institution }}</p>
                            @endif
                            @if($application->education_specialty)
                                <p><strong>Mutaxassislik:</strong> {{ $application->education_specialty }}</p>
                            @endif
                            @if($application->graduation_year)
                                <p><strong>Bitirgan yili:</strong> {{ $application->graduation_year }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Ish tajribasi</h6>
                            @if($application->experience_years)
                                <p><strong>Tajriba:</strong> {{ $application->experience_years }} yil</p>
                            @endif
                            @if($application->work_experience)
                                <p class="mb-0">{!! nl2br(e($application->work_experience)) !!}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills -->
            @if($application->skills || $application->languages)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Ko'nikmalar</h5>
                </div>
                <div class="card-body">
                    @if($application->skills)
                        <h6 class="text-muted mb-2">Ko'nikmalar:</h6>
                        <p>{{ $application->skills }}</p>
                    @endif
                    @if($application->languages)
                        <h6 class="text-muted mb-2">Tillar:</h6>
                        <p class="mb-0">{{ $application->languages }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Cover Letter -->
            @if($application->cover_letter)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-envelope-open-text me-2"></i>Motivatsiya xati</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{!! nl2br(e($application->cover_letter)) !!}</p>
                </div>
            </div>
            @endif

            <!-- Response History -->
            @if($application->response_sent_at)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-reply me-2"></i>Yuborilgan javob</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Javob yuborildi:</strong>
                            <span>{{ $application->response_sent_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <p class="mb-0">{!! nl2br(e($application->response_message)) !!}</p>
                        @if($application->responseSender)
                            <small class="text-muted mt-2 d-block">
                                Yuborgan: {{ $application->responseSender->name }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Vacancy Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Vakansiya</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $application->vacancy->title ?? '-' }}</h6>
                    @if($application->vacancy)
                        <p class="text-muted mb-2">{{ $application->vacancy->department }}</p>
                        <span class="badge bg-secondary">{{ $application->vacancy->employment_type_label }}</span>
                    @endif
                </div>
            </div>

            <!-- Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Holat</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.vacancy-applications.update-status', $application) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Joriy holat</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Models\VacancyApplication::STATUSES as $key => $status)
                                    <option value="{{ $key }}" {{ $application->status === $key ? 'selected' : '' }}>
                                        {{ $status['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ichki izohlar</label>
                            <textarea name="internal_notes" class="form-control" rows="3" placeholder="Faqat xodimlar uchun...">{{ $application->internal_notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Holatni saqlash
                        </button>
                    </form>
                </div>
            </div>

            <!-- Send Response -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Javob yuborish</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.vacancy-applications.send-response', $application) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Yangi holat (ixtiyoriy)</label>
                            <select name="status" class="form-select">
                                <option value="">O'zgartirmaslik</option>
                                @foreach(\App\Models\VacancyApplication::STATUSES as $key => $status)
                                    <option value="{{ $key }}">{{ $status['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Xabar matni <span class="text-danger">*</span></label>
                            <textarea name="response_message" class="form-control" rows="5" required
                                      placeholder="Nomzodga yuboriladigan xabar..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-paper-plane me-2"></i>Javob yuborish
                        </button>
                    </form>
                </div>
            </div>

            <!-- Meta Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Ma'lumot</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Ariza sanasi:</td>
                            <td>{{ $application->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        @if($application->reviewed_at)
                        <tr>
                            <td class="text-muted">Ko'rilgan:</td>
                            <td>{{ $application->reviewed_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        @endif
                        @if($application->reviewer)
                        <tr>
                            <td class="text-muted">Ko'rgan:</td>
                            <td>{{ $application->reviewer->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
