@extends('layouts.dashboard-new')

@section('title', "O'quv reja mavzulari")
@section('page-title', "O'quv reja mavzulari")

@section('content')
<div class="container-fluid">
    <!-- Filter Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Yo'nalish va fanni tanlang</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('structure.academic.curriculum.topics') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Ta'lim yo'nalishi <span class="text-danger">*</span></label>
                        <select name="program_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">Yo'nalishni tanlang</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ $programId == $program->id ? 'selected' : '' }}>
                                    {{ $program->code }} - {{ $program->name_uz }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fan <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">Fanni tanlang</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->code }} - {{ $subject->name_uz }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">O'quv yili</label>
                        <select name="academic_year" class="form-select" onchange="this.form.submit()">
                            @php
                                $currentYear = date('Y');
                                for ($i = -1; $i <= 2; $i++) {
                                    $year = $currentYear + $i;
                                    $yearStr = $year . '-' . ($year + 1);
                                    $selected = $academicYear == $yearStr ? 'selected' : '';
                                    echo "<option value=\"$yearStr\" $selected>$yearStr</option>";
                                }
                            @endphp
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Semestr <span class="text-danger">*</span></label>
                        <select name="semester" class="form-select" required onchange="this.form.submit()">
                            <option value="">Semestr</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ $semester == $i ? 'selected' : '' }}>
                                    {{ $i }}-semestr
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Ko'rish
                            </button>
                            @if($programId && $subjectId && $semester)
                                <button type="button" class="btn btn-success" onclick="addNewTopic()">
                                    <i class="fas fa-plus"></i> Qo'shish
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($programId && $subjectId && $semester)
        <!-- Import/Export Actions -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Import/Export</h6>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('structure.academic.curriculum.template') }}" class="btn btn-outline-info">
                            <i class="fas fa-download"></i> Namuna yuklab olish
                        </a>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-upload"></i> Import qilish
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Topics Statistics -->
        <div id="topicsStats"></div>
        
        <!-- Topics Table/Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Mavzular ro'yxati</h5>
                <span class="badge bg-primary" id="topicCount">0 ta mavzu</span>
            </div>
            <div class="card-body">
                <form action="{{ route('structure.academic.curriculum.saveTopics') }}" method="POST" id="topicsForm">
                    @csrf
                    <input type="hidden" name="program_id" value="{{ $programId }}">
                    <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                    <input type="hidden" name="academic_year" value="{{ $academicYear }}">
                    <input type="hidden" name="semester_number" value="{{ $semester }}">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="topicsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="60" class="text-center">Hafta</th>
                                    <th width="60" class="text-center">Dars №</th>
                                    <th>Mavzu nomi</th>
                                    <th width="150" class="text-center">Dars turi</th>
                                    <th width="80" class="text-center">Soat</th>
                                    <th width="250">Qo'shimcha ma'lumot</th>
                                    <th width="100" class="text-center">Amallar</th>
                                </tr>
                            </thead>
                            <tbody id="topicsBody">
                                @if($topics)
                                    @foreach($topics as $week => $weekTopics)
                                        @foreach($weekTopics as $topic)
                                            <tr class="topic-row">
                                                <td>
                                                    <input type="number" name="topics[{{ $loop->parent->index * 10 + $loop->index }}][week_number]" 
                                                           class="form-control form-control-sm" value="{{ $topic->week_number }}" min="1" max="20" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="topics[{{ $loop->parent->index * 10 + $loop->index }}][lesson_number]" 
                                                           class="form-control form-control-sm" value="{{ $topic->lesson_number }}" min="1" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="topics[{{ $loop->parent->index * 10 + $loop->index }}][topic_name_uz]" 
                                                           class="form-control form-control-sm" value="{{ $topic->topic_name_uz }}" required>
                                                    <input type="text" name="topics[{{ $loop->parent->index * 10 + $loop->index }}][topic_name_ru]" 
                                                           class="form-control form-control-sm mt-1" value="{{ $topic->topic_name_ru }}" placeholder="Ruscha (ixtiyoriy)">
                                                </td>
                                                <td>
                                                    <select name="topics[{{ $loop->parent->index * 10 + $loop->index }}][lesson_type]" 
                                                            class="form-select form-select-sm lesson-type-select" required>
                                                        <option value="lecture" {{ $topic->lesson_type == 'lecture' ? 'selected' : '' }}>Ma'ruza</option>
                                                        <option value="practice" {{ $topic->lesson_type == 'practice' ? 'selected' : '' }}>Amaliyot</option>
                                                        <option value="seminar" {{ $topic->lesson_type == 'seminar' ? 'selected' : '' }}>Seminar</option>
                                                        <option value="lab" {{ $topic->lesson_type == 'lab' ? 'selected' : '' }}>Laboratoriya</option>
                                                        <option value="independent" {{ $topic->lesson_type == 'independent' ? 'selected' : '' }}>Mustaqil ta'lim</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="topics[{{ $loop->parent->index * 10 + $loop->index }}][hours]" 
                                                           class="form-control form-control-sm" value="{{ $topic->hours }}" min="1" max="8" required>
                                                </td>
                                                <td>
                                                    <textarea name="topics[{{ $loop->parent->index * 10 + $loop->index }}][description]" 
                                                              class="form-control form-control-sm" rows="2" placeholder="Tavsif">{{ $topic->description }}</textarea>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeTopic(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-success" onclick="addNewTopic()">
                                <i class="fas fa-plus"></i> Mavzu qo'shish
                            </button>
                            <button type="button" class="btn btn-info" onclick="addBulkTopics()">
                                <i class="fas fa-list-ol"></i> Ko'plab qo'shish
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-warning" onclick="clearAllTopics()">
                                <i class="fas fa-trash-alt"></i> Tozalash
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Barcha mavzularni saqlash
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('structure.academic.curriculum.importTopics') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="program_id" value="{{ $programId }}">
                <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                <input type="hidden" name="academic_year" value="{{ $academicYear }}">
                <input type="hidden" name="semester_number" value="{{ $semester }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">Mavzularni import qilish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">CSV fayl tanlang</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                        <small class="text-muted">Faqat .csv formatlar qabul qilinadi</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6>Fayl tuzilishi:</h6>
                        <ol class="mb-0 small">
                            <li>Hafta raqami</li>
                            <li>Dars raqami</li>
                            <li>Mavzu nomi (uz)</li>
                            <li>Mavzu nomi (ru)</li>
                            <li>Dars turi (Ma'ruza/Amaliyot/Seminar/Laboratoriya)</li>
                            <li>Soatlar</li>
                            <li>Tavsif</li>
                            <li>Kutilayotgan natijalar</li>
                            <li>O'qitish usullari</li>
                            <li>Baholash usullari</li>
                            <li>Adabiyotlar</li>
                            <li>Uy vazifasi</li>
                            <li>Online (ha/yo'q)</li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Import qilish</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let topicIndex = {{ $topics ? $topics->flatten()->count() : 0 }};

function addNewTopic() {
    const tbody = document.getElementById('topicsBody');
    const lastRow = tbody.querySelector('tr:last-child');
    let weekNumber = 1;
    let lessonNumber = 1;
    
    if (lastRow) {
        weekNumber = parseInt(lastRow.querySelector('input[name*="week_number"]').value) || 1;
        lessonNumber = parseInt(lastRow.querySelector('input[name*="lesson_number"]').value) + 1 || 1;
        
        // Agar dars raqami 2 dan katta bo'lsa, keyingi haftaga o'tish
        if (lessonNumber > 2) {
            weekNumber++;
            lessonNumber = 1;
        }
    }
    
    const row = document.createElement('tr');
    row.className = 'topic-row';
    row.innerHTML = `
        <td class="text-center">
            <input type="number" name="topics[${topicIndex}][week_number]" 
                   class="form-control form-control-sm text-center" value="${weekNumber}" min="1" max="20" required>
        </td>
        <td class="text-center">
            <input type="number" name="topics[${topicIndex}][lesson_number]" 
                   class="form-control form-control-sm text-center" value="${lessonNumber}" min="1" required>
        </td>
        <td>
            <input type="text" name="topics[${topicIndex}][topic_name_uz]" 
                   class="form-control form-control-sm mb-1" required placeholder="Mavzu nomi (uz)">
            <input type="text" name="topics[${topicIndex}][topic_name_ru]" 
                   class="form-control form-control-sm" placeholder="Mavzu nomi (ru) - ixtiyoriy">
        </td>
        <td>
            <select name="topics[${topicIndex}][lesson_type]" 
                    class="form-select form-select-sm lesson-type-select" required>
                <option value="lecture">Ma'ruza</option>
                <option value="practice">Amaliyot</option>
                <option value="seminar">Seminar</option>
                <option value="lab">Laboratoriya</option>
                <option value="independent">Mustaqil ta'lim</option>
            </select>
        </td>
        <td class="text-center">
            <input type="number" name="topics[${topicIndex}][hours]" 
                   class="form-control form-control-sm text-center" value="2" min="1" max="8" required>
        </td>
        <td>
            <textarea name="topics[${topicIndex}][description]" 
                      class="form-control form-control-sm" rows="2" placeholder="Izoh, tavsif..."></textarea>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeTopic(this)" title="O'chirish">
                <i class="fas fa-trash"></i>
            </button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="duplicateTopic(this)" title="Nusxalash">
                <i class="fas fa-copy"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    topicIndex++;
    
    // Focus on the new topic name input
    row.querySelector('input[name*="topic_name_uz"]').focus();
    
    // Update statistics
    updateStatistics();
}

function addBulkTopics() {
    const count = prompt('Nechta mavzu qo\'shmoqchisiz?', '10');
    if (count && parseInt(count) > 0) {
        for (let i = 0; i < parseInt(count); i++) {
            addNewTopic();
        }
    }
}

function duplicateTopic(button) {
    const sourceRow = button.closest('tr');
    const tbody = document.getElementById('topicsBody');
    
    const newRow = document.createElement('tr');
    newRow.className = 'topic-row';
    
    // Get values from source row
    const weekNum = sourceRow.querySelector('input[name*="week_number"]').value;
    const lessonNum = parseInt(sourceRow.querySelector('input[name*="lesson_number"]').value) + 1;
    const topicUz = sourceRow.querySelector('input[name*="topic_name_uz"]').value;
    const topicRu = sourceRow.querySelector('input[name*="topic_name_ru"]').value;
    const lessonType = sourceRow.querySelector('select[name*="lesson_type"]').value;
    const hours = sourceRow.querySelector('input[name*="hours"]').value;
    const description = sourceRow.querySelector('textarea[name*="description"]').value;
    
    newRow.innerHTML = `
        <td class="text-center">
            <input type="number" name="topics[${topicIndex}][week_number]" 
                   class="form-control form-control-sm text-center" value="${weekNum}" min="1" max="20" required>
        </td>
        <td class="text-center">
            <input type="number" name="topics[${topicIndex}][lesson_number]" 
                   class="form-control form-control-sm text-center" value="${lessonNum}" min="1" required>
        </td>
        <td>
            <input type="text" name="topics[${topicIndex}][topic_name_uz]" 
                   class="form-control form-control-sm mb-1" value="${topicUz}" required placeholder="Mavzu nomi (uz)">
            <input type="text" name="topics[${topicIndex}][topic_name_ru]" 
                   class="form-control form-control-sm" value="${topicRu}" placeholder="Mavzu nomi (ru) - ixtiyoriy">
        </td>
        <td>
            <select name="topics[${topicIndex}][lesson_type]" 
                    class="form-select form-select-sm lesson-type-select" required>
                <option value="lecture" ${lessonType === 'lecture' ? 'selected' : ''}>Ma'ruza</option>
                <option value="practice" ${lessonType === 'practice' ? 'selected' : ''}>Amaliyot</option>
                <option value="seminar" ${lessonType === 'seminar' ? 'selected' : ''}>Seminar</option>
                <option value="lab" ${lessonType === 'lab' ? 'selected' : ''}>Laboratoriya</option>
                <option value="independent" ${lessonType === 'independent' ? 'selected' : ''}>Mustaqil ta'lim</option>
            </select>
        </td>
        <td class="text-center">
            <input type="number" name="topics[${topicIndex}][hours]" 
                   class="form-control form-control-sm text-center" value="${hours}" min="1" max="8" required>
        </td>
        <td>
            <textarea name="topics[${topicIndex}][description]" 
                      class="form-control form-control-sm" rows="2" placeholder="Izoh, tavsif...">${description}</textarea>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeTopic(this)" title="O'chirish">
                <i class="fas fa-trash"></i>
            </button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="duplicateTopic(this)" title="Nusxalash">
                <i class="fas fa-copy"></i>
            </button>
        </td>
    `;
    
    // Insert after source row
    sourceRow.parentNode.insertBefore(newRow, sourceRow.nextSibling);
    topicIndex++;
    
    updateStatistics();
}

function removeTopic(button) {
    if (confirm('Mavzuni o\'chirishni xohlaysizmi?')) {
        button.closest('tr').remove();
        updateStatistics();
    }
}

function clearAllTopics() {
    if (confirm('Barcha mavzularni o\'chirishni xohlaysizmi?')) {
        document.getElementById('topicsBody').innerHTML = '';
        topicIndex = 0;
        updateStatistics();
    }
}

function updateStatistics() {
    const rows = document.querySelectorAll('#topicsBody tr');
    let totalHours = 0;
    let lectureHours = 0;
    let practiceHours = 0;
    let labHours = 0;
    let seminarHours = 0;
    let independentHours = 0;
    
    rows.forEach(row => {
        const hours = parseInt(row.querySelector('input[name*="hours"]').value) || 0;
        const type = row.querySelector('select[name*="lesson_type"]').value;
        
        totalHours += hours;
        
        switch(type) {
            case 'lecture': lectureHours += hours; break;
            case 'practice': practiceHours += hours; break;
            case 'lab': labHours += hours; break;
            case 'seminar': seminarHours += hours; break;
            case 'independent': independentHours += hours; break;
        }
    });
    
    // Update topic count badge
    const topicCountBadge = document.getElementById('topicCount');
    if (topicCountBadge) {
        topicCountBadge.textContent = rows.length + ' ta mavzu';
    }
    
    // Display statistics if container exists
    const statsContainer = document.getElementById('topicsStats');
    if (statsContainer) {
        statsContainer.innerHTML = `
            <div class="alert alert-info">
                <div class="row">
                    <div class="col-md-6">
                        <strong>📊 Statistika:</strong><br>
                        Jami mavzular: <strong>${rows.length}</strong> ta<br>
                        Jami soatlar: <strong>${totalHours}</strong> soat (${Math.floor(totalHours/2)} para)
                    </div>
                    <div class="col-md-6">
                        <strong>📚 Dars turlari bo'yicha:</strong><br>
                        Ma'ruza: <strong>${lectureHours}</strong> soat | 
                        Amaliyot: <strong>${practiceHours}</strong> soat<br>
                        Laboratoriya: <strong>${labHours}</strong> soat | 
                        Seminar: <strong>${seminarHours}</strong> soat | 
                        Mustaqil: <strong>${independentHours}</strong> soat
                    </div>
                </div>
            </div>
        `;
    }
}

// Auto-update lesson types based on pattern
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('lesson-type-select')) {
        const row = e.target.closest('tr');
        const nextRow = row.nextElementSibling;
        
        if (nextRow && e.target.value === 'lecture') {
            const nextSelect = nextRow.querySelector('.lesson-type-select');
            if (nextSelect && !nextSelect.dataset.manual) {
                nextSelect.value = 'practice';
            }
        }
    }
    
    // Update statistics on any change
    if (e.target.name && e.target.name.includes('topics[')) {
        updateStatistics();
    }
});

// Mark manually changed selects
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('lesson-type-select')) {
        e.target.dataset.manual = 'true';
    }
});

// Initialize statistics on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStatistics();
});
</script>
@endpush
@endsection