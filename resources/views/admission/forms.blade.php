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

    .forms-container {
        background: var(--lighter-green);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .forms-header {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark-green));
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .form-builder-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.1);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .field-item {
        background: var(--lighter-green);
        border: 2px solid var(--light-green);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        cursor: move;
        transition: all 0.3s ease;
    }

    .field-item:hover {
        background: var(--light-green);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.2);
    }

    .field-item.dragging {
        opacity: 0.5;
    }

    .field-item.selected {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    .field-controls {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        margin-top: 0.5rem;
    }

    .field-type-badge {
        background: var(--primary-green);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .step-badge {
        background: #3b82f6;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .required-badge {
        background: #ef4444;
        color: white;
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
    }

    .add-field-btn {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark-green));
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .add-field-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
    }

    .sidebar-fields {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.1);
        position: sticky;
        top: 2rem;
    }

    .field-template {
        background: var(--lighter-green);
        border: 2px dashed var(--primary-green);
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .field-template:hover {
        background: var(--light-green);
        border-style: solid;
    }

    .drop-zone {
        min-height: 200px;
        border: 3px dashed var(--primary-green);
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .drop-zone.drag-over {
        background: var(--light-green);
        border-style: solid;
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
        color: white;
    }

    .step-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .step-tab {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        background: var(--lighter-green);
        border: 2px solid var(--light-green);
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .step-tab.active {
        background: var(--primary-green);
        color: white;
        border-color: var(--primary-green);
    }

    .step-tab:hover:not(.active) {
        background: var(--light-green);
    }

    .settings-section {
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .settings-section:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }

    .file-config-section {
        background: #fef3c7;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loading-spinner {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        text-align: center;
    }
</style>

<div class="forms-container">
    <div class="container-fluid">
        <div class="forms-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h1 class="mb-0"><i class="fas fa-edit me-2"></i> Qabul Formasi Sozlamalari</h1>
                    <p class="mb-0 mt-2 opacity-90">Online ariza formasini sozlash va tahrirlash</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admission.apply') }}" target="_blank" class="btn btn-light">
                        <i class="fas fa-external-link-alt me-2"></i> Formani Ko'rish
                    </a>
                    <button class="btn btn-warning" onclick="previewForm()">
                        <i class="fas fa-eye me-2"></i> Preview
                    </button>
                </div>
            </div>
        </div>

        <!-- Step Tabs -->
        <div class="form-builder-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 text-success">Bosqichlar</h5>
                <span class="text-muted small">Jami maydonlar: <strong id="totalFieldsCount">0</strong></span>
            </div>
            <div class="step-tabs" id="stepTabs">
                <div class="step-tab active" data-step="1" onclick="switchStep(1)">
                    <i class="fas fa-user me-1"></i> 1. Shaxsiy ma'lumotlar
                    <span class="badge bg-secondary ms-1" id="step1Count">0</span>
                </div>
                <div class="step-tab" data-step="2" onclick="switchStep(2)">
                    <i class="fas fa-graduation-cap me-1"></i> 2. Ta'lim
                    <span class="badge bg-secondary ms-1" id="step2Count">0</span>
                </div>
                <div class="step-tab" data-step="3" onclick="switchStep(3)">
                    <i class="fas fa-university me-1"></i> 3. Yo'nalish
                    <span class="badge bg-secondary ms-1" id="step3Count">0</span>
                </div>
                <div class="step-tab" data-step="4" onclick="switchStep(4)">
                    <i class="fas fa-file-alt me-1"></i> 4. Hujjatlar
                    <span class="badge bg-secondary ms-1" id="step4Count">0</span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Field Templates Sidebar -->
            <div class="col-lg-2 col-md-3">
                <div class="sidebar-fields">
                    <h6 class="mb-3 text-success"><i class="fas fa-plus-circle me-1"></i> Maydon Qo'shish</h6>

                    <div class="field-template" data-field-type="text" onclick="addFieldToForm('text')">
                        <i class="fas fa-font me-2"></i> Matn
                    </div>

                    <div class="field-template" data-field-type="email" onclick="addFieldToForm('email')">
                        <i class="fas fa-envelope me-2"></i> Email
                    </div>

                    <div class="field-template" data-field-type="phone" onclick="addFieldToForm('phone')">
                        <i class="fas fa-phone me-2"></i> Telefon
                    </div>

                    <div class="field-template" data-field-type="date" onclick="addFieldToForm('date')">
                        <i class="fas fa-calendar me-2"></i> Sana
                    </div>

                    <div class="field-template" data-field-type="select" onclick="addFieldToForm('select')">
                        <i class="fas fa-list me-2"></i> Tanlash
                    </div>

                    <div class="field-template" data-field-type="radio" onclick="addFieldToForm('radio')">
                        <i class="fas fa-dot-circle me-2"></i> Radio
                    </div>

                    <div class="field-template" data-field-type="checkbox" onclick="addFieldToForm('checkbox')">
                        <i class="fas fa-check-square me-2"></i> Checkbox
                    </div>

                    <div class="field-template" data-field-type="textarea" onclick="addFieldToForm('textarea')">
                        <i class="fas fa-align-left me-2"></i> Textarea
                    </div>

                    <div class="field-template" data-field-type="file" onclick="addFieldToForm('file')">
                        <i class="fas fa-file-upload me-2"></i> Fayl
                    </div>

                    <div class="field-template" data-field-type="heading" onclick="addFieldToForm('heading')">
                        <i class="fas fa-heading me-2"></i> Sarlavha
                    </div>
                </div>
            </div>

            <!-- Form Builder Area -->
            <div class="col-lg-6 col-md-5">
                <div class="form-builder-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0 text-success">
                            <span id="currentStepTitle">1-Bosqich: Shaxsiy ma'lumotlar</span>
                        </h5>
                    </div>

                    <div class="drop-zone" id="dropZone">
                        <div class="text-muted">
                            <i class="fas fa-hand-pointer fa-2x mb-2"></i>
                            <p>Chap tarafdagi maydonlarni bosing yoki bu yerga sudrab tashlang</p>
                        </div>
                    </div>

                    <div id="formFields"></div>

                    <div class="mt-4 d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-success-custom" onclick="saveForm()" id="saveBtn">
                            <i class="fas fa-save me-2"></i> Saqlash
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetToDefaults()">
                            <i class="fas fa-undo me-2"></i> Default holatga qaytarish
                        </button>
                    </div>
                </div>
            </div>

            <!-- Field Settings Panel -->
            <div class="col-lg-4 col-md-4">
                <div class="form-builder-card" style="position: sticky; top: 2rem;">
                    <h5 class="mb-3 text-success"><i class="fas fa-cog me-2"></i> Maydon Sozlamalari</h5>

                    <div id="fieldSettings" style="display: none;">
                        <!-- Basic Settings -->
                        <div class="settings-section">
                            <h6 class="text-muted mb-3"><i class="fas fa-info-circle me-1"></i> Asosiy</h6>

                            <div class="mb-3">
                                <label class="form-label small">Maydon kaliti (field_key) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="fieldKey" placeholder="masalan: first_name">
                                <small class="text-muted">Faqat lotin harflari va pastki chiziq</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Turi</label>
                                <select class="form-select form-select-sm" id="fieldType" onchange="onFieldTypeChange()">
                                    <option value="text">Matn</option>
                                    <option value="email">Email</option>
                                    <option value="phone">Telefon</option>
                                    <option value="date">Sana</option>
                                    <option value="select">Tanlash ro'yxati</option>
                                    <option value="radio">Radio tugmalar</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="textarea">Ko'p qatorli matn</option>
                                    <option value="file">Fayl yuklash</option>
                                    <option value="heading">Sarlavha</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Bosqich</label>
                                <select class="form-select form-select-sm" id="fieldStep">
                                    <option value="1">1 - Shaxsiy ma'lumotlar</option>
                                    <option value="2">2 - Ta'lim ma'lumotlari</option>
                                    <option value="3">3 - Yo'nalish tanlash</option>
                                    <option value="4">4 - Hujjatlar</option>
                                </select>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="fieldRequired">
                                <label class="form-check-label" for="fieldRequired">
                                    Majburiy maydon
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="fieldActive" checked>
                                <label class="form-check-label" for="fieldActive">
                                    Faol (formada ko'rinadi)
                                </label>
                            </div>
                        </div>

                        <!-- Labels -->
                        <div class="settings-section">
                            <h6 class="text-muted mb-3"><i class="fas fa-language me-1"></i> Sarlavhalar</h6>

                            <div class="mb-2">
                                <label class="form-label small">O'zbekcha <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="labelUz" placeholder="Maydon nomi">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small">Ruscha</label>
                                <input type="text" class="form-control form-control-sm" id="labelRu" placeholder="Название поля">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small">Inglizcha</label>
                                <input type="text" class="form-control form-control-sm" id="labelEn" placeholder="Field label">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small">Placeholder</label>
                                <input type="text" class="form-control form-control-sm" id="fieldPlaceholder" placeholder="Placeholder matn">
                            </div>
                        </div>

                        <!-- Options (for select, radio, checkbox) -->
                        <div class="settings-section" id="optionsSection" style="display: none;">
                            <h6 class="text-muted mb-3"><i class="fas fa-list-ul me-1"></i> Variantlar</h6>
                            <div id="optionsList"></div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addOption()">
                                <i class="fas fa-plus me-1"></i> Variant qo'shish
                            </button>
                        </div>

                        <!-- File Config (for file type) -->
                        <div class="settings-section" id="fileConfigSection" style="display: none;">
                            <h6 class="text-muted mb-3"><i class="fas fa-file-alt me-1"></i> Fayl sozlamalari</h6>

                            <div class="mb-2">
                                <label class="form-label small">Maksimal hajm (KB)</label>
                                <input type="number" class="form-control form-control-sm" id="fileMaxSize" value="5120" min="100" max="51200">
                                <small class="text-muted">5120 KB = 5 MB</small>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small">Ruxsat etilgan formatlar</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input file-ext" type="checkbox" id="extJpg" value="jpg" checked>
                                        <label class="form-check-label small" for="extJpg">JPG</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input file-ext" type="checkbox" id="extJpeg" value="jpeg" checked>
                                        <label class="form-check-label small" for="extJpeg">JPEG</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input file-ext" type="checkbox" id="extPng" value="png" checked>
                                        <label class="form-check-label small" for="extPng">PNG</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input file-ext" type="checkbox" id="extPdf" value="pdf" checked>
                                        <label class="form-check-label small" for="extPdf">PDF</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input file-ext" type="checkbox" id="extDoc" value="doc">
                                        <label class="form-check-label small" for="extDoc">DOC</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input file-ext" type="checkbox" id="extDocx" value="docx">
                                        <label class="form-check-label small" for="extDocx">DOCX</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small">Saqlash papkasi</label>
                                <input type="text" class="form-control form-control-sm" id="fileStoragePath" value="admission/uploads" placeholder="admission/uploads">
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-success btn-sm flex-grow-1" onclick="updateField()">
                                <i class="fas fa-check me-1"></i> Yangilash
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteSelectedField()">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="text-center text-muted py-4" id="noFieldSelected">
                        <i class="fas fa-mouse-pointer fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">Sozlash uchun maydon tanlang</p>
                    </div>
                </div>

                <!-- Contact Settings Card -->
                <div class="form-builder-card mt-4" style="border-top: 3px solid #3b82f6;">
                    <h5 class="mb-3" style="color: #3b82f6;"><i class="fas fa-phone-alt me-2"></i> Kontakt Ma'lumotlari</h5>
                    <p class="text-muted small mb-3">Ariza sahifasida ko'rsatiladigan yordam bo'limi ma'lumotlari</p>

                    <div class="mb-3">
                        <label class="form-label small"><i class="fas fa-phone me-1 text-success"></i> Telefon</label>
                        <input type="text" class="form-control form-control-sm" id="contactPhone" placeholder="+998 90 123-45-67" value="{{ $contactSettings['phone'] ?? '+998 90 123-45-67' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small"><i class="fab fa-telegram me-1 text-info"></i> Telegram</label>
                        <input type="text" class="form-control form-control-sm" id="contactTelegram" placeholder="@username" value="{{ $contactSettings['telegram'] ?? '@tourism_admission' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small"><i class="fas fa-envelope me-1 text-primary"></i> Email</label>
                        <input type="email" class="form-control form-control-sm" id="contactEmail" placeholder="email@example.com" value="{{ $contactSettings['email'] ?? 'admission@tourism.uz' }}">
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="contactShowHelp" {{ ($contactSettings['show_help_section'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="contactShowHelp">
                            Yordam bo'limini ko'rsatish
                        </label>
                    </div>

                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> O'zgarishlarni saqlash uchun yuqoridagi "Saqlash" tugmasini bosing</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="loading-spinner">
        <div class="spinner-border text-success mb-3" role="status">
            <span class="visually-hidden">Yuklanmoqda...</span>
        </div>
        <p class="mb-0" id="loadingText">Yuklanmoqda...</p>
    </div>
</div>

<script>
// Global state
let allFields = [];
let currentStep = 1;
let selectedFieldId = null;
let fieldIdCounter = 1;
let contactSettings = {
    phone: '+998 90 123-45-67',
    telegram: '@tourism_admission',
    email: 'admission@tourism.uz',
    show_help_section: true
};

const stepTitles = {
    1: "Shaxsiy ma'lumotlar",
    2: "Ta'lim ma'lumotlari",
    3: "Yo'nalish tanlash",
    4: "Hujjatlar"
};

const fieldTypeLabels = {
    text: "Matn",
    email: "Email",
    phone: "Telefon",
    date: "Sana",
    select: "Tanlash",
    radio: "Radio",
    checkbox: "Checkbox",
    textarea: "Textarea",
    file: "Fayl",
    heading: "Sarlavha"
};

const fieldTypeIcons = {
    text: "fa-font",
    email: "fa-envelope",
    phone: "fa-phone",
    date: "fa-calendar",
    select: "fa-list",
    radio: "fa-dot-circle",
    checkbox: "fa-check-square",
    textarea: "fa-align-left",
    file: "fa-file-upload",
    heading: "fa-heading"
};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadSavedForm();
    initDragAndDrop();
});

// Load saved form from database
function loadSavedForm() {
    showLoading('Maydonlar yuklanmoqda...');

    fetch('{{ route("admission.forms") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();

        if (data.fields && Array.isArray(data.fields)) {
            allFields = data.fields.map((field, index) => ({
                id: field.id || ('field_' + (index + 1)),
                db_id: field.id || null,
                field_key: field.field_key,
                field_type: field.field_type,
                label_uz: field.label_uz,
                label_ru: field.label_ru || '',
                label_en: field.label_en || '',
                placeholder: field.placeholder || '',
                options: field.options || [],
                is_required: field.is_required,
                step: field.step,
                sort_order: field.sort_order || index,
                file_config: field.file_config || {},
                is_active: field.is_active !== false
            }));

            fieldIdCounter = allFields.length + 1;
        }

        // Load contact settings
        if (data.contactSettings) {
            contactSettings = data.contactSettings;
            updateContactSettingsUI();
        }

        renderCurrentStep();
        updateStepCounts();
    })
    .catch(error => {
        hideLoading();
        console.error('Error loading form:', error);
        showToast('Maydonlarni yuklashda xatolik', 'error');
    });
}

// Switch between steps
function switchStep(step) {
    currentStep = step;

    // Update tab UI
    document.querySelectorAll('.step-tab').forEach(tab => {
        tab.classList.toggle('active', parseInt(tab.dataset.step) === step);
    });

    // Update title
    document.getElementById('currentStepTitle').textContent = `${step}-Bosqich: ${stepTitles[step]}`;

    // Deselect field
    selectedFieldId = null;
    document.getElementById('fieldSettings').style.display = 'none';
    document.getElementById('noFieldSelected').style.display = 'block';

    renderCurrentStep();
}

// Render fields for current step
function renderCurrentStep() {
    const stepFields = allFields
        .filter(f => f.step === currentStep)
        .sort((a, b) => a.sort_order - b.sort_order);

    const container = document.getElementById('formFields');
    const dropZone = document.getElementById('dropZone');

    if (stepFields.length === 0) {
        dropZone.style.display = 'block';
        container.innerHTML = '';
        return;
    }

    dropZone.style.display = 'none';

    let html = '';
    stepFields.forEach((field, index) => {
        const isSelected = field.id === selectedFieldId;
        const icon = fieldTypeIcons[field.field_type] || 'fa-question';
        const typeLabel = fieldTypeLabels[field.field_type] || field.field_type;

        html += `
            <div class="field-item ${isSelected ? 'selected' : ''} ${!field.is_active ? 'opacity-50' : ''}"
                 data-field-id="${field.id}"
                 data-index="${index}"
                 onclick="selectField('${field.id}')"
                 draggable="true">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <i class="fas ${icon} text-success"></i>
                            <span class="field-type-badge">${typeLabel}</span>
                            <strong>${field.label_uz}</strong>
                            ${field.is_required ? '<span class="required-badge">Majburiy</span>' : ''}
                            ${!field.is_active ? '<span class="badge bg-secondary">Nofaol</span>' : ''}
                        </div>
                        <small class="text-muted">key: ${field.field_key}</small>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); selectField('${field.id}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); removeField('${field.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    initFieldDragging();
}

// Update step counts
function updateStepCounts() {
    for (let step = 1; step <= 4; step++) {
        const count = allFields.filter(f => f.step === step && f.is_active).length;
        const countEl = document.getElementById(`step${step}Count`);
        if (countEl) countEl.textContent = count;
    }

    const totalActive = allFields.filter(f => f.is_active).length;
    document.getElementById('totalFieldsCount').textContent = totalActive;
}

// Add new field
function addFieldToForm(type) {
    const newField = {
        id: 'new_' + fieldIdCounter++,
        db_id: null,
        field_key: generateFieldKey(type),
        field_type: type,
        label_uz: getDefaultLabel(type),
        label_ru: '',
        label_en: '',
        placeholder: '',
        options: (type === 'select' || type === 'radio' || type === 'checkbox') ? ['Variant 1', 'Variant 2'] : [],
        is_required: false,
        step: currentStep,
        sort_order: allFields.filter(f => f.step === currentStep).length,
        file_config: type === 'file' ? { max_size: 5120, allowed_extensions: ['jpg', 'jpeg', 'png', 'pdf'], storage_path: 'admission/uploads' } : {},
        is_active: true
    };

    allFields.push(newField);
    renderCurrentStep();
    updateStepCounts();
    selectField(newField.id);
}

function generateFieldKey(type) {
    const timestamp = Date.now();
    return `${type}_${timestamp}`;
}

function getDefaultLabel(type) {
    const labels = {
        text: "Matn maydoni",
        email: "Email manzil",
        phone: "Telefon raqami",
        date: "Sana",
        select: "Tanlash",
        radio: "Tanlov",
        checkbox: "Belgilash",
        textarea: "Izoh",
        file: "Fayl yuklash",
        heading: "Bo'lim sarlavhasi"
    };
    return labels[type] || "Yangi maydon";
}

// Select field for editing
function selectField(fieldId) {
    selectedFieldId = fieldId;
    const field = allFields.find(f => f.id === fieldId);

    if (!field) return;

    // Update UI
    document.querySelectorAll('.field-item').forEach(item => {
        item.classList.toggle('selected', item.dataset.fieldId === fieldId);
    });

    document.getElementById('fieldSettings').style.display = 'block';
    document.getElementById('noFieldSelected').style.display = 'none';

    // Populate form
    document.getElementById('fieldKey').value = field.field_key;
    document.getElementById('fieldType').value = field.field_type;
    document.getElementById('fieldStep').value = field.step;
    document.getElementById('fieldRequired').checked = field.is_required;
    document.getElementById('fieldActive').checked = field.is_active;
    document.getElementById('labelUz').value = field.label_uz;
    document.getElementById('labelRu').value = field.label_ru || '';
    document.getElementById('labelEn').value = field.label_en || '';
    document.getElementById('fieldPlaceholder').value = field.placeholder || '';

    // Show/hide options section
    const showOptions = ['select', 'radio', 'checkbox'].includes(field.field_type);
    document.getElementById('optionsSection').style.display = showOptions ? 'block' : 'none';
    if (showOptions) {
        renderOptions(field.options || []);
    }

    // Show/hide file config section
    const showFileConfig = field.field_type === 'file';
    document.getElementById('fileConfigSection').style.display = showFileConfig ? 'block' : 'none';
    if (showFileConfig) {
        const config = field.file_config || {};
        document.getElementById('fileMaxSize').value = config.max_size || 5120;
        document.getElementById('fileStoragePath').value = config.storage_path || 'admission/uploads';

        // Set extension checkboxes
        const extensions = config.allowed_extensions || ['jpg', 'jpeg', 'png', 'pdf'];
        document.querySelectorAll('.file-ext').forEach(checkbox => {
            checkbox.checked = extensions.includes(checkbox.value);
        });
    }
}

// Update selected field
function updateField() {
    if (!selectedFieldId) return;

    const fieldIndex = allFields.findIndex(f => f.id === selectedFieldId);
    if (fieldIndex === -1) return;

    const field = allFields[fieldIndex];
    const newStep = parseInt(document.getElementById('fieldStep').value);
    const stepChanged = field.step !== newStep;

    // Validate field_key
    const newKey = document.getElementById('fieldKey').value.trim();
    if (!newKey || !/^[a-z][a-z0-9_]*$/.test(newKey)) {
        showToast('Field key noto\'g\'ri formatda. Faqat kichik lotin harflari, raqamlar va _ ishlatilsin.', 'error');
        return;
    }

    // Check for duplicate keys
    const isDuplicate = allFields.some(f => f.id !== selectedFieldId && f.field_key === newKey);
    if (isDuplicate) {
        showToast('Bu field_key allaqachon mavjud!', 'error');
        return;
    }

    // Update field
    field.field_key = newKey;
    field.field_type = document.getElementById('fieldType').value;
    field.step = newStep;
    field.is_required = document.getElementById('fieldRequired').checked;
    field.is_active = document.getElementById('fieldActive').checked;
    field.label_uz = document.getElementById('labelUz').value;
    field.label_ru = document.getElementById('labelRu').value;
    field.label_en = document.getElementById('labelEn').value;
    field.placeholder = document.getElementById('fieldPlaceholder').value;

    // Update options if applicable
    if (['select', 'radio', 'checkbox'].includes(field.field_type)) {
        field.options = getOptionsFromUI();
    }

    // Update file config if applicable
    if (field.field_type === 'file') {
        field.file_config = {
            max_size: parseInt(document.getElementById('fileMaxSize').value) || 5120,
            storage_path: document.getElementById('fileStoragePath').value || 'admission/uploads',
            allowed_extensions: Array.from(document.querySelectorAll('.file-ext:checked')).map(cb => cb.value)
        };
    }

    // If step changed, update sort order and switch to new step
    if (stepChanged) {
        field.sort_order = allFields.filter(f => f.step === newStep).length;
        currentStep = newStep;
        switchStep(newStep);
    } else {
        renderCurrentStep();
    }

    updateStepCounts();
    showToast('Maydon yangilandi', 'success');
}

// Field type change handler
function onFieldTypeChange() {
    const type = document.getElementById('fieldType').value;

    const showOptions = ['select', 'radio', 'checkbox'].includes(type);
    document.getElementById('optionsSection').style.display = showOptions ? 'block' : 'none';

    const showFileConfig = type === 'file';
    document.getElementById('fileConfigSection').style.display = showFileConfig ? 'block' : 'none';
}

// Options management
function renderOptions(options) {
    const container = document.getElementById('optionsList');
    let html = '';

    options.forEach((option, index) => {
        html += `
            <div class="input-group input-group-sm mb-2">
                <input type="text" class="form-control option-input" value="${escapeHtml(option)}" data-index="${index}">
                <button class="btn btn-outline-danger" type="button" onclick="removeOption(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    });

    container.innerHTML = html;
}

function getOptionsFromUI() {
    return Array.from(document.querySelectorAll('.option-input')).map(input => input.value.trim()).filter(v => v);
}

function addOption() {
    const field = allFields.find(f => f.id === selectedFieldId);
    if (!field) return;

    field.options = field.options || [];
    field.options.push('Yangi variant');
    renderOptions(field.options);
}

function removeOption(index) {
    const field = allFields.find(f => f.id === selectedFieldId);
    if (!field || !field.options) return;

    field.options.splice(index, 1);
    renderOptions(field.options);
}

// Remove field
function removeField(fieldId) {
    if (!confirm("Bu maydonni o'chirmoqchimisiz?")) return;

    allFields = allFields.filter(f => f.id !== fieldId);

    if (selectedFieldId === fieldId) {
        selectedFieldId = null;
        document.getElementById('fieldSettings').style.display = 'none';
        document.getElementById('noFieldSelected').style.display = 'block';
    }

    renderCurrentStep();
    updateStepCounts();
}

function deleteSelectedField() {
    if (selectedFieldId) {
        removeField(selectedFieldId);
    }
}

// Save form to database
function saveForm() {
    showLoading('Saqlanmoqda...');

    // Update options from UI before saving
    if (selectedFieldId) {
        const field = allFields.find(f => f.id === selectedFieldId);
        if (field && ['select', 'radio', 'checkbox'].includes(field.field_type)) {
            field.options = getOptionsFromUI();
        }
    }

    // Prepare data for saving
    const fieldsToSave = allFields.map((field, index) => ({
        id: field.db_id,
        field_key: field.field_key,
        field_type: field.field_type,
        label_uz: field.label_uz,
        label_ru: field.label_ru || null,
        label_en: field.label_en || null,
        placeholder: field.placeholder || null,
        options: field.options && field.options.length > 0 ? field.options : null,
        is_required: field.is_required,
        step: field.step,
        sort_order: index,
        file_config: field.file_config && Object.keys(field.file_config).length > 0 ? field.file_config : null,
        is_active: field.is_active
    }));

    // Get contact settings from UI
    const currentContactSettings = getContactSettingsFromUI();

    fetch('{{ route("admission.forms.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ fields: fieldsToSave, contactSettings: currentContactSettings })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();

        if (data.success) {
            showToast('Forma muvaffaqiyatli saqlandi!', 'success');
            // Reload to get updated IDs
            loadSavedForm();
        } else {
            showToast(data.message || 'Xatolik yuz berdi!', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('Xatolik yuz berdi!', 'error');
    });
}

// Reset to defaults
function resetToDefaults() {
    if (!confirm("Barcha maydonlarni default holatga qaytarmoqchimisiz? Bu joriy sozlamalarni o'chiradi.")) return;

    showLoading("Default maydonlar yuklanmoqda...");

    fetch('{{ route("admission.forms.reset") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();

        if (data.success) {
            showToast('Default maydonlar tiklandi!', 'success');
            loadSavedForm();
        } else {
            showToast(data.message || 'Xatolik yuz berdi!', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('Xatolik yuz berdi!', 'error');
    });
}

// Drag and drop for reordering
function initDragAndDrop() {
    const dropZone = document.getElementById('dropZone');

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', function(e) {
        this.classList.remove('drag-over');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
    });
}

function initFieldDragging() {
    const container = document.getElementById('formFields');
    let draggedElement = null;

    container.querySelectorAll('.field-item').forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedElement = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });

        item.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            updateSortOrder();
        });

        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (draggedElement && draggedElement !== this) {
                const rect = this.getBoundingClientRect();
                const midY = rect.top + rect.height / 2;

                if (e.clientY < midY) {
                    this.parentNode.insertBefore(draggedElement, this);
                } else {
                    this.parentNode.insertBefore(draggedElement, this.nextSibling);
                }
            }
        });
    });
}

