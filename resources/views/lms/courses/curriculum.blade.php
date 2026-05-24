@extends('layouts.dashboard-new')

@section('title', 'O\'quv rejasi — ' . $course->title)
@section('page-title', 'O\'quv rejasi')

@section('content')

<x-lms-alerts />

{{-- Header --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('lms.courses.edit', $course) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Orqaga
                </a>
                <div style="width:1px;height:24px;background:var(--c-border)"></div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:var(--c-text)">{{ $course->title }}</div>
                    <div style="font-size:11px;color:var(--c-text-3)">O'quv rejasini boshqarish</div>
                </div>
            </div>
            <button class="btn btn-sm" style="background:var(--c-teal);color:#fff"
                    data-bs-toggle="modal" data-bs-target="#topicModal"
                    onclick="resetTopicModal()">
                <i class="fas fa-plus-circle me-1"></i>Yangi mavzu
            </button>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Main content --}}
    <div class="col-lg-8">
        @php
            $topicsByWeek = $course->topics()->orderedByWeek()->get()->groupBy('week_number');
        @endphp

        @forelse($topicsByWeek as $weekNumber => $topics)
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div style="width:26px;height:26px;border-radius:50%;background:rgba(20,184,166,.12);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--c-teal);flex-shrink:0">
                    {{ $weekNumber }}
                </div>
                <span style="font-size:13px;font-weight:700;color:var(--c-text)">{{ $weekNumber }}-hafta</span>
            </div>
            <div class="card-body p-0">
                @foreach($topics as $topic)
                <div class="px-4 py-3" style="border-bottom:1px solid var(--c-border)">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div style="flex:1;min-width:0">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                <span style="font-size:13px;font-weight:700;color:var(--c-text)">
                                    {{ $topic->order_number }}. {{ $topic->title }}
                                </span>
                                @if($topic->duration_minutes)
                                <span class="badge" style="background:rgba(14,165,233,.12);color:var(--c-sky);font-size:10px">
                                    <i class="fas fa-clock me-1"></i>{{ $topic->duration_minutes }} daq
                                </span>
                                @endif
                            </div>
                            @if($topic->description)
                            <div style="font-size:12px;color:var(--c-text-3);margin-bottom:6px">{{ $topic->description }}</div>
                            @endif

                            {{-- Topic resources --}}
                            @if($topic->resources->count() > 0)
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($topic->resources as $resource)
                                @php
                                    $rTypeMap = ['material'=>['fa-file-alt','var(--c-sky)'],'video'=>['fa-video','var(--c-rose)'],'test'=>['fa-clipboard-check','var(--c-emerald)'],'file'=>['fa-file-pdf','var(--c-violet)'],'link'=>['fa-link','var(--c-teal)'],'image'=>['fa-image','var(--c-amber)']];
                                    [$rIcon,$rColor] = $rTypeMap[$resource->resource_type] ?? ['fa-file','var(--c-text-3)'];
                                @endphp
                                <div class="d-flex align-items-center gap-1 px-2 py-1 rounded" style="background:var(--c-bg);border:1px solid var(--c-border);font-size:11px">
                                    <i class="fas {{ $rIcon }}" style="color:{{ $rColor }}"></i>
                                    <span style="color:var(--c-text-2)">{{ $resource->file_name ?? $resource->description ?? ucfirst($resource->resource_type) }}</span>
                                    <form action="{{ route('lms.courses.topics.resources.destroy', [$course, $topic, $resource]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none;border:none;padding:0 0 0 4px;color:var(--c-rose);line-height:1;cursor:pointer;font-size:10px">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <a href="{{ route('lms.courses.topics.show', [$course, $topic]) }}"
                               class="action-btn" style="background:rgba(14,165,233,.1);color:var(--c-sky)"
                               title="Tahrirlash"
                               onclick="event.preventDefault(); editTopic({{ $topic->id }})">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="action-btn" style="background:rgba(20,184,166,.1);color:var(--c-teal)"
                                    title="Resurs biriktirish"
                                    onclick="openResourceModal({{ $topic->id }}, '{{ addslashes($topic->title) }}')">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <form action="{{ route('lms.courses.topics.destroy', [$course, $topic]) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Bu mavzuni o\'chirmoqchimisiz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn" style="background:rgba(244,63,94,.1);color:var(--c-rose)" title="O'chirish">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-5" style="color:var(--c-text-3)">
                <i class="fas fa-book-open mb-2" style="display:block;font-size:36px"></i>
                <div style="font-size:14px;color:var(--c-text-2);margin-bottom:4px">Mavzular mavjud emas</div>
                <div style="font-size:12px">O'quv rejasiga mavzular qo'shing</div>
                <button class="btn btn-sm mt-3" style="background:var(--c-teal);color:#fff"
                        data-bs-toggle="modal" data-bs-target="#topicModal"
                        onclick="resetTopicModal()">
                    <i class="fas fa-plus me-1"></i>Mavzu qo'shish
                </button>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-chart-bar" style="color:var(--c-teal)"></i>
                <span>Statistika</span>
            </div>
            <div class="card-body p-0">
                @php
                    $stats = [
                        ['Jami mavzular', $course->topics->count(), 'var(--c-teal)'],
                        ['Jami resurslar', $course->topics->sum(fn($t) => $t->resources->count()), 'var(--c-sky)'],
                        ['Haftalar soni', $topicsByWeek->count(), 'var(--c-violet)'],
                    ];
                @endphp
                @foreach($stats as [$label, $val, $color])
                <div class="d-flex align-items-center justify-content-between px-4 py-2" style="border-bottom:1px solid var(--c-border)">
                    <span style="font-size:13px;color:var(--c-text-2)">{{ $label }}:</span>
                    <span style="font-size:14px;font-weight:700;color:{{ $color }}">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-lightbulb" style="color:var(--c-amber)"></i>
                <span>Maslahatlar</span>
            </div>
            <div class="card-body p-0">
                @foreach([
                    'Har bir mavzuga aniq nom bering',
                    'Resurslarni tartib bilan joylashtiring',
                    'Har haftaga 3–5 ta mavzu tavsiya etiladi',
                ] as $tip)
                <div class="d-flex align-items-start gap-2 px-4 py-2" style="border-bottom:1px solid var(--c-border)">
                    <i class="fas fa-check-circle mt-1" style="color:var(--c-emerald);font-size:11px;flex-shrink:0"></i>
                    <span style="font-size:12px;color:var(--c-text-2)">{{ $tip }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Topic Modal --}}
