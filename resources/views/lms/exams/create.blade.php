@extends('layouts.dashboard-new')

@section('title', 'Yangi imtihon — LMS')
@section('page-title', 'Yangi imtihon yaratish')

@section('content')

<form method="POST" action="{{ route('lms.exams.store') }}">
    @csrf

    <div class="row g-4">
        {{-- Main --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--c-teal)"></i>
                    <span>Asosiy ma'lumotlar</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Imtihon nomi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               name="title" value="{{ old('title') }}" required
                               placeholder="Masalan: Matematika — Oraliq nazorat">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Fan <span class="text-danger">*</span></label>
                            <select class="form-select @error('subject_id') is-invalid @enderror"
                                    name="subject_id" required>
                                <option value="">Fanni tanlang</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Imtihon turi <span class="text-danger">*</span></label>
                            <select class="form-select @error('exam_type') is-invalid @enderror"
                                    name="exam_type" required>
                                <option value="joriy"    {{ old('exam_type') == 'joriy'    ? 'selected' : '' }}>Joriy nazorat</option>
                                <option value="oraliq"   {{ old('exam_type') == 'oraliq'   ? 'selected' : '' }}>Oraliq nazorat</option>
                                <option value="yakuniy"  {{ old('exam_type') == 'yakuniy'  ? 'selected' : '' }}>Yakuniy nazorat</option>
                                <option value="practice" {{ old('exam_type', 'practice') == 'practice' ? 'selected' : '' }}>Mashq test</option>
                            </select>
                            @error('exam_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Guruhlar</label>
                        <select name="group_ids[]" multiple class="form-select" style="min-height:110px">
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ in_array($group->id, old('group_ids', [])) ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Ctrl+Click orqali bir nechta guruhni tanlang. Bo'sh qoldirilsa barcha guruhlarga ochiq.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tavsif</label>
                        <textarea name="description" rows="3" class="form-control"
                                  placeholder="Imtihon haqida qisqacha ma'lumot...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Ko'rsatmalar</label>
                        <textarea name="instructions" rows="3" class="form-control"
                                  placeholder="Talabalar uchun ko'rsatmalar...">{{ old('instructions') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-clock" style="color:var(--c-emerald)"></i>
                    <span>Vaqt sozlamalari</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Davomiyligi (daqiqa) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror"
                                   name="duration_minutes" value="{{ old('duration_minutes', 60) }}" required min="5" max="300">
                            @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Boshlanish vaqti</label>
                            <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror"
                                   name="start_time" value="{{ old('start_time') }}">
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tugash vaqti</label>
                            <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror"
                                   name="end_time" value="{{ old('end_time') }}">
                            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-percentage" style="color:var(--c-amber)"></i>
                    <span>Baholash sozlamalari</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Maksimal ball <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('max_score') is-invalid @enderror"
                                   name="max_score" value="{{ old('max_score', 100) }}" required min="1" max="100" step="0.5">
                            @error('max_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">O'tish bali <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('passing_score') is-invalid @enderror"
                                   name="passing_score" value="{{ old('passing_score', 60) }}" required min="0" max="100" step="0.5">
                            @error('passing_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jurnal foizi (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('weight_percentage') is-invalid @enderror"
                                   name="weight_percentage" value="{{ old('weight_percentage', 100) }}" required min="0" max="100" step="0.5">
                            <div class="form-text">Jurnalga qanday foizda o'tadi</div>
                            @error('weight_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Maksimal urinishlar <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('max_attempts') is-invalid @enderror"
                                   name="max_attempts" value="{{ old('max_attempts', 1) }}" required min="1" max="10">
                            @error('max_attempts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kirish paroli</label>
                            <input type="text" class="form-control @error('access_password') is-invalid @enderror"
                                   name="access_password" value="{{ old('access_password') }}"
                                   placeholder="Bo'sh qoldirilsa parolsiz">
                            @error('access_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-sliders-h" style="color:var(--c-violet)"></i>
                    <span>Qo'shimcha sozlamalar</span>
                </div>
                <div class="card-body">
                    @php
                        $checks = [
                            ['shuffle_questions','Savollarni aralashtirish',true],
                            ['shuffle_answers','Javoblarni aralashtirish',true],
                            ['show_correct_answers',"To'g'ri javobni ko'rsatish",false],
                            ['show_score_immediately','Natijani darhol ko\'rsatish',true],
                            ['allow_retake',"Qayta topshirishga ruxsat",false],
                        ];
                    @endphp
                    @foreach($checks as [$name,$label,$default])
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="{{ $name }}"
                               name="{{ $name }}" value="1"
                               {{ old($name, $default) ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $name }}" style="font-size:13px">{{ $label }}</label>
                    </div>
                    @endforeach
                    <hr style="border-color:var(--c-border)">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="sync_to_journal"
                               name="sync_to_journal" value="1"
                               {{ old('sync_to_journal', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sync_to_journal" style="font-size:13px">Jurnalga avtomatik o'tkazish</label>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info" style="color:var(--c-sky)"></i>
                    <span>Keyingi qadam</span>
                </div>
                <div class="card-body">
                    <p style="font-size:13px;color:var(--c-text-2);margin-bottom:16px">
                        Imtihon yaratilgandan so'ng savollar qo'shishingiz mumkin bo'ladi.
                    </p>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Imtihonni yaratish
                        </button>
                        <a href="{{ route('lms.exams.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Ortga qaytish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection
