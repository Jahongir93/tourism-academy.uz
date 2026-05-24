@extends('layouts.dashboard-new')

@section('title', 'Xodim ma\'lumotlari')

@section('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #0d4f3c 0%, #16a085 100%);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }

    .profile-photo {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.3);
        object-fit: cover;
        background: white;
    }

    .profile-photo-placeholder {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: rgba(255,255,255,0.7);
    }

    .info-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 1.25rem;
        overflow: hidden;
    }

    .info-card-header {
        padding: 0.875rem 1rem;
        font-weight: 600;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-card-header.green { background: linear-gradient(135deg, #0d4f3c, #16a085); color: white; }
    .info-card-header.light { background: #e8f5f0; color: #0d4f3c; }
    .info-card-header.blue { background: linear-gradient(135deg, #2980b9, #3498db); color: white; }
    .info-card-header.orange { background: linear-gradient(135deg, #d35400, #e67e22); color: white; }

    .info-card-body { padding: 1rem; }

    .info-row {
        display: flex;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .info-row:last-child { border-bottom: none; }

    .info-label {
        color: #6b7280;
        font-size: 0.875rem;
        min-width: 130px;
        flex-shrink: 0;
    }

    .info-value {
        font-weight: 500;
        color: #1f2937;
    }

    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .status-active { background: rgba(16, 185, 129, 0.15); color: #059669; }
    .status-leave { background: rgba(245, 158, 11, 0.15); color: #d97706; }
    .status-inactive { background: rgba(239, 68, 68, 0.15); color: #dc2626; }

    .stat-box {
        background: #f0fdf4;
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        border: 1px solid #bbf7d0;
    }

    .stat-value { font-size: 1.5rem; font-weight: 700; color: #0d4f3c; }
    .stat-label { font-size: 0.8rem; color: #6b7280; }

    .tag {
        background: #e8f5f0;
        color: #0d4f3c;
        padding: 0.25rem 0.625rem;
        border-radius: 4px;
        font-size: 0.8rem;
        display: inline-block;
        margin: 0.125rem;
    }

    .edu-item {
        background: #f9fafb;
        border-radius: 6px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border-left: 3px solid #16a085;
    }
    .edu-item:last-child { margin-bottom: 0; }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-green { background: linear-gradient(135deg, #0d4f3c, #16a085); color: white; border: none; }
    .btn-green:hover { opacity: 0.9; color: white; }
    .btn-light { background: #e8f5f0; color: #0d4f3c; border: 1px solid #d1f2eb; }
    .btn-light:hover { background: #d1f2eb; color: #0d4f3c; }
    .btn-outline { background: white; color: #6b7280; border: 1px solid #e5e7eb; }
    .btn-outline:hover { background: #f9fafb; color: #374151; }

    .empty-text { color: #9ca3af; font-style: italic; font-size: 0.875rem; }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                @if($employee->photo_url)
                    <img src="{{ asset($employee->photo_url) }}" class="profile-photo" alt="Photo">
                @else
                    <div class="profile-photo-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                @endif
                <div>
                    <h4 class="mb-1 fw-bold">{{ $employee->full_name }}</h4>
                    <div class="mb-2" style="opacity: 0.9">
                        <i class="fas fa-id-badge me-1"></i>
                        {{ $employee->employee_code ?? 'Kod yo\'q' }}
                    </div>
                    @if($employee->employmentDetail && $employee->employmentDetail->position)
                        <div style="opacity: 0.85; font-size: 0.9rem">
                            <i class="fas fa-briefcase me-1"></i>
                            {{ $employee->employmentDetail->position->name_uz ?? $employee->employmentDetail->position->name ?? '' }}
                        </div>
                    @endif
                </div>
            </div>
            <div>
                @if($employee->status == 'active')
                    <span class="status-badge status-active"><i class="fas fa-check-circle"></i> Faol</span>
                @elseif($employee->status == 'leave')
                    <span class="status-badge status-leave"><i class="fas fa-clock"></i> Ta'tilda</span>
                @else
                    <span class="status-badge status-inactive"><i class="fas fa-times-circle"></i> Nofaol</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mb-4 d-flex gap-2 flex-wrap">
        <a href="{{ route('employees.edit', $employee) }}" class="btn-action btn-green">
            <i class="fas fa-edit"></i> Tahrirlash
        </a>
        <a href="{{ route('employees.index') }}" class="btn-action btn-light">
            <i class="fas fa-arrow-left"></i> Ortga
        </a>
        <button type="button" class="btn-action btn-outline" onclick="window.print()">
            <i class="fas fa-print"></i> Chop etish
        </button>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-4">
            <!-- Personal Info -->
            <div class="info-card">
                <div class="info-card-header green">
                    <i class="fas fa-user-circle"></i> Shaxsiy ma'lumotlar
                </div>
                <div class="info-card-body">
                    <div class="info-row">
                        <span class="info-label">JSHSHIR:</span>
                        <span class="info-value">{{ $employee->jshshir ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tug'ilgan sana:</span>
                        <span class="info-value">
                            {{ $employee->birth_date?->format('d.m.Y') ?? '-' }}
                            @if($employee->age)
                                <small class="text-muted">({{ $employee->age }} yosh)</small>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Jinsi:</span>
                        <span class="info-value">
                            @if($employee->gender == 'male')
                                <i class="fas fa-mars text-primary"></i> Erkak
                            @else
                                <i class="fas fa-venus text-danger"></i> Ayol
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Pasport:</span>
                        <span class="info-value">{{ $employee->passport_series }} {{ $employee->passport_number }}</span>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="info-card">
                <div class="info-card-header light">
                    <i class="fas fa-address-book"></i> Aloqa ma'lumotlari
                </div>
                <div class="info-card-body">
                    <div class="info-row">
                        <span class="info-label">Telefon:</span>
                        <span class="info-value">
                            @if($employee->phone)
                                <a href="tel:{{ $employee->phone }}" class="text-decoration-none text-success">
                                    <i class="fas fa-phone me-1"></i>{{ $employee->phone }}
                                </a>
                            @else -
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">
                            @if($employee->email)
                                <a href="mailto:{{ $employee->email }}" class="text-decoration-none text-primary">
                                    <i class="fas fa-envelope me-1"></i>{{ $employee->email }}
                                </a>
                            @else -
                            @endif
                        </span>
                    </div>
                    @if($employee->telegram)
                    <div class="info-row">
                        <span class="info-label">Telegram:</span>
                        <span class="info-value"><i class="fab fa-telegram text-info me-1"></i>{{ $employee->telegram }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Doimiy manzil:</span>
                        <span class="info-value">{{ $employee->address_permanent ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Hozirgi manzil:</span>
                        <span class="info-value">{{ $employee->address_current ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- Employment Details -->
            <div class="info-card">
                <div class="info-card-header blue">
                    <i class="fas fa-briefcase"></i> Mehnat faoliyati
                </div>
                <div class="info-card-body">
                    @if($employee->employmentDetail)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Lavozim:</span>
                                <span class="info-value">{{ $employee->employmentDetail->position?->name_uz ?? $employee->employmentDetail->position?->name ?? '-' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Bo'lim:</span>
                                <span class="info-value">{{ $employee->employmentDetail->department?->name_uz ?? $employee->employmentDetail->department?->name ?? '-' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Fakultet:</span>
                                <span class="info-value">{{ $employee->employmentDetail->faculty?->name_uz ?? $employee->employmentDetail->faculty?->name ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Ishga qabul:</span>
                                <span class="info-value">{{ $employee->employmentDetail->hire_date?->format('d.m.Y') ?? '-' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Ish turi:</span>
                                <span class="info-value">
                                    @if($employee->employmentDetail->employment_type == 'asosiy')
                                        <span class="badge bg-success">Asosiy</span>
                                    @elseif($employee->employmentDetail->employment_type == 'qoshimcha')
                                        <span class="badge bg-info">O'rindosh</span>
                                    @else
                                        {{ $employee->employmentDetail->employment_type ?? '-' }}
                                    @endif
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Stavka:</span>
                                <span class="info-value">{{ $employee->employmentDetail->stavka ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <p class="empty-text text-center mb-0 py-3">
                        <i class="fas fa-info-circle me-1"></i> Mehnat ma'lumotlari kiritilmagan
                    </p>
                    @endif

                    {{-- Mehnat daftarchasi --}}
                    @php
                        $workBook = null;
                        try {
                            $workBook = $employee->documents?->where('document_type', 'mehnat_daftarchasi')->first();
                        } catch (\Exception $e) {}
                    @endphp
                    @if($workBook)
                    <hr class="my-3">
                    <div class="info-row">
                        <span class="info-label">Mehnat daftarchasi:</span>
                        <span class="info-value">
                            @if($workBook->file_path)
                                <a href="{{ asset('storage/' . $workBook->file_path) }}" target="_blank" class="text-decoration-none">
                                    <i class="fas fa-file-pdf text-danger me-1"></i> Ko'rish
                                </a>
                            @endif
                            @if($workBook->document_number)
                                <span class="ms-2 text-muted">№ {{ $workBook->document_number }}</span>
                            @endif
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Education -->
            <div class="info-card">
                <div class="info-card-header orange">
                    <i class="fas fa-graduation-cap"></i> Ta'lim ma'lumotlari
                </div>
                <div class="info-card-body">
                    @php
                        $educations = collect();
                        try { $educations = $employee->educations ?? collect(); } catch (\Exception $e) {}
                        $levels = [
                            'secondary' => "O'rta",
                            'secondary_special' => "O'rta maxsus",
                            'bachelor' => 'Bakalavr',
                            'master' => 'Magistr',
                            'phd' => 'PhD',
                            'dsc' => 'DSc',
                            'candidate' => 'Fan nomzodi',
                            'doctor' => 'Fan doktori'
                        ];
                    @endphp
                    @if($educations->count() > 0)
                        @foreach($educations as $education)
                        <div class="edu-item">
                            <div class="fw-semibold" style="color: #0d4f3c">
                                <i class="fas fa-award me-1"></i>
                                {{ $levels[$education->education_level] ?? $education->education_level ?? '-' }}
                            </div>
                            <div class="mt-1">{{ $education->institution ?? '-' }}</div>
                            <div class="text-muted" style="font-size: 0.85rem">
                                @if($education->speciality)
                                    <i class="fas fa-bookmark me-1"></i>{{ $education->speciality }}
                                @endif
                                @if($education->graduation_date)
                                    <span class="ms-2"><i class="fas fa-calendar me-1"></i>{{ $education->graduation_date->format('Y') }}</span>
                                @endif
                                @if($education->diploma_number)
                                    <span class="ms-2"><i class="fas fa-certificate me-1"></i>Diplom: {{ $education->diploma_number }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                    <p class="empty-text text-center mb-0 py-3">
                        <i class="fas fa-info-circle me-1"></i> Ta'lim ma'lumotlari kiritilmagan
                    </p>
                    @endif

                    {{-- Ta'lim hujjatlari (Diplom va boshqalar) --}}
                    @php
                        $eduDocs = collect();
                        try {
                            $eduDocs = $employee->documents?->whereIn('document_type', ['diplom', 'diploma', 'attestat', 'sertifikat', 'certificate']) ?? collect();
                        } catch (\Exception $e) {}
                    @endphp
                    @if($eduDocs->count() > 0)
                    <hr class="my-3">
                    <h6 class="mb-2"><i class="fas fa-file-alt me-1"></i> Yuklangan hujjatlar</h6>
                    @foreach($eduDocs as $doc)
                    <div class="info-row">
                        <span class="info-label">
                            @switch($doc->document_type)
                                @case('diplom')
                                @case('diploma')
                                    Diplom
                                    @break
                                @case('attestat')
                                    Attestat
                                    @break
                                @case('sertifikat')
                                @case('certificate')
                                    Sertifikat
                                    @break
                                @default
                                    {{ $doc->document_name ?? $doc->document_type }}
                            @endswitch
                        </span>
                        <span class="info-value">
                            @if($doc->file_path)
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-decoration-none">
                                    <i class="fas fa-file-pdf text-danger me-1"></i> Ko'rish
                                </a>
                            @endif
                            @if($doc->document_number)
                                <span class="ms-2 text-muted">№ {{ $doc->document_number }}</span>
                            @endif
                        </span>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            <!-- Teacher Info -->
            @if($employee->is_teacher)
            <div class="info-card">
                <div class="info-card-header green">
                    <i class="fas fa-chalkboard-teacher"></i> O'qituvchi ma'lumotlari
                </div>
                <div class="info-card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <div class="stat-box">
                                @php $subjectsCount = 0; try { $subjectsCount = $employee->subjects?->count() ?? 0; } catch (\Exception $e) {} @endphp
                                <div class="stat-value">{{ $subjectsCount }}</div>
                                <div class="stat-label">Fanlar</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-box">
                                @php $groupsCount = 0; try { $groupsCount = $employee->groups?->count() ?? 0; } catch (\Exception $e) {} @endphp
                                <div class="stat-value">{{ $groupsCount }}</div>
                                <div class="stat-label">Guruhlar</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-box">
                                <div class="stat-value">{{ $employee->calculateWorkload() }}</div>
                                <div class="stat-label">Soat/hafta</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="mb-2"><i class="fas fa-book me-1"></i> Fanlar</h6>
                            @php $subjects = collect(); try { $subjects = $employee->subjects ?? collect(); } catch (\Exception $e) {} @endphp
                            @if($subjects->count() > 0)
                                <div>@foreach($subjects as $subject)<span class="tag">{{ $subject->name_uz ?? $subject->name ?? '-' }}</span>@endforeach</div>
                            @else
                                <p class="empty-text mb-0">Fanlar biriktirilmagan</p>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="mb-2"><i class="fas fa-users me-1"></i> Guruhlar</h6>
                            @php $groups = collect(); try { $groups = $employee->groups ?? collect(); } catch (\Exception $e) {} @endphp
                            @if($groups->count() > 0)
                                <div>@foreach($groups as $group)<span class="tag">{{ $group->name ?? '-' }}</span>@endforeach</div>
                            @else
                                <p class="empty-text mb-0">Guruhlar biriktirilmagan</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
