@extends('layouts.dashboard-new')

@section('title', 'Kursni tahrirlash — LMS')
@section('page-title', 'Kursni tahrirlash')

@section('content')

<form action="{{ route('lms.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        {{-- Main --}}
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--c-teal)"></i>
                    <span>Asosiy ma'lumotlar</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Kurs nomi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $course->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Tavsif</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4">{{ old('description', $course->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="objectives" class="form-label">Kurs maqsadlari</label>
                        <textarea class="form-control @error('objectives') is-invalid @enderror"
                                  id="objectives" name="objectives" rows="3">{{ old('objectives', $course->objectives) }}</textarea>
                        @error('objectives')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="requirements" class="form-label">Talablar</label>
                        <textarea class="form-control @error('requirements') is-invalid @enderror"
                                  id="requirements" name="requirements" rows="3">{{ old('requirements', $course->requirements) }}</textarea>
                        @error('requirements')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="subject_id" class="form-label">Fan</label>
                            <select class="form-select @error('subject_id') is-invalid @enderror"
                                    id="subject_id" name="subject_id">
                                <option value="">Fanni tanlang</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $course->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label for="language" class="form-label">Til <span class="text-danger">*</span></label>
                            <select class="form-select @error('language') is-invalid @enderror"
                                    id="language" name="language" required>
                                <option value="uz" {{ old('language', $course->language) == 'uz' ? 'selected' : '' }}>O'zbek</option>
                                <option value="ru" {{ old('language', $course->language) == 'ru' ? 'selected' : '' }}>Rus</option>
                                <option value="en" {{ old('language', $course->language) == 'en' ? 'selected' : '' }}>Ingliz</option>
                            </select>
                            @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label for="level" class="form-label">Daraja <span class="text-danger">*</span></label>
                            <select class="form-select @error('level') is-invalid @enderror"
                                    id="level" name="level" required>
                                <option value="beginner"     {{ old('level', $course->level) == 'beginner'     ? 'selected' : '' }}>Boshlang'ich</option>
                                <option value="intermediate" {{ old('level', $course->level) == 'intermediate' ? 'selected' : '' }}>O'rta</option>
                                <option value="advanced"     {{ old('level', $course->level) == 'advanced'     ? 'selected' : '' }}>Yuqori</option>
                            </select>
                            @error('level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-alt" style="color:var(--c-sky)"></i>
                    <span>Kurs tafsilotlari</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="duration_weeks" class="form-label">Davomiyligi (hafta)</label>
                            <input type="number" class="form-control @error('duration_weeks') is-invalid @enderror"
                                   id="duration_weeks" name="duration_weeks" value="{{ old('duration_weeks', $course->duration_weeks) }}" min="1" max="52">
                            @error('duration_weeks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="hours_per_week" class="form-label">Haftada soat</label>
                            <input type="number" class="form-control @error('hours_per_week') is-invalid @enderror"
                                   id="hours_per_week" name="hours_per_week" value="{{ old('hours_per_week', $course->hours_per_week) }}" min="1" max="40">
                            @error('hours_per_week')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="credit_hours" class="form-label">Kredit soat</label>
                            <input type="number" class="form-control @error('credit_hours') is-invalid @enderror"
                                   id="credit_hours" name="credit_hours" value="{{ old('credit_hours', $course->credit_hours) }}" min="1" max="10">
                            @error('credit_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Boshlanish sanasi</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                   id="start_date" name="start_date" value="{{ old('start_date', $course->start_date?->format('Y-m-d')) }}">
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Tugash sanasi</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                   id="end_date" name="end_date" value="{{ old('end_date', $course->end_date?->format('Y-m-d')) }}">
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="enrollment_start" class="form-label">Ro'yxatdan o'tish boshlanishi</label>
                            <input type="date" class="form-control @error('enrollment_start') is-invalid @enderror"
                                   id="enrollment_start" name="enrollment_start" value="{{ old('enrollment_start', $course->enrollment_start?->format('Y-m-d')) }}">
                            @error('enrollment_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="enrollment_end" class="form-label">Ro'yxatdan o'tish tugashi</label>
                            <input type="date" class="form-control @error('enrollment_end') is-invalid @enderror"
                                   id="enrollment_end" name="enrollment_end" value="{{ old('enrollment_end', $course->enrollment_end?->format('Y-m-d')) }}">
                            @error('enrollment_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-cog" style="color:var(--c-violet)"></i>
                    <span>Sozlamalar</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label">Narxi (so'm)</label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price" value="{{ old('price', $course->price) }}" min="0" step="1000">
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="max_students" class="form-label">Maksimal talabalar</label>
                            <input type="number" class="form-control @error('max_students') is-invalid @enderror"
                                   id="max_students" name="max_students" value="{{ old('max_students', $course->max_students) }}" min="1">
                            @error('max_students')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="passing_score" class="form-label">O'tish bali (%)</label>
                            <input type="number" class="form-control @error('passing_score') is-invalid @enderror"
                                   id="passing_score" name="passing_score" value="{{ old('passing_score', $course->passing_score) }}" min="0" max="100">
                            @error('passing_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="d-flex gap-4 flex-wrap">
                        @foreach([['certificate_available','Sertifikat beriladi'],['auto_enrollment',"Avtomatik ro'yxatdan o'tish"],['is_published','Nashr qilingan'],['is_featured','Taniqli']] as [$name,$label])
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="{{ $name }}"
                                   name="{{ $name }}" value="1" {{ old($name, $course->$name) ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-image" style="color:var(--c-sky)"></i>
                    <span>Media</span>
                </div>
                <div class="card-body">
                    @if($course->thumbnail)
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="form-label mb-0">Joriy rasm</span>
                            <form action="{{ route('lms.courses.deleteThumbnail', $course) }}" method="POST"
                                  onsubmit="return confirm('Rasmni o\'chirmoqchimisiz?')" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="padding:2px 8px;font-size:11px">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" class="img-fluid rounded"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22200%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22400%22 height=%22200%22/%3E%3C/svg%3E'">
                    </div>
                    @endif
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">{{ $course->thumbnail ? 'Yangi rasm' : 'Kurs rasmi' }}</label>
                        <input type="file" class="form-control @error('thumbnail') is-invalid @enderror"
                               id="thumbnail" name="thumbnail" accept="image/*">
                        <div class="form-text">PNG, JPG (maks: 2MB)</div>
                        @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="thumbnail-preview" class="mt-2"></div>
                    </div>

                    @if($course->intro_video)
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="form-label mb-0">Joriy video</span>
                            <form action="{{ route('lms.courses.deleteIntroVideo', $course) }}" method="POST"
                                  onsubmit="return confirm('Videoni o\'chirmoqchimisiz?')" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="padding:2px 8px;font-size:11px">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        <video controls class="w-100 rounded" style="max-height:140px">
                            <source src="{{ asset('storage/' . $course->intro_video) }}" type="video/mp4">
                        </video>
                    </div>
                    @endif
                    <div>
                        <label for="intro_video" class="form-label">{{ $course->intro_video ? 'Yangi video' : 'Kirish videosi' }}</label>
                        <input type="file" class="form-control @error('intro_video') is-invalid @enderror"
                               id="intro_video" name="intro_video" accept="video/*">
                        <div class="form-text">MP4, AVI (maks: 100MB)</div>
                        @error('intro_video')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="video-info" class="mt-2"></div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-tags" style="color:var(--c-amber)"></i>
                    <span>Teglar</span>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control @error('tags') is-invalid @enderror"
                           id="tags" name="tags"
                           value="{{ old('tags', $course->tags ? implode(', ', $course->tags) : '') }}"
                           placeholder="dasturlash, web, frontend">
                    <div class="form-text">Vergul bilan ajrating</div>
                    @error('tags')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-book-open" style="color:var(--c-emerald)"></i>
                    <span>O'quv rejasi</span>
                </div>
                <div class="card-body">
                    <p class="mb-3" style="font-size:13px;color:var(--c-text-2)">
                        Kursga mavzular yarating va har bir mavzuga resurslar biriktiring.
                    </p>
                    <a href="{{ route('lms.courses.curriculum', $course) }}" class="btn btn-sm w-100"
                       style="background:var(--c-emerald);color:#fff">
                        <i class="fas fa-book-open me-1"></i>O'quv rejasini boshqarish
                    </a>
                    <div class="mt-3 pt-3" style="border-top:1px solid var(--c-border)">
                        <div style="font-size:12px;color:var(--c-text-2)">
                            <i class="fas fa-list me-1" style="color:var(--c-teal)"></i>
                            Mavzular: <strong>{{ $course->topics()->count() }}</strong>
                        </div>
                        <div style="font-size:12px;color:var(--c-text-2);margin-top:4px">
                            <i class="fas fa-paperclip me-1" style="color:var(--c-teal)"></i>
                            Resurslar: <strong>{{ $course->topics()->withCount('resources')->get()->sum('resources_count') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('lms.courses.show', $course) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Bekor qilish
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
const thumbInput = document.getElementById('thumbnail');
if (thumbInput) {
    thumbInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) { alert('Rasm hajmi 2MB dan kichik bo\'lishi kerak!'); this.value = ''; return; }
        if (!file.type.match('image.*')) { alert('Faqat rasm fayllarini yuklash mumkin!'); this.value = ''; return; }
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('thumbnail-preview').innerHTML =
                `<img src="${e.target.result}" class="img-fluid rounded" style="max-height:160px">`;
        };
        reader.readAsDataURL(file);
    });
}

const videoInput = document.getElementById('intro_video');
if (videoInput) {
    videoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        if (file.size > 100 * 1024 * 1024) { alert('Video hajmi 100MB dan kichik bo\'lishi kerak!'); this.value = ''; return; }
        if (!file.type.match('video.*')) { alert('Faqat video fayllarini yuklash mumkin!'); this.value = ''; return; }
        const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
        document.getElementById('video-info').innerHTML =
            `<div class="alert alert-info py-2 px-3 mb-0" style="font-size:12px">
                <i class="fas fa-video me-1"></i><strong>${file.name}</strong> — ${sizeInMB} MB
            </div>`;
    });
}
</script>
@endpush
