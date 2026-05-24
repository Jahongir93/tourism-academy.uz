@extends('layouts.dashboard-new')

@section('title', 'Kursni o\'rganish — LMS')
@section('page-title', 'Kursni o\'rganish')

@section('content')

<div class="row">
    {{-- Sidebar resources --}}
    <div class="col-md-3">
        <div class="card sticky-top" style="top:80px">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-graduation-cap" style="color:var(--c-teal)"></i>
                <span style="font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $course->title }}</span>
            </div>
            <div class="card-body p-0" style="max-height:70vh;overflow-y:auto">
                @if($resources->count() > 0)
                @foreach($resources as $week => $weekResources)
                <div style="border-bottom:1px solid var(--c-border)">
                    <div class="px-3 py-2" style="background:var(--c-bg);font-size:12px;font-weight:700;color:var(--c-text-2)">
                        {{ $week ? $week.'-hafta' : 'Umumiy resurslar' }}
                    </div>
                    @foreach($weekResources as $resource)
                    <a href="#resource-{{ $resource->id }}"
                       class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none resource-link"
                       onclick="loadResource({{ $resource->id }}); return false;"
                       id="res-link-{{ $resource->id }}"
                       style="border-bottom:1px solid var(--c-border);transition:background .15s">
                        <i class="fas {{ $resource->icon }}" style="font-size:12px;flex-shrink:0;color:var(--c-teal)"></i>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:12px;color:var(--c-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $resource->title }}</div>
                            @if($resource->duration)
                            <div style="font-size:10px;color:var(--c-text-3)">{{ $resource->duration_formatted }}</div>
                            @endif
                        </div>
                        @if($resource->is_mandatory)
                        <span class="badge" style="background:rgba(244,63,94,.12);color:var(--c-rose);font-size:9px;flex-shrink:0">M</span>
                        @endif
                    </a>
                    @endforeach
                </div>
                @endforeach
                @else
                <div class="p-3 text-center" style="font-size:13px;color:var(--c-text-3)">Resurslar mavjud emas</div>
                @endif
            </div>
            @if($enrollment)
            <div class="card-body py-2" style="border-top:1px solid var(--c-border)">
                <div style="font-size:11px;color:var(--c-text-3);margin-bottom:4px">Jarayon</div>
                <div class="progress mb-1" style="height:8px;background:var(--c-border);border-radius:4px">
                    <div style="width:{{ $enrollment->progress_percentage }}%;height:100%;background:var(--c-teal);border-radius:4px;transition:width .3s"></div>
                </div>
                <div class="d-flex justify-content-between" style="font-size:11px;color:var(--c-text-3)">
                    <span>{{ $enrollment->progress_percentage }}%</span>
                    <span>{{ $enrollment->last_accessed_at ? $enrollment->last_accessed_at->diffForHumans() : 'Yangi' }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Main content area --}}
    <div class="col-md-9">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('lms.courses.show', $course) }}" style="font-size:12px;color:var(--c-teal)">
                        <i class="fas fa-arrow-left me-1"></i>{{ $course->title }}
                    </a>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="previousResource()">
                        <i class="fas fa-chevron-left"></i> Oldingi
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="nextResource()">
                        Keyingi <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="resource-content" style="min-height:500px">
                <div class="text-center py-5" style="color:var(--c-text-3)">
                    <i class="fas fa-book-open mb-3" style="display:block;font-size:36px"></i>
                    <h5 style="color:var(--c-text-2)">Boshlash uchun chapdan resursni tanlang</h5>
                    <p style="font-size:13px">Kurs materiallarini ko'rish uchun chap menyudan resursni tanlang</p>
                </div>
            </div>
        </div>

        <div class="card" id="resource-actions" style="display:none">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h6 id="resource-title" style="color:var(--c-text)"></h6>
                        <p id="resource-description" style="font-size:13px;color:var(--c-text-3);margin:0"></p>
                    </div>
                    <div class="col-md-5 text-end">
                        <button class="btn btn-sm me-2" style="background:var(--c-emerald);color:#fff" onclick="markComplete()">
                            <i class="fas fa-check me-1"></i>Tugatildi
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="download-btn" style="display:none">
                            <i class="fas fa-download me-1"></i>Yuklab olish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentResourceId = null;
