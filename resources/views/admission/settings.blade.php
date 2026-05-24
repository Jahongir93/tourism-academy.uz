@extends('layouts.dashboard-new')

@section('content')
<style>
    :root {
        --primary-green: #10b981;
        --primary-dark-green: #059669;
        --secondary-green: #34d399;
        --light-green: #d1fae5;
        --lighter-green: #ecfdf5;
        --accent-green: #6ee7b7;
    }

    .settings-container {
        background: var(--lighter-green);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .settings-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.1);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .settings-header {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark-green));
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .nav-pills .nav-link {
        color: var(--primary-dark-green);
        background-color: var(--lighter-green);
        border-radius: 8px;
        margin-bottom: 0.5rem;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link:hover {
        background-color: var(--light-green);
        transform: translateX(5px);
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
        color: white;
    }

    .form-control, .form-select {
        border: 2px solid var(--light-green);
        border-radius: 8px;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }

    .btn-success-custom {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark-green));
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
    }

    .switch-container {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    .switch-input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .switch-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    .switch-input:checked + .switch-slider {
        background-color: var(--primary-green);
    }

    .switch-input:checked + .switch-slider:before {
        transform: translateX(30px);
    }

    .deadline-input {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .requirement-item {
        background: var(--lighter-green);
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .add-requirement {
        background: var(--light-green);
        border: 2px dashed var(--primary-green);
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .add-requirement:hover {
        background: var(--accent-green);
        border-style: solid;
    }
</style>

<div class="settings-container">
    <div class="container">
        <div class="settings-header">
            <h1 class="mb-0"><i class="fas fa-cog me-2"></i> Qabul Parametrlari</h1>
            <p class="mb-0 mt-2 opacity-90">Online qabul tizimini sozlash va boshqarish</p>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="settings-card">
                    <h5 class="mb-3 text-success">Sozlamalar</h5>
                    <ul class="nav nav-pills flex-column" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active w-100 text-start" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button">
                                <i class="fas fa-sliders-h me-2"></i> Umumiy
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100 text-start" id="deadlines-tab" data-bs-toggle="pill" data-bs-target="#deadlines" type="button">
                                <i class="fas fa-calendar-alt me-2"></i> Muddatlar
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100 text-start" id="requirements-tab" data-bs-toggle="pill" data-bs-target="#requirements" type="button">
                                <i class="fas fa-list-check me-2"></i> Talablar
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100 text-start" id="notifications-tab" data-bs-toggle="pill" data-bs-target="#notifications" type="button">
                                <i class="fas fa-bell me-2"></i> Xabarnomalar
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100 text-start" id="payment-tab" data-bs-toggle="pill" data-bs-target="#payment" type="button">
                                <i class="fas fa-credit-card me-2"></i> To'lov
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-9">
                <div class="settings-card">
                    <form action="{{ route('admission.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="tab-content" id="settingsTabContent">
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                <h4 class="mb-4 text-success">Umumiy Sozlamalar</h4>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Qabul holati</label>
                                        <div class="d-flex align-items-center">
                                            <label class="switch-container">
                                                <input type="checkbox" class="switch-input" name="admission_open" {{ ($settings['admission_open'] ?? false) ? 'checked' : '' }}>
                                                <span class="switch-slider"></span>
                                            </label>
                                            <span class="ms-3">{{ ($settings['admission_open'] ?? false) ? 'Ochiq' : 'Yopiq' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Maksimal arizalar soni</label>
                                        <input type="number" class="form-control" name="max_applications" value="{{ $settings['max_applications'] ?? 1000 }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">O'quv yili</label>
                                        <select class="form-select" name="academic_year">
                                            <option value="2024-2025" {{ ($settings['academic_year'] ?? '') == '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                                            <option value="2025-2026" {{ ($settings['academic_year'] ?? '') == '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Qabul turi</label>
                                        <select class="form-select" name="admission_type">
                                            <option value="bakalavr" {{ ($settings['admission_type'] ?? '') == 'bakalavr' ? 'selected' : '' }}>Bakalavr</option>
                                            <option value="magistr" {{ ($settings['admission_type'] ?? '') == 'magistr' ? 'selected' : '' }}>Magistratura</option>
                                            <option value="both" {{ ($settings['admission_type'] ?? '') == 'both' ? 'selected' : '' }}>Ikkalasi</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Qabul haqida ma'lumot</label>
                                    <textarea class="form-control" name="admission_info" rows="4">{{ $settings['admission_info'] ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="deadlines" role="tabpanel">
                                <h4 class="mb-4 text-success">Muddatlar</h4>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Qabul boshlanishi</label>
                                        <input type="datetime-local" class="form-control" name="admission_start" value="{{ $settings['admission_start'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Qabul tugashi</label>
                                        <input type="datetime-local" class="form-control" name="admission_end" value="{{ $settings['admission_end'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Hujjatlar qabuli boshlanishi</label>
                                        <input type="datetime-local" class="form-control" name="document_start" value="{{ $settings['document_start'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Hujjatlar qabuli tugashi</label>
                                        <input type="datetime-local" class="form-control" name="document_end" value="{{ $settings['document_end'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Imtihon sanasi</label>
                                        <input type="datetime-local" class="form-control" name="exam_date" value="{{ $settings['exam_date'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Natijalar e'lon qilish</label>
                                        <input type="datetime-local" class="form-control" name="results_date" value="{{ $settings['results_date'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="requirements" role="tabpanel">
                                <h4 class="mb-4 text-success">Qabul Talablari</h4>

                                <div class="mb-4">
                                    <h5>Hujjatlar ro'yxati</h5>
                                    <div id="documentsList">
                                        <div class="requirement-item">
                                            <span><i class="fas fa-file me-2"></i> Pasport nusxasi</span>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="required_docs[passport]" checked>
                                            </div>
                                        </div>
                                        <div class="requirement-item">
                                            <span><i class="fas fa-graduation-cap me-2"></i> Diplom yoki attestat</span>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="required_docs[diploma]" checked>
                                            </div>
                                        </div>
                                        <div class="requirement-item">
                                            <span><i class="fas fa-camera me-2"></i> 3x4 rasm</span>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="required_docs[photo]" checked>
                                            </div>
                                        </div>
                                        <div class="requirement-item">
                                            <span><i class="fas fa-certificate me-2"></i> Til sertifikati</span>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="required_docs[language_cert]">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Minimal ball</label>
                                    <input type="number" class="form-control" name="min_score" value="{{ $settings['min_score'] ?? 56 }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Yosh chegarasi</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="number" class="form-control" name="min_age" placeholder="Minimal yosh" value="{{ $settings['min_age'] ?? 16 }}">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control" name="max_age" placeholder="Maksimal yosh" value="{{ $settings['max_age'] ?? 35 }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="notifications" role="tabpanel">
                                <h4 class="mb-4 text-success">Xabarnoma Sozlamalari</h4>

                                <div class="mb-3">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="emailNotifications" name="email_notifications" {{ ($settings['email_notifications'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="emailNotifications">
                                            Email orqali xabarnoma yuborish
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="smsNotifications" name="sms_notifications" {{ ($settings['sms_notifications'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="smsNotifications">
                                            SMS orqali xabarnoma yuborish
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Admin email manzili</label>
                                    <input type="email" class="form-control" name="admin_email" value="{{ $settings['admin_email'] ?? '' }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Xabarnoma shabloni</label>
                                    <textarea class="form-control" name="notification_template" rows="5">{{ $settings['notification_template'] ?? 'Hurmatli {name}, sizning arizangiz muvaffaqiyatli qabul qilindi. Ariza raqami: {application_number}' }}</textarea>
                                    <small class="text-muted">Foydalanish mumkin: {name}, {application_number}, {status}, {date}</small>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="payment" role="tabpanel">
                                <h4 class="mb-4 text-success">To'lov Sozlamalari</h4>

                                <div class="mb-3">
                                    <label class="form-label">Ariza to'lovi (so'm)</label>
                                    <input type="number" class="form-control" name="application_fee" value="{{ $settings['application_fee'] ?? 50000 }}">
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="paymentRequired" name="payment_required" {{ ($settings['payment_required'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="paymentRequired">
                                            To'lov majburiy
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">To'lov tizimlari</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="payment_methods[payme]" id="payme" {{ ($settings['payment_methods']['payme'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payme">Payme</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="payment_methods[click]" id="click" {{ ($settings['payment_methods']['click'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="click">Click</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="payment_methods[bank]" id="bank" {{ ($settings['payment_methods']['bank'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="bank">Bank o'tkazmasi</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bank rekvizitlari</label>
                                    <textarea class="form-control" name="bank_details" rows="4">{{ $settings['bank_details'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success-custom">
                                <i class="fas fa-save me-2"></i> Saqlash
                            </button>
                            <button type="button" class="btn btn-secondary ms-2" onclick="location.reload()">
                                <i class="fas fa-undo me-2"></i> Bekor qilish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const switches = document.querySelectorAll('.switch-input');
    switches.forEach(switchInput => {
        switchInput.addEventListener('change', function() {
            const label = this.closest('.d-flex').querySelector('span');
            if (label) {
                label.textContent = this.checked ? 'Ochiq' : 'Yopiq';
            }
        });
    });
});
</script>
@endsection