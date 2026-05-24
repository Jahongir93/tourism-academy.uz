@extends('layouts.dashboard-new')

@section('title', $book->title . ' — E-kutubxona')
@section('page-title', $book->title)

@section('content')

<x-lms-alerts />

{{-- Action buttons --}}
<div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
    @if($book->allow_online_reading)
    <a href="{{ route('lms.library.read', $book) }}" class="btn btn-sm"
       style="background:var(--c-teal);color:#fff">
        <i class="fas fa-book-reader me-1"></i>O'qish
    </a>
    @endif
    @if($book->allow_download)
    <a href="{{ route('lms.library.download', $book) }}" class="btn btn-sm"
       style="background:var(--c-sky);color:#fff">
        <i class="fas fa-download me-1"></i>Yuklab olish
    </a>
    @endif
    @can('update', $book)
    <a href="{{ route('lms.library.edit', $book) }}" class="btn btn-sm"
       style="background:var(--c-amber);color:#fff">
        <i class="fas fa-edit me-1"></i>Tahrirlash
    </a>
    @endcan
    <a href="{{ route('lms.library.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
        <i class="fas fa-arrow-left me-1"></i>Ortga
    </a>
</div>

<div class="row g-4">
    {{-- Main --}}
    <div class="col-lg-8">

        {{-- Book header --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex gap-4">
                    <div style="flex-shrink:0">
                        @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                             style="width:120px;height:160px;object-fit:cover;border-radius:8px;border:2px solid var(--c-border)">
                        @else
                        <div style="width:120px;height:160px;background:linear-gradient(135deg,var(--c-teal),var(--c-emerald));border-radius:8px;display:flex;align-items:center;justify-content:center">
                            <i class="fas fa-book" style="font-size:36px;color:#fff"></i>
                        </div>
                        @endif
                    </div>
                    <div style="flex:1;min-width:0">
                        <h2 style="font-size:20px;font-weight:700;color:var(--c-text);margin-bottom:4px">{{ $book->title }}</h2>
                        <div style="font-size:14px;color:var(--c-text-2);margin-bottom:12px">
                            <i class="fas fa-user-edit me-1" style="color:var(--c-teal)"></i>{{ $book->author }}
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @if($book->libraryCategory)
                            <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">
                                <i class="fas fa-folder me-1"></i>{{ $book->libraryCategory->name_uz ?? $book->libraryCategory->name ?? 'Umumiy' }}
                            </span>
                            @endif
                            @if($book->publication_year)
                            <span class="badge" style="background:rgba(14,165,233,.12);color:var(--c-sky);font-size:11px">
                                <i class="fas fa-calendar me-1"></i>{{ $book->publication_year }}
                            </span>
                            @endif
                            @if($book->pages)
                            <span class="badge" style="background:rgba(124,58,237,.12);color:var(--c-violet);font-size:11px">
                                <i class="fas fa-file-alt me-1"></i>{{ $book->pages }} sahifa
                            </span>
                            @endif
                            <span class="badge" style="background:rgba(245,158,11,.12);color:var(--c-amber);font-size:11px">
                                <i class="fas fa-language me-1"></i>{{ strtoupper($book->language) }}
                            </span>
                        </div>
                        <div class="d-flex gap-4" style="font-size:12px;color:var(--c-text-3)">
                            <span><i class="fas fa-eye me-1"></i>{{ $book->view_count ?? 0 }} ko'rildi</span>
                            <span><i class="fas fa-download me-1"></i>{{ $book->download_count ?? 0 }} yuklandi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($book->description)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-align-left" style="color:var(--c-teal)"></i>
                <span>Kitob haqida</span>
            </div>
            <div class="card-body">
                <p style="font-size:14px;color:var(--c-text-2);line-height:1.7;margin:0">{!! nl2br(e($book->description)) !!}</p>
            </div>
        </div>
        @endif

        {{-- Additional info --}}
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-info-circle" style="color:var(--c-sky)"></i>
                <span>Qo'shimcha ma'lumotlar</span>
            </div>
            <div class="card-body p-0">
                @php
                    $extraRows = [];
                    if ($book->isbn) $extraRows[] = ['fas fa-barcode','var(--c-teal)','ISBN', $book->isbn];
                    if ($book->publisher) $extraRows[] = ['fas fa-building','var(--c-sky)','Nashriyot', $book->publisher];
                    if ($book->edition) $extraRows[] = ['fas fa-layer-group','var(--c-violet)','Nashr', $book->edition];
                    if ($book->file_size) $extraRows[] = ['fas fa-hdd','var(--c-emerald)','Fayl hajmi', number_format($book->file_size / 1024 / 1024, 2) . ' MB'];
                    if ($book->file_type) $extraRows[] = ['fas fa-file','var(--c-rose)','Fayl turi', strtoupper($book->file_type)];
                    if ($book->uploader) $extraRows[] = ['fas fa-user-circle','var(--c-amber)','Yuklagan', $book->uploader->name];
                    $extraRows[] = ['fas fa-clock','var(--c-text-3)','Yuklangan sana', $book->created_at->format('d.m.Y')];
                @endphp
                @foreach($extraRows as [$icon,$color,$label,$value])
                <div class="d-flex align-items-center px-4 py-2" style="border-bottom:1px solid var(--c-border)">
                    <i class="fas {{ $icon }} me-2" style="color:{{ $color }};width:14px"></i>
                    <span style="width:130px;font-size:12px;color:var(--c-text-3);flex-shrink:0">{{ $label }}</span>
                    <span style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $value }}</span>
                </div>
                @endforeach

                @if($book->tags && count($book->tags) > 0)
                <div class="px-4 py-3">
                    <div style="font-size:12px;color:var(--c-text-3);margin-bottom:8px">Teglar</div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($book->tags as $tag)
                        <span class="badge" style="background:rgba(20,184,166,.1);color:var(--c-teal);font-size:11px">#{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        @if(isset($relatedBooks) && $relatedBooks->count() > 0)
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-layer-group" style="color:var(--c-teal)"></i>
                <span>O'xshash kitoblar</span>
            </div>
            <div class="card-body p-0">
                @foreach($relatedBooks as $related)
                <a href="{{ route('lms.library.show', $related) }}"
                   class="d-flex gap-3 px-3 py-3 text-decoration-none"
                   style="border-bottom:1px solid var(--c-border);transition:background .15s"
                   onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background=''">
                    <div style="flex-shrink:0">
                        @if($related->cover_image)
                        <img src="{{ asset('storage/' . $related->cover_image) }}" alt="{{ $related->title }}"
                             style="width:44px;height:56px;object-fit:cover;border-radius:6px;border:1px solid var(--c-border)">
                        @else
                        <div style="width:44px;height:56px;background:linear-gradient(135deg,var(--c-teal),var(--c-emerald));border-radius:6px;display:flex;align-items:center;justify-content:center">
                            <i class="fas fa-book" style="color:#fff;font-size:14px"></i>
                        </div>
                        @endif
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $related->title }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">{{ $related->author }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)"><i class="fas fa-eye me-1"></i>{{ $related->view_count ?? 0 }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
