@extends('layouts.dashboard-new')

@section('title', "LMS Dashboard — HEMIS")
@section('page-title', "Online Ta'lim Platformasi")

@section('styles')
<style>
.lms-module-card {
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    padding:22px 16px;background:var(--c-surface);border:1px solid var(--c-border);
    border-radius:12px;text-decoration:none;transition:all .2s;text-align:center;
}
.lms-module-card:hover {
    transform:translateY(-3px);box-shadow:0 6px 20px rgba(0,0,0,.08);
    border-color:var(--c-teal);text-decoration:none;
}
.lms-module-icon {
    width:52px;height:52px;border-radius:12px;display:flex;align-items:center;
    justify-content:center;font-size:20px;margin-bottom:10px;
}
.lms-module-title { font-size:13px;font-weight:700;color:var(--c-text);margin-bottom:2px; }
.lms-module-count { font-size:12px;color:var(--c-text-3); }
.lms-section-header {
    display:flex;align-items:center;gap:8px;padding:14px 18px;
    border-bottom:1px solid var(--c-border);font-weight:700;font-size:14px;color:var(--c-text);
}
.lms-list-item {
    display:flex;align-items:flex-start;gap:10px;padding:12px 18px;
    border-bottom:1px solid var(--c-border);transition:background .15s;text-decoration:none;
}
.lms-list-item:last-child { border-bottom:none; }
.lms-list-item:hover { background:var(--c-bg);text-decoration:none; }
.lms-file-icon {
    width:38px;height:38px;border-radius:8px;display:flex;align-items:center;
    justify-content:center;font-size:16px;flex-shrink:0;
}
.lms-course-card {
    background:var(--c-surface);border:1px solid var(--c-border);border-radius:12px;
    overflow:hidden;transition:all .2s;
}
.lms-course-card:hover { transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,0,0,.08);border-color:var(--c-teal); }
.lms-forum-avatar {
    width:40px;height:40px;border-radius:50%;display:flex;align-items:center;
    justify-content:center;font-size:15px;font-weight:700;color:white;flex-shrink:0;
    background:linear-gradient(135deg,var(--c-teal),var(--c-emerald));
}
</style>
@endsection

@section('content')

{{-- Module cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('lms.courses.index') }}" class="lms-module-card">
            <div class="lms-module-icon" style="background:rgba(20,184,166,.12);color:var(--c-teal)">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="lms-module-title">Kurslar</div>
            <div class="lms-module-count">{{ $stats['total_courses'] ?? 0 }} ta kurs</div>
        </a>
    </div>
    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('lms.materials.index') }}" class="lms-module-card">
            <div class="lms-module-icon" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">
                <i class="fas fa-book"></i>
            </div>
            <div class="lms-module-title">O'quv materiallari</div>
            <div class="lms-module-count">{{ $stats['total_materials'] ?? 0 }} ta material</div>
        </a>
    </div>
    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('lms.videos.index') }}" class="lms-module-card">
            <div class="lms-module-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                <i class="fas fa-video"></i>
            </div>
            <div class="lms-module-title">Video darslar</div>
            <div class="lms-module-count">{{ $stats['total_videos'] ?? 0 }} ta video</div>
        </a>
    </div>
    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('lms.tests.index') }}" class="lms-module-card">
            <div class="lms-module-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="lms-module-title">Interaktiv testlar</div>
            <div class="lms-module-count">{{ $stats['total_tests'] ?? 0 }} ta test</div>
        </a>
    </div>
    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('lms.forum.index') }}" class="lms-module-card">
            <div class="lms-module-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-comments"></i>
            </div>
            <div class="lms-module-title">Forum</div>
            <div class="lms-module-count">{{ $stats['forum_posts'] ?? 0 }} ta post</div>
        </a>
    </div>
    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('lms.library.index') }}" class="lms-module-card">
            <div class="lms-module-icon" style="background:rgba(244,63,94,.12);color:var(--c-rose)">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="lms-module-title">E-kutubxona</div>
            <div class="lms-module-count">{{ $stats['total_books'] ?? 0 }} ta kitob</div>
        </a>
    </div>
    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('lms.certificates.index') }}" class="lms-module-card">
            <div class="lms-module-icon" style="background:rgba(20,184,166,.12);color:var(--c-teal)">
                <i class="fas fa-certificate"></i>
            </div>
            <div class="lms-module-title">Sertifikatlar</div>
            <div class="lms-module-count">{{ $stats['my_certificates'] ?? 0 }} ta sertifikat</div>
        </a>
    </div>
    <div class="col-6 col-sm-4 col-xl-3">
        <a href="{{ route('lms.progress') }}" class="lms-module-card">
            <div class="lms-module-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="lms-module-title">Mening yutuqlarim</div>
            <div class="lms-module-count">{{ $stats['my_enrollments'] ?? 0 }} ta kurs</div>
        </a>
    </div>
