@extends('layouts.dashboard-new')

@section('title', 'CMS Dashboard')
@section('page-title', 'Content Management System')

@section('styles')
<style>
.cms-module-link { transition:background .15s,transform .15s; }
.cms-module-link:hover { background:rgba(124,58,237,.07) !important; transform:translateX(4px); }
.quick-action { border:2px solid var(--c-border);border-radius:12px;padding:18px 12px;text-align:center;text-decoration:none;display:block;transition:all .15s;background:var(--c-bg); }
.quick-action:hover { border-color:var(--c-violet);background:rgba(124,58,237,.05); }
</style>
@endsection

@section('content')

<x-lms-alerts />

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    @php
    $stats = [
        ['fa-file-alt','var(--c-violet)','rgba(124,58,237,.1)', App\Models\CmsPage::count(), 'Sahifalar', route('cms.pages.index')],
        ['fa-newspaper','var(--c-rose)','rgba(244,63,94,.1)',   App\Models\CmsNews::count() ?? 0, 'Yangiliklar', route('cms.news.index')],
        ['fa-calendar-alt','var(--c-sky)','rgba(14,165,233,.1)', App\Models\CmsEvent::count() ?? 0, 'Tadbirlar', route('cms.events.index')],
        ['fa-photo-video','var(--c-amber)','rgba(245,158,11,.1)', App\Models\CmsMedia::count() ?? 0, 'Media fayllar', route('cms.media.index')],
    ];
    @endphp
    @foreach($stats as [$icon,$color,$bg,$count,$label,$link])
    <div class="col-lg-3 col-md-6">
        <a href="{{ $link }}" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-card-icon" style="background:{{ $bg }}">
                    <i class="fas {{ $icon }}" style="color:{{ $color }}"></i>
                </div>
                <div class="stat-card-value">{{ $count }}</div>
                <div class="stat-card-label">{{ $label }}</div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- Quick actions --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-bolt" style="color:var(--c-amber)"></i>
        <span>Tezkor harakatlar</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @php
            $actions = [
                [route('cms.pages.create'),'fa-file-plus','var(--c-violet)','rgba(124,58,237,.08)','Yangi sahifa','Statik sahifa yaratish'],
                [route('cms.news.create'),'fa-newspaper','var(--c-rose)','rgba(244,63,94,.08)','Yangi yangilik','Maqola qo\'shish'],
                [route('cms.events.create'),'fa-calendar-plus','var(--c-sky)','rgba(14,165,233,.08)','Yangi tadbir','Voqea qo\'shish'],
                [route('cms.media.index'),'fa-cloud-upload-alt','var(--c-amber)','rgba(245,158,11,.08)','Media yuklash','Fayl qo\'shish'],
            ];
            @endphp
            @foreach($actions as [$href,$icon,$color,$bg,$label,$hint])
            <div class="col-lg-3 col-md-6">
                <a href="{{ $href }}" class="quick-action">
                    <i class="fas {{ $icon }} mb-2" style="font-size:24px;color:{{ $color }};display:block"></i>
                    <div style="font-size:13px;font-weight:700;color:var(--c-text)">{{ $label }}</div>
                    <div style="font-size:11px;color:var(--c-text-3)">{{ $hint }}</div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Bottom row --}}
<div class="row g-4">
    {{-- Recent pages --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-clock" style="color:var(--c-violet)"></i>
                    <span>So'nggi sahifalar</span>
                </div>
                <a href="{{ route('cms.pages.index') }}" style="font-size:12px;color:var(--c-text-3);text-decoration:none">
                    Barchasi <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @foreach(App\Models\CmsPage::latest()->take(6)->get() as $page)
                <a href="{{ route('cms.pages.edit', $page) }}" class="text-decoration-none">
                    <div class="d-flex align-items-center justify-content-between px-3 py-2"
                         style="border-bottom:1px solid var(--c-border);transition:background .12s"
                         onmouseover="this.style.background='rgba(124,58,237,.04)'" onmouseout="this.style.background=''">
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $page->title_uz }}</div>
                            <div style="font-size:11px;color:var(--c-text-3)">{{ $page->created_at->diffForHumans() }}</div>
                        </div>
                        @php $st = $page->status; @endphp
                        <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;
                            background:{{ $st==='published' ? 'rgba(16,185,129,.12)' : ($st==='draft' ? 'rgba(245,158,11,.12)' : 'rgba(100,116,139,.12)') }};
                            color:{{ $st==='published' ? 'var(--c-emerald)' : ($st==='draft' ? 'var(--c-amber)' : 'var(--c-text-3)') }}">
                            {{ $st==='published' ? 'Faol' : ($st==='draft' ? 'Qoralama' : 'Arxiv') }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- CMS modules --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-th-large" style="color:var(--c-sky)"></i>
                <span>CMS Modullar</span>
            </div>
            <div class="card-body p-0">
                @php
                $modules = [
                    [route('cms.pages.index'),'fa-file-alt','var(--c-violet)','Sahifalar','Statik sahifalar'],
                    [route('cms.news.index'),'fa-newspaper','var(--c-rose)','Yangiliklar','Yangiliklar va maqolalar'],
                    [route('cms.events.index'),'fa-calendar-alt','var(--c-sky)','Tadbirlar','Tadbirlar va voqealar'],
                    [route('cms.media.index'),'fa-photo-video','var(--c-amber)','Media','Rasm va fayllar'],
                    [route('cms.menus.index'),'fa-bars','var(--c-emerald)','Menyular','Navigatsiya menyulari'],
                    [route('cms.themes.index'),'fa-palette','var(--c-violet)','Temalar','Sayt dizayni'],
                ];
                @endphp
                @foreach($modules as [$href,$icon,$color,$label,$hint])
                <a href="{{ $href }}" class="text-decoration-none">
                    <div class="cms-module-link d-flex align-items-center gap-3 px-3 py-3"
                         style="border-bottom:1px solid var(--c-border)">
                        <div style="width:36px;height:36px;border-radius:9px;background:rgba(0,0,0,.04);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas {{ $icon }}" style="color:{{ $color }};font-size:15px"></i>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:var(--c-text)">{{ $label }}</div>
                            <div style="font-size:11px;color:var(--c-text-3)">{{ $hint }}</div>
                        </div>
                        <i class="fas fa-chevron-right ms-auto" style="font-size:11px;color:var(--c-text-3)"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
