@extends('layouts.dashboard-new')

@section('title', $book->title . ' — E-kutubxona')
@section('page-title', 'Kitob o\'qish')

@section('styles')
<style>
#pdf-container { background:var(--c-bg);min-height:500px;display:flex;align-items:center;justify-content:center;padding:24px;border-radius:0 0 10px 10px; }
</style>
@endsection

@section('content')

{{-- Controls bar --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('lms.library.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Orqaga
                </a>
                <div style="width:1px;height:24px;background:var(--c-border)"></div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:var(--c-text)">{{ $book->title }}</div>
                    <div style="font-size:11px;color:var(--c-text-3)">{{ $book->author }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center gap-1 p-1 rounded" style="background:var(--c-bg);border:1px solid var(--c-border)">
                    <button onclick="zoomOut()" class="btn btn-sm btn-link p-1" title="Kichiklashtirish" style="color:var(--c-text-2)">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <span id="zoomLevel" style="font-size:12px;font-weight:600;color:var(--c-text);min-width:36px;text-align:center">100%</span>
                    <button onclick="zoomIn()" class="btn btn-sm btn-link p-1" title="Kattalashtirish" style="color:var(--c-text-2)">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>
                @if($book->file_path)
                <a href="{{ route('lms.library.download', $book) }}" class="btn btn-sm"
                   style="background:var(--c-teal);color:#fff">
                    <i class="fas fa-download me-1"></i>Yuklab olish
                </a>
                @endif
                <button onclick="toggleFullscreen()" class="btn btn-sm btn-outline-secondary" title="To'liq ekran">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- PDF viewer --}}
<div class="card mb-4">
    <div id="pdf-container">
        <div id="pdf-loading" class="text-center" style="color:var(--c-text-3)">
            <div class="spinner-border mb-2" role="status" style="color:var(--c-teal)">
                <span class="visually-hidden">Yuklanmoqda...</span>
            </div>
            <div style="font-size:13px">Kitob yuklanmoqda...</div>
        </div>
        <canvas id="pdf-canvas" style="display:none;box-shadow:0 4px 24px rgba(0,0,0,.15);max-width:100%"></canvas>
    </div>

    {{-- Navigation --}}
    <div class="card-body py-3" style="border-top:1px solid var(--c-border)">
        <div class="d-flex align-items-center justify-content-center gap-4">
            <button onclick="prevPage()" id="prev-page" class="btn btn-sm" style="background:var(--c-teal);color:#fff">
                <i class="fas fa-chevron-left me-1"></i>Oldingi
            </button>
            <div class="d-flex align-items-center gap-2 px-3 py-1 rounded" style="background:var(--c-bg);border:1px solid var(--c-border)">
                <span style="font-size:12px;color:var(--c-text-3)">Sahifa</span>
                <input type="number" id="page-num" min="1" value="1" onchange="goToPage(this.value)"
                       style="width:50px;text-align:center;border:1px solid var(--c-border);border-radius:6px;padding:2px 4px;font-size:13px;font-weight:600;color:var(--c-text);background:transparent">
                <span style="font-size:12px;color:var(--c-text-3)">/</span>
                <span id="page-count" style="font-size:13px;font-weight:600;color:var(--c-text)">—</span>
            </div>
            <button onclick="nextPage()" id="next-page" class="btn btn-sm" style="background:var(--c-teal);color:#fff">
                Keyingi <i class="fas fa-chevron-right ms-1"></i>
            </button>
        </div>
        <div class="mt-3" style="height:4px;background:var(--c-border);border-radius:2px;overflow:hidden">
            <div id="progress-bar" style="width:0%;height:100%;background:var(--c-teal);transition:width .3s"></div>
        </div>
    </div>
</div>

{{-- Book info --}}
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-info-circle" style="color:var(--c-teal)"></i>
        <span>Kitob haqida</span>
    </div>
    <div class="card-body p-0">
        @php
            $rows = [['fas fa-book','Nomi',$book->title],['fas fa-user-edit','Muallif',$book->author ?? "Noma'lum"]];
            if ($book->publisher) $rows[] = ['fas fa-building','Nashriyot',$book->publisher];
            if (isset($book->category) && $book->category) $rows[] = ['fas fa-tag','Kategoriya',$book->category->name];
            if ($book->pages) $rows[] = ['fas fa-file-alt','Sahifalar soni',$book->pages];
            if ($book->publication_year) $rows[] = ['fas fa-calendar','Nashr yili',$book->publication_year];
        @endphp
        @foreach($rows as [$icon,$label,$value])
        <div class="d-flex align-items-center px-4 py-2" style="border-bottom:1px solid var(--c-border)">
            <i class="fas {{ $icon }} me-2" style="color:var(--c-teal);width:14px"></i>
            <span style="width:120px;font-size:12px;color:var(--c-text-3);flex-shrink:0">{{ $label }}</span>
            <span style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $value }}</span>
        </div>
        @endforeach
        @if($book->description)
        <div class="px-4 py-3">
            <div style="font-size:12px;color:var(--c-text-3);margin-bottom:4px">Tavsif</div>
            <p style="font-size:13px;color:var(--c-text-2);margin:0;line-height:1.6">{{ $book->description }}</p>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

let pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null, scale = 1.5;
const canvas = document.getElementById('pdf-canvas');
const ctx = canvas.getContext('2d');
const loadingEl = document.getElementById('pdf-loading');
const url = '{{ asset("storage/" . $book->file_path) }}';

pdfjsLib.getDocument(url).promise.then(function(doc) {
    pdfDoc = doc;
    document.getElementById('page-count').textContent = doc.numPages;
    loadingEl.style.display = 'none';
    canvas.style.display = 'block';
    renderPage(pageNum);
}).catch(function(err) {
    loadingEl.innerHTML = `<div style="color:var(--c-rose)">
        <i class="fas fa-exclamation-triangle mb-2" style="display:block;font-size:28px"></i>
        <div style="font-size:13px">Kitobni yuklashda xatolik: ${err.message}</div>
    </div>`;
});

function renderPage(num) {
    pageRendering = true;
    pdfDoc.getPage(num).then(function(page) {
        const viewport = page.getViewport({scale});
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        page.render({canvasContext: ctx, viewport}).promise.then(function() {
            pageRendering = false;
            if (pageNumPending !== null) { renderPage(pageNumPending); pageNumPending = null; }
            document.getElementById('progress-bar').style.width = (num / pdfDoc.numPages * 100) + '%';
        });
    });
    document.getElementById('page-num').value = num;
    document.getElementById('prev-page').disabled = num <= 1;
    document.getElementById('next-page').disabled = num >= (pdfDoc?.numPages || 1);
}

function queueRenderPage(num) {
    if (pageRendering) pageNumPending = num;
    else renderPage(num);
}

function prevPage() { if (pageNum > 1) { pageNum--; queueRenderPage(pageNum); } }
function nextPage() { if (pageNum < pdfDoc.numPages) { pageNum++; queueRenderPage(pageNum); } }
function goToPage(num) { num = parseInt(num); if (num >= 1 && num <= pdfDoc.numPages) { pageNum = num; queueRenderPage(pageNum); } }
function zoomIn() { scale += 0.25; document.getElementById('zoomLevel').textContent = Math.round(scale*100)+'%'; queueRenderPage(pageNum); }
function zoomOut() { if (scale <= 0.5) return; scale -= 0.25; document.getElementById('zoomLevel').textContent = Math.round(scale*100)+'%'; queueRenderPage(pageNum); }
function toggleFullscreen() {
    const el = document.getElementById('pdf-container');
    if (!document.fullscreenElement) el.requestFullscreen().catch(e => console.error(e));
    else document.exitFullscreen();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft') prevPage();
    if (e.key === 'ArrowRight') nextPage();
    if (e.key === '+' || e.key === '=') zoomIn();
    if (e.key === '-') zoomOut();
    if (e.key === 'f' || e.key === 'F') toggleFullscreen();
});
</script>
@endpush