</div>

{{-- Quick actions for teachers --}}
@if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
<div class="card mb-4">
    <div class="lms-section-header">
        <i class="fas fa-bolt" style="color:var(--c-amber)"></i>
        Tezkor amallar
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-6 col-md-3">
                <a href="{{ route('lms.courses.create') }}" class="lms-module-card" style="padding:14px">
                    <div class="lms-module-icon" style="background:rgba(20,184,166,.12);color:var(--c-teal);width:40px;height:40px;font-size:16px">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="lms-module-title" style="font-size:12px">Yangi kurs</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('lms.materials.create') }}" class="lms-module-card" style="padding:14px">
                    <div class="lms-module-icon" style="background:rgba(16,185,129,.12);color:var(--c-emerald);width:40px;height:40px;font-size:16px">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="lms-module-title" style="font-size:12px">Material yuklash</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('lms.videos.create') }}" class="lms-module-card" style="padding:14px">
                    <div class="lms-module-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet);width:40px;height:40px;font-size:16px">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="lms-module-title" style="font-size:12px">Video qo'shish</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('lms.tests.create') }}" class="lms-module-card" style="padding:14px">
                    <div class="lms-module-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber);width:40px;height:40px;font-size:16px">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="lms-module-title" style="font-size:12px">Test yaratish</div>
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Featured courses + recent content --}}
<div class="row g-4 mb-4">
    {{-- Featured Courses --}}
    <div class="col-12 col-xl-8">
        <div class="card h-100">
            <div class="lms-section-header justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-graduation-cap" style="color:var(--c-teal)"></i>
                    Ommabop kurslar
                </div>
                <a href="{{ route('lms.courses.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:12px">
                    Barchasi <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse($featuredCourses ?? [] as $course)
                    <div class="col-12 col-sm-6">
                        <div class="lms-course-card">
                            <div style="height:100px;overflow:hidden;background:linear-gradient(135deg,var(--c-teal),var(--c-emerald));display:flex;align-items:center;justify-content:center">
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                         style="width:100%;height:100%;object-fit:cover"
                                         onerror="this.style.display='none'">
                                @else
                                    <i class="fas fa-graduation-cap text-white" style="font-size:32px;opacity:.6"></i>
                                @endif
                            </div>
                            <div class="p-3">
                                <div style="font-weight:700;font-size:13px;color:var(--c-text);margin-bottom:4px;line-height:1.3">{{ Str::limit($course->title, 50) }}</div>
                                <div style="font-size:12px;color:var(--c-text-3);margin-bottom:8px">{{ $course->teacher?->name ?? "O'qituvchi" }}</div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:10px">
                                        <i class="fas fa-users me-1"></i>{{ $course->enrollment_count ?? 0 }}
                                    </span>
                                    <a href="{{ route('lms.courses.show', $course) }}" class="btn btn-sm btn-primary" style="font-size:11px;padding:3px 10px">Ko'rish</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="empty-state py-4">
                            <div class="empty-state-icon"><i class="fas fa-graduation-cap"></i></div>
                            <div class="empty-state-sub">Kurslar mavjud emas</div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Materials --}}
    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="lms-section-header justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-book" style="color:var(--c-emerald)"></i>
                    Yangi materiallar
                    @if(isset($stats['total_materials']))
                    <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:10px">{{ $stats['total_materials'] }}</span>
                    @endif
                </div>
                <a href="{{ route('lms.materials.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:11px">Barchasi</a>
            </div>
            <div style="overflow-y:auto;max-height:300px">
                @forelse($recentMaterials as $material)
                @php
                    $micons = ['presentation'=>['fa-file-powerpoint','rgba(234,88,12,.12)','var(--c-amber)'],'document'=>['fa-file-word','rgba(14,165,233,.12)','var(--c-sky)'],'spreadsheet'=>['fa-file-excel','rgba(16,185,129,.12)','var(--c-emerald)'],'pdf'=>['fa-file-pdf','rgba(244,63,94,.12)','var(--c-rose)'],'other'=>['fa-file','rgba(148,163,184,.12)','var(--c-text-3)']];
                    [$micon,$mbg,$mc] = $micons[$material->material_type] ?? $micons['other'];
                @endphp
                <a href="{{ route('lms.materials.show', $material) }}" class="lms-list-item">
                    <div class="lms-file-icon" style="background:{{ $mbg }};color:{{ $mc }}">
                        <i class="fas {{ $micon }}"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $material->title }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">{{ $material->subject?->name_uz ?? '' }} • {{ $material->created_at->diffForHumans() }}</div>
                    </div>
                </a>
                @empty
                <div class="p-4 text-center" style="color:var(--c-text-3);font-size:13px">Materiallar mavjud emas</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Videos + Tests + Forum --}}
