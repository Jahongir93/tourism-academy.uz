@extends('layouts.dashboard-new')

@section('title', 'Yangilikni tahrirlash — CMS')
@section('page-title', 'Yangilikni tahrirlash')

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

<form action="{{ route('cms.news.update', $news) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
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
                               id="title_uz" name="title_uz" value="{{ old('title_uz', $news->title_uz) }}" required>
                        @error('title_uz')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="title_ru" class="form-label" style="font-size:13px">Sarlavha (Rus)</label>
                            <input type="text" class="form-control @error('title_ru') is-invalid @enderror"
                                   id="title_ru" name="title_ru" value="{{ old('title_ru', $news->title_ru) }}">
                            @error('title_ru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="title_en" class="form-label" style="font-size:13px">Sarlavha (Ingliz)</label>
                            <input type="text" class="form-control @error('title_en') is-invalid @enderror"
                                   id="title_en" name="title_en" value="{{ old('title_en', $news->title_en) }}">
                            @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div>
                        <label for="slug" class="form-label" style="font-size:13px">Slug (URL)</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                               id="slug" name="slug" value="{{ old('slug', $news->slug) }}">
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
                        <textarea class="form-control" id="excerpt_uz" name="excerpt_uz" rows="3">{{ old('excerpt_uz', $news->excerpt_uz) }}</textarea>
                        <div style="font-size:11px;color:var(--c-text-3);margin-top:4px">Bosh sahifada va ro'yxatlarda ko'rinadigan qisqa matn</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="excerpt_ru" class="form-label" style="font-size:13px">Qisqa tavsif (Rus)</label>
                            <textarea class="form-control" id="excerpt_ru" name="excerpt_ru" rows="3">{{ old('excerpt_ru', $news->excerpt_ru) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="excerpt_en" class="form-label" style="font-size:13px">Qisqa tavsif (Ingliz)</label>
                            <textarea class="form-control" id="excerpt_en" name="excerpt_en" rows="3">{{ old('excerpt_en', $news->excerpt_en) }}</textarea>
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
                        <textarea class="form-control tinymce-editor" id="content_uz" name="content_uz" rows="15">{{ old('content_uz', $news->content_uz) }}</textarea>
                        @error('content_uz')<div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="content_ru" class="form-label" style="font-size:13px">Mazmun (Rus)</label>
                        <textarea class="form-control tinymce-editor" id="content_ru" name="content_ru" rows="12">{{ old('content_ru', $news->content_ru) }}</textarea>
                    </div>
                    <div>
                        <label for="content_en" class="form-label" style="font-size:13px">Mazmun (Ingliz)</label>
                        <textarea class="form-control tinymce-editor" id="content_en" name="content_en" rows="12">{{ old('content_en', $news->content_en) }}</textarea>
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
                    @if($news->status !== 'published')
                    <button type="submit" name="status" value="published" class="btn" style="background:var(--c-emerald);color:#fff">
                        <i class="fas fa-paper-plane me-1"></i>Saqlash va chop etish
                    </button>
                    @endif
                    @if($news->status === 'published')
                    <a href="{{ route('news.show', $news->id) }}" target="_blank" class="btn btn-outline-secondary">
                        <i class="fas fa-eye me-1"></i>Ko'rish
                    </a>
                    @endif
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
                            <option value="draft"     {{ old('status',$news->status)==='draft'     ? 'selected':'' }}>Qoralama</option>
                            <option value="published" {{ old('status',$news->status)==='published' ? 'selected':'' }}>Chop etilgan</option>
                            <option value="archived"  {{ old('status',$news->status)==='archived'  ? 'selected':'' }}>Arxivlangan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="published_at" class="form-label" style="font-size:13px">Nashr sanasi</label>
                        <input type="datetime-local" class="form-control" id="published_at" name="published_at"
                               value="{{ old('published_at', $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : '') }}">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                               {{ old('is_featured',$news->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured" style="font-size:13px">
                            <i class="fas fa-star me-1" style="color:var(--c-amber)"></i>Tanlangan yangilik
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_breaking" name="is_breaking" value="1"
                               {{ old('is_breaking',$news->is_breaking) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_breaking" style="font-size:13px">
                            <i class="fas fa-bolt me-1" style="color:var(--c-rose)"></i>Tezkor yangilik
                        </label>
                    </div>
                    <div style="border-top:1px solid var(--c-border);padding-top:12px;font-size:12px;color:var(--c-text-3)">
                        <div class="mb-1"><i class="fas fa-eye me-1"></i>Ko'rishlar: {{ number_format($news->views_count) }}</div>
                        <div class="mb-1"><i class="fas fa-calendar me-1"></i>Yaratilgan: {{ $news->created_at->format('d.m.Y') }}</div>
                        <div><i class="fas fa-edit me-1"></i>Yangilangan: {{ $news->updated_at->format('d.m.Y') }}</div>
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
                            <option value="{{ $cat->id }}" {{ old('category_id',$news->category_id)==$cat->id ? 'selected':'' }}>
                                {{ $cat->name_uz }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tags" class="form-label" style="font-size:13px">Teglar</label>
                        <input type="text" class="form-control" id="tags" name="tags"
                               value="{{ old('tags', is_array($news->tags) ? implode(', ', $news->tags) : $news->tags) }}"
                               placeholder="teg1, teg2, teg3">
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
                    @if($news->featured_image)
                    <div class="mb-3">
                        <img src="{{ asset($news->featured_image) }}" alt="{{ $news->title_uz }}"
                             class="img-fluid rounded" id="current_image"
                             onerror="this.onerror=null;this.src='https://via.placeholder.com/800x450/e5e7eb/6b7280?text=No+Image'">
                    </div>
                    @endif
                    <div class="file-drop-area">
                        <input type="file" name="featured_image" accept="image/*" id="featured_image_input">
                        <i class="fas fa-image mb-1" style="font-size:24px;color:var(--c-amber)"></i>
                        <div style="font-size:12px;font-weight:600;color:var(--c-text);margin-top:4px">Yangi rasm tanlang</div>
                        <div style="font-size:11px;color:var(--c-text-3)">800×450 px tavsiya etiladi</div>
                    </div>
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
    content_style: 'body { font-family:"Times New Roman",Times,serif; font-size:14pt; line-height:1.6; padding:20px; background:#fff; } img { max-width:100%; height:auto; } table { border-collapse:collapse; width:100%; } table td, table th { border:1px solid #ccc; padding:8px; }',
    language: 'uz',
    image_advtab: true,
    automatic_uploads: true,
    images_upload_url: '{{ route("cms.upload.image") }}',
    images_upload_handler: function(blobInfo) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('_token', '{{ csrf_token() }}');
            fetch('{{ route("cms.upload.image") }}', { method: 'POST', body: formData })
                .then(r => r.ok ? r.json() : Promise.reject('HTTP ' + r.status))
                .then(result => result.location ? resolve(result.location) : reject('Upload failed'))
                .catch(err => reject('Upload failed: ' + err));
        });
    },
    branding: false,
    promotion: false
});

document.getElementById('featured_image_input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview_img').src = e.target.result;
            document.getElementById('image_preview').style.display = 'block';
            const current = document.getElementById('current_image');
            if (current) current.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
