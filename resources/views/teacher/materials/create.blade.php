@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-upload me-2"></i>
                        Yangi material yuklash
                    </h4>
                    <p class="mb-0 opacity-75">Darslar uchun material, video yoki havola qo'shing</p>
                </div>
                <div>
                    <a href="{{ route('teacher.materials.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.materials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Material ma'lumotlari
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Type Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-layer-group text-primary me-1"></i>Material turi
                                <span class="text-danger">*</span>
                            </label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="type" id="type_document" value="document" checked onchange="updateFormFields()">
                                    <label class="btn btn-outline-primary w-100 py-3" for="type_document">
                                        <i class="fas fa-file-alt fa-2x mb-2 d-block"></i>
                                        <strong>Hujjat</strong>
                                        <small class="d-block text-muted">PDF, Word, Excel</small>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="type" id="type_video" value="video" onchange="updateFormFields()">
                                    <label class="btn btn-outline-danger w-100 py-3" for="type_video">
                                        <i class="fas fa-video fa-2x mb-2 d-block"></i>
                                        <strong>Video</strong>
                                        <small class="d-block text-muted">YouTube, Vimeo</small>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="type" id="type_presentation" value="presentation" onchange="updateFormFields()">
                                    <label class="btn btn-outline-warning w-100 py-3" for="type_presentation">
                                        <i class="fas fa-file-powerpoint fa-2x mb-2 d-block"></i>
                                        <strong>Taqdimot</strong>
                                        <small class="d-block text-muted">PowerPoint</small>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="type" id="type_link" value="link" onchange="updateFormFields()">
                                    <label class="btn btn-outline-info w-100 py-3" for="type_link">
                                        <i class="fas fa-link fa-2x mb-2 d-block"></i>
                                        <strong>Tashqi havola</strong>
                                        <small class="d-block text-muted">Boshqa sayt</small>
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-book text-primary me-1"></i>Fan
                                <span class="text-danger">*</span>
                            </label>
                            <select name="subject_id"
                                    id="subjectSelect"
                                    class="form-select @error('subject_id') is-invalid @enderror"
                                    required
                                    onchange="updateGroupsList()">
                                <option value="">Fanni tanlang</option>
                                @foreach($subjects as $subjectData)
                                <option value="{{ $subjectData['subject']->id }}"
                                        data-groups="{{ json_encode($subjectData['groups']->map(function($g) { return ['id' => $g->id, 'name' => $g->name]; })) }}"
                                        {{ old('subject_id') == $subjectData['subject']->id ? 'selected' : '' }}>
                                    {{ $subjectData['subject']->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-heading text-primary me-1"></i>Sarlavha
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}"
                                   placeholder="Material nomi"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-align-left text-primary me-1"></i>Tavsif
                            </label>
                            <textarea name="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Material haqida qisqacha ma'lumot">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload (for document and presentation) -->
                        <div class="mb-3" id="fileField">
                            <label class="form-label fw-bold">
                                <i class="fas fa-paperclip text-primary me-1"></i>Fayl yuklash
                                <span class="text-danger">*</span>
                            </label>
                            <input type="file"
                                   name="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                            <small class="text-muted">Maksimal hajm: 50MB</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Video URL (for video) -->
                        <div class="mb-3 d-none" id="videoField">
                            <label class="form-label fw-bold">
                                <i class="fas fa-video text-danger me-1"></i>Video URL
                                <span class="text-danger">*</span>
                            </label>
                            <input type="url"
                                   name="video_url"
                                   class="form-control @error('video_url') is-invalid @enderror"
                                   value="{{ old('video_url') }}"
                                   placeholder="https://www.youtube.com/watch?v=...">
                            <small class="text-muted">YouTube, Vimeo yoki boshqa video havolasi</small>
                            @error('video_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- External Link (for link) -->
                        <div class="mb-3 d-none" id="linkField">
                            <label class="form-label fw-bold">
                                <i class="fas fa-link text-info me-1"></i>Tashqi havola
                                <span class="text-danger">*</span>
                            </label>
                            <input type="url"
                                   name="external_link"
                                   class="form-control @error('external_link') is-invalid @enderror"
                                   value="{{ old('external_link') }}"
                                   placeholder="https://example.com">
                            @error('external_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 mb-4">
                <!-- Groups Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Guruhlar (ixtiyoriy)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="groupsList">
                            <p class="text-muted text-center small">Avval fanni tanlang</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-check me-2"></i>Materialni saqlash
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<script>
function updateFormFields() {
    const type = document.querySelector('input[name="type"]:checked').value;
    const fileField = document.getElementById('fileField');
    const videoField = document.getElementById('videoField');
    const linkField = document.getElementById('linkField');

    // Hide all fields
    fileField.classList.add('d-none');
    videoField.classList.add('d-none');
    linkField.classList.add('d-none');

    // Show relevant field
    if (type === 'document' || type === 'presentation') {
        fileField.classList.remove('d-none');
    } else if (type === 'video') {
        videoField.classList.remove('d-none');
    } else if (type === 'link') {
        linkField.classList.remove('d-none');
    }
}

function updateGroupsList() {
    const subjectSelect = document.getElementById('subjectSelect');
    const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
    const groupsData = selectedOption.getAttribute('data-groups');
    const groupsList = document.getElementById('groupsList');

    if (!groupsData) {
        groupsList.innerHTML = '<p class="text-muted text-center small">Avval fanni tanlang</p>';
        return;
    }

    const groups = JSON.parse(groupsData);

    if (groups.length === 0) {
        groupsList.innerHTML = '<p class="text-muted text-center small">Bu fan uchun guruhlar yo\'q</p>';
        return;
    }

    let html = '<div class="form-check mb-3">';
    html += '<input class="form-check-input" type="checkbox" id="selectAll" onclick="toggleAllGroups(this)">';
    html += '<label class="form-check-label fw-bold" for="selectAll">Barchasini tanlash</label>';
    html += '</div><hr>';

    groups.forEach(group => {
        html += `
            <div class="form-check mb-2">
                <input class="form-check-input group-checkbox"
                       type="checkbox"
                       name="group_ids[]"
                       value="${group.id}"
                       id="group_${group.id}">
                <label class="form-check-label" for="group_${group.id}">
                    ${group.name}
                </label>
            </div>
        `;
    });

    groupsList.innerHTML = html;
}

function toggleAllGroups(checkbox) {
    const groupCheckboxes = document.querySelectorAll('.group-checkbox');
    groupCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateFormFields();
    const subjectSelect = document.getElementById('subjectSelect');
    if (subjectSelect.value) {
        updateGroupsList();
    }
});
</script>
@endsection
