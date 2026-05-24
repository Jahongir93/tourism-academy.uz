@extends('layouts.dashboard-new')

@section('title', 'Yangi tadbir — CMS')
@section('page-title', 'Yangi tadbir yaratish')

@section('styles')
<style>
.file-drop-area { border:2px dashed var(--c-border);border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:all .2s;background:var(--c-bg);position:relative; }
.file-drop-area:hover { border-color:var(--c-sky);background:rgba(14,165,233,.03); }
.file-drop-area input[type="file"] { position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer; }
</style>
@endsection

@section('content')

<x-lms-alerts />

<form action="{{ route('cms.events.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        {{-- Main --}}
        <div class="col-lg-8">

            {{-- Basic info --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--c-sky)"></i>
                    <span>Asosiy ma'lumotlar</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px">Sarlavha (O'zbek) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title_uz') is-invalid @enderror"
                               name="title_uz" value="{{ old('title_uz') }}" required>
                        @error('title_uz')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:13px">Sarlavha (Rus)</label>
                            <input type="text" class="form-control" name="title_ru" value="{{ old('title_ru') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:13px">Sarlavha (Ingliz)</label>
                            <input type="text" class="form-control" name="title_en" value="{{ old('title_en') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px">Qisqa tavsif (O'zbek) <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description_uz') is-invalid @enderror"
                                  name="description_uz" rows="3" required>{{ old('description_uz') }}</textarea>
                        @error('description_uz')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:13px">Qisqa tavsif (Rus)</label>
                            <textarea class="form-control" name="description_ru" rows="3">{{ old('description_ru') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:13px">Qisqa tavsif (Ingliz)</label>
                            <textarea class="form-control" name="description_en" rows="3">{{ old('description_en') }}</textarea>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:13px">To'liq ma'lumot (O'zbek)</label>
                        <textarea class="form-control" name="content_uz" rows="6">{{ old('content_uz') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Location --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-map-marker-alt" style="color:var(--c-rose)"></i>
                    <span>Joylashuv va sana</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:13px">Boshlanish sanasi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                   name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:13px">Tugash sanasi</label>
                            <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_online" name="is_online" value="1">
                            <label class="form-check-label" for="is_online" style="font-size:13px">Online tadbir</label>
                        </div>
                    </div>
                    <div id="offline-fields">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:13px">Joylashuv</label>
                                <input type="text" class="form-control" name="location" value="{{ old('location') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:13px">Maydon / Bino</label>
                                <input type="text" class="form-control" name="venue" value="{{ old('venue') }}">
                            </div>
                        </div>
                    </div>
                    <div id="online-field" style="display:none">
                        <label class="form-label" style="font-size:13px">Online havola (Zoom, Google Meet ...)</label>
                        <input type="url" class="form-control" name="online_link" value="{{ old('online_link') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Actions --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-tasks" style="color:var(--c-teal)"></i>
                    <span>Amallar</span>
                </div>
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn" style="background:var(--c-sky);color:#fff">
                        <i class="fas fa-save me-1"></i>Saqlash
                    </button>
                    <a href="{{ route('cms.events.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>

            {{-- Settings --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-sliders-h" style="color:var(--c-sky)"></i>
                    <span>Tadbir sozlamalari</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px">Turi <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                            <option value="">— Tanlang —</option>
                            <option value="conference" {{ old('type')==='conference' ? 'selected':'' }}>Konferensiya</option>
                            <option value="seminar"    {{ old('type')==='seminar'    ? 'selected':'' }}>Seminar</option>
                            <option value="workshop"   {{ old('type')==='workshop'   ? 'selected':'' }}>Trening</option>
                            <option value="meeting"    {{ old('type')==='meeting'    ? 'selected':'' }}>Yig'ilish</option>
                            <option value="ceremony"   {{ old('type')==='ceremony'   ? 'selected':'' }}>Marosim</option>
                            <option value="other"      {{ old('type')==='other'      ? 'selected':'' }}>Boshqa</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px">Holat <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                            <option value="upcoming"  {{ old('status')==='upcoming'  ? 'selected':'' }}>Yaqinlashayotgan</option>
                            <option value="ongoing"   {{ old('status')==='ongoing'   ? 'selected':'' }}>Davom etmoqda</option>
                            <option value="completed" {{ old('status')==='completed' ? 'selected':'' }}>Tugallangan</option>
                            <option value="cancelled" {{ old('status')==='cancelled' ? 'selected':'' }}>Bekor qilingan</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1">
                        <label class="form-check-label" for="is_featured" style="font-size:13px">Tanlangan tadbir</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="requires_registration" name="requires_registration" value="1">
                        <label class="form-check-label" for="requires_registration" style="font-size:13px">Ro'yxatdan o'tish talab qilinadi</label>
                    </div>
                    <div id="max-participants-field" style="display:none">
                        <label class="form-label" style="font-size:13px">Maksimal ishtirokchilar</label>
                        <input type="number" class="form-control" name="max_participants"
                               value="{{ old('max_participants') }}" min="1">
                    </div>
                </div>
            </div>

            {{-- Image --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-image" style="color:var(--c-amber)"></i>
                    <span>Tadbir rasmi</span>
                </div>
                <div class="card-body">
                    <div class="file-drop-area">
                        <input type="file" name="featured_image" accept="image/*" id="event_image_input">
                        <i class="fas fa-image mb-2" style="font-size:28px;color:var(--c-amber)"></i>
                        <div style="font-size:13px;font-weight:600;color:var(--c-text);margin-bottom:2px">Rasm tanlang</div>
                        <div style="font-size:11px;color:var(--c-text-3)">Tavsiya: 1200×630 px</div>
                    </div>
                    <div id="event_preview" class="mt-2" style="display:none">
                        <img src="" alt="Preview" class="img-fluid rounded" id="event_preview_img">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isOnline = document.getElementById('is_online');
    const offlineFields = document.getElementById('offline-fields');
    const onlineField = document.getElementById('online-field');
    const requiresReg = document.getElementById('requires_registration');
    const maxParticipants = document.getElementById('max-participants-field');

    isOnline.addEventListener('change', function() {
        offlineFields.style.display = this.checked ? 'none' : '';
        onlineField.style.display   = this.checked ? ''     : 'none';
    });

    requiresReg.addEventListener('change', function() {
        maxParticipants.style.display = this.checked ? '' : 'none';
    });

    document.getElementById('event_image_input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('event_preview_img').src = e.target.result;
                document.getElementById('event_preview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
