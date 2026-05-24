@extends('layouts.dashboard-new')

@section('title', 'Yangi talaba qo\'shish')
@section('page-title', 'Yangi talaba qo\'shish')

@section('content')

{{-- Back --}}
<div class="mb-4">
    <a href="{{ route('students.index') }}"
       class="d-inline-flex align-items-center gap-2 text-decoration-none"
       style="color:var(--c-text-2);font-size:13px;font-weight:500">
        <i class="fas fa-arrow-left fa-sm"></i> Talabalar ro'yxatiga qaytish
    </a>
</div>

@if($errors->any())
<div class="alert mb-4" style="background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.25);color:var(--c-text)">
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="fas fa-exclamation-circle" style="color:var(--c-rose)"></i>
        <strong style="color:var(--c-rose)">Xatoliklar:</strong>
    </div>
    <ul class="mb-0 ps-3" style="font-size:13px">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
@csrf

{{-- ── Header card ── --}}
<div class="card mb-4">
    <div class="card-header" style="background:linear-gradient(135deg,var(--c-sky),#38BDF8);border-radius:var(--r-lg) var(--r-lg) 0 0;padding:24px 28px">
        <h2 style="color:#fff;font-size:18px;font-weight:700;margin:0 0 4px">Yangi talaba qo'shish</h2>
        <p style="color:rgba(255,255,255,.7);margin:0;font-size:13px">
            Faqat ism, familiya va guruhni tanlang — qolganini talabaning o'zi to'ldiradi
        </p>
    </div>

    {{-- ── Required fields ── --}}
    <div class="card-body">
        <div class="p-4 rounded-3 mb-4" style="background:rgba(14,165,233,.06);border:1px solid rgba(14,165,233,.18)">
            <div class="d-flex align-items-center gap-2 mb-4">
                <div style="width:32px;height:32px;border-radius:8px;background:rgba(14,165,233,.15);display:flex;align-items:center;justify-content:center;color:var(--c-sky)">
                    <i class="fas fa-user-graduate fa-sm"></i>
                </div>
                <span style="font-weight:700;color:var(--c-text)">Majburiy ma'lumotlar</span>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label">Familiya <span style="color:var(--c-rose)">*</span></label>
                    <input type="text" name="last_name_latin" value="{{ old('last_name_latin') }}" required
                           class="form-control form-control-lg" placeholder="Abdullayev">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Ism <span style="color:var(--c-rose)">*</span></label>
                    <input type="text" name="first_name_latin" value="{{ old('first_name_latin') }}" required
                           class="form-control form-control-lg" placeholder="Abbos">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Otasining ismi</label>
                    <input type="text" name="middle_name_latin" value="{{ old('middle_name_latin') }}"
                           class="form-control form-control-lg" placeholder="Aliyevich">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label">
                    Guruh <span style="color:var(--c-rose)">*</span>
                    <span style="font-size:12px;color:var(--c-text-3);font-weight:400">(fakultet va yo'nalish avtomatik tanlanadi)</span>
                </label>
                <select name="group_id" id="group_id" required class="form-select form-select-lg">
                    <option value="">Guruhni tanlang</option>
                    @foreach($groups as $group)
                        @php
                            $specialty = $group->specialty;
                            $faculty   = $specialty ? $specialty->faculty : null;
                            $label     = $group->name;
                            if ($specialty) $label .= ' — ' . ($specialty->name_uz ?? $specialty->code);
                            if ($faculty)   $label .= ' (' . ($faculty->name_uz ?? $faculty->name) . ')';
                        @endphp
                        <option value="{{ $group->id }}"
                                data-faculty="{{ $faculty->id ?? '' }}"
                                data-specialty="{{ $specialty->id ?? '' }}"
                                data-course="{{ $group->course ?? 1 }}"
                                data-faculty-name="{{ $faculty->name_uz ?? $faculty->name ?? '' }}"
                                data-specialty-name="{{ $specialty->name_uz ?? $specialty->code ?? '' }}"
                                {{ old('group_id') == $group->id ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="faculty_id"   id="faculty_id"   value="{{ old('faculty_id') }}">
                <input type="hidden" name="specialty_id" id="specialty_id" value="{{ old('specialty_id') }}">
                <input type="hidden" name="course"       id="course"       value="{{ old('course', 1) }}">
            </div>

            {{-- Selected info --}}
            <div id="selectedInfo" class="d-none mt-3 p-3 rounded-3" style="background:#fff;border:1px solid rgba(14,165,233,.2)">
                <div class="row g-3 text-sm">
                    <div class="col-4">
                        <div style="font-size:11px;color:var(--c-text-3);text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px">Fakultet</div>
                        <div style="font-size:13px;font-weight:600;color:var(--c-text)" id="displayFaculty">—</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:11px;color:var(--c-text-3);text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px">Yo'nalish</div>
                        <div style="font-size:13px;font-weight:600;color:var(--c-text)" id="displaySpecialty">—</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:11px;color:var(--c-text-3);text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px">Kurs</div>
                        <div style="font-size:13px;font-weight:600;color:var(--c-sky)" id="displayCourse">—</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Optional fields ── --}}
        <div style="border:1px solid var(--c-border);border-radius:var(--r-md);overflow:hidden">
            <button type="button" onclick="toggleOptional()"
                    class="w-100 d-flex align-items-center justify-content-between px-4 py-3"
                    style="background:var(--c-bg);border:none;cursor:pointer;color:var(--c-text-2)">
                <span class="d-flex align-items-center gap-2" style="font-size:13px;font-weight:600">
                    <i class="fas fa-sliders-h" style="color:var(--c-sky)"></i>
                    Qo'shimcha ma'lumotlar <span style="font-weight:400;font-size:12px;color:var(--c-text-3)">(ixtiyoriy)</span>
                </span>
                <i id="optionalIcon" class="fas fa-chevron-down" style="font-size:12px;transition:transform .2s"></i>
            </button>

            <div id="optionalFields" style="display:none;padding:24px;border-top:1px solid var(--c-border)">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Tug'ilgan sana</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="form-control">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Jinsi</label>
                        <select name="gender" class="form-select">
                            <option value="">Tanlang</option>
                            <option value="erkak" {{ old('gender') == 'erkak' ? 'selected' : '' }}>Erkak</option>
                            <option value="ayol"  {{ old('gender') == 'ayol'  ? 'selected' : '' }}>Ayol</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="+998 90 123 45 67">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="student@tas.uz">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Pasport seriya</label>
                        <input type="text" name="passport_series" value="{{ old('passport_series') }}" maxlength="2" class="form-control" placeholder="AA">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Pasport raqam</label>
                        <input type="text" name="passport_number" value="{{ old('passport_number') }}" maxlength="7" class="form-control" placeholder="1234567">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Manzil</label>
                        <textarea name="permanent_address" rows="2" class="form-control" placeholder="Yashash manzili">{{ old('permanent_address') }}</textarea>
                    </div>

                    {{-- Photo upload --}}
                    <div class="col-12">
                        <label class="form-label">Talaba rasmi</label>
                        <div class="d-flex align-items-center gap-4">
                            <div id="photoPreviewContainer" class="position-relative flex-shrink-0"
                                 style="width:80px;height:80px;border-radius:12px;border:2px dashed var(--c-border);overflow:hidden;background:var(--c-bg);display:flex;align-items:center;justify-content:center">
                                <i id="placeholderIcon" class="fas fa-user fa-2x" style="color:var(--c-text-3)"></i>
                                <img id="photoPreview" class="d-none w-100 h-100" style="object-fit:cover" alt="Preview">
                                <button type="button" id="removePhotoBtn" class="d-none position-absolute top-0 end-0 btn btn-danger"
                                        style="padding:2px 5px;font-size:10px;border-radius:0 0 0 6px">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="flex-fill">
                                <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/jpg" class="form-control">
                                <p class="mt-1 mb-0" style="font-size:11px;color:var(--c-text-3)">JPG, PNG · Maksimal 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Form actions ── --}}
    <div class="card-body d-flex justify-content-between align-items-center" style="border-top:1px solid var(--c-border)">
        <p class="mb-0" style="font-size:12px;color:var(--c-text-3)">
            <i class="fas fa-info-circle me-1"></i>Login va parol avtomatik yaratiladi
        </p>
        <div class="d-flex gap-3">
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                Bekor qilish
            </a>
            <button type="submit" class="btn btn-primary px-5">
                <i class="fas fa-save me-2"></i>Saqlash
            </button>
        </div>
    </div>
