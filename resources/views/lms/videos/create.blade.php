@extends('layouts.dashboard-new')

@section('title', 'Yangi video qo\'shish — LMS')
@section('page-title', 'Yangi video qo\'shish')

@section('styles')
<style>
.file-drop-area {
    border:2px dashed var(--c-border);border-radius:10px;padding:28px 20px;
    text-align:center;cursor:pointer;transition:all .2s;background:var(--c-bg);position:relative;
}
.file-drop-area:hover { border-color:var(--c-teal);background:rgba(20,184,166,.04); }
.file-drop-area input[type="file"] { position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer; }
.type-btn { border:2px solid var(--c-border);border-radius:10px;padding:14px;text-align:center;cursor:pointer;transition:all .15s;background:var(--c-bg); }
.type-btn.active { border-color:var(--c-teal);background:rgba(20,184,166,.06); }
</style>
@endsection

@section('content')

<x-lms-alerts />

<form action="{{ route('lms.videos.store') }}" method="POST" enctype="multipart/form-data" id="videoForm">
    @csrf

    <div class="row g-4">
        {{-- Main --}}
        <div class="col-lg-8">

            {{-- Basic info --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--c-teal)"></i>
                    <span>Video ma'lumotlari</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Video nomi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}"
                               placeholder="Masalan: 1-dars — Kirish" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Tavsif</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="Video haqida qisqacha ma'lumot...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="subject_id" class="form-label">Fan <span class="text-danger">*</span></label>
                            <select class="form-select @error('subject_id') is-invalid @enderror"
                                    id="subject_id" name="subject_id" required>
                                <option value="">Fanni tanlang</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name_uz }}
                                </option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label for="week_number" class="form-label">Hafta</label>
                            <input type="number" class="form-control @error('week_number') is-invalid @enderror"
                                   id="week_number" name="week_number" value="{{ old('week_number') }}"
                                   min="1" max="52" placeholder="1">
                            @error('week_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label for="order_number" class="form-label">Tartib</label>
                            <input type="number" class="form-control @error('order_number') is-invalid @enderror"
                                   id="order_number" name="order_number" value="{{ old('order_number', 0) }}"
                                   min="0" placeholder="0">
                            @error('order_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Video type --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-video" style="color:var(--c-rose)"></i>
                    <span>Video manba <span class="text-danger">*</span></span>
                </div>
                <div class="card-body">
                    <input type="hidden" name="video_type" id="video_type" value="{{ old('video_type', 'youtube') }}">
                    <div class="row g-3 mb-4">
                        @foreach([
                            ['youtube','fa-youtube','var(--c-rose)','rgba(244,63,94,.1)','YouTube','YouTube havolasi'],
                            ['upload','fa-upload','var(--c-sky)','rgba(14,165,233,.1)','Fayl yuklash','MP4, AVI, MOV'],
                            ['external','fa-link','var(--c-violet)','rgba(124,58,237,.1)','Tashqi havola','Boshqa URL'],
                        ] as [$type,$icon,$color,$bg,$label,$hint])
                        <div class="col-4">
                            <div class="type-btn {{ old('video_type','youtube') === $type ? 'active' : '' }}"
                                 onclick="selectType('{{ $type }}')">
                                <i class="fab {{ $icon }}" style="font-size:24px;color:{{ $color }};display:block;margin-bottom:6px"></i>
                                <div style="font-size:13px;font-weight:700;color:var(--c-text)">{{ $label }}</div>
                                <div style="font-size:11px;color:var(--c-text-3)">{{ $hint }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- YouTube / External URL --}}
                    <div id="field-url" style="{{ old('video_type','youtube') === 'upload' ? 'display:none' : '' }}">
                        <label for="video_url" class="form-label" id="url-label">YouTube havolasi <span class="text-danger">*</span></label>
                        <input type="url" class="form-control @error('video_url') is-invalid @enderror"
                               id="video_url" name="video_url" value="{{ old('video_url') }}"
                               placeholder="https://www.youtube.com/watch?v=...">
                        @error('video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- File upload --}}
                    <div id="field-file" style="{{ old('video_type','youtube') !== 'upload' ? 'display:none' : '' }}">
                        <label class="form-label">Video fayli <span class="text-danger">*</span></label>
                        <div class="file-drop-area @error('video_file') border-danger @enderror">
                            <input type="file" name="video_file" accept=".mp4,.avi,.mov,.mkv,.webm"
                                   onchange="updateFileName(this,'video-name')">
                            <i class="fas fa-cloud-upload-alt mb-2" style="font-size:32px;color:var(--c-teal)"></i>
                            <div style="font-weight:600;color:var(--c-text);margin-bottom:4px">Video faylni yuklash</div>
                            <div style="font-size:11px;color:var(--c-text-3)">MP4, AVI, MOV, MKV, WEBM &nbsp;|&nbsp; Maks: <strong>500MB</strong></div>
                        </div>
                        <div id="video-name" class="mt-2 d-none" style="font-size:13px;color:var(--c-text-2)">
                            <i class="fas fa-video me-1" style="color:var(--c-teal)"></i>
                            <span></span>
                        </div>
                        @error('video_file')<div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Thumbnail --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-image" style="color:var(--c-amber)"></i>
                    <span>Muqova rasm (ixtiyoriy)</span>
                </div>
                <div class="card-body">
                    <div class="file-drop-area">
                        <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="updateFileName(this,'thumb-name')">
                        <i class="fas fa-image mb-2" style="font-size:28px;color:var(--c-amber)"></i>
                        <div style="font-weight:600;color:var(--c-text);margin-bottom:4px">Rasm yuklash</div>
                        <div style="font-size:11px;color:var(--c-text-3)">JPG, PNG, WEBP &nbsp;|&nbsp; Maks: 5MB</div>
                    </div>
                    <div id="thumb-name" class="mt-2 d-none" style="font-size:13px;color:var(--c-text-2)">
                        <i class="fas fa-image me-1" style="color:var(--c-amber)"></i><span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-tasks" style="color:var(--c-teal)"></i>
                    <span>Amallar</span>
                </div>
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn" style="background:var(--c-teal);color:#fff">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('lms.videos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Orqaga
                    </a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-sliders-h" style="color:var(--c-sky)"></i>
                    <span>Sozlamalar</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="duration" class="form-label" style="font-size:13px">Davomiyligi (soniya)</label>
                        <input type="number" class="form-control" id="duration" name="duration"
                               value="{{ old('duration') }}" min="0" placeholder="Masalan: 1800">
                        <div class="form-text">30 daqiqa = 1800 soniya</div>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active" style="font-size:13px">Faol holat</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="allow_download" name="allow_download" value="1"
                               {{ old('allow_download') ? 'checked' : '' }}>
                        <label class="form-check-label" for="allow_download" style="font-size:13px">Yuklab olishga ruxsat</label>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-lightbulb" style="color:var(--c-amber)"></i>
                    <span>Ko'rsatmalar</span>
                </div>
                <div class="card-body p-0">
                    @foreach([
                        ['fa-youtube','var(--c-rose)','YouTube havolasini to\'liq kiriting'],
                        ['fa-file-video','var(--c-sky)','Fayl hajmi 500MB dan oshmasin'],
                        ['fa-clock','var(--c-violet)','Davomiylikni soniyada kiriting'],
                        ['fa-book','var(--c-teal)','To\'g\'ri fanni tanlang'],
                    ] as [$icon,$color,$text])
                    <div class="d-flex align-items-start gap-2 px-3 py-2" style="border-bottom:1px solid var(--c-border)">
                        <i class="fas {{ $icon }} mt-1" style="color:{{ $color }};font-size:11px;flex-shrink:0"></i>
                        <span style="font-size:12px;color:var(--c-text-2)">{{ $text }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
function selectType(type) {
    document.getElementById('video_type').value = type;
    document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
    event.currentTarget.classList.add('active');
    document.getElementById('field-url').style.display = type === 'upload' ? 'none' : '';
    document.getElementById('field-file').style.display = type === 'upload' ? '' : 'none';
    document.getElementById('url-label').firstChild.textContent = type === 'external' ? 'Tashqi havola ' : 'YouTube havolasi ';
}

function updateFileName(input, divId) {
    const div = document.getElementById(divId);
    if (input.files && input.files[0]) {
        const f = input.files[0];
        div.querySelector('span').textContent = f.name + ' (' + (f.size/1024/1024).toFixed(2) + ' MB)';
        div.classList.remove('d-none');
    } else {
        div.classList.add('d-none');
    }
}
</script>
@endpush
