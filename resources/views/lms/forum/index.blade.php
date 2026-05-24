@extends('layouts.dashboard-new')

@section('title', 'Forum — LMS')
@section('page-title', 'Forum')

@section('styles')
<style>
.forum-avatar {
    width:44px;height:44px;border-radius:50%;display:flex;align-items:center;
    justify-content:center;font-size:17px;font-weight:700;color:white;flex-shrink:0;
    background:linear-gradient(135deg,var(--c-teal),var(--c-emerald));
}
.forum-thread {
    display:flex;align-items:flex-start;gap:14px;padding:16px 20px;
    border-bottom:1px solid var(--c-border);transition:background .15s;
}
.forum-thread:last-child { border-bottom:none; }
.forum-thread:hover { background:var(--c-bg); }
.forum-stat-pill {
    display:inline-flex;align-items:center;gap:4px;
    font-size:12px;color:var(--c-text-3);padding:4px 8px;
    background:var(--c-bg);border-radius:6px;border:1px solid var(--c-border);
}
</style>
@endsection

@section('content')

{{-- Stats --}}
@if(count($posts ?? []) > 0)
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-sky)">
            <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ count($posts ?? []) }}</div>
            <div class="stat-card-label">Jami mavzular</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-emerald)">
            <div class="stat-card-icon" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ collect($posts ?? [])->where('is_answered', true)->count() }}</div>
            <div class="stat-card-label">Hal qilingan</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-violet)">
            <div class="stat-card-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                <i class="fas fa-comment"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ collect($posts ?? [])->sum('reply_count') }}</div>
            <div class="stat-card-label">Jami javoblar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-thumbtack"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ collect($posts ?? [])->where('is_pinned', true)->count() }}</div>
            <div class="stat-card-label">Mahkamlangan</div>
        </div>
    </div>
</div>
@endif

<x-lms-alerts />

{{-- Threads --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-comments" style="color:var(--c-teal)"></i>
            <span>Forum muhokamalar</span>
            @if(count($posts ?? []))
            <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">{{ count($posts) }} ta</span>
            @endif
        </div>
        <a href="{{ route('lms.forum.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Yangi mavzu
        </a>
    </div>
    <div class="card-body p-0">
        @forelse($posts ?? [] as $post)
        <div class="forum-thread">
            <div class="forum-avatar">{{ substr($post->user->name ?? 'F', 0, 1) }}</div>
            <div style="flex:1;min-width:0">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-1">
                    <a href="{{ route('lms.forum.show', $post) }}"
                       style="font-weight:700;font-size:14px;color:var(--c-text);text-decoration:none;line-height:1.3"
                       class="flex-1">
                        {{ $post->title }}
                        @if($post->is_locked)
                            <i class="fas fa-lock ms-1" style="color:var(--c-rose);font-size:11px"></i>
                        @endif
                    </a>
                    <div class="d-flex gap-1 flex-shrink-0">
                        @if($post->is_pinned)
                            <span class="badge" style="background:rgba(245,158,11,.12);color:var(--c-amber);font-size:10px">
                                <i class="fas fa-thumbtack me-1"></i>Pin
                            </span>
                        @endif
                        @if($post->is_answered)
                            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:10px">
                                <i class="fas fa-check-circle me-1"></i>Hal
                            </span>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mb-2" style="font-size:12px;color:var(--c-text-3)">
                    <span><i class="fas fa-user me-1"></i>{{ $post->user->name ?? '' }}</span>
                    @if($post->subject)
                        <span class="badge" style="background:rgba(14,165,233,.12);color:var(--c-sky);font-size:10px">
                            <i class="fas fa-book me-1"></i>{{ $post->subject->name_uz }}
                        </span>
                    @endif
                    <span><i class="fas fa-clock me-1"></i>{{ $post->created_at->diffForHumans() }}</span>
                </div>
                @if($post->content)
                <div style="font-size:12px;color:var(--c-text-2);margin-bottom:8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                    {{ Str::limit(strip_tags($post->content), 200) }}
                </div>
                @endif
                <div class="d-flex gap-2">
                    <div class="forum-stat-pill">
                        <i class="fas fa-comment" style="color:var(--c-sky)"></i>
                        {{ $post->reply_count ?? 0 }} javob
                    </div>
                    <div class="forum-stat-pill">
                        <i class="fas fa-eye" style="color:var(--c-violet)"></i>
                        {{ $post->view_count ?? 0 }} ko'rildi
                    </div>
                    <div class="forum-stat-pill">
                        <i class="fas fa-thumbs-up" style="color:var(--c-emerald)"></i>
                        {{ $post->like_count ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state py-5">
            <div class="empty-state-icon"><i class="fas fa-comments"></i></div>
            <div class="empty-state-sub">Hech qanday mavzu mavjud emas</div>
            <div class="mt-3">
                <a href="{{ route('lms.forum.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Birinchi mavzuni yaratish
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

@endsection
