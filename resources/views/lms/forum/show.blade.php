@extends('layouts.dashboard-new')

@section('title', $post->title . ' - Forum - LMS')
@section('page-title', $post->title)

@section('content')

<x-lms-alerts />

    <!-- Main Post -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <!-- Post Header -->
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div class="flex-grow-1">
                    <h1 class="h2 mb-3">{{ $post->title }}</h1>
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center me-2 rounded-circle"
                                 style="width:40px;height:40px;background:linear-gradient(135deg,var(--c-teal),var(--c-emerald));flex-shrink:0">
                                <span class="text-white fw-bold">{{ substr($post->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $post->user->name }}</div>
                                <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        @if($post->subject)
                            <span class="badge bg-primary">
                                <i class="fas fa-book me-1"></i>
                                {{ $post->subject->name_uz }}
                            </span>
                        @endif

                        @if($post->is_pinned)
                            <span class="badge bg-warning">
                                <i class="fas fa-thumbtack me-1"></i>
                                Mahkamlangan
                            </span>
                        @endif

                        @if($post->is_answered)
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Hal qilindi
                            </span>
                        @endif

                        @if($post->is_locked)
                            <span class="badge bg-danger">
                                <i class="fas fa-lock me-1"></i>
                                Yopilgan
                            </span>
                        @endif
                    </div>
                </div>

                @can('update', $post)
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('lms.forum.edit', $post) }}">
                            <i class="fas fa-edit me-2"></i>Tahrirlash
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('lms.forum.destroy', $post) }}" method="POST"
                                  onsubmit="return confirm('Rostan o\'chirmoqchimisiz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash me-2"></i>O'chirish
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endcan
            </div>

            <!-- Post Content -->
            <div class="post-content mb-4">
                {!! nl2br(e($post->content)) !!}
            </div>

            <!-- Attachments -->
            @if($post->attachments && count($post->attachments) > 0)
            <div class="attachments mb-4">
                <h6 class="text-muted mb-3">
                    <i class="fas fa-paperclip me-2"></i>Biriktirilgan fayllar:
                </h6>
                <div class="row g-2">
                    @foreach($post->attachments as $attachment)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-3">
                                    @php
                                        $isImage = in_array(strtolower($attachment['extension'] ?? ''), ['jpg', 'jpeg', 'png', 'gif']);
                                    @endphp

                                    @if($isImage)
                                        <img src="{{ asset('storage/' . $attachment['path']) }}"
                                             alt="{{ $attachment['name'] }}"
                                             class="rounded"
                                             style="width: 60px; height: 60px; object-fit: cover;"
                                             data-bs-toggle="modal"
                                             data-bs-target="#imageModal{{ $loop->index }}"
                                             role="button">
                                    @else
                                        <div class="text-center" style="width: 60px;">
                                            <i class="fas fa-file-{{ $attachment['extension'] === 'pdf' ? 'pdf' : ($attachment['extension'] === 'doc' || $attachment['extension'] === 'docx' ? 'word' : ($attachment['extension'] === 'xls' || $attachment['extension'] === 'xlsx' ? 'excel' : 'archive')) }} fa-3x text-primary"></i>
                                        </div>
                                    @endif

                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-medium text-truncate">{{ $attachment['name'] }}</div>
                                        <small class="text-muted">{{ number_format($attachment['size'] / 1024, 2) }} KB</small>
                                    </div>

                                    <a href="{{ asset('storage/' . $attachment['path']) }}"
                                       download="{{ $attachment['name'] }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($isImage)
                    <!-- Image Modal -->
                    <div class="modal fade" id="imageModal{{ $loop->index }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ $attachment['name'] }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="{{ asset('storage/' . $attachment['path']) }}"
                                         alt="{{ $attachment['name'] }}"
                                         class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Like/Dislike Buttons -->
            @auth
            <div class="d-flex gap-2 mb-3 border-top pt-3">
                <button type="button"
                        class="btn btn-outline-success btn-sm reaction-btn"
                        data-post-id="{{ $post->id }}"
                        data-type="like"
                        data-active="{{ $post->userReaction()?->type === 'like' ? 'true' : 'false' }}">
                    <i class="fas fa-thumbs-up me-1"></i>
                    <span class="likes-count">{{ $post->likes()->count() }}</span>
                    Yoqdi
                </button>
                <button type="button"
                        class="btn btn-outline-danger btn-sm reaction-btn"
                        data-post-id="{{ $post->id }}"
                        data-type="dislike"
                        data-active="{{ $post->userReaction()?->type === 'dislike' ? 'true' : 'false' }}">
                    <i class="fas fa-thumbs-down me-1"></i>
                    <span class="dislikes-count">{{ $post->dislikes()->count() }}</span>
                    Yoqmadi
                </button>
            </div>
            @endauth

            <!-- Post Stats -->
            <div class="d-flex gap-4 text-muted small">
                <span><i class="fas fa-eye me-1"></i> {{ $post->view_count ?? 0 }} ko'rildi</span>
                <span><i class="fas fa-comment me-1"></i> {{ $post->reply_count ?? 0 }} javob</span>
            </div>
        </div>
    </div>

    <!-- Replies Section -->
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-comments" style="color:var(--c-teal)"></i>
            <span>Javoblar ({{ $post->reply_count ?? 0 }})</span>
        </div>
        <div class="card-body">
            <!-- Reply Form (if not locked) -->
            @if(!$post->is_locked)
            <div class="mb-4 p-4 rounded" style="background:var(--c-bg)">
                <h6 class="mb-3">Javob yozish</h6>
                <form action="{{ route('lms.forum.reply', $post) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <textarea class="form-control @error('content') is-invalid @enderror"
                                  name="content" rows="4"
                                  placeholder="Javobingizni yozing..." required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reply_attachments" class="form-label">
                            <i class="fas fa-paperclip me-2"></i>Fayllar va rasmlar (ixtiyoriy)
                        </label>
                        <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                               id="reply_attachments" name="attachments[]" multiple
                               accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
                        @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>
                            Maksimal hajm: 10MB. Formatlar: Rasm, PDF, Word, Excel, ZIP
                        </small>
                        <div id="replyFilePreview" class="mt-2"></div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-reply me-2"></i>Javob berish
                    </button>
                </form>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-lock me-2"></i>
                Bu mavzu yopilgan. Yangi javoblar qo'shib bo'lmaydi.
            </div>
            @endif

            <!-- Replies List -->
            <div class="replies-list">
                @forelse($post->replies as $reply)
                <div class="border-start border-3 border-primary ps-4 mb-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                             style="width:40px;height:40px;background:linear-gradient(135deg,var(--c-sky),var(--c-violet));flex-shrink:0">
                            <span class="text-white fw-bold">{{ substr($reply->user->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>{{ $reply->user->name }}</strong>
                                    <small class="text-muted ms-2">{{ $reply->created_at->diffForHumans() }}</small>
                                </div>
                                @if($reply->user_id === Auth::id() || Auth::user()->hasRole(['SuperAdmin', 'admin']))
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form action="{{ route('lms.forum.destroy', $reply) }}" method="POST"
                                                  onsubmit="return confirm('Javobni o\'chirmoqchimisiz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i>O'chirish
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                @endif
                            </div>
                            <div class="reply-content mb-2">
                                {!! nl2br(e($reply->content)) !!}
                            </div>

                            <!-- Reply Like/Dislike Buttons -->
                            @auth
                            <div class="d-flex gap-2 mb-3">
                                <button type="button"
                                        class="btn btn-outline-success btn-sm reaction-btn"
                                        data-post-id="{{ $reply->id }}"
                                        data-type="like"
                                        data-active="{{ $reply->userReaction()?->type === 'like' ? 'true' : 'false' }}">
                                    <i class="fas fa-thumbs-up me-1"></i>
                                    <span class="likes-count">{{ $reply->likes()->count() }}</span>
                                </button>
                                <button type="button"
                                        class="btn btn-outline-danger btn-sm reaction-btn"
                                        data-post-id="{{ $reply->id }}"
                                        data-type="dislike"
                                        data-active="{{ $reply->userReaction()?->type === 'dislike' ? 'true' : 'false' }}">
                                    <i class="fas fa-thumbs-down me-1"></i>
                                    <span class="dislikes-count">{{ $reply->dislikes()->count() }}</span>
                                </button>
                            </div>
                            @endauth

                            <!-- Reply Attachments -->
                            @if($reply->attachments && count($reply->attachments) > 0)
                            <div class="attachments mt-3">
                                <small class="text-muted d-block mb-2">
                                    <i class="fas fa-paperclip me-1"></i>Biriktirilgan fayllar:
                                </small>
                                <div class="row g-2">
                                    @foreach($reply->attachments as $replyAttachment)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    @php
                                                        $isReplyImage = in_array(strtolower($replyAttachment['extension'] ?? ''), ['jpg', 'jpeg', 'png', 'gif']);
                                                    @endphp

                                                    @if($isReplyImage)
                                                        <img src="{{ asset('storage/' . $replyAttachment['path']) }}"
                                                             alt="{{ $replyAttachment['name'] }}"
                                                             class="rounded"
                                                             style="width: 50px; height: 50px; object-fit: cover;"
                                                             data-bs-toggle="modal"
                                                             data-bs-target="#replyImageModal{{ $reply->id }}_{{ $loop->index }}"
                                                             role="button">
                                                    @else
                                                        <div class="text-center" style="width: 50px;">
                                                            <i class="fas fa-file-{{ $replyAttachment['extension'] === 'pdf' ? 'pdf' : ($replyAttachment['extension'] === 'doc' || $replyAttachment['extension'] === 'docx' ? 'word' : ($replyAttachment['extension'] === 'xls' || $replyAttachment['extension'] === 'xlsx' ? 'excel' : 'archive')) }} fa-2x text-primary"></i>
                                                        </div>
                                                    @endif

                                                    <div class="flex-grow-1 min-w-0">
                                                        <div class="small text-truncate">{{ $replyAttachment['name'] }}</div>
                                                        <small class="text-muted">{{ number_format($replyAttachment['size'] / 1024, 2) }} KB</small>
                                                    </div>

                                                    <a href="{{ asset('storage/' . $replyAttachment['path']) }}"
                                                       download="{{ $replyAttachment['name'] }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($isReplyImage)
                                    <!-- Reply Image Modal -->
                                    <div class="modal fade" id="replyImageModal{{ $reply->id }}_{{ $loop->index }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ $replyAttachment['name'] }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ asset('storage/' . $replyAttachment['path']) }}"
                                                         alt="{{ $replyAttachment['name'] }}"
                                                         class="img-fluid">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-4">Hali javoblar yo'q. Birinchi bo'lib javob bering!</p>
                @endforelse
            </div>
        </div>
    </div>

@push('scripts')
<script>
// Like/Dislike Reaction Handler
document.querySelectorAll('.reaction-btn').forEach(button => {
    button.addEventListener('click', async function() {
        const postId = this.dataset.postId;
        const type = this.dataset.type;
        const isActive = this.dataset.active === 'true';

        try {
            const response = await fetch(`/lms/forum/${postId}/react`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ type })
            });

            const data = await response.json();

            if (data.success) {
                // Update counts
                const container = this.closest('.d-flex');
                const likeBtn = container.querySelector('[data-type="like"]');
                const dislikeBtn = container.querySelector('[data-type="dislike"]');

                likeBtn.querySelector('.likes-count').textContent = data.likes_count;
                dislikeBtn.querySelector('.dislikes-count').textContent = data.dislikes_count;

                // Update active states
                if (data.action === 'removed') {
                    this.dataset.active = 'false';
                    this.classList.remove('active');
                    if (type === 'like') {
                        this.classList.remove('btn-success');
                        this.classList.add('btn-outline-success');
                    } else {
                        this.classList.remove('btn-danger');
                        this.classList.add('btn-outline-danger');
                    }
                } else if (data.action === 'added') {
                    this.dataset.active = 'true';
                    if (type === 'like') {
                        this.classList.remove('btn-outline-success');
                        this.classList.add('btn-success');
                    } else {
                        this.classList.remove('btn-outline-danger');
                        this.classList.add('btn-danger');
                    }
                } else if (data.action === 'updated') {
                    // Remove active from other button
                    const otherBtn = type === 'like' ? dislikeBtn : likeBtn;
                    otherBtn.dataset.active = 'false';
                    if (type === 'like') {
                        dislikeBtn.classList.remove('btn-danger');
                        dislikeBtn.classList.add('btn-outline-danger');
                        this.classList.remove('btn-outline-success');
                        this.classList.add('btn-success');
                    } else {
                        likeBtn.classList.remove('btn-success');
                        likeBtn.classList.add('btn-outline-success');
                        this.classList.remove('btn-outline-danger');
                        this.classList.add('btn-danger');
                    }
                    this.dataset.active = 'true';
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Xatolik yuz berdi. Iltimos qaytadan urinib ko\'ring.');
        }
    });
});

// Set initial active states
document.querySelectorAll('.reaction-btn[data-active="true"]').forEach(btn => {
    const type = btn.dataset.type;
    if (type === 'like') {
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-success');
    } else {
        btn.classList.remove('btn-outline-danger');
        btn.classList.add('btn-danger');
    }
});

// Reply file preview
document.getElementById('reply_attachments')?.addEventListener('change', function(e) {
    const preview = document.getElementById('replyFilePreview');
    preview.innerHTML = '';

    if (this.files.length > 0) {
        const container = document.createElement('div');
        container.className = 'border rounded p-3 bg-white';

        const title = document.createElement('h6');
        title.className = 'mb-3';
        title.innerHTML = '<i class="fas fa-file me-2"></i>Tanlangan fayllar:';
        container.appendChild(title);

        Array.from(this.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'd-flex align-items-center gap-2 mb-2 p-2 bg-light rounded border';

            const icon = getFileIcon(file.type, file.name);
            const size = formatFileSize(file.size);

            fileItem.innerHTML = `
                <i class="${icon} text-primary"></i>
                <div class="flex-grow-1">
                    <div class="fw-medium">${file.name}</div>
                    <small class="text-muted">${size}</small>
                </div>
            `;

            container.appendChild(fileItem);
        });

        preview.appendChild(container);
    }
});

function getFileIcon(mimeType, filename) {
    if (mimeType.startsWith('image/')) return 'fas fa-image';
    if (mimeType === 'application/pdf') return 'fas fa-file-pdf';
    if (mimeType.includes('word')) return 'fas fa-file-word';
    if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'fas fa-file-excel';
    if (mimeType.includes('zip') || mimeType.includes('rar')) return 'fas fa-file-archive';
    return 'fas fa-file';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}
</script>
@endpush
@endsection
