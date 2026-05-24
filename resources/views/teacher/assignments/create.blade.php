@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-plus me-2"></i>
                        Yangi topshiriq yaratish
                    </h4>
                    <p class="mb-0 opacity-75">Talabalar uchun yangi topshiriq</p>
                </div>
                <div>
                    <a href="{{ route('teacher.assignments.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.assignments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Topshiriq ma'lumotlari
                        </h5>
                    </div>
                    <div class="card-body">
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
                                   placeholder="Topshiriq sarlavhasi"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-align-left text-primary me-1"></i>Tavsif
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="6"
                                      placeholder="Topshiriq tavsifi va ko'rsatmalar"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-paperclip text-primary me-1"></i>Fayl biriktirish
                            </label>
                            <input type="file"
                                   name="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar">
                            <small class="text-muted">Maksimal hajm: 10MB (PDF, Word, Excel, PowerPoint, ZIP)</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 mb-4">
                <!-- Settings Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Sozlamalar
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Deadline -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar text-danger me-1"></i>Muddat
                                <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local"
                                   name="deadline"
                                   class="form-control @error('deadline') is-invalid @enderror"
                                   value="{{ old('deadline') }}"
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   required>
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Max Score -->
                        <div class="mb-0">
                            <label class="form-label fw-bold">
                                <i class="fas fa-star text-warning me-1"></i>Maksimal ball
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   name="max_score"
                                   class="form-control @error('max_score') is-invalid @enderror"
                                   value="{{ old('max_score', 100) }}"
                                   min="1"
                                   max="100"
                                   required>
                            @error('max_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Groups Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Guruhlar
                            <span class="text-danger">*</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="groupsList">
                            <p class="text-muted text-center">Avval fanni tanlang</p>
                        </div>
                        @error('group_ids')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-check me-2"></i>Topshiriqni saqlash
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
function updateGroupsList() {
    const subjectSelect = document.getElementById('subjectSelect');
    const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
    const groupsData = selectedOption.getAttribute('data-groups');
    const groupsList = document.getElementById('groupsList');

    if (!groupsData) {
        groupsList.innerHTML = '<p class="text-muted text-center">Avval fanni tanlang</p>';
        return;
    }

    const groups = JSON.parse(groupsData);

    if (groups.length === 0) {
        groupsList.innerHTML = '<p class="text-muted text-center">Bu fan uchun guruhlar yo\'q</p>';
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

// Initialize on page load if subject is already selected
document.addEventListener('DOMContentLoaded', function() {
    const subjectSelect = document.getElementById('subjectSelect');
    if (subjectSelect.value) {
        updateGroupsList();
    }
});
</script>
@endsection
