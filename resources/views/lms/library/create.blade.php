@extends('layouts.dashboard-new')

@section('title', 'Yangi kitob yuklash — E-kutubxona')
@section('page-title', 'Yangi kitob yuklash')

@section('styles')
<style>
.file-drop-area {
    border: 2px dashed var(--c-border);
    border-radius: 10px;
    padding: 28px 20px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    background: var(--c-bg);
    position: relative;
}
.file-drop-area:hover { border-color: var(--c-rose); background: rgba(244,63,94,.03); }
.file-drop-area input[type="file"] {
    position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;
}
</style>
@endsection

@section('content')

<x-lms-alerts />

<form action="{{ route('lms.library.store') }}" method="POST" enctype="multipart/form-data" id="libraryForm">
    @csrf

    <div class="row g-4">
        {{-- Main --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--c-teal)"></i>
                    <span>Asosiy ma'lumotlar</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Kitob nomi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}"
                               placeholder="Masalan: Turizm asoslari" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="author" class="form-label">Muallif <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('author') is-invalid @enderror"
                               id="author" name="author" value="{{ old('author') }}"
                               placeholder="Muallif ismi" required>
                        @error('author')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Kategoriya <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id" required>
                            <option value="">Kategoriyani tanlang</option>
                            @foreach(\App\Models\LmsLibraryCategory::where('is_active', true)->orderBy('order_number')->get() as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name_uz ?? $category->name ?? $category->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Tavsif</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="Kitob haqida qisqacha ma'lumot">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="publication_year" class="form-label">Nashr yili</label>
                            <input type="number" class="form-control @error('publication_year') is-invalid @enderror"
                                   id="publication_year" name="publication_year" value="{{ old('publication_year') }}"
                                   min="1900" max="{{ date('Y') }}" placeholder="{{ date('Y') }}">
                            @error('publication_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="pages" class="form-label">Sahifalar soni</label>
                            <input type="number" class="form-control @error('pages') is-invalid @enderror"
                                   id="pages" name="pages" value="{{ old('pages') }}" min="1" placeholder="350">
                            @error('pages')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="language" class="form-label">Til <span class="text-danger">*</span></label>
                            <select class="form-select @error('language') is-invalid @enderror"
                                    id="language" name="language" required>
                                <option value="uz" {{ old('language', 'uz') == 'uz' ? 'selected' : '' }}>O'zbek tili</option>
                                <option value="ru" {{ old('language') == 'ru' ? 'selected' : '' }}>Rus tili</option>
                                <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>Ingliz tili</option>
                            </select>
                            @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cover_image" class="form-label">Muqova rasmi</label>
                        <input type="file" class="form-control @error('cover_image') is-invalid @enderror"
                               id="cover_image" name="cover_image" accept="image/*">
                        <div class="form-text">JPG, PNG yoki JPEG (Maks: 2MB)</div>
                        @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="cover-preview" class="mt-2"></div>
                    </div>

                    <div>
                        <label class="form-label">Kitob fayli (PDF) <span class="text-danger">*</span></label>
                        <div class="file-drop-area @error('file') border-danger @enderror">
                            <input type="file" id="file" name="file" accept=".pdf" required
                                   onchange="updateFileName(this)">
                            <i class="fas fa-file-pdf mb-2" style="font-size:36px;color:var(--c-rose)"></i>
                            <div style="font-weight:600;color:var(--c-text);margin-bottom:4px">PDF faylni yuklash uchun bosing</div>
                            <div style="font-size:12px;color:var(--c-text-3)">yoki shu joyga sudrab keling</div>
                            <div style="font-size:11px;color:var(--c-text-3);margin-top:6px">Maks: <strong>50MB</strong></div>
                        </div>
                        <div id="file-name" class="mt-2 d-none" style="font-size:13px;color:var(--c-text-2)">
                            <i class="fas fa-file-pdf me-1" style="color:var(--c-rose)"></i>
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
                    <i class="fas fa-cog" style="color:var(--c-teal)"></i>
                    <span>Sozlamalar</span>
                </div>
                <div class="card-body">
                    @foreach([['allow_download','Yuklab olishga ruxsat',true],['allow_online_reading',"Online o'qishga ruxsat",true],['is_featured','Tanlangan kitob',false],['is_active','Faol',true]] as [$name,$label,$default])
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="{{ $name }}"
                               name="{{ $name }}" value="1" {{ old($name, $default) ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $name }}" style="font-size:13px">{{ $label }}</label>
                    </div>
                    @endforeach

                    <hr style="border-color:var(--c-border)">

                    <div class="d-grid gap-2">
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            <i class="fas fa-upload me-2" id="submit-icon"></i>
                            <span id="submit-text">Kitobni yuklash</span>
                        </button>
                        <a href="{{ route('lms.library.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Ortga qaytish
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--c-sky)"></i>
                    <span>Ma'lumot</span>
                </div>
                <div class="card-body">
                    <ul class="mb-0" style="font-size:13px;color:var(--c-text-2);padding-left:16px">
                        <li class="mb-2">Faqat PDF formatdagi fayllar qabul qilinadi</li>
                        <li class="mb-2">Fayl hajmi <strong>50MB</strong> dan oshmasligi kerak</li>
                        <li class="mb-2">Kitob nomi va muallif majburiy maydonlar</li>
                        <li>Muqova rasmi ixtiyoriy, lekin tavsiya etiladi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Upload progress modal --}}
