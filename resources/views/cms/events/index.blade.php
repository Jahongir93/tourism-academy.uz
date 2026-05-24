@extends('layouts.dashboard-new')

@section('title', 'CMS — Tadbirlar')
@section('page-title', 'Tadbirlar boshqaruvi')

@section('styles')
<style>
.action-btn { width:30px;height:30px;border-radius:7px;border:1px solid var(--c-border);background:var(--c-bg);display:inline-flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;transition:all .12s;text-decoration:none;color:var(--c-text-2); }
.action-btn:hover { border-color:var(--c-teal);color:var(--c-teal);background:rgba(20,184,166,.07); }
.action-btn.danger:hover { border-color:var(--c-rose);color:var(--c-rose);background:rgba(244,63,94,.07); }
</style>
@endsection

@section('content')

<x-lms-alerts />

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-calendar-alt" style="color:var(--c-sky)"></i>
            <span>Tadbirlar ro'yxati</span>
        </div>
        <a href="{{ route('cms.events.create') }}" class="btn btn-sm" style="background:var(--c-sky);color:#fff">
            <i class="fas fa-plus me-1"></i>Yangi tadbir
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr style="background:var(--c-bg);font-size:12px;color:var(--c-text-3)">
                        <th style="padding:12px 16px;width:50px">#</th>
                        <th style="padding:12px 16px">Nomi</th>
                        <th style="padding:12px 16px">Turi</th>
                        <th style="padding:12px 16px">Boshlanish</th>
                        <th style="padding:12px 16px">Joyi</th>
                        <th style="padding:12px 16px">Holat</th>
                        <th style="padding:12px 16px">Ishtirokchilar</th>
                        <th style="padding:12px 16px;width:90px">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events ?? [] as $event)
                    <tr>
                        <td style="padding:10px 16px;color:var(--c-text-3);font-size:13px">{{ $event->id }}</td>
                        <td style="padding:10px 16px">
                            <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ Str::limit($event->title_uz, 50) }}</div>
                        </td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--c-text-2)">{{ $event->type }}</td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--c-text-2)">{{ $event->start_date->format('d.m.Y H:i') }}</td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--c-text-2)">{{ $event->location ?? 'Online' }}</td>
                        <td style="padding:10px 16px">
                            @php
                            $s = $event->status;
                            $bg = $s==='upcoming' ? 'rgba(14,165,233,.12)' : ($s==='ongoing' ? 'rgba(16,185,129,.12)' : 'rgba(100,116,139,.12)');
                            $clr = $s==='upcoming' ? 'var(--c-sky)' : ($s==='ongoing' ? 'var(--c-emerald)' : 'var(--c-text-3)');
                            $lbl = $s==='upcoming' ? 'Yaqinlashayotgan' : ($s==='ongoing' ? 'Davom etmoqda' : 'Tugallangan');
                            @endphp
                            <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;background:{{ $bg }};color:{{ $clr }}">
                                {{ $lbl }}
                            </span>
                        </td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--c-text-2)">
                            @if($event->requires_registration)
                                {{ $event->registered_count }} / {{ $event->max_participants ?? '∞' }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:10px 16px">
                            <div class="d-flex gap-1">
                                <a href="{{ route('cms.events.edit', $event) }}" class="action-btn" title="Tahrirlash">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('cms.events.destroy', $event) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tadbirni o\'chirishni tasdiqlaysizmi?')">
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
                            <i class="fas fa-calendar-alt fa-2x mb-2" style="color:var(--c-border)"></i>
                            <div style="font-size:14px;color:var(--c-text-3)">Tadbirlar topilmadi</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
