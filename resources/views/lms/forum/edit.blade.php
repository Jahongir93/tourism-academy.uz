@extends('layouts.dashboard-new')

@section('title', 'Mavzuni tahrirlash — Forum — LMS')
@section('page-title', 'Mavzuni tahrirlash')

@section('content')

<x-lms-alerts />

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-edit" style="color:var(--c-teal)"></i>
                <span>Mavzu ma'lumotlarini tahrirlash</span>
            </div>
            <div class="card-body">
                <form action="{{ route('lms.forum.update', $post) }}" method="POST" id="forumEditForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label">Mavzu sarlavhasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $post->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Fan (ixtiyoriy)</label>
                        <select class="form-select @error('subject_id') is-invalid @enderror"
                                id="subject_id" name="subject_id">
                            <option value="">Fanni tanlang</option>
                            @foreach($subjects ?? [] as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $post->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name_uz }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Agar savol ma'lum bir fanga tegishli bo'lsa, tanlang</div>
                        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label">Savol yoki muammo tavsifi <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror"
                                  id="content" name="content" rows="10" required>{{ old('content', $post->content) }}</textarea>
                        <div class="form-text">Qanchalik batafsil yozsangiz, shunchalik yaxshi javob olasiz</div>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Saqlash
                        </button>
                        <a href="{{ route('lms.forum.show', $post) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Bekor qilish
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-info-circle" style="color:var(--c-sky)"></i>
                <span>Ma'lumot</span>
            </div>
            <div class="card-body p-3">
                @php
                    $infoRows = [
                        ['fas fa-user','var(--c-teal)',"Muallif:",$post->user->name],
                        ['fas fa-calendar','var(--c-emerald)',"Yaratilgan:",$post->created_at->format('d.m.Y H:i')],
                        ['fas fa-eye','var(--c-sky)',"Ko'rildi:",($post->view_count ?? 0) . ' marta'],
                        ['fas fa-comment','var(--c-violet)',"Javoblar:",($post->reply_count ?? 0) . ' ta'],
                    ];
                @endphp
                @foreach($infoRows as [$icon,$color,$label,$value])
                <div class="d-flex align-items-start gap-2 mb-2">
                    <i class="{{ $icon }}" style="color:{{ $color }};margin-top:2px;flex-shrink:0"></i>
                    <div>
                        <div style="font-size:11px;color:var(--c-text-3)">{{ $label }}</div>
                        <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $value }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card" style="border-color:var(--c-rose)">
            <div class="card-header d-flex align-items-center gap-2" style="border-color:var(--c-rose)">
                <i class="fas fa-exclamation-triangle" style="color:var(--c-rose)"></i>
                <span style="color:var(--c-rose)">Xavfli hudud</span>
            </div>
            <div class="card-body">
                <p style="font-size:13px;color:var(--c-text-2);margin-bottom:12px">
                    Mavzuni o'chirsangiz, barcha javoblar ham o'chiriladi. Bu amalni bekor qilib bo'lmaydi.
                </p>
                <form action="{{ route('lms.forum.destroy', $post) }}" method="POST"
                      onsubmit="return confirm('Rostan ham bu mavzuni o\'chirmoqchimisiz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100" style="font-size:13px">
                        <i class="fas fa-trash me-2"></i>Mavzuni o'chirish
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
