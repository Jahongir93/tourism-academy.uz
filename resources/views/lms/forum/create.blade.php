@extends('layouts.dashboard-new')

@section('title', 'Yangi mavzu yaratish — Forum — LMS')
@section('page-title', 'Yangi mavzu yaratish')

@section('content')

<x-lms-alerts />

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-comments" style="color:var(--c-teal)"></i>
                <span>Mavzu ma'lumotlari</span>
            </div>
            <div class="card-body">
                <form action="{{ route('lms.forum.store') }}" method="POST" enctype="multipart/form-data" id="forumForm">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Mavzu sarlavhasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required
                               placeholder="Masalan: Laravel da qanday qilib...">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Fan (ixtiyoriy)</label>
                        <select class="form-select @error('subject_id') is-invalid @enderror"
                                id="subject_id" name="subject_id">
                            <option value="">Fanni tanlang</option>
                            @foreach($subjects ?? [] as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name_uz }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Agar savol ma'lum bir fanga tegishli bo'lsa, tanlang</div>
                        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Savol yoki muammo tavsifi <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror"
                                  id="content" name="content" rows="10" required
                                  placeholder="Savolingiz yoki muammoingizni batafsil yozing...">{{ old('content') }}</textarea>
                        <div class="form-text">Qanchalik batafsil yozsangiz, shunchalik yaxshi javob olasiz</div>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="attachments" class="form-label">
                            <i class="fas fa-paperclip me-1"></i>Fayllar va rasmlar (ixtiyoriy)
                        </label>
                        <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                               id="attachments" name="attachments[]" multiple
                               accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Bir nechta fayl tanlashingiz mumkin. Ruxsat: Rasm, PDF, Word, Excel, ZIP. Maks: 10MB.
                        </div>
                        @error('attachments.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="filePreview" class="mt-2"></div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Joylash
                        </button>
                        <a href="{{ route('lms.forum.index') }}" class="btn btn-outline-secondary">
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
                <i class="fas fa-lightbulb" style="color:var(--c-amber)"></i>
                <span>Foydali maslahatlar</span>
            </div>
            <div class="card-body">
                <ul class="mb-0" style="font-size:13px;color:var(--c-text-2);padding-left:16px">
                    <li class="mb-2">Sarlavhani qisqa va aniq yozing</li>
                    <li class="mb-2">Muammoni batafsil tasvirlab bering</li>
                    <li class="mb-2">Kod namunalarini qo'shing (agar kerak)</li>
                    <li class="mb-2">Nima qilishni sinab ko'rganingizni yozing</li>
                    <li>Xushmuomala va hurmatli bo'ling</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-triangle" style="color:var(--c-rose)"></i>
                <span>Qoidalar</span>
            </div>
            <div class="card-body">
                <ul class="mb-0" style="font-size:13px;color:var(--c-text-2);padding-left:16px">
                    <li class="mb-2">Spam yoki reklama joylashtirmang</li>
                    <li class="mb-2">Boshqalarni haqorat qilmang</li>
                    <li class="mb-2">Takroriy mavzular yaratmang</li>
                    <li class="mb-2">Tegishli fanni tanlang</li>
                    <li>Maxfiy ma'lumotlarni ulashmang</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('attachments').addEventListener('change', function() {
    const preview = document.getElementById('filePreview');
    preview.innerHTML = '';
    if (!this.files.length) return;

    const container = document.createElement('div');
    container.className = 'border rounded p-3';
    container.style = 'background:var(--c-bg)';

    const title = document.createElement('div');
    title.style = 'font-size:13px;font-weight:600;color:var(--c-text);margin-bottom:8px';
    title.innerHTML = '<i class="fas fa-file me-2" style="color:var(--c-teal)"></i>Tanlangan fayllar:';
    container.appendChild(title);

    Array.from(this.files).forEach(file => {
        const item = document.createElement('div');
        item.className = 'd-flex align-items-center gap-2 mb-1 p-2 rounded';
        item.style = 'background:var(--c-surface);border:1px solid var(--c-border)';
        const icon = getFileIcon(file.type);
        const size = formatFileSize(file.size);
        item.innerHTML = `<i class="${icon}" style="color:var(--c-teal)"></i>
            <div style="flex:1;min-width:0">
                <div style="font-size:12px;font-weight:600;color:var(--c-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${file.name}</div>
                <div style="font-size:11px;color:var(--c-text-3)">${size}</div>
            </div>`;
        container.appendChild(item);
    });
    preview.appendChild(container);
});

function getFileIcon(mimeType) {
    if (mimeType.startsWith('image/')) return 'fas fa-image';
    if (mimeType === 'application/pdf') return 'fas fa-file-pdf';
    if (mimeType.includes('word')) return 'fas fa-file-word';
    if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'fas fa-file-excel';
    if (mimeType.includes('zip') || mimeType.includes('rar')) return 'fas fa-file-archive';
    return 'fas fa-file';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}
</script>
@endpush
