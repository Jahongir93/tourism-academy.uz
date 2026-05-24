@extends('layouts.dashboard-new')

@section('title', 'Excel dan test import qilish — LMS')
@section('page-title', 'Excel dan test import qilish')

@section('content')

<x-lms-alerts />

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-file-upload" style="color:var(--c-teal)"></i>
                <span>Excel faylini yuklash</span>
            </div>
            <div class="card-body">
                <form action="{{ route('lms.tests.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    <div class="mb-3">
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
                        <div class="form-text">Import qilinadigan test qaysi fanga tegishli ekanini tanlang</div>
                    </div>

                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Excel fayli <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('excel_file') is-invalid @enderror"
                               id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                        @error('excel_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Faqat .xlsx yoki .xls formatdagi fayllar qabul qilinadi. Maksimal hajm: 10MB.</div>
                    </div>

                    <div class="p-3 rounded mb-3" style="background:rgba(14,165,233,.06);border:1px solid rgba(14,165,233,.15)">
                        <div style="font-size:12px;font-weight:600;color:var(--c-sky);margin-bottom:6px">
                            <i class="fas fa-info-circle me-1"></i>Eslatma
                        </div>
                        <ul style="font-size:12px;color:var(--c-text-2);margin:0;padding-left:16px">
                            <li class="mb-1">Excel faylida test ma'lumotlari va savollar to'g'ri formatda bo'lishi kerak</li>
                            <li class="mb-1"><strong>Har bir ustun rangi bilan ajratilgan</strong> — qulaylik uchun</li>
                            <li class="mb-1">Namuna faylni yuklab olish uchun o'ng tarafdagi tugmani bosing</li>
                            <li class="mb-1">Savol turlari: <code>multiple_choice</code>, <code>true_false</code>, <code>short_answer</code></li>
                            <li>Ko'p tanlovli savollar uchun to'g'ri javob A, B, C yoki D harfi bilan belgilanadi</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" id="importBtn" class="btn" style="background:var(--c-teal);color:#fff">
                            <i class="fas fa-upload me-1" id="importIcon"></i>
                            <span id="importText">Import qilish</span>
                        </button>
                        <a href="{{ route('lms.tests.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Bekor qilish
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-download" style="color:var(--c-emerald)"></i>
                <span>Rangli namuna fayli</span>
            </div>
            <div class="card-body">
                <p style="font-size:13px;color:var(--c-text-2);margin-bottom:12px">
                    Excel faylini to'g'ri formatda tayyorlash uchun rangli namuna faylni yuklab oling.
                    <strong style="color:var(--c-teal)">Har bir ustun turli rang bilan ajratilgan!</strong>
                </p>
                <a href="{{ route('lms.tests.template.download') }}" class="btn w-100 mb-3"
                   style="background:var(--c-emerald);color:#fff">
                    <i class="fas fa-file-excel me-1"></i>Rangli namuna faylni yuklash (.xlsx)
                </a>
                <div class="p-2 rounded" style="background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.15)">
                    <div style="font-size:11px;font-weight:600;color:var(--c-emerald);margin-bottom:6px">
                        <i class="fas fa-palette me-1"></i>Rangli formatlanish:
                    </div>
                    @foreach([
                        ['var(--c-amber)','Savol — Sariq rangda'],
                        ['var(--c-rose)','Turi — Pushti rangda'],
                        ['var(--c-sky)','Ball — Ko\'k rangda'],
                        ['var(--c-violet)','Tushuntirish — Moviy rangda'],
                        ['var(--c-emerald)',"To'g'ri javob — Yashil rangda"],
                    ] as [$c,$t])
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div style="width:10px;height:10px;border-radius:2px;background:{{ $c }};flex-shrink:0"></div>
                        <span style="font-size:11px;color:var(--c-text-2)">{{ $t }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-question-circle" style="color:var(--c-sky)"></i>
                <span>Qanday ishlaydi?</span>
            </div>
            <div class="card-body p-0">
                @foreach([
                    ['Rangli namuna faylni yuklang','Yuqoridagi tugmadan rangli Excel namuna faylni yuklab oling'],
                    ['Faylni oching','Excel da ochganda har bir ustun rangi bilan ajratilgan bo\'ladi'],
                    ["Ma'lumotlarni kiriting",'Ranglar bo\'yicha test ma\'lumotlari va savollarni to\'ldiring'],
                    ['Fanni tanlang','Test qaysi fanga tegishli ekanini belgilang'],
                    ['Faylni yuklang','Tayyorlangan Excel faylni tizimga yuklang'],
                    ['Import natijasi','Tizim savollarni import qiladi va test tayyor bo\'ladi'],
                ] as $i => [$title, $desc])
                <div class="d-flex align-items-start gap-3 px-4 py-3" style="border-bottom:1px solid var(--c-border)">
                    <div style="width:22px;height:22px;border-radius:50%;background:rgba(20,184,166,.12);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--c-teal);flex-shrink:0">
                        {{ $i + 1 }}
                    </div>
                    <div>
                        <div style="font-size:12px;font-weight:600;color:var(--c-text)">{{ $title }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">{{ $desc }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('importForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('excel_file');
    const subjectSelect = document.getElementById('subject_id');

    if (!fileInput.files.length) { e.preventDefault(); alert('Excel faylini tanlang!'); return; }
    if (!subjectSelect.value) { e.preventDefault(); alert('Fanni tanlang!'); return; }

    const file = fileInput.files[0];
    if (file.size > 10 * 1024 * 1024) { e.preventDefault(); alert('Fayl hajmi 10MB dan oshmasligi kerak!'); return; }
    const ext = file.name.toLowerCase().split('.').pop();
    if (!['xlsx','xls'].includes(ext)) { e.preventDefault(); alert('Faqat .xlsx yoki .xls formatdagi fayllar qabul qilinadi!'); return; }

    document.getElementById('importBtn').disabled = true;
    document.getElementById('importIcon').className = 'fas fa-spinner fa-spin me-1';
    document.getElementById('importText').textContent = 'Import qilinmoqda...';
});
</script>
@endpush
