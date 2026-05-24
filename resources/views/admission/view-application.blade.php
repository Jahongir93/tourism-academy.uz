@extends('layouts.dashboard-new')

@section('title', 'Ariza tafsilotlari - Onlayn Qabul')

@section('content')
<style>
    :root {
        --primary-green: #16a085;
        --dark-green: #0d4f3c;
        --light-green: #e8f5f0;
        --accent-green: #48c9b0;
    }

    .status-badge {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-reviewing {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-accepted {
        background: #d1fae5;
        color: #065f46;
    }

    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-waitlist {
        background: #e5e7eb;
        color: #374151;
    }

    .info-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .info-label {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .info-value {
        color: #111827;
        font-size: 1rem;
        font-weight: 600;
    }
</style>

<div class="container-fluid py-4">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Ariza tafsilotlari</h4>
            <p class="text-muted mb-0">Ariza ID: #{{ $application->id }}</p>
        </div>
        <div>
            <a href="{{ route('admission.applications') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Orqaga
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="info-card">
                <h5 class="mb-4">
                    <i class="fas fa-user text-primary me-2"></i>
                    Shaxsiy ma'lumotlar
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Familiya</div>
                        <div class="info-value">{{ $application->last_name }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Ism</div>
                        <div class="info-value">{{ $application->first_name }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Otasining ismi</div>
                        <div class="info-value">{{ $application->middle_name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Tug'ilgan sana</div>
                        <div class="info-value">{{ $application->birth_date ? \Carbon\Carbon::parse($application->birth_date)->format('d.m.Y') : '-' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Jinsi</div>
                        <div class="info-value">{{ $application->gender == 'male' ? 'Erkak' : 'Ayol' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-label">JSHSHIR</div>
                        <div class="info-value">{{ $application->passport_number ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="info-card">
                <h5 class="mb-4">
                    <i class="fas fa-phone text-primary me-2"></i>
                    Aloqa ma'lumotlari
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Telefon</div>
                        <div class="info-value">{{ $application->phone }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $application->email }}</div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="info-label">Manzil</div>
                        <div class="info-value">{{ $application->address ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Education Information -->
            <div class="info-card">
                <h5 class="mb-4">
                    <i class="fas fa-graduation-cap text-primary me-2"></i>
                    Ta'lim ma'lumotlari
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Fakultet</div>
                        <div class="info-value">{{ $application->faculty->name_uz ?? '-' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Mutaxassislik</div>
                        <div class="info-value">{{ $application->specialty->name_uz ?? '-' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Ta'lim shakli</div>
                        <div class="info-value">
                            @if($application->education_type == 'kunduzgi')
                                Kunduzgi
                            @elseif($application->education_type == 'sirtqi')
                                Sirtqi
                            @elseif($application->education_type == 'kechki')
                                Kechki
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Ta'lim tili</div>
                        <div class="info-value">{{ $application->education_language ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Uploaded Documents -->
            <div class="info-card">
                <h5 class="mb-4">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Yuklangan hujjatlar
                </h5>
                <div class="row">
                    @php
                        $hasDocuments = false;
                        $documents = [];

                        // Check for passport copy
                        if ($application->passport_copy_path) {
                            $documents[] = [
                                'label' => 'Pasport nusxasi',
                                'path' => $application->passport_copy_path,
                                'icon' => 'fa-id-card'
                            ];
                            $hasDocuments = true;
                        }

                        // Check for diploma/CV copy
                        if ($application->diploma_copy_path) {
                            $label = $application->form_version == 2 ? 'CV (Resume)' : 'Diplom nusxasi';
                            $documents[] = [
                                'label' => $label,
                                'path' => $application->diploma_copy_path,
                                'icon' => 'fa-file-pdf'
                            ];
                            $hasDocuments = true;
                        }

                        // Check for certificate copy
                        if ($application->certificate_copy_path) {
                            $documents[] = [
                                'label' => 'Sertifikat nusxasi',
                                'path' => $application->certificate_copy_path,
                                'icon' => 'fa-certificate'
                            ];
                            $hasDocuments = true;
                        }

                        // Check for photo
                        if ($application->photo_path) {
                            $documents[] = [
                                'label' => 'Foto',
                                'path' => $application->photo_path,
                                'icon' => 'fa-image'
                            ];
                            $hasDocuments = true;
                        }
                    @endphp

                    @if($hasDocuments)
                        @foreach($documents as $doc)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100 d-flex flex-column justify-content-between" style="background: #f8f9fa;">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas {{ $doc['icon'] }} text-primary me-2" style="font-size: 1.5rem;"></i>
                                        <span class="fw-bold">{{ $doc['label'] }}</span>
                                    </div>
                                    <small class="text-muted d-block text-truncate">{{ basename($doc['path']) }}</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ asset('storage/' . $doc['path']) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary flex-fill">
                                        <i class="fas fa-eye me-1"></i>Ko'rish
                                    </a>
                                    <a href="{{ asset('storage/' . $doc['path']) }}"
                                       download
                                       class="btn btn-sm btn-primary flex-fill">
                                        <i class="fas fa-download me-1"></i>Yuklab olish
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Hech qanday hujjat yuklanmagan
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Selected Modules (for version 2 forms) -->
            @if($application->form_version == 2 && !empty($application->form_data) && isset($application->form_data['selectedModules']))
            <div class="info-card">
                <h5 class="mb-4">
                    <i class="fas fa-th-large text-primary me-2"></i>
                    Tanlangan modullar
                </h5>
                <div class="row">
                    @php
                        $modules = $application->form_data['selectedModules'] ?? [];
                        // Handle if modules is a JSON string
                        if (is_string($modules)) {
                            $modules = json_decode($modules, true) ?? [];
                        }
                    @endphp
                    @if(is_array($modules) && count($modules) > 0)
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($modules as $module)
                                <span class="badge bg-primary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                    <i class="fas fa-check-circle me-1"></i>{{ $module }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Modullar tanlanmagan
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Status & Actions -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="info-card">
                <h5 class="mb-3">Holati</h5>
                <div class="text-center mb-4">
                    <span class="status-badge status-{{ $application->status }}">
                        @if($application->status == 'pending')
                            Kutilmoqda
                        @elseif($application->status == 'reviewing')
                            Ko'rib chiqilmoqda
                        @elseif($application->status == 'accepted')
                            Qabul qilindi
                        @elseif($application->status == 'rejected')
                            Rad etildi
                        @elseif($application->status == 'waitlist')
                            Kutish ro'yxatida
                        @endif
                    </span>
                </div>

                <form action="{{ route('admission.update-status', $application) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Holatni o'zgartirish</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                            <option value="reviewing" {{ $application->status == 'reviewing' ? 'selected' : '' }}>Ko'rib chiqilmoqda</option>
                            <option value="accepted" {{ $application->status == 'accepted' ? 'selected' : '' }}>Qabul qilindi</option>
                            <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rad etildi</option>
                            <option value="waitlist" {{ $application->status == 'waitlist' ? 'selected' : '' }}>Kutish ro'yxatida</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Izoh</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $application->notes }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                </form>
            </div>

            <!-- Timeline -->
            <div class="info-card">
                <h5 class="mb-3">
                    <i class="fas fa-clock text-primary me-2"></i>
                    Vaqt chizig'i
                </h5>
                <div class="mb-3">
                    <div class="info-label">Ariza topshirildi</div>
                    <div class="info-value">{{ $application->created_at->format('d.m.Y H:i') }}</div>
                </div>
                @if($application->updated_at != $application->created_at)
                <div class="mb-3">
                    <div class="info-label">Oxirgi yangilanish</div>
                    <div class="info-value">{{ $application->updated_at->format('d.m.Y H:i') }}</div>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="info-card">
                <h5 class="mb-3">Amallar</h5>

                @if($application->status !== 'accepted')
                <button type="button" class="btn btn-success w-100 mb-3" data-bs-toggle="modal" data-bs-target="#enrollModal">
                    <i class="fas fa-user-graduate me-2"></i>Qabul qilish va talaba sifatida ro'yxatga olish
                </button>
                @else
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle me-2"></i>
                    Bu abituriyent allaqachon qabul qilingan
                </div>
                @endif

                <a href="{{ route('admission.export-pdf', $application) }}" class="btn btn-outline-primary w-100 mb-2" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i>PDF ko'rish
                </a>
                <a href="{{ route('admission.export-pdf', $application) }}?download=1" class="btn btn-primary w-100">
                    <i class="fas fa-download me-2"></i>PDF yuklab olish
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Enroll Modal -->
@if($application->status !== 'accepted')
<div class="modal fade" id="enrollModal" tabindex="-1" aria-labelledby="enrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="enrollModalLabel">
                    <i class="fas fa-user-graduate me-2"></i>Talaba sifatida ro'yxatga olish
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admission.accept-enroll', $application) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Applicant Info Summary -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-user me-2"></i>F.I.O:</strong>
                                {{ $application->full_name }}
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-graduation-cap me-2"></i>Fakultet:</strong>
                                {{ $application->faculty->name_uz ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Student ID -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-id-badge me-1"></i>Talaba ID raqami
                            </label>
                            <input type="text" name="student_id" class="form-control form-control-lg"
                                   value="{{ $suggestedStudentId ?? '' }}"
                                   placeholder="Avtomatik hosil qilinadi">
                            <small class="text-muted">Bo'sh qoldirsangiz avtomatik hosil bo'ladi</small>
                        </div>

                        <!-- Group Selection -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-users me-1"></i>Guruhni tanlang
                            </label>
                            <select name="group_id" class="form-select form-select-lg">
                                <option value="">-- Guruhsiz (keyinroq tayinlanadi) --</option>
                                @foreach($groups ?? [] as $group)
                                    <option value="{{ $group->id }}">
                                        {{ $group->name }}
                                        @if($group->course) ({{ $group->course }}-kurs) @endif
                                        @if($group->current_students && $group->max_students)
                                            [{{ $group->current_students }}/{{ $group->max_students }}]
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if(empty($groups) || count($groups) == 0)
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Bu fakultet/yo'nalish uchun guruhlar topilmadi
                                </small>
                            @endif
                        </div>
                    </div>

                    <!-- Login Info -->
                    <div class="alert alert-warning mt-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-key me-2"></i>Talaba uchun login ma'lumotlari:
                        </h6>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Login (Email):</strong>
                                <code class="ms-2">{{ $application->email }}</code>
                            </div>
                            <div class="col-md-6">
                                <strong>Parol:</strong>
                                <code class="ms-2">{{ $application->passport_series }}{{ $application->passport_number }}</code>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Bekor qilish
                    </button>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check me-2"></i>Tasdiqlash va ro'yxatga olish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