<div class="modal fade" id="uploadModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <i class="fas fa-cloud-upload-alt mb-3" style="font-size:48px;color:var(--c-teal)"></i>
                <h5 style="color:var(--c-text)">Kitob yuklanmoqda...</h5>
                <p id="upload-status" style="font-size:13px;color:var(--c-text-2)">Iltimos, kuting</p>
                <div class="progress mb-2" style="height:10px">
                    <div id="progress-bar" class="progress-bar" style="width:0%;background:var(--c-teal);transition:width .3s"></div>
                </div>
                <div class="d-flex justify-content-between" style="font-size:12px;color:var(--c-text-3)">
                    <span id="progress-text">0%</span>
                    <span id="upload-size">0 MB / 0 MB</span>
                </div>
                <div id="upload-speed" style="font-size:11px;color:var(--c-text-3);margin-top:4px"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateFileName(input) {
    const div = document.getElementById('file-name');
    const text = document.getElementById('file-name-text');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        text.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
        div.classList.remove('d-none');
    } else {
        div.classList.add('d-none');
    }
}

document.getElementById('cover_image').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { alert('Rasm hajmi 2MB dan kichik bo\'lishi kerak!'); this.value = ''; return; }
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('cover-preview').innerHTML =
            `<img src="${e.target.result}" class="img-fluid rounded" style="max-height:120px">`;
    };
    reader.readAsDataURL(file);
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('libraryForm');
    const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const fileInput = document.getElementById('file');
        if (!fileInput.files || !fileInput.files[0]) {
            alert('Iltimos, kitob faylini tanlang!');
            return;
        }

        const formData = new FormData(form);
        const totalSize = fileInput.files[0].size;
        const startTime = Date.now();
        const submitBtn = document.getElementById('submit-btn');
        const submitIcon = document.getElementById('submit-icon');
        const submitText = document.getElementById('submit-text');

        uploadModal.show();
        submitBtn.disabled = true;
        submitIcon.className = 'fas fa-spinner fa-spin me-2';
        submitText.textContent = 'Yuklanmoqda...';

        const xhr = new XMLHttpRequest();
        xhr.upload.addEventListener('progress', function(e) {
            if (!e.lengthComputable) return;
            const pct = Math.round(e.loaded / e.total * 100);
            document.getElementById('progress-bar').style.width = pct + '%';
            document.getElementById('progress-text').textContent = pct + '%';
            document.getElementById('upload-size').textContent =
                (e.loaded / 1024 / 1024).toFixed(2) + ' MB / ' + (e.total / 1024 / 1024).toFixed(2) + ' MB';
            const elapsed = (Date.now() - startTime) / 1000;
            if (elapsed > 0) {
                document.getElementById('upload-speed').textContent =
                    'Tezlik: ' + (e.loaded / elapsed / 1024 / 1024).toFixed(2) + ' MB/s';
            }
            const statuses = [[30,'Fayl serverga yuborilmoqda...'],[70,'Yuklash davom etmoqda...'],[100,'Deyarli tayyor...']];
            document.getElementById('upload-status').textContent =
                statuses.find(([t]) => pct < t)?.[1] ?? 'Server javobini kutmoqda...';
        });

        xhr.addEventListener('load', function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                document.getElementById('progress-bar').style.width = '100%';
                document.getElementById('upload-status').textContent = 'Muvaffaqiyatli yuklandi!';
                setTimeout(() => {
                    try {
                        const resp = JSON.parse(xhr.responseText);
                        window.location.href = resp.redirect || '{{ route("lms.library.index") }}';
                    } catch {
                        window.location.href = xhr.responseURL || '{{ route("lms.library.index") }}';
                    }
                }, 1000);
            } else {
                uploadModal.hide();
                submitBtn.disabled = false;
                submitIcon.className = 'fas fa-upload me-2';
                submitText.textContent = 'Kitobni yuklash';
                try {
                    const resp = JSON.parse(xhr.responseText);
                    if (resp.errors) {
                        alert('Xatoliklar:\n' + Object.values(resp.errors).flat().join('\n'));
                    } else {
                        alert(resp.message || 'Xatolik yuz berdi!');
                    }
                } catch { alert('Xatolik yuz berdi! Server bilan muammo.'); }
            }
        });

        xhr.addEventListener('error', function() {
            uploadModal.hide();
            submitBtn.disabled = false;
            submitIcon.className = 'fas fa-upload me-2';
            submitText.textContent = 'Kitobni yuklash';
            alert('Tarmoq xatoligi! Internet aloqasini tekshiring.');
        });

        xhr.open('POST', form.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    });
});
</script>
@endpush