function updateSortOrder() {
    const container = document.getElementById('formFields');
    const items = container.querySelectorAll('.field-item');

    items.forEach((item, index) => {
        const fieldId = item.dataset.fieldId;
        const field = allFields.find(f => f.id === fieldId);
        if (field) {
            field.sort_order = index;
        }
    });
}

// Preview form
function previewForm() {
    window.open('{{ route("admission.apply") }}', '_blank');
}

// Utility functions
function showLoading(text = 'Yuklanmoqda...') {
    document.getElementById('loadingText').textContent = text;
    document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

function showToast(message, type = 'info') {
    // Simple alert for now, can be replaced with a proper toast library
    if (type === 'error') {
        alert('Xatolik: ' + message);
    } else {
        alert(message);
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Contact Settings Functions
function updateContactSettingsUI() {
    const phoneInput = document.getElementById('contactPhone');
    const telegramInput = document.getElementById('contactTelegram');
    const emailInput = document.getElementById('contactEmail');
    const showHelpCheckbox = document.getElementById('contactShowHelp');

    if (phoneInput) phoneInput.value = contactSettings.phone || '';
    if (telegramInput) telegramInput.value = contactSettings.telegram || '';
    if (emailInput) emailInput.value = contactSettings.email || '';
    if (showHelpCheckbox) showHelpCheckbox.checked = contactSettings.show_help_section !== false;
}

function getContactSettingsFromUI() {
    const phoneInput = document.getElementById('contactPhone');
    const telegramInput = document.getElementById('contactTelegram');
    const emailInput = document.getElementById('contactEmail');
    const showHelpCheckbox = document.getElementById('contactShowHelp');

    return {
        phone: phoneInput ? phoneInput.value : contactSettings.phone,
        telegram: telegramInput ? telegramInput.value : contactSettings.telegram,
        email: emailInput ? emailInput.value : contactSettings.email,
        show_help_section: showHelpCheckbox ? showHelpCheckbox.checked : true
    };
}
</script>
@endsection
