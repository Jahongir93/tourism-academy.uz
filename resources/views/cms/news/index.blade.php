@extends('layouts.dashboard-new')

@section('title', 'CMS — Yangiliklar')
@section('page-title', 'Yangiliklar boshqaruvi')

@section('styles')
<style>
.action-btn { width:30px;height:30px;border-radius:7px;border:1px solid var(--c-border);background:var(--c-bg);display:inline-flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;transition:all .12s;text-decoration:none;color:var(--c-text-2); }
.action-btn:hover { border-color:var(--c-teal);color:var(--c-teal);background:rgba(20,184,166,.07); }
.action-btn.danger:hover { border-color:var(--c-rose);color:var(--c-rose);background:rgba(244,63,94,.07); }
</style>
@endsection

@section('content')

<x-lms-alerts />

<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-newspaper" style="color:var(--c-rose)"></i>
            <span>Yangiliklar ro'yxati</span>
        </div>
        <a href="{{ route('cms.news.create') }}" class="btn btn-sm" style="background:var(--c-violet);color:#fff">
            <i class="fas fa-plus me-1"></i>Yangi yangilik
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr style="background:var(--c-bg);font-size:12px;color:var(--c-text-3)">
                        <th style="width:50px;padding:12px 16px">#</th>
                        <th style="padding:12px 16px">Sarlavha</th>
                        <th style="padding:12px 16px">Kategoriya</th>
                        <th style="padding:12px 16px">Muallif</th>
                        <th style="padding:12px 16px">Holat</th>
                        <th style="padding:12px 16px">Ko'rishlar</th>
                        <th style="padding:12px 16px">Sana</th>
                        <th style="padding:12px 16px;width:90px">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news ?? [] as $item)
                    <tr>
                        <td style="padding:10px 16px;color:var(--c-text-3);font-size:13px">{{ $item->id }}</td>
                        <td style="padding:10px 16px">
                            <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ Str::limit($item->title_uz, 55) }}</div>
                        </td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--c-text-2)">{{ $item->category->name_uz ?? '—' }}</td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--c-text-2)">{{ $item->author->name ?? '—' }}</td>
                        <td style="padding:10px 16px">
                            @php $s = $item->status; @endphp
                            <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;
                                background:{{ $s==='published' ? 'rgba(16,185,129,.12)' : ($s==='draft' ? 'rgba(245,158,11,.12)' : 'rgba(100,116,139,.12)') }};
                                color:{{ $s==='published' ? 'var(--c-emerald)' : ($s==='draft' ? 'var(--c-amber)' : 'var(--c-text-3)') }}">
                                {{ $s==='published' ? 'Chop etilgan' : ($s==='draft' ? 'Qoralama' : 'Arxivlangan') }}
                            </span>
                        </td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--c-text-2)">{{ number_format($item->views_count) }}</td>
                        <td style="padding:10px 16px;font-size:12px;color:var(--c-text-3)">{{ $item->created_at->format('d.m.Y') }}</td>
                        <td style="padding:10px 16px">
                            <div class="d-flex gap-1">
                                <a href="{{ route('cms.news.edit', $item) }}" class="action-btn" title="Tahrirlash">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('cms.news.destroy', $item) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yangilikni o\'chirishni tasdiqlaysizmi?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn danger" title="O'chirish">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-newspaper fa-2x mb-2" style="color:var(--c-border)"></i>
                            <div style="font-size:14px;color:var(--c-text-3)">Yangiliklar topilmadi</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
