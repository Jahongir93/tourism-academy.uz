@extends('layouts.dashboard-new')

@section('title', 'CMS — Media kutubxona')
@section('page-title', 'Media kutubxona')

@section('styles')
<style>
.media-card { border:1px solid var(--c-border);border-radius:12px;overflow:hidden;background:var(--c-bg);transition:all .15s; }
.media-card:hover { border-color:var(--c-violet);box-shadow:0 4px 16px rgba(124,58,237,.1); }
.media-thumb { height:160px;object-fit:cover;width:100%; }
.media-icon-wrap { height:160px;display:flex;align-items:center;justify-content:center; }
.action-btn { width:30px;height:30px;border-radius:7px;border:1px solid var(--c-border);background:var(--c-bg);display:inline-flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;transition:all .12s;text-decoration:none;color:var(--c-text-2); }
.action-btn:hover { border-color:var(--c-teal);color:var(--c-teal);background:rgba(20,184,166,.07); }
.action-btn.danger:hover { border-color:var(--c-rose);color:var(--c-rose);background:rgba(244,63,94,.07); }
</style>
@endsection

@section('content')

<x-lms-alerts />

<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-photo-video" style="color:var(--c-amber)"></i>
            <span>Media fayllari</span>
        </div>
        <button type="button" class="btn btn-sm" style="background:var(--c-amber);color:#fff"
                data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload me-1"></i>Fayl yuklash
        </button>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($media ?? [] as $item)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="media-card">
                    @if($item->is_image)
                        <img src="{{ asset('storage/' . $item->path) }}" class="media-thumb"
                             alt="{{ $item->alt_text ?? $item->name }}"
                             onerror="this.onerror=null;this.src='https://via.placeholder.com/400x300/e5e7eb/6b7280?text=Image+Error'">
                    @elseif($item->is_pdf)
                        <div class="media-icon-wrap" style="background:rgba(244,63,94,.06)">
                            <i class="fas fa-file-pdf fa-3x" style="color:var(--c-rose)"></i>
                        </div>
                    @elseif(str_contains($item->mime_type ?? '', 'word') || str_contains($item->mime_type ?? '', 'document'))
                        <div class="media-icon-wrap" style="background:rgba(14,165,233,.06)">
                            <i class="fas fa-file-word fa-3x" style="color:var(--c-sky)"></i>
                        </div>
                    @else
                        <div class="media-icon-wrap" style="background:var(--c-bg)">
                            <i class="fas fa-file fa-3x" style="color:var(--c-text-3)"></i>
                        </div>
                    @endif
                    <div style="padding:12px">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:2px" title="{{ $item->name }}">{{ $item->name }}</div>
                        <div style="font-size:11px;color:var(--c-text-3);margin-bottom:10px">{{ $item->formatted_size }}</div>

                        @if($item->is_image)
                        <div class="mb-2">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm"
                                       style="font-size:11px"
                                       value='<img src="{{ asset('storage/' . $item->path) }}" alt="{{ $item->alt_text ?? $item->name }}">'
                                       readonly id="code-{{ $item->id }}">
                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                        onclick="copyCode({{ $item->id }})" title="Nusxa olish">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ asset('storage/' . $item->path) }}" class="action-btn" target="_blank" title="Ko'rish" style="flex:1;border-radius:8px;width:auto;height:28px">
                                <i class="fas fa-eye me-1"></i><span style="font-size:12px">Ko'rish</span>
                            </a>
                            <button type="button" class="action-btn danger" onclick="deleteMedia({{ $item->id }})" title="O'chirish">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-photo-video fa-3x mb-3" style="color:var(--c-border)"></i>
                <div style="font-size:15px;font-weight:600;color:var(--c-text-2);margin-bottom:4px">Media fayllar topilmadi</div>
                <div style="font-size:13px;color:var(--c-text-3)">Yuklash uchun yuqoridagi tugmani bosing</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Upload Modal --}}
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--c-border)">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-cloud-upload-alt" style="color:var(--c-amber)"></i>
                        <h6 class="modal-title mb-0">Fayl yuklash</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="files" class="form-label" style="font-size:13px">Fayllarni tanlang</label>
                        <input type="file" class="form-control" id="files" name="files[]" multiple required
                               accept="image/*,.pdf,.doc,.docx">
                        <div style="font-size:11px;color:var(--c-text-3);margin-top:6px">
                            JPG, PNG, GIF, WebP, SVG, PDF, Word (.docx) &nbsp;|&nbsp; Maks: <strong>10MB</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="alt_text" class="form-label" style="font-size:13px">Alt text (ixtiyoriy)</label>
                        <input type="text" class="form-control" id="alt_text" name="alt_text">
                    </div>
                    <div id="uploadProgress" class="mb-2" style="display:none">
                        <div class="progress" style="height:6px">
                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                 style="width:100%;background:var(--c-amber)"></div>
                        </div>
                        <div style="font-size:12px;color:var(--c-text-3);margin-top:4px">Yuklanmoqda...</div>
                    </div>
                    <div id="uploadError" class="alert alert-danger py-2" style="display:none;font-size:13px"></div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--c-border)">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-sm" id="uploadBtn" style="background:var(--c-amber);color:#fff">
                        <i class="fas fa-upload me-1"></i>Yuklash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadProgress = document.getElementById('uploadProgress');
    const uploadError = document.getElementById('uploadError');

    uploadError.style.display = 'none';
    uploadProgress.style.display = 'block';
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Yuklanmoqda...';

    fetch('{{ route('cms.media.upload') }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            throw new Error(data.message || 'Yuklashda xatolik');
        }
    })
    .catch(err => {
        uploadError.textContent = err.message;
        uploadError.style.display = 'block';
        uploadProgress.style.display = 'none';
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i>Yuklash';
    });
});

function copyCode(id) {
    const input = document.getElementById('code-' + id);
    input.select();
    document.execCommand('copy');
    const btn = event.target.closest('button');
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i>';
    setTimeout(() => { btn.innerHTML = orig; }, 2000);
}

function deleteMedia(id) {
    if (!confirm('Ushbu faylni o\'chirmoqchimisiz?')) return;
    fetch(`/cms/media/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { window.location.reload(); }
        else { alert('O\'chirishda xatolik: ' + (data.message || 'Noma\'lum xatolik')); }
    })
    .catch(err => { alert('O\'chirishda xatolik: ' + err.message); });
}
</script>
@endpush
