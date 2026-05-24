@extends('layouts.dashboard-new')

@section('title', 'Sahifani tahrirlash — CMS')
@section('page-title', 'Sahifani tahrirlash')

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

<form action="{{ route('cms.pages.update', $page) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        {{-- Main --}}
        <div class="col-lg-8">

            {{-- Title --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-heading" style="color:var(--c-violet)"></i>
                    <span>Sahifa sarlavhasi</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title_uz" class="form-label" style="font-size:13px">Sarlavha (O'zbek) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title_uz') is-invalid @enderror"
                               id="title_uz" name="title_uz" value="{{ old('title_uz', $page->title_uz) }}" required>
                        @error('title_uz')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="title_ru" class="form-label" style="font-size:13px">Sarlavha (Rus)</label>
                            <input type="text" class="form-control @error('title_ru') is-invalid @enderror"
                                   id="title_ru" name="title_ru" value="{{ old('title_ru', $page->title_ru) }}">
                            @error('title_ru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="title_en" class="form-label" style="font-size:13px">Sarlavha (Ingliz)</label>
                            <input type="text" class="form-control @error('title_en') is-invalid @enderror"
                                   id="title_en" name="title_en" value="{{ old('title_en', $page->title_en) }}">
                            @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div>
                        <label for="slug" class="form-label" style="font-size:13px">Slug (URL)</label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-size:13px">/</span>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                   id="slug" name="slug" value="{{ old('slug', $page->slug) }}">
                            <button type="button" class="btn btn-outline-secondary" onclick="generateSlug()" title="Avtomatik yaratish">
                                <i class="fas fa-magic"></i>
                            </button>
                        </div>
                        @error('slug')<div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-edit" style="color:var(--c-emerald)"></i>
                    <span>Sahifa mazmuni</span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="content_uz" class="form-label" style="font-size:13px">Mazmun (O'zbek)</label>
                        <textarea class="form-control tinymce-editor" id="content_uz" name="content_uz" rows="10">{{ old('content_uz', $page->content_uz) }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label for="content_ru" class="form-label" style="font-size:13px">Mazmun (Rus)</label>
                        <textarea class="form-control tinymce-editor" id="content_ru" name="content_ru" rows="10">{{ old('content_ru', $page->content_ru) }}</textarea>
                    </div>
                    <div>
                        <label for="content_en" class="form-label" style="font-size:13px">Mazmun (Ingliz)</label>
                        <textarea class="form-control tinymce-editor" id="content_en" name="content_en" rows="10">{{ old('content_en', $page->content_en) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-search" style="color:var(--c-sky)"></i>
                    <span>SEO sozlamalari</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="meta_description_uz" class="form-label" style="font-size:13px">Meta tavsif (O'zbek)</label>
                        <textarea class="form-control" id="meta_description_uz" name="meta_description_uz" rows="2">{{ old('meta_description_uz', $page->meta_description_uz) }}</textarea>
                    </div>
                    <div>
                        <label for="meta_keywords" class="form-label" style="font-size:13px">Kalit so'zlar</label>
                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                               value="{{ old('meta_keywords', $page->meta_keywords) }}">
                        <div style="font-size:11px;color:var(--c-text-3);margin-top:4px">Vergul bilan ajrating</div>
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
                    <a href="{{ route('cms.pages.preview', $page) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="fas fa-eye me-1"></i>Ko'rish
                    </a>
                    <a href="{{ route('cms.pages.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>

            {{-- Status --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-sliders-h" style="color:var(--c-sky)"></i>
                    <span>Holat</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label" style="font-size:13px">Holat</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft"     {{ old('status',$page->status)==='draft'     ? 'selected':'' }}>Qoralama</option>
                            <option value="published" {{ old('status',$page->status)==='published' ? 'selected':'' }}>Chop etilgan</option>
                            <option value="archived"  {{ old('status',$page->status)==='archived'  ? 'selected':'' }}>Arxivlangan</option>
                        </select>
                    </div>
                    <div>
                        <label for="published_at" class="form-label" style="font-size:13px">Nashr sanasi</label>
                        <input type="datetime-local" class="form-control" id="published_at" name="published_at"
                               value="{{ old('published_at', $page->published_at?->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>
            </div>

            {{-- Settings --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-cog" style="color:var(--c-amber)"></i>
                    <span>Sozlamalar</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="parent_id" class="form-label" style="font-size:13px">Ota sahifa</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">Yo'q</option>
                            @foreach($pages ?? [] as $p)
                            @if($p->id !== $page->id)
                            <option value="{{ $p->id }}" {{ $page->parent_id == $p->id ? 'selected':'' }}>{{ $p->title_uz }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="template" class="form-label" style="font-size:13px">Shablon</label>
                        <select class="form-select" id="template" name="template">
                            <option value="default"  {{ $page->template==='default'  ? 'selected':'' }}>Standart</option>
                            <option value="homepage" {{ $page->template==='homepage' ? 'selected':'' }}>Bosh sahifa</option>
                            <option value="sidebar"  {{ $page->template==='sidebar'  ? 'selected':'' }}>Sidebar bilan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="order_position" class="form-label" style="font-size:13px">Tartib raqami</label>
                        <input type="number" class="form-control" id="order_position" name="order_position"
                               value="{{ old('order_position', $page->order_position) }}" min="0">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="is_homepage" name="is_homepage" value="1"
                               {{ $page->is_homepage ? 'checked':'' }}>
                        <label class="form-check-label" for="is_homepage" style="font-size:13px">Bosh sahifa</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="show_in_menu" name="show_in_menu" value="1"
                               {{ $page->show_in_menu ? 'checked':'' }}>
                        <label class="form-check-label" for="show_in_menu" style="font-size:13px">Menyuda ko'rsatish</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show_in_footer" name="show_in_footer" value="1"
                               {{ $page->show_in_footer ? 'checked':'' }}>
                        <label class="form-check-label" for="show_in_footer" style="font-size:13px">Footerda ko'rsatish</label>
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
                    @if($page->featured_image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $page->featured_image) }}" class="img-fluid rounded" alt="Joriy rasm">
                    </div>
                    @endif
                    <div class="file-drop-area">
                        <input type="file" name="featured_image" accept="image/*">
                        <i class="fas fa-image mb-1" style="font-size:24px;color:var(--c-amber)"></i>
                        <div style="font-size:12px;font-weight:600;color:var(--c-text);margin-top:4px">{{ $page->featured_image ? 'Yangi rasm tanlang' : 'Rasm tanlang' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
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
                .then(r => r.json())
                .then(res => res.location ? resolve(res.location) : reject('Upload failed'))
                .catch(err => reject('Upload failed: ' + err));
        });
    },
    branding: false,
    promotion: false
});

const translitMap = {
    'а':'a','б':'b','в':'v','г':'g','д':'d','е':'e','ё':'yo','ж':'zh','з':'z','и':'i','й':'y',
    'к':'k','л':'l','м':'m','н':'n','о':'o','п':'p','р':'r','с':'s','т':'t','у':'u',
    'ф':'f','х':'x','ц':'ts','ч':'ch','ш':'sh','щ':'shch','ъ':'','ы':'i','ь':'','э':'e',
    'ю':'yu','я':'ya','ў':'o','қ':'q','ғ':'g','ҳ':'h'
};

function transliterate(text) {
    text = text.toLowerCase().replace(/o['ʻ]/g,'o').replace(/g['ʻ]/g,'g');
    for (const [k,v] of Object.entries(translitMap)) text = text.split(k).join(v);
    return text;
}

function generateSlug() {
    document.getElementById('slug').value = transliterate(document.getElementById('title_uz').value)
        .replace(/[^a-z0-9\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-').replace(/^-|-$/g,'');
}
</script>
@endpush
