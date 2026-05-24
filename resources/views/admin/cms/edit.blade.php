@extends('layouts.dashboard')

@section('title', 'CMS Tahrirlash - ' . ucfirst($section))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2"><i class="fas fa-edit"></i> {{ ucfirst(str_replace('_', ' ', $section)) }}</h1>
                    <p class="text-muted">Kontent maydοnlarini tahrirlash (Uzbek, English, Russian)</p>
                </div>
                <a href="{{ route('admin.cms.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Orqaga
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.cms.update', $section) }}" method="POST">
                @csrf

                <div id="content-fields">
                    @forelse($contents as $index => $content)
                        <div class="content-item mb-4 p-4 border rounded bg-light">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Kalit nomi:</label>
                                    <input type="text" name="contents[{{ $index }}][key]"
                                           value="{{ $content->key }}"
                                           class="form-control" required readonly>
                                    <input type="hidden" name="contents[{{ $index }}][type]" value="{{ $content->type }}">
                                    <input type="hidden" name="contents[{{ $index }}][order]" value="{{ $content->order }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <img src="{{ asset('assets/flags/uz.png') }}" width="20" class="me-1" alt="UZ">
                                        O'zbekcha
                                    </label>
                                    @if($content->type === 'textarea')
                                        <textarea name="contents[{{ $index }}][value_uz]"
                                                  class="form-control" rows="4">{{ $content->value_uz }}</textarea>
                                    @else
                                        <input type="{{ $content->type === 'number' ? 'number' : 'text' }}"
                                               name="contents[{{ $index }}][value_uz]"
                                               value="{{ $content->value_uz }}"
                                               class="form-control">
                                    @endif
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <img src="{{ asset('assets/flags/en.png') }}" width="20" class="me-1" alt="EN">
                                        English
                                    </label>
                                    @if($content->type === 'textarea')
                                        <textarea name="contents[{{ $index }}][value_en]"
                                                  class="form-control" rows="4">{{ $content->value_en }}</textarea>
                                    @else
                                        <input type="{{ $content->type === 'number' ? 'number' : 'text' }}"
                                               name="contents[{{ $index }}][value_en]"
                                               value="{{ $content->value_en }}"
                                               class="form-control">
                                    @endif
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <img src="{{ asset('assets/flags/ru.png') }}" width="20" class="me-1" alt="RU">
                                        Русский
                                    </label>
                                    @if($content->type === 'textarea')
                                        <textarea name="contents[{{ $index }}][value_ru]"
                                                  class="form-control" rows="4">{{ $content->value_ru }}</textarea>
                                    @else
                                        <input type="{{ $content->type === 'number' ? 'number' : 'text' }}"
                                               name="contents[{{ $index }}][value_ru]"
                                               value="{{ $content->value_ru }}"
                                               class="form-control">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Bu bo'lim uchun hali kontent mavjud emas. Iltimos, ushbu bo'limga kontent qo'shish uchun ma'lumotlar bazasiga qo'lda kiritish kerak.
                        </div>
                    @endforelse
                </div>

                @if($contents->count() > 0)
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Saqlash
                        </button>
                        <a href="{{ route('admin.cms.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Bekor qilish
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-question-circle"></i> Yo'riqnoma</h5>
        </div>
        <div class="card-body">
            <p><strong>Qanday ishlatish:</strong></p>
            <ul>
                <li>Har bir kontent elementi uchta tilda tahrirlash mumkin: O'zbekcha, Inglizcha va Ruscha</li>
                <li>Kalit nomi (key) - bu maydonning identifikatori, o'zgartirib bo'lmaydi</li>
                <li>Matnli maydonlar uchun oddiy input, katta matnlar uchun textarea ishlatiladi</li>
                <li>Raqamli maydonlar uchun faqat raqam kiritish mumkin</li>
                <li>O'zgarishlarni saqlash uchun "Saqlash" tugmasini bosing</li>
            </ul>
        </div>
    </div>
</div>
@endsection
