@extends('layouts.dashboard-new')

@section('title', 'E-kutubxona — LMS')
@section('page-title', 'Elektron kutubxona')

@section('styles')
<style>
.book-card {
    background:var(--c-surface);border:1px solid var(--c-border);border-radius:10px;
    overflow:hidden;transition:all .2s;text-decoration:none;display:block;
}
.book-card:hover { transform:translateY(-3px);box-shadow:0 6px 18px rgba(0,0,0,.09);border-color:var(--c-teal);text-decoration:none; }
.book-thumb {
    height:160px;overflow:hidden;background:var(--c-bg);
    display:flex;align-items:center;justify-content:center;position:relative;
}
.book-thumb img { width:100%;height:100%;object-fit:cover; }
.cat-pill {
    display:inline-flex;align-items:center;padding:5px 12px;border-radius:20px;
    font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .15s;
    border:1px solid var(--c-border);color:var(--c-text-2);background:var(--c-surface);
}
.cat-pill:hover { border-color:var(--c-teal);color:var(--c-teal);text-decoration:none; }
.cat-pill.active { background:var(--c-teal);color:#fff;border-color:var(--c-teal); }
</style>
@endsection

@section('content')

{{-- Search + actions --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('lms.library.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-7">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Qidirish</label>
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Kitob nomi, muallif yoki mavzu..." class="form-control" style="padding-left:34px!important">
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2 align-self-end">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="fas fa-search me-1"></i>Qidirish</button>
                    <a href="{{ route('lms.library.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2 align-self-end">
                    @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
                    <a href="{{ route('lms.library.categories') }}" class="btn btn-outline-secondary flex-fill" style="font-size:12px">
                        <i class="fas fa-folder-tree me-1"></i>Kataloglar
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Category pills --}}
<div class="d-flex align-items-center gap-2 flex-wrap mb-4">
    <span style="font-size:12px;font-weight:600;color:var(--c-text-3)">
        <i class="fas fa-filter me-1"></i>Kategoriyalar:
    </span>
    <a href="{{ route('lms.library.index') }}" class="cat-pill {{ !request('category_id') ? 'active' : '' }}">
        <i class="fas fa-list me-1"></i>Barchasi
    </a>
    @foreach($categories ?? [] as $category)
    <a href="{{ route('lms.library.index', ['category_id' => $category->id]) }}"
       class="cat-pill {{ request('category_id') == $category->id ? 'active' : '' }}">
        <i class="fas fa-folder me-1"></i>
        {{ $category->name_uz ?? $category->name ?? $category->name_en }}
        <span class="ms-1" style="opacity:.7;font-size:11px">({{ $category->books_count ?? 0 }})</span>
    </a>
    @endforeach
</div>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-book-open" style="color:var(--c-teal)"></i>
        <span style="font-weight:700;color:var(--c-text)">Kitoblar</span>
        @if(isset($books) && $books->total())
        <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">{{ $books->total() }} ta</span>
        @endif
    </div>
    @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
    <a href="{{ route('lms.library.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i>Kitob qo'shish
    </a>
    @endif
</div>

{{-- Books grid --}}
<div class="row g-3">
    @forelse($books ?? [] as $book)
    <div class="col-6 col-sm-4 col-md-3 col-xl-2">
        <a href="{{ route('lms.library.show', $book) }}" class="book-card">
            <div class="book-thumb">
                @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}">
                @else
                    <i class="fas fa-book" style="font-size:40px;color:var(--c-text-3)"></i>
                @endif
                <div style="position:absolute;top:6px;left:6px">
                    <span style="background:rgba(20,184,166,.85);color:#fff;font-size:10px;font-weight:600;padding:2px 6px;border-radius:4px">
                        {{ $book->libraryCategory->name_uz ?? $book->libraryCategory->name ?? 'Umumiy' }}
                    </span>
                </div>
                <div style="position:absolute;bottom:6px;right:6px">
                    <span style="background:rgba(0,0,0,.6);color:#fff;font-size:10px;font-weight:700;padding:2px 5px;border-radius:3px;text-transform:uppercase">
                        {{ $book->file_type ?? 'PDF' }}
                    </span>
                </div>
            </div>
            <div class="p-2">
                <div style="font-size:12px;font-weight:700;color:var(--c-text);line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:3px">
                    {{ $book->title }}
                </div>
                <div style="font-size:11px;color:var(--c-text-3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    <i class="fas fa-user fa-xs me-1" style="color:var(--c-teal)"></i>{{ $book->author ?? "Noma'lum" }}
                </div>
                <div class="d-flex align-items-center justify-content-between mt-1" style="border-top:1px solid var(--c-border);padding-top:4px;font-size:10px;color:var(--c-text-3)">
                    <span><i class="fas fa-file-alt me-1"></i>{{ $book->pages ?? '-' }}</span>
                    <span><i class="fas fa-download me-1"></i>{{ $book->download_count ?? 0 }}</span>
                    <span><i class="fas fa-eye me-1"></i>{{ $book->view_count ?? 0 }}</span>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="empty-state py-5">
                    <div class="empty-state-icon"><i class="fas fa-book-open"></i></div>
                    <div class="empty-state-sub">Kitoblar topilmadi</div>
                    @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
                    <div class="mt-3">
                        <a href="{{ route('lms.library.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Kitob qo'shish
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if(isset($books) && $books->hasPages())
<div class="mt-4 px-1 py-3 d-flex align-items-center justify-content-between" style="border-top:1px solid var(--c-border)">
    <span style="font-size:13px;color:var(--c-text-2)">
        {{ $books->firstItem() }}–{{ $books->lastItem() }} / {{ $books->total() }} ta kitob
    </span>
    {{ $books->withQueryString()->links() }}
</div>
@endif

@endsection