<div class="modal fade" id="topicModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="topicForm" action="{{ route('lms.courses.topics.store', $course) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="POST" id="topicMethod">
                <div class="modal-header" style="border-bottom:1px solid var(--c-border)">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="topicModalTitle">
                        <i class="fas fa-plus-circle" style="color:var(--c-teal)"></i>
                        Yangi mavzu qo'shish
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">
                            Mavzu nomi <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" id="topicTitle" required class="form-control"
                               placeholder="Masalan: Kirish va asosiy tushunchalar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Tavsif</label>
                        <textarea name="description" id="topicDescription" rows="3" class="form-control"
                                  placeholder="Mavzu haqida qisqacha ma'lumot"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-4">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">
                                Hafta raqami <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="week_number" id="topicWeek" required min="1" value="1" class="form-control">
                        </div>
                        <div class="col-4">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Tartib raqami</label>
                            <input type="number" name="order_number" id="topicOrder" min="1" value="1" class="form-control">
                        </div>
                        <div class="col-4">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Davomiylik (daq)</label>
                            <input type="number" name="duration_minutes" id="topicDuration" min="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_published" id="topicPublished" value="1" checked class="form-check-input">
                        <label for="topicPublished" class="form-check-label" style="font-size:13px">Nashr qilingan</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--c-border)">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--c-teal);color:#fff">
                        <i class="fas fa-save me-1"></i>Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Resource Modal --}}
<div class="modal fade" id="resourceModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="resourceForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="topic_id" id="resourceTopicId">
                <input type="hidden" name="resource_type" id="resourceType">
                <div class="modal-header" style="border-bottom:1px solid var(--c-border)">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="fas fa-paperclip" style="color:var(--c-sky)"></i>
                        <span id="resourceModalTitle">Resurs biriktirish</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Type selector --}}
                    <div class="mb-4">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">
                            Resurs turini tanlang <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2">
                            @foreach([
                                ['file','fa-file-pdf','var(--c-violet)','rgba(124,58,237,.1)','PDF / Hujjat'],
                                ['video','fa-video','var(--c-rose)','rgba(244,63,94,.1)','Video'],
                                ['material','fa-book','var(--c-sky)','rgba(14,165,233,.1)','Material'],
                                ['test','fa-clipboard-check','var(--c-emerald)','rgba(16,185,129,.1)','Test'],
                                ['link','fa-link','var(--c-teal)','rgba(20,184,166,.1)','Havola'],
                                ['image','fa-image','var(--c-amber)','rgba(245,158,11,.1)','Rasm'],
                            ] as [$type,$icon,$color,$bg,$label])
                            <div class="col-6 col-md-4 col-lg-2">
                                <button type="button" class="w-100 res-type-btn py-3 rounded text-center"
                                        style="border:2px solid var(--c-border);background:var(--c-bg);cursor:pointer;transition:all .15s"
                                        onclick="selectResourceType('{{ $type }}', this)">
                                    <i class="fas {{ $icon }}" style="font-size:20px;color:{{ $color }};display:block;margin-bottom:4px"></i>
                                    <span style="font-size:11px;font-weight:600;color:var(--c-text-2)">{{ $label }}</span>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- File upload --}}
                    <div id="field-file" class="mb-3" style="display:none">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Fayl yuklash <span class="text-danger">*</span></label>
                        <input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" class="form-control">
                        <div class="form-text">PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX (maks 50MB)</div>
                    </div>

                    {{-- Material select --}}
                    <div id="field-material" class="mb-3" style="display:none">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Material tanlang <span class="text-danger">*</span></label>
                        <input type="text" id="materialSearch" placeholder="Qidirish..." class="form-control mb-2"
                               onkeyup="filterOpts('materialSearch','materialSelect')">
                        <select name="material_id" id="materialSelect" size="6" class="form-select">
                            <option value="">Material tanlang</option>
                            @foreach(\App\Models\LmsMaterial::where('is_active',true)->where('subject_id',$course->subject_id)->get() as $m)
                            <option value="{{ $m->id }}">{{ $m->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Video select --}}
                    <div id="field-video" class="mb-3" style="display:none">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Video tanlang <span class="text-danger">*</span></label>
                        <input type="text" id="videoSearch" placeholder="Qidirish..." class="form-control mb-2"
                               onkeyup="filterOpts('videoSearch','videoSelect')">
                        <select name="video_id" id="videoSelect" size="6" class="form-select">
                            <option value="">Video tanlang</option>
                            @foreach(\App\Models\LmsVideo::where('is_active',true)->where('subject_id',$course->subject_id)->get() as $v)
                            <option value="{{ $v->id }}">{{ $v->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Test select --}}
                    <div id="field-test" class="mb-3" style="display:none">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Test tanlang <span class="text-danger">*</span></label>
                        <input type="text" id="testSearch" placeholder="Qidirish..." class="form-control mb-2"
                               onkeyup="filterOpts('testSearch','testSelect')">
                        <select name="test_id" id="testSelect" size="6" class="form-select">
                            <option value="">Test tanlang</option>
                            @foreach(\App\Models\LmsPracticeTest::where('is_active',true)->where('subject_id',$course->subject_id)->get() as $t)
                            <option value="{{ $t->id }}">{{ $t->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Link --}}
                    <div id="field-link" class="mb-3" style="display:none">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Havola (URL) <span class="text-danger">*</span></label>
                        <input type="url" name="external_link" class="form-control" placeholder="https://example.com">
                    </div>

                    {{-- Image upload --}}
                    <div id="field-image" class="mb-3" style="display:none">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Rasm yuklash <span class="text-danger">*</span></label>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="form-control">
                        <div class="form-text">JPG, PNG, GIF, WEBP (maks 10MB)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Tavsif</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Resurs haqida qisqacha ma'lumot"></textarea>
                    </div>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input type="checkbox" name="is_mandatory" value="1" class="form-check-input" id="resMandatory">
                            <label for="resMandatory" class="form-check-label" style="font-size:13px">Majburiy</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_downloadable" value="1" class="form-check-input" id="resDownloadable" checked>
                            <label for="resDownloadable" class="form-check-label" style="font-size:13px">Yuklab olish mumkin</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--c-border)">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--c-sky);color:#fff">
                        <i class="fas fa-save me-1"></i>Biriktirish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const COURSE_ID = {{ $course->id }};

