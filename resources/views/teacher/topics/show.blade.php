@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('teacher.topics.index') }}">Fanlar</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('teacher.topics.subject', $topic->subject_id) }}">{{ $topic->subject->name }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $topic->title }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h3>{{ $topic->title }}</h3>
                <div class="btn-group">
                    <a href="{{ route('teacher.topics.edit', [$topic->subject_id, $topic->id]) }}"
                       class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Tahrirlash
                    </a>
                    <form action="{{ route('teacher.topics.destroy', [$topic->subject_id, $topic->id]) }}"
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> O'chirish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Topic Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Mavzu Ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    @if($topic->description)
                        <div class="mb-3">
                            <h6>Qisqacha Tavsif:</h6>
                            <p class="text-muted">{{ $topic->description }}</p>
                        </div>
                    @endif

                    @if($topic->content)
                        <div class="mb-3">
                            <h6>To'liq Tarkib:</h6>
                            <div class="border rounded p-3 bg-light">
                                {!! nl2br(e($topic->content)) !!}
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted d-block">Tartib</small>
                                <h4 class="mb-0">{{ $topic->order }}</h4>
                            </div>
                        </div>
                        @if($topic->duration_hours)
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <small class="text-muted d-block">Davomiyligi</small>
                                    <h4 class="mb-0">{{ $topic->duration_hours }} <small>soat</small></h4>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted d-block">Resurslar</small>
                                <h4 class="mb-0">{{ $topic->resources->count() }} <small>ta</small></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resources Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Resurslar</h5>
                    <button type="button"
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#addResourceModal">
                        <i class="bi bi-plus-circle"></i> Resurs Qo'shish
                    </button>
                </div>
                <div class="card-body">
                    @if($topic->resources->isEmpty())
                        <div class="alert alert-info">
                            Bu mavzu uchun hali resurslar qo'shilmagan.
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($topic->resources as $resource)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                @if($resource->type === 'file')
                                                    <i class="bi bi-file-earmark text-primary"></i>
                                                @elseif($resource->type === 'video')
                                                    <i class="bi bi-play-circle text-danger"></i>
                                                @else
                                                    <i class="bi bi-link-45deg text-info"></i>
                                                @endif
                                                {{ $resource->title }}
                                                <span class="badge bg-secondary ms-2">{{ ucfirst($resource->type) }}</span>
                                            </h6>
                                            @if($resource->description)
                                                <p class="mb-2 text-muted small">{{ $resource->description }}</p>
                                            @endif

                                            @if($resource->type === 'file' && $resource->file_path)
                                                <div class="small text-muted">
                                                    <i class="bi bi-paperclip"></i>
                                                    {{ basename($resource->file_path) }}
                                                    @if($resource->file_size)
                                                        ({{ $resource->formatted_file_size }})
                                                    @endif
                                                    <a href="{{ Storage::url($resource->file_path) }}"
                                                       class="ms-2 btn btn-sm btn-outline-primary"
                                                       download>
                                                        <i class="bi bi-download"></i> Yuklab olish
                                                    </a>
                                                </div>
                                            @elseif(($resource->type === 'video' || $resource->type === 'link') && $resource->url)
                                                <div class="small">
                                                    <a href="{{ $resource->url }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-box-arrow-up-right"></i> Ochish
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <form action="{{ route('teacher.topics.resource.delete', [$topic->subject_id, $topic->id, $resource->id]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Ma'lumot</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>Fan:</strong><br>
                            {{ $topic->subject->name }}
                        </li>
                        <li class="mb-2">
                            <strong>O'qituvchi:</strong><br>
                            @if($topic->teacher)
                                {{ $topic->teacher->user->name }}
                            @else
                                <span class="text-muted">Belgilanmagan</span>
                            @endif
                        </li>
                        <li class="mb-2">
                            <strong>Yaratilgan:</strong><br>
                            {{ $topic->created_at->format('d.m.Y H:i') }}
                        </li>
                        <li>
                            <strong>Yangilangan:</strong><br>
                            {{ $topic->updated_at->format('d.m.Y H:i') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Resource Modal -->
<div class="modal fade" id="addResourceModal" tabindex="-1" aria-labelledby="addResourceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('teacher.topics.resource.add', [$topic->subject_id, $topic->id]) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addResourceModalLabel">Resurs Qo'shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="resource_title" class="form-label">Resurs Nomi <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="resource_title"
                               name="title"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="resource_type" class="form-label">Resurs Turi <span class="text-danger">*</span></label>
                        <select class="form-select"
                                id="resource_type"
                                name="type"
                                required
                                onchange="toggleResourceFields(this.value)">
                            <option value="">Tanlang...</option>
                            <option value="file">Fayl</option>
                            <option value="video">Video Link</option>
                            <option value="link">Web Havola</option>
                        </select>
                    </div>

                    <div class="mb-3" id="file_field" style="display: none;">
                        <label for="resource_file" class="form-label">Fayl <span class="text-danger">*</span></label>
                        <input type="file"
                               class="form-control"
                               id="resource_file"
                               name="file"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar">
                        <small class="text-muted">Maksimal hajm: 100MB</small>
                    </div>

                    <div class="mb-3" id="video_field" style="display: none;">
                        <label for="video_url" class="form-label">Video URL <span class="text-danger">*</span></label>
                        <input type="url"
                               class="form-control"
                               id="video_url"
                               name="video_url"
                               placeholder="https://www.youtube.com/watch?v=...">
                        <small class="text-muted">YouTube, Vimeo va boshqa video linklar</small>
                    </div>

                    <div class="mb-3" id="link_field" style="display: none;">
                        <label for="link_url" class="form-label">Web Havola <span class="text-danger">*</span></label>
                        <input type="url"
                               class="form-control"
                               id="link_url"
                               name="link_url"
                               placeholder="https://example.com">
                    </div>

                    <div class="mb-3">
                        <label for="resource_description" class="form-label">Tavsif</label>
                        <textarea class="form-control"
                                  id="resource_description"
                                  name="description"
                                  rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Qo'shish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleResourceFields(type) {
    document.getElementById('file_field').style.display = 'none';
    document.getElementById('video_field').style.display = 'none';
    document.getElementById('link_field').style.display = 'none';

    document.getElementById('resource_file').removeAttribute('required');
    document.getElementById('video_url').removeAttribute('required');
    document.getElementById('link_url').removeAttribute('required');

    if (type === 'file') {
        document.getElementById('file_field').style.display = 'block';
        document.getElementById('resource_file').setAttribute('required', 'required');
    } else if (type === 'video') {
        document.getElementById('video_field').style.display = 'block';
        document.getElementById('video_url').setAttribute('required', 'required');
    } else if (type === 'link') {
        document.getElementById('link_field').style.display = 'block';
        document.getElementById('link_url').setAttribute('required', 'required');
    }
}
</script>
@endpush
@endsection