<div class="row g-4 mb-4">
    {{-- Recent Videos --}}
    <div class="col-12 col-md-4">
        <div class="card h-100">
            <div class="lms-section-header justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-video" style="color:var(--c-violet)"></i>
                    Yangi videolar
                </div>
                <a href="{{ route('lms.videos.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:11px">Barchasi</a>
            </div>
            <div>
                @forelse($recentVideos as $video)
                <a href="{{ route('lms.videos.show', $video) }}" class="lms-list-item">
                    <div class="lms-file-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $video->title }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">{{ $video->duration_formatted ?? '' }} • {{ $video->created_at->diffForHumans() }}</div>
                    </div>
                </a>
                @empty
                <div class="p-4 text-center" style="color:var(--c-text-3);font-size:13px">Videolar mavjud emas</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Upcoming Tests --}}
    <div class="col-12 col-md-4">
        <div class="card h-100">
            <div class="lms-section-header justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-clipboard-list" style="color:var(--c-amber)"></i>
                    Testlar
                </div>
                <a href="{{ route('lms.tests.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:11px">Barchasi</a>
            </div>
            <div>
                @forelse($upcomingTests as $test)
                <a href="{{ route('lms.tests.show', $test) }}" class="lms-list-item">
                    <div class="lms-file-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $test->title }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">{{ $test->question_count ?? 0 }} savol
                            @if($test->available_until)
                             • <span style="color:var(--c-rose)">{{ $test->available_until->format('d.m.Y') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                @empty
                <div class="p-4 text-center" style="color:var(--c-text-3);font-size:13px">Testlar mavjud emas</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Forum --}}
    <div class="col-12 col-md-4">
        <div class="card h-100">
            <div class="lms-section-header justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-comments" style="color:var(--c-sky)"></i>
                    Forum
                </div>
                <a href="{{ route('lms.forum.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:11px">Barchasi</a>
            </div>
            <div>
                @forelse($recentForumPosts as $post)
                <a href="{{ route('lms.forum.show', $post) }}" class="lms-list-item">
                    <div class="lms-forum-avatar">{{ substr($post->user?->name ?? 'F', 0, 1) }}</div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $post->title }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">
                            {{ $post->reply_count ?? 0 }} javob • {{ $post->created_at->diffForHumans() }}
                            @if($post->is_answered)
                            <span class="badge ms-1" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:10px">Hal</span>
                            @endif
                        </div>
                    </div>
                </a>
                @empty
                <div class="p-4 text-center" style="color:var(--c-text-3);font-size:13px">Forum postlari mavjud emas</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Featured Books --}}
@if(isset($featuredBooks) && count($featuredBooks) > 0)
<div class="card">
    <div class="lms-section-header justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-book-open" style="color:var(--c-rose)"></i>
            Tavsiya etilgan kitoblar
        </div>
        <a href="{{ route('lms.library.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:11px">E-kutubxona</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($featuredBooks as $book)
            <div class="col-6 col-md-3">
                <a href="{{ route('lms.library.show', $book) }}" class="lms-course-card d-block text-decoration-none">
                    <div style="height:120px;overflow:hidden;background:var(--c-bg);display:flex;align-items:center;justify-content:center">
                        @if($book->cover_image)
                            <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" style="width:100%;height:100%;object-fit:cover">
                        @else
                            <i class="fas fa-book" style="font-size:36px;color:var(--c-text-3)"></i>
                        @endif
                    </div>
                    <div class="p-2">
                        <div style="font-size:12px;font-weight:700;color:var(--c-text)">{{ Str::limit($book->title, 40) }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">{{ $book->author ?? '' }}</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection
