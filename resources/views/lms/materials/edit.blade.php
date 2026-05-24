@extends('layouts.dashboard-new')

@section('title', 'Materialni tahrirlash — LMS')
@section('page-title', 'Materialni tahrirlash')

@section('styles')
<style>
.file-drop-area {
    border: 2px dashed var(--c-border);
    border-radius: 10px;
    padding: 24px 20px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    background: var(--c-bg);
    position: relative;
}
.file-drop-area:hover { border-color: var(--c-teal); background: rgba(20,184,166,.04); }
.file-drop-area input[type="file"] {
    position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;
}
</style>
@endsection

@section('content')

<x-lms-alerts />

<form action="{{ route('lms.materials.update', $material) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        {{-- Main --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--c-teal)"></i>
                    <span>Asosiy ma'lumotlar</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Material nomi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $material->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Tavsif</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4">{{ old('description', $material->description) }}</textarea>
                        <div class="form-text">Material haqida qisqacha ma'lumot</div>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="subject_id" class="form-label">Fan <span class="text-danger">*</span></label>
                            <select class="form-select @error('subject_id') is-invalid @enderror"
                                    id="subject_id" name="subject_id" required>
                                <option value="">Fanni tanlang</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $material->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="material_type" class="form-label">Material turi <span class="text-danger">*</span></label>
                            <select class="form-select @error('material_type') is-invalid @enderror"
                                    id="material_type" name="material_type" required>
                                <option value="">Turni tanlang</option>
                                <option value="presentation" {{ old('material_type', $material->material_type) == 'presentation' ? 'selected' : '' }}>Taqdimot (PPT)</option>
                                <option value="document"     {{ old('material_type', $material->material_type) == 'document'     ? 'selected' : '' }}>Hujjat (DOC)</option>
                                <option value="spreadsheet"  {{ old('material_type', $material->material_type) == 'spreadsheet'  ? 'selected' : '' }}>Jadval (Excel)</option>
                                <option value="pdf"          {{ old('material_type', $material->material_type) == 'pdf'          ? 'selected' : '' }}>PDF</option>
                                <option value="other"        {{ old('material_type', $material->material_type) == 'other'        ? 'selected' : '' }}>Boshqa</option>
                            </select>
                            @error('material_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="week_number" class="form-label">Hafta raqami</label>
                            <input type="number" class="form-control @error('week_number') is-invalid @enderror"
                                   id="week_number" name="week_number" value="{{ old('week_number', $material->week_number) }}"
                                   min="1" max="16">
                            <div class="form-text">Qaysi haftaga tegishli (1–16)</div>
                            @error('week_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="order_number" class="form-label">Tartib raqami</label>
                            <input type="number" class="form-control @error('order_number') is-invalid @enderror"
                                   id="order_number" name="order_number" value="{{ old('order_number', $material->order_number ?? 0) }}"
                                   min="0">
                            <div class="form-text">Ko'rinish tartibi</div>
                            @error('order_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-file-upload" style="color:var(--c-sky)"></i>
                    <span>Fayl yangilash (ixtiyoriy)</span>
                </div>
                <div class="card-body">
                    @if($material->file_path)
                    <div class="d-flex align-items-center gap-3 p-3 mb-3 rounded" style="background:rgba(20,184,166,.08);border:1px solid rgba(20,184,166,.2)">
                        <i class="fas fa-check-circle" style="color:var(--c-teal);font-size:20px;flex-shrink:0"></i>
                        <div>
                            <div style="font-weight:600;font-size:13px;color:var(--c-text)">Mavjud fayl:</div>
                            <div style="font-size:12px;color:var(--c-text-2)">
                                <i class="fas fa-file me-1"></i>{{ $material->file_name }}
                            </div>
                            <div style="font-size:11px;color:var(--c-text-3)">Hajmi: {{ $material->file_size_formatted }}</div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="form-label">Yangi fayl yuklash</label>
                        <div class="file-drop-area @error('file') border-danger @enderror">
                            <input type="file" id="file" name="file"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar"
                                   onchange="updateFileName(this)">
                            <i class="fas fa-cloud-upload-alt mb-2" style="font-size:28px;color:var(--c-teal)"></i>
                            <div style="font-size:13px;font-weight:600;color:var(--c-text);margin-bottom:4px">Bosing yoki sudrab keling</div>
                            <div style="font-size:11px;color:var(--c-text-3)">
                                Maks: <strong>50MB</strong> &nbsp;|&nbsp; PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR
                            </div>
                        </div>
                        <div id="file-name" class="mt-2 d-none" style="font-size:13px;color:var(--c-text-2)">
                            <i class="fas fa-file-alt me-1" style="color:var(--c-teal)"></i>
                            <span id="file-name-text"></span>
                        </div>
                        @error('file')<div class="text-danger mt-1" style="font-size:13px"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-tasks" style="color:var(--c-teal)"></i>
                    <span>Amallar</span>
                </div>
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>O'zgarishlarni saqlash
                    </button>
                    <a href="{{ route('lms.materials.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Bekor qilish
                    </a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--c-sky)"></i>
                    <span>Ma'lumot</span>
                </div>
                <div class="card-body p-3">
                    @php
                        $infoRows = [
                            ['fas fa-user','var(--c-teal)',"Yuklagan:",$material->teacher?->name ?? "Noma'lum"],
                            ['fas fa-calendar','var(--c-emerald)',"Yaratilgan:",$material->created_at->format('d.m.Y H:i')],
                            ['fas fa-download','var(--c-violet)',"Yuklab olishlar:",($material->download_count ?? 0) . ' marta'],
                        ];
                    @endphp
                    @foreach($infoRows as [$icon,$color,$label,$value])
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <i class="{{ $icon }}" style="color:{{ $color }};margin-top:2px;flex-shrink:0"></i>
                        <div>
                            <div style="font-size:11px;color:var(--c-text-3)">{{ $label }}</div>
                            <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $value }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-lightbulb" style="color:var(--c-amber)"></i>
                    <span>Ko'rsatmalar</span>
                </div>
                <div class="card-body">
                    <ul class="mb-0" style="font-size:13px;color:var(--c-text-2);padding-left:16px">
                        <li class="mb-2">Material nomi aniq va tushunarli bo'lsin</li>
                        <li class="mb-2">Fayl hajmi <strong>50MB</strong> dan oshmasin</li>
                        <li class="mb-2">Tegishli fanni to'g'ri tanlang</li>
                        <li>Hafta raqami 1 dan 16 gacha bo'lsin</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
function updateFileName(input) {
    const fileNameDiv = document.getElementById('file-name');
    const fileNameText = document.getElementById('file-name-text');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = (file.size / (1024 * 1024)).toFixed(2);
        fileNameText.textContent = `${file.name} (${fileSize} MB)`;
        fileNameDiv.classList.remove('d-none');
    } else {
        fileNameDiv.classList.add('d-none');
    }
}
</script>
@endpush
