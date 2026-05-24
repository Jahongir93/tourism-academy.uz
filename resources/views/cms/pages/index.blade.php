@extends('layouts.dashboard-new')

@section('title', 'CMS — Sahifalar')
@section('page-title', 'Sahifalar boshqaruvi')

@section('styles')
<style>
.action-btn { width:30px;height:30px;border-radius:7px;border:1px solid var(--c-border);background:var(--c-bg);display:inline-flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;transition:all .12s;text-decoration:none;color:var(--c-text-2); }
.action-btn:hover { border-color:var(--c-teal);color:var(--c-teal);background:rgba(20,184,166,.07); }
.action-btn.danger:hover { border-color:var(--c-rose);color:var(--c-rose);background:rgba(244,63,94,.07); }
</style>
@endsection

@section('content')

<x-lms-alerts />

{{-- Filter + create --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-file-alt" style="color:var(--c-violet)"></i>
            <span>Sahifalar ro'yxati</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('cms.pages.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Qidirish..." value="{{ request('search') }}" style="width:180px">
                <select name="status" class="form-select form-select-sm" style="width:160px"
                        onchange="this.form.submit()">
                    <option value="">Barcha holatlar</option>
                    <option value="draft"     {{ request('status')==='draft'     ? 'selected':'' }}>Qoralama</option>
                    <option value="published" {{ request('status')==='published' ? 'selected':'' }}>Chop etilgan</option>
                    <option value="archived"  {{ request('status')==='archived'  ? 'selected':'' }}>Arxivlangan</option>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            <a href="{{ route('cms.pages.create') }}" class="btn btn-sm" style="background:var(--c-violet);color:#fff">
                <i class="fas fa-plus me-1"></i>Yangi sahifa
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr style="background:var(--c-bg);font-size:12px;color:var(--c-text-3)">
                        <th style="padding:12px 16px;width:50px">#</th>
                        <th style="padding:12px 16px">Sarlavha</th>
                        <th style="padding:12px 16px">Slug</th>
                        <th style="padding:12px 16px">Holat</th>
                        <th style="padding:12px 16px">Ota sahifa</th>
                        <th style="padding:12px 16px">Ko'rishlar</th>
                        <th style="padding:12px 16px">Sana</th>
                        <th style="padding:12px 16px;width:120px">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                    <tr>
                        <td style="padding:10px 16px;color:var(--c-text-3);font-size:13px">{{ $page->id }}</td>
                        <td style="padding:10px 16px">
                            <div class="d-flex align-items-center gap-2">
                                @if($page->is_homepage)
                                <i class="fas fa-home" style="color:var(--c-violet);font-size:11px" title="Bosh sahifa"></i>
                                @endif
                                @if($page->show_in_menu)
                                <i class="fas fa-bars" style="color:var(--c-sky);font-size:11px" title="Menyuda"></i>
                                @endif
                                <span style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $page->title_uz }}</span>
                            </div>
                        </td>
                        <td style="padding:10px 16px">
                            <code style="font-size:11px;background:var(--c-bg);border:1px solid var(--c-border);padding:2px 7px;border-radius:5px;color:var(--c-text-2)">{{ $page->slug }}</code>
                        </td>
                        <td style="padding:10px 16px">
                            @php $s = $page->status; @endphp
                            <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;
                                background:{{ $s==='published' ? 'rgba(16,185,129,.12)' : ($s==='draft' ? 'rgba(245,158,11,.12)' : 'rgba(100,116,139,.12)') }};
                                color:{{ $s==='published' ? 'var(--c-emerald)' : ($s==='draft' ? 'var(--c-amber)' : 'var(--c-text-3)') }}">
                                {{ $s==='published' ? 'Chop etilgan' : ($s==='draft' ? 'Qoralama' : 'Arxivlangan') }}
                            </span>
                        </td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--c-text-2)">
                            {{ $page->parent ? $page->parent->title_uz : '—' }}
                        </td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--c-text-2)">{{ number_format($page->views_count) }}</td>
                        <td style="padding:10px 16px;font-size:12px;color:var(--c-text-3)">{{ $page->created_at->format('d.m.Y') }}</td>
                        <td style="padding:10px 16px">
                            <div class="d-flex gap-1">
                                <a href="{{ route('cms.pages.edit', $page) }}" class="action-btn" title="Tahrirlash">
                                    <i class="fas fa-pen"></i>
                                </a>
                                @if($page->status === 'published')
                                <a href="{{ route('cms.page.public', $page->slug) }}" class="action-btn" title="Saytda ko'rish" target="_blank">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                @else
                                <a href="{{ route('cms.pages.preview', $page) }}" class="action-btn" title="Ko'rish" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endif
                                <button type="button" class="action-btn" title="Linkni nusxalash"
                                        onclick="copyLink('{{ route('cms.page.public', $page->slug) }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                                @unless($page->is_homepage)
                                <form action="{{ route('cms.pages.destroy', $page) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Sahifani o\'chirmoqchimisiz?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn danger" title="O'chirish">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-file-alt fa-2x mb-2" style="color:var(--c-border)"></i>
                            <div style="font-size:14px;color:var(--c-text-3)">Sahifalar topilmadi</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($pages) && method_exists($pages, 'links'))
        <div class="px-3 py-2 border-top">{{ $pages->links() }}</div>
        @endif
    </div>
</div>

{{-- URL info --}}
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-info-circle" style="color:var(--c-sky)"></i>
        <span>Sahifalarga qanday kirish mumkin?</span>
    </div>
    <div class="card-body">
        <p style="font-size:13px;color:var(--c-text-2);margin-bottom:8px">CMS orqali yaratilgan sahifalarga quyidagi format orqali kirish mumkin:</p>
        <code style="font-size:12px;background:var(--c-bg);border:1px solid var(--c-border);padding:5px 12px;border-radius:7px;color:var(--c-text)">
            {{ url('/s') }}/sahifa-slug
        </code>
        <p style="font-size:12px;color:var(--c-text-3);margin-top:8px;margin-bottom:0">
            <strong>Misol:</strong> Agar slug <code>biz-haqimizda</code> bo'lsa →
            <a href="{{ url('/s/biz-haqimizda') }}" target="_blank" style="color:var(--c-violet)">{{ url('/s/biz-haqimizda') }}</a>
        </p>
    </div>
</div>

@endsection

@push('scripts')
<script>
function copyLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        alert('Link nusxalandi: ' + url);
    }).catch(function() {
        prompt('Linkni nusxalang:', url);
    });
}
</script>
@endpush