let allResources = [];

document.addEventListener('DOMContentLoaded', function() {
    @if($resources->count() > 0)
        @foreach($resources as $weekResources)
            @foreach($weekResources as $resource)
                allResources.push({{ $resource->id }});
            @endforeach
        @endforeach
    @endif
    if (allResources.length > 0) loadResource(allResources[0]);
});

function loadResource(resourceId) {
    currentResourceId = resourceId;
    document.querySelectorAll('.resource-link').forEach(l => {
        l.style.background = '';
        l.style.color = '';
    });
    const active = document.getElementById('res-link-' + resourceId);
    if (active) { active.style.background = 'rgba(20,184,166,.08)'; }

    document.getElementById('resource-content').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border" role="status" style="color:var(--c-teal)">
                <span class="visually-hidden">Yuklanmoqda...</span>
            </div>
        </div>`;

    fetch('/lms/resources/' + resourceId + '/view', {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(data => displayResource(data))
    .catch(() => displayPlaceholderContent(resourceId));
}

function displayPlaceholderContent(resourceId) {
    document.getElementById('resource-content').innerHTML = `
        <div class="p-4">
            <div class="alert alert-info"><i class="fas fa-info-circle me-1"></i>Resurs yuklanmoqda yoki mavjud emas.</div>
        </div>`;
    document.getElementById('resource-actions').style.display = 'block';
    document.getElementById('resource-title').textContent = 'Resurs #' + resourceId;
    document.getElementById('resource-description').textContent = '';
}

function displayResource(data) {
    let content = '';
    switch(data.resource_type) {
        case 'video':
            content = `<video controls style="width:100%;max-height:500px"><source src="${data.file_url}" type="video/mp4">Video qo'llab-quvvatlanmaydi.</video>`;
            break;
        case 'document': case 'presentation':
            content = `<embed src="${data.file_url}" type="application/pdf" width="100%" height="600px" />`;
            break;
        case 'audio':
            content = `<audio controls style="width:100%"><source src="${data.file_url}" type="audio/mpeg"></audio>`;
            break;
        case 'link':
            content = `<div class="p-4"><h5>${data.title}</h5><p>${data.description||''}</p><a href="${data.external_url}" target="_blank" class="btn btn-sm" style="background:var(--c-teal);color:#fff"><i class="fas fa-external-link-alt me-1"></i>Havolani ochish</a></div>`;
            break;
        default:
            content = `<div class="p-4"><h5>${data.title}</h5><p>${data.description||''}</p></div>`;
    }
    document.getElementById('resource-content').innerHTML = content;
    document.getElementById('resource-actions').style.display = 'block';
    document.getElementById('resource-title').textContent = data.title;
    document.getElementById('resource-description').textContent = data.description || '';
    const dlBtn = document.getElementById('download-btn');
    if (data.is_downloadable && data.file_url) {
        dlBtn.style.display = 'inline-block';
        dlBtn.onclick = () => window.open(data.file_url, '_blank');
    } else { dlBtn.style.display = 'none'; }
}

function previousResource() {
    const idx = allResources.indexOf(currentResourceId);
    if (idx > 0) loadResource(allResources[idx - 1]);
}

function nextResource() {
    const idx = allResources.indexOf(currentResourceId);
    if (idx < allResources.length - 1) loadResource(allResources[idx + 1]);
}

function markComplete() {
    if (!currentResourceId) return;
    fetch('/lms/resources/' + currentResourceId + '/complete', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(() => { alert("Resurs tugatildi deb belgilandi!"); nextResource(); })
    .catch(() => { alert("Resurs tugatildi deb belgilandi!"); nextResource(); });
}
</script>
@endpush
