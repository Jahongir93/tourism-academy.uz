@extends('layouts.dashboard-new')

@section('title', $material->title . ' — LMS')
@section('page-title', $material->title)

@section('content')

<x-lms-alerts />

@php
    $typeIcons = [
        'presentation' => ['fa-file-powerpoint','var(--c-amber)','rgba(245,158,11,.1)'],
        'document'     => ['fa-file-word','var(--c-sky)','rgba(14,165,233,.1)'],
        'spreadsheet'  => ['fa-file-excel','var(--c-emerald)','rgba(16,185,129,.1)'],
        'pdf'          => ['fa-file-pdf','var(--c-rose)','rgba(244,63,94,.1)'],
        'other'        => ['fa-file','var(--c-text-3)','rgba(0,0,0,.05)'],
    ];
    [$typeIcon, $typeColor, $typeBg] = $typeIcons[$material->material_type] ?? $typeIcons['other'];
@endphp

{{-- Action buttons --}}
<div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
    <a href="{{ route('lms.materials.download', $material) }}" class="btn btn-sm"
       style="background:var(--c-teal);color:#fff">
        <i class="fas fa-download me-1"></i>Faylni yuklash
    </a>
    @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
        @if(Auth::user()->id == $material->teacher_id || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
        <a href="{{ route('lms.materials.edit', $material) }}" class="btn btn-sm"
           style="background:var(--c-amber);color:#fff">
            <i class="fas fa-edit me-1"></i>Tahrirlash
        </a>
        <form action="{{ route('lms.materials.destroy', $material) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Materialni o\'chirishga ishonchingiz komilmi?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">
                <i class="fas fa-trash me-1"></i>O'chirish
            </button>
        </form>
        @endif
    @endif
    <a href="{{ route('lms.materials.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
        <i class="fas fa-arrow-left me-1"></i>Ortga
    </a>
</div>

<div class="row g-4">
    {{-- Main --}}
    <div class="col-lg-8">

        @if($material->description)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-align-left" style="color:var(--c-teal)"></i>
                <span>Tavsif</span>
            </div>
            <div class="card-body">
                <p style="font-size:14px;color:var(--c-text-2);line-height:1.7;margin:0">{{ $material->description }}</p>
            </div>
        </div>
        @endif

        {{-- File info --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-info-circle" style="color:var(--c-sky)"></i>
                <span>Fayl ma'lumotlari</span>
            </div>
            <div class="card-body p-0">
                @php
                    $infoRows = [
                        ['fas fa-file-alt','var(--c-sky)','rgba(14,165,233,.1)','Fayl nomi', Str::limit($material->file_name, 40)],
                        ['fas fa-database','var(--c-emerald)','rgba(16,185,129,.1)','Hajmi', $material->file_size_formatted],
                        ['fas fa-download','var(--c-violet)','rgba(124,58,237,.1)','Yuklab olishlar', $material->download_count],
                        ['fas fa-calendar','var(--c-amber)','rgba(245,158,11,.1)','Yuklangan', $material->created_at?->format('d.m.Y')],
                    ];
                @endphp
                @foreach($infoRows as [$icon,$color,$bg,$label,$value])
                <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid var(--c-border)">
                    <div style="width:36px;height:36px;background:{{ $bg }};border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas {{ $icon }}" style="color:{{ $color }};font-size:14px"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--c-text-3)">{{ $label }}</div>
                        <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $value }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Meta --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-tag" style="color:var(--c-violet)"></i>
                <span>Qo'shimcha ma'lumotlar</span>
            </div>
            <div class="card-body p-0">
                @php
                    $metaRows = [
                        ['Fan', $material->subject?->name_uz ?? '—'],
                        ["O'qituvchi", $material->teacher?->name ?? '—'],
                        ['Tur', ucfirst($material->material_type)],
                    ];
                    if ($material->week_number) $metaRows[] = ['Hafta', $material->week_number . '-hafta'];
                @endphp
                @foreach($metaRows as [$label,$value])
                <div class="d-flex align-items-center px-4 py-2" style="border-bottom:1px solid var(--c-border)">
                    <span style="width:140px;font-size:12px;color:var(--c-text-3);flex-shrink:0">{{ $label }}</span>
                    <span style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- PDF preview --}}
        @if($material->material_type === 'pdf')
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-eye" style="color:var(--c-rose)"></i>
                <span>Oldindan ko'rish</span>
            </div>
            <div class="card-body p-0">
                <iframe src="{{ asset('storage/' . $material->file_path) }}"
                        style="width:100%;height:480px;border:none;display:block"></iframe>
            </div>
            <div class="card-body py-2 text-center" style="font-size:12px;color:var(--c-text-3)">
                <i class="fas fa-info-circle me-1"></i>To'liq ko'rish uchun faylni yuklang
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Stat card --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-chart-line" style="color:var(--c-violet)"></i>
                <span>Statistika</span>
            </div>
            <div class="card-body p-0">
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid var(--c-border)">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-eye" style="color:var(--c-sky);font-size:14px"></i>
                        <span style="font-size:13px;color:var(--c-text-2)">Ko'rildi</span>
                    </div>
                    <span style="font-size:16px;font-weight:700;color:var(--c-text)">{{ $material->view_count ?? 0 }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid var(--c-border)">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-download" style="color:var(--c-emerald);font-size:14px"></i>
                        <span style="font-size:13px;color:var(--c-text-2)">Yuklandi</span>
                    </div>
                    <span style="font-size:16px;font-weight:700;color:var(--c-text)">{{ $material->download_count }}</span>
                </div>
                <div class="px-4 py-3" style="border-bottom:1px solid var(--c-border)">
                    <div style="font-size:11px;color:var(--c-text-3);margin-bottom:2px">Qo'shilgan</div>
                    <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $material->created_at?->diffForHumans() }}</div>
                    <div style="font-size:11px;color:var(--c-text-3)">{{ $material->created_at?->format('d.m.Y, H:i') }}</div>
                </div>
                @if($material->updated_at && $material->updated_at->ne($material->created_at))
                <div class="px-4 py-3">
                    <div style="font-size:11px;color:var(--c-text-3);margin-bottom:2px">Yangilangan</div>
                    <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $material->updated_at?->diffForHumans() }}</div>
                    <div style="font-size:11px;color:var(--c-text-3)">{{ $material->updated_at?->format('d.m.Y, H:i') }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Related materials --}}
        @if($relatedMaterials->count() > 0)
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-layer-group" style="color:var(--c-teal)"></i>
                <span>Shunga o'xshash</span>
            </div>
            <div class="card-body p-0">
                @foreach($relatedMaterials as $related)
                @php
                    [$rIcon,$rColor,$rBg] = $typeIcons[$related->material_type] ?? $typeIcons['other'];
                @endphp
                <a href="{{ route('lms.materials.show', $related) }}"
                   class="d-flex align-items-start gap-3 px-4 py-3 text-decoration-none"
                   style="border-bottom:1px solid var(--c-border);transition:background .15s"
                   onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background=''">
                    <div style="width:34px;height:34px;background:{{ $rBg }};border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px">
                        <i class="fas {{ $rIcon }}" style="color:{{ $rColor }};font-size:13px"></i>
                    </div>
                    <div style="min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $related->title }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">
                            <i class="fas fa-user me-1"></i>{{ $related->teacher?->name ?? "O'qituvchi" }}
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
