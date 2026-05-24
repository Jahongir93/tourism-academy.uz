@extends('layouts.dashboard-new')

@section('title', 'Yangi material yuklash — LMS')
@section('page-title', 'Yangi material yuklash')

@section('styles')
<style>
.file-drop-area {
    border: 2px dashed var(--c-border);
    border-radius: 10px;
    padding: 32px 20px;
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

<form action="{{ route('lms.materials.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        {{-- Main --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--c-teal)"></i>
                    <span>Material ma'lumotlari</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Material nomi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}"
                               placeholder="Masalan: 1-ma'ruza — Kirish" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Tavsif</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="Material haqida qisqacha ma'lumot...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="subject_id" class="form-label">Fan <span class="text-danger">*</span></label>
                            <select class="form-select @error('subject_id') is-invalid @enderror"
                                    id="subject_id" name="subject_id" required>
                                <option value="">Fanni tanlang</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                                <option value="presentation" {{ old('material_type') == 'presentation' ? 'selected' : '' }}>Taqdimot (PPT)</option>
                                <option value="document"     {{ old('material_type') == 'document'     ? 'selected' : '' }}>Hujjat (DOC)</option>
                                <option value="spreadsheet"  {{ old('material_type') == 'spreadsheet'  ? 'selected' : '' }}>Jadval (Excel)</option>
                                <option value="pdf"          {{ old('material_type') == 'pdf'          ? 'selected' : '' }}>PDF</option>
                                <option value="other"        {{ old('material_type') == 'other'        ? 'selected' : '' }}>Boshqa</option>
                            </select>
                            @error('material_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="week_number" class="form-label">Hafta raqami</label>
                            <input type="number" class="form-control @error('week_number') is-invalid @enderror"
                                   id="week_number" name="week_number" value="{{ old('week_number') }}"
                                   min="1" max="16" placeholder="1–16">
                            <div class="form-text">Qaysi haftaga tegishli</div>
                            @error('week_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="order_number" class="form-label">Tartib raqami</label>
                            <input type="number" class="form-control @error('order_number') is-invalid @enderror"
                                   id="order_number" name="order_number" value="{{ old('order_number', 0) }}"
                                   min="0" placeholder="0">
                            <div class="form-text">Ko'rinish tartibi</div>
                            @error('order_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Fayl <span class="text-danger">*</span></label>
                        <div class="file-drop-area @error('file') border-danger @enderror">
                            <input type="file" id="file" name="file" required
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar"
                                   onchange="updateFileName(this)">
                            <i class="fas fa-cloud-upload-alt mb-3" style="font-size:36px;color:var(--c-teal)"></i>
                            <div style="font-weight:600;color:var(--c-text);margin-bottom:4px">Faylni yuklash uchun bosing</div>
                            <div style="font-size:12px;color:var(--c-text-3)">yoki shu joyga sudrab olib keling</div>
                            <div style="font-size:11px;color:var(--c-text-3);margin-top:8px">
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
                        <i class="fas fa-upload me-2"></i>Material yuklash
                    </button>
                    <a href="{{ route('lms.materials.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Ortga qaytish
                    </a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-lightbulb" style="color:var(--c-amber)"></i>
                    <span>Ko'rsatmalar</span>
                </div>
                <div class="card-body">
                    <ul class="mb-0" style="font-size:13px;color:var(--c-text-2);padding-left:16px">
                        <li class="mb-2">Fayl hajmi <strong>50MB</strong> dan oshmasligi kerak</li>
                        <li class="mb-2">Fayl nomi tushunarli bo'lishi kerak</li>
                        <li class="mb-2">Hafta raqami to'g'ri ko'rsatilsin</li>
                        <li class="mb-2">Tegishli fanni to'g'ri tanlang</li>
                        <li>Material turi fayl formatiga mos bo'lsin</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-file-archive" style="color:var(--c-violet)"></i>
                    <span>Fayl turlari</span>
                </div>
                <div class="card-body p-3">
                    @php
                        $fileTypes = [
                            ['fas fa-file-pdf','var(--c-rose)','rgba(244,63,94,.1)','PDF','.pdf'],
                            ['fas fa-file-word','var(--c-sky)','rgba(14,165,233,.1)','Hujjat','.doc, .docx'],
                            ['fas fa-file-powerpoint','var(--c-amber)','rgba(245,158,11,.1)','Taqdimot','.ppt, .pptx'],
                            ['fas fa-file-excel','var(--c-emerald)','rgba(16,185,129,.1)','Jadval','.xls, .xlsx'],
                            ['fas fa-file-archive','var(--c-text-3)','rgba(148,163,184,.1)','Arxiv','.zip, .rar'],
                        ];
                    @endphp
                    @foreach($fileTypes as [$icon,$color,$bg,$label,$ext])
                    <div class="d-flex align-items-center justify-content-between mb-2 px-2 py-1 rounded" style="background:{{ $bg }}">
                        <span style="font-size:13px"><i class="{{ $icon }} me-2" style="color:{{ $color }}"></i>{{ $label }}</span>
                        <span style="font-size:11px;color:var(--c-text-3)">{{ $ext }}</span>
                    </div>
                    @endforeach
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