function resetTopicModal() {
    document.getElementById('topicModalTitle').innerHTML = '<i class="fas fa-plus-circle" style="color:var(--c-teal)"></i> Yangi mavzu qo\'shish';
    document.getElementById('topicForm').reset();
    document.getElementById('topicMethod').value = 'POST';
    document.getElementById('topicForm').action = '/lms/courses/' + COURSE_ID + '/topics';
}

function editTopic(topicId) {
    fetch('/lms/courses/' + COURSE_ID + '/topics/' + topicId)
        .then(r => r.json())
        .then(d => {
            document.getElementById('topicModalTitle').innerHTML = '<i class="fas fa-edit" style="color:var(--c-sky)"></i> Mavzuni tahrirlash';
            document.getElementById('topicTitle').value = d.title;
            document.getElementById('topicDescription').value = d.description || '';
            document.getElementById('topicWeek').value = d.week_number;
            document.getElementById('topicOrder').value = d.order_number;
            document.getElementById('topicDuration').value = d.duration_minutes || '';
            document.getElementById('topicPublished').checked = d.is_published;
            document.getElementById('topicMethod').value = 'PUT';
            document.getElementById('topicForm').action = '/lms/courses/' + COURSE_ID + '/topics/' + topicId;
            new bootstrap.Modal(document.getElementById('topicModal')).show();
        });
}

function openResourceModal(topicId, topicTitle) {
    document.getElementById('resourceModalTitle').textContent = 'Resurs biriktirish: ' + topicTitle;
    document.getElementById('resourceForm').reset();
    document.getElementById('resourceTopicId').value = topicId;
    document.getElementById('resourceType').value = '';
    document.getElementById('resourceForm').action = '/lms/courses/' + COURSE_ID + '/topics/' + topicId + '/resources';
    ['file','material','video','test','link','image'].forEach(t => {
        document.getElementById('field-' + t).style.display = 'none';
    });
    document.querySelectorAll('.res-type-btn').forEach(b => {
        b.style.borderColor = 'var(--c-border)';
        b.style.background = 'var(--c-bg)';
    });
    new bootstrap.Modal(document.getElementById('resourceModal')).show();
}

function selectResourceType(type, btn) {
    document.getElementById('resourceType').value = type;
    ['file','material','video','test','link','image'].forEach(t => {
        document.getElementById('field-' + t).style.display = t === type ? 'block' : 'none';
    });
    document.querySelectorAll('.res-type-btn').forEach(b => {
        b.style.borderColor = 'var(--c-border)';
        b.style.background = 'var(--c-bg)';
    });
    btn.style.borderColor = 'var(--c-teal)';
    btn.style.background = 'rgba(20,184,166,.08)';
}

function filterOpts(searchId, selectId) {
    const q = document.getElementById(searchId).value.toLowerCase();
    const sel = document.getElementById(selectId);
    for (let opt of sel.options) {
        opt.style.display = opt.text.toLowerCase().includes(q) ? '' : 'none';
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        bootstrap.Modal.getInstance(document.getElementById('topicModal'))?.hide();
        bootstrap.Modal.getInstance(document.getElementById('resourceModal'))?.hide();
    }
});
</script>
@endpush
