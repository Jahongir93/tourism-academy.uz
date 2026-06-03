@extends('layouts.dashboard-new')

@section('title', 'Yangi yangilik — CMS')
@section('page-title', 'Yangi yangilik qo\'shish')

@section('styles')
<style>
.tox-tinymce { border-radius:0.375rem; }
.file-drop-area { border:2px dashed var(--c-border);border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:all .2s;background:var(--c-bg);position:relative; }
.file-drop-area:hover { border-color:var(--c-violet);background:rgba(124,58,237,.03); }
.file-drop-area input[type="file"] { position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer; }
</style>
@endsection

@section('content')

<x-lms-alerts />

<form action="{{ route('cms.news.store') }}" method="POST" enctype="multipart/form-data" id="newsForm">
    @csrf
    <div class="row g-4">
        {{-- Main --}}
        <div class="col-lg-8">

            {{-- Title --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-heading" style="color:var(--c-violet)"></i>
                    <span>Yangilik sarlavhasi</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title_uz" class="form-label" style="font-size:13px">Sarlavha (O'zbek) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title_uz') is-invalid @enderror"
                               id="title_uz" name="title_uz" value="{{ old('title_uz') }}" required>
                        @error('title_uz')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="title_ru" class="form-label" style="font-size:13px">Sarlavha (Rus)</label>
                            <input type="text" class="form-control @error('title_ru') is-invalid @enderror"
                                   id="title_ru" name="title_ru" value="{{ old('title_ru') }}">
                            @error('title_ru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="title_en" class="form-label" style="font-size:13px">Sarlavha (Ingliz)</label>
                            <input type="text" class="form-control @error('title_en') is-invalid @enderror"
                                   id="title_en" name="title_en" value="{{ old('title_en') }}">
                            @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="slug" class="form-label" style="font-size:13px">Slug (URL)</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                               id="slug" name="slug" value="{{ old('slug') }}" placeholder="Avtomatik yaratiladi">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Excerpt --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-align-left" style="color:var(--c-sky)"></i>
                    <span>Qisqa tavsif</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="excerpt_uz" class="form-label" style="font-size:13px">Qisqa tavsif (O'zbek)</label>
                        <textarea class="form-control" id="excerpt_uz" name="excerpt_uz" rows="3">{{ old('excerpt_uz') }}</textarea>
                        <div style="font-size:11px;color:var(--c-text-3);margin-top:4px">Bosh sahifada va ro'yxatlarda ko'rinadigan qisqa matn</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="excerpt_ru" class="form-label" style="font-size:13px">Qisqa tavsif (Rus)</label>
                            <textarea class="form-control" id="excerpt_ru" name="excerpt_ru" rows="3">{{ old('excerpt_ru') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="excerpt_en" class="form-label" style="font-size:13px">Qisqa tavsif (Ingliz)</label>
                            <textarea class="form-control" id="excerpt_en" name="excerpt_en" rows="3">{{ old('excerpt_en') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-edit" style="color:var(--c-emerald)"></i>
                    <span>Yangilik mazmuni</span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="content_uz" class="form-label" style="font-size:13px">Mazmun (O'zbek) <span class="text-danger">*</span></label>
                        <textarea class="form-control tinymce-editor" id="content_uz" name="content_uz" rows="15">{{ old('content_uz') }}</textarea>
                        @error('content_uz')<div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="content_ru" class="form-label" style="font-size:13px">Mazmun (Rus)</label>
                        <textarea class="form-control tinymce-editor" id="content_ru" name="content_ru" rows="12">{{ old('content_ru') }}</textarea>
                    </div>
                    <div class="mb-0">
                        <label for="content_en" class="form-label" style="font-size:13px">Mazmun (Ingliz)</label>
                        <textarea class="form-control tinymce-editor" id="content_en" name="content_en" rows="12">{{ old('content_en') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Actions --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-tasks" style="color:var(--c-teal)"></i>
                    <span>Amallar</span>
                </div>
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn" style="background:var(--c-violet);color:#fff">
                        <i class="fas fa-save me-1"></i>Saqlash
                    </button>
                    <button type="submit" name="status" value="published" class="btn" style="background:var(--c-emerald);color:#fff">
                        <i class="fas fa-paper-plane me-1"></i>Saqlash va chop etish
                    </button>
                    <a href="{{ route('cms.news.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>

            {{-- Status --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-sliders-h" style="color:var(--c-sky)"></i>
                    <span>Holat va sozlamalar</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label" style="font-size:13px">Holat</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft"     {{ old('status')==='draft'     ? 'selected':'' }}>Qoralama</option>
                            <option value="published" {{ old('status')==='published' ? 'selected':'' }}>Chop etilgan</option>
                            <option value="archived"  {{ old('status')==='archived'  ? 'selected':'' }}>Arxivlangan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="published_at" class="form-label" style="font-size:13px">Nashr sanasi</label>
                        <input type="datetime-local" class="form-control" id="published_at" name="published_at"
                               value="{{ old('published_at') }}">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                               {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured" style="font-size:13px">
                            <i class="fas fa-star me-1" style="color:var(--c-amber)"></i>Tanlangan yangilik
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_breaking" name="is_breaking" value="1"
                               {{ old('is_breaking') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_breaking" style="font-size:13px">
                            <i class="fas fa-bolt me-1" style="color:var(--c-rose)"></i>Tezkor yangilik
                        </label>
                    </div>
                </div>
            </div>

            {{-- Category --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-tag" style="color:var(--c-violet)"></i>
                    <span>Kategoriya va teglar</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category_id" class="form-label" style="font-size:13px">Kategoriya</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">Kategoriyasiz</option>
                            @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id ? 'selected':'' }}>
                                {{ $cat->name_uz }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tags" class="form-label" style="font-size:13px">Teglar</label>
                        <input type="text" class="form-control" id="tags" name="tags"
                               value="{{ old('tags') }}" placeholder="teg1, teg2, teg3">
                        <div style="font-size:11px;color:var(--c-text-3);margin-top:4px">Vergul bilan ajrating</div>
                    </div>
                </div>
            </div>

            {{-- Featured image --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-image" style="color:var(--c-amber)"></i>
                    <span>Asosiy rasm</span>
                </div>
                <div class="card-body">
                    <div class="file-drop-area">
                        {{-- name yo'q: faqat AJAX uchun, formada multipart yuborilmaydi (WAF-safe) --}}
                        <input type="file" accept="image/*" id="featured_image_input" data-no-waf>
                        <input type="hidden" name="featured_image_path" id="featured_image_path">
                        <i class="fas fa-image mb-2" style="font-size:28px;color:var(--c-amber)"></i>
                        <div style="font-size:13px;font-weight:600;color:var(--c-text);margin-bottom:2px">Rasm tanlang</div>
                        <div style="font-size:11px;color:var(--c-text-3)">Tavsiya: 800×450 px</div>
                    </div>
                    <div id="image_status" style="font-size:12px;margin-top:6px;"></div>
                    <div id="image_preview" class="mt-2" style="display:none">
                        <img src="" alt="Preview" class="img-fluid rounded" id="preview_img">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('js/waf-safe-submit.js') }}"></script>
<script>
tinymce.init({
    selector: '.tinymce-editor',
    height: 500,
    menubar: 'file edit view insert format tools table help',
    plugins: [
        'advlist','autolink','lists','link','image','charmap','preview',
        'anchor','searchreplace','visualblocks','code','fullscreen',
        'insertdatetime','media','table','help','wordcount','emoticons',
        'template','pagebreak','nonbreaking','quickbars','autoresize'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | pagebreak | removeformat | fullscreen code help',
    toolbar_mode: 'sliding',
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
    quickbars_insert_toolbar: 'quickimage quicktable',
    content_style: 'body { font-family:"Times New Roman",Times,serif; font-size:14pt; line-height:1.6; padding:20px; background:#fff; } img { max-width:100%; height:auto; } table { border-collapse:collapse; width:100%; } table td, table th { border:1px solid #ccc; padding:8px; }',
    language: 'uz',
    image_advtab: true,
    image_caption: true,
    automatic_uploads: true,
    images_upload_url: '{{ route("cms.upload.image") }}',
    images_upload_handler: function(blobInfo) {
        return new Promise((resolve, reject) => {
            fetch('{{ route("cms.upload.image.b64") }}', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                body: JSON.stringify({ name: blobInfo.filename(), type: blobInfo.blob().type, data: blobInfo.base64() })
            })
                .then(r => r.ok ? r.json() : Promise.reject('HTTP ' + r.status))
                .then(result => result.location ? resolve(result.location) : reject('Upload failed'))
                .catch(err => reject('Upload failed: ' + err));
        });
    },
    file_picker_types: 'image',
    file_picker_callback: function(callback, value, meta) {
        if (meta.filetype === 'image') {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.onchange = function() {
                const file = this.files[0];
                const reader = new FileReader();
                reader.onload = function() {
                    const id = 'blobid' + Date.now();
                    const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    const base64 = reader.result.split(',')[1];
                    const blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    callback(blobInfo.blobUri(), { title: file.name });
                };
                reader.readAsDataURL(file);
            };
            input.click();
        }
    },
    paste_data_images: true,
    browser_spellcheck: true,
    contextmenu: 'link image table',
    branding: false,
    promotion: false
});

document.getElementById('title_uz').addEventListener('blur', function() {
    const slugField = document.getElementById('slug');
    if (!slugField.value) {
        slugField.value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }
});

document.getElementById('featured_image_input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const status = document.getElementById('image_status');
    const reader = new FileReader();
    reader.onload = function(ev) {
        // preview darhol
        document.getElementById('preview_img').src = ev.target.result;
        document.getElementById('image_preview').style.display = 'block';
        // WAF-safe: base64 JSON sifatida darhol yuklash
        status.textContent = 'Yuklanmoqda...'; status.style.color = '#f59e0b';
        fetch('{{ route("cms.upload.image.b64") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body: JSON.stringify({ name: file.name, type: file.type, data: ev.target.result.split(',')[1] })
        })
        .then(r => r.ok ? r.json() : Promise.reject('HTTP ' + r.status))
        .then(res => {
            if (res.path) {
                document.getElementById('featured_image_path').value = res.path;
                status.textContent = '✓ Rasm yuklandi'; status.style.color = '#10b981';
            } else { throw new Error(res.error || 'xato'); }
        })
        .catch(err => { status.textContent = '✗ Yuklashda xatolik: ' + err; status.style.color = '#ef4444'; });
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
