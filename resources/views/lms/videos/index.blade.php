@extends('layouts.dashboard-new')

@section('title', 'Video darslar — LMS')
@section('page-title', 'Video darslar')

@section('styles')
<style>
.lms-video-card {
    background:var(--c-surface);border:1px solid var(--c-border);border-radius:12px;
    overflow:hidden;transition:all .2s;
}
.lms-video-card:hover { transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.08);border-color:var(--c-teal); }
.lms-video-thumb {
    height:160px;overflow:hidden;position:relative;
    background:linear-gradient(135deg,var(--c-teal),var(--c-emerald));
    display:flex;align-items:center;justify-content:center;
}
.lms-video-thumb img { width:100%;height:100%;object-fit:cover; }
.lms-play-overlay {
    position:absolute;inset:0;background:rgba(0,0,0,.35);
    display:flex;align-items:center;justify-content:center;
    opacity:0;transition:opacity .2s;
}
.lms-video-card:hover .lms-play-overlay { opacity:1; }
.lms-play-btn {
    width:54px;height:54px;background:rgba(255,255,255,.9);border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-size:20px;color:var(--c-teal);padding-left:3px;
}
</style>
@endsection

@section('content')

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-video" style="color:var(--c-teal)"></i>
        <span style="font-weight:700;color:var(--c-text)">Video darslar</span>
        @php $total = isset($videos) && method_exists($videos,'total') ? $videos->total() : count($videos ?? []); @endphp
        @if($total)
        <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">{{ $total }} ta</span>
        @endif
    </div>
    @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
    <a href="{{ route('lms.videos.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i>Video qo'shish
    </a>
    @endif
</div>

<x-lms-alerts />

<div class="row g-4">
    @forelse($videos ?? [] as $video)
    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
        <div class="lms-video-card">
            <div class="lms-video-thumb">
                @if($video->thumbnail_path)
                    <img src="{{ asset($video->thumbnail_path) }}" alt="{{ $video->title }}">
                @else
                    <i class="fas fa-play-circle text-white" style="font-size:48px;opacity:.5"></i>
                @endif
                <div class="lms-play-overlay">
                    <div class="lms-play-btn"><i class="fas fa-play"></i></div>
                </div>
                @if($video->duration_formatted)
                <div style="position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,.7);color:#fff;font-size:11px;font-weight:700;padding:3px 7px;border-radius:4px">
                    <i class="fas fa-clock me-1"></i>{{ $video->duration_formatted }}
                </div>
                @endif
                <div style="position:absolute;top:8px;left:8px;background:rgba(244,63,94,.85);color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:3px">
                    HD
                </div>
                @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
                    @if(Auth::user()->id == $video->teacher_id || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
                    <div style="position:absolute;top:8px;right:8px">
                        <a href="{{ route('lms.videos.edit', $video) }}"
                           class="btn btn-sm" style="background:rgba(245,158,11,.9);color:#fff;padding:3px 8px;font-size:11px">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                    @endif
                @endif
            </div>
            <div class="p-3">
                <div style="font-weight:700;font-size:13px;color:var(--c-text);margin-bottom:4px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.6em">
                    {{ $video->title }}
                </div>
                @if($video->subject)
                <span class="badge mb-2" style="background:rgba(14,165,233,.12);color:var(--c-sky);font-size:10px">
                    <i class="fas fa-book me-1"></i>{{ $video->subject->name_uz }}
                </span>
                @endif
                <div style="font-size:12px;color:var(--c-text-3);margin-bottom:8px">
                    <i class="fas fa-user-tie me-1" style="color:var(--c-teal)"></i>{{ $video->teacher?->name ?? "O'qituvchi" }}
                </div>
                <div class="d-flex align-items-center justify-content-between" style="border-top:1px solid var(--c-border);padding-top:8px;font-size:11px;color:var(--c-text-3)">
                    <span><i class="fas fa-eye me-1" style="color:var(--c-sky)"></i>{{ $video->view_count ?? 0 }}</span>
                    <span><i class="fas fa-thumbs-up me-1" style="color:var(--c-emerald)"></i>{{ $video->like_count ?? 0 }}</span>
                    <span>{{ $video->created_at?->diffForHumans() ?? 'Bugun' }}</span>
                </div>
                <a href="{{ route('lms.videos.watch', $video) }}" class="btn btn-sm mt-2 w-100"
                   style="background:var(--c-teal);color:#fff;font-size:12px">
                    <i class="fas fa-play me-1"></i>Ko'rish
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="empty-state py-5">
                    <div class="empty-state-icon"><i class="fas fa-video"></i></div>
                    <div class="empty-state-sub">Video darslar topilmadi</div>
                    @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
                    <div class="mt-3">
                        <a href="{{ route('lms.videos.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Video qo'shish
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if(isset($videos) && method_exists($videos,'hasPages') && $videos->hasPages())
<div class="mt-4 px-1 py-3 d-flex align-items-center justify-content-between" style="border-top:1px solid var(--c-border)">
    <span style="font-size:13px;color:var(--c-text-2)">
        {{ $videos->firstItem() }}–{{ $videos->lastItem() }} / {{ $videos->total() }} ta video
    </span>
    {{ $videos->withQueryString()->links() }}
</div>
@endif

@endsection