</div>

</form>

<script>
function toggleOptional() {
    const fields = document.getElementById('optionalFields');
    const icon   = document.getElementById('optionalIcon');
    const isHidden = fields.style.display === 'none';
    fields.style.display = isHidden ? 'block' : 'none';
    icon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
}

document.getElementById('group_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('faculty_id').value   = opt.dataset.faculty   || '';
    document.getElementById('specialty_id').value = opt.dataset.specialty || '';
    document.getElementById('course').value       = opt.dataset.course    || '1';

    const infoDiv = document.getElementById('selectedInfo');
    if (this.value) {
        document.getElementById('displayFaculty').textContent   = opt.dataset.facultyName   || '—';
        document.getElementById('displaySpecialty').textContent = opt.dataset.specialtyName || '—';
        document.getElementById('displayCourse').textContent    = (opt.dataset.course || '1') + '-kurs';
        infoDiv.classList.remove('d-none');
    } else {
        infoDiv.classList.add('d-none');
    }
});

document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { alert('Rasm hajmi 2MB dan oshmasligi kerak!'); this.value = ''; return; }
    const reader = new FileReader();
    reader.onload = function(ev) {
        document.getElementById('photoPreview').src = ev.target.result;
        document.getElementById('photoPreview').classList.remove('d-none');
        document.getElementById('placeholderIcon').classList.add('d-none');
        document.getElementById('removePhotoBtn').classList.remove('d-none');
    };
    reader.readAsDataURL(file);
});

document.getElementById('removePhotoBtn').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('photoInput').value = '';
    document.getElementById('photoPreview').classList.add('d-none');
    document.getElementById('placeholderIcon').classList.remove('d-none');
    this.classList.add('d-none');
});
</script>
@endsection
