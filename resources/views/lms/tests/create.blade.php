@extends('layouts.dashboard-new')

@section('title', 'Yangi test yaratish — LMS')
@section('page-title', 'Yangi test yaratish')

@section('styles')
<style>
.question-item {
    border: 1px solid var(--c-border);
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 12px;
    background: var(--c-bg);
}
.question-item .question-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
</style>
@endsection

@section('content')

<form action="{{ route('lms.tests.store') }}" method="POST" id="testForm">
    @csrf

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
                        <label for="title" class="form-label">Test nomi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Tavsif</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="subject_id" class="form-label">Fan</label>
                            <select class="form-select @error('subject_id') is-invalid @enderror"
                                    id="subject_id" name="subject_id">
                                <option value="">Fanni tanlang</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="difficulty" class="form-label">Qiyinlik darajasi <span class="text-danger">*</span></label>
                            <select class="form-select @error('difficulty') is-invalid @enderror"
                                    id="difficulty" name="difficulty" required>
                                <option value="easy"   {{ old('difficulty', 'easy') == 'easy'   ? 'selected' : '' }}>Oson</option>
                                <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>O'rta</option>
                                <option value="hard"   {{ old('difficulty') == 'hard'   ? 'selected' : '' }}>Qiyin</option>
                            </select>
                            @error('difficulty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-list-ol" style="color:var(--c-sky)"></i>
                        <span>Savollar</span>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addQuestion()">
                        <i class="fas fa-plus me-1"></i>Savol qo'shish
                    </button>
                </div>
                <div class="card-body" id="questionsContainer">
                    <div id="emptyMsg" class="text-center py-4" style="color:var(--c-text-3);font-size:13px">
                        <i class="fas fa-question-circle mb-2" style="font-size:28px;display:block"></i>
                        Hali savollar qo'shilmagan
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-clock" style="color:var(--c-amber)"></i>
                    <span>Test sozlamalari</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="time_limit" class="form-label">Vaqt chegarasi (daqiqa)</label>
                        <input type="number" class="form-control @error('time_limit') is-invalid @enderror"
                               id="time_limit" name="time_limit" value="{{ old('time_limit', 30) }}" min="1" max="300">
                        @error('time_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="passing_score" class="form-label">O'tish bali (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('passing_score') is-invalid @enderror"
                               id="passing_score" name="passing_score" value="{{ old('passing_score', 60) }}" min="0" max="100" required>
                        @error('passing_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="attempts_allowed" class="form-label">Urinishlar soni</label>
                        <input type="number" class="form-control @error('attempts_allowed') is-invalid @enderror"
                               id="attempts_allowed" name="attempts_allowed" value="{{ old('attempts_allowed', 1) }}" min="1" max="10">
                        @error('attempts_allowed')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="available_from" class="form-label">Boshlanish sanasi</label>
                        <input type="datetime-local" class="form-control @error('available_from') is-invalid @enderror"
                               id="available_from" name="available_from" value="{{ old('available_from') }}">
                        @error('available_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0">
                        <label for="available_until" class="form-label">Tugash sanasi</label>
                        <input type="datetime-local" class="form-control @error('available_until') is-invalid @enderror"
                               id="available_until" name="available_until" value="{{ old('available_until') }}">
                        @error('available_until')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-sliders-h" style="color:var(--c-violet)"></i>
                    <span>Qo'shimcha sozlamalar</span>
                </div>
                <div class="card-body">
                    @php
                        $checks = [
                            ['shuffle_questions','Savollarni aralashtirish',true],
                            ['shuffle_answers','Javoblarni aralashtirish',false],
                            ['show_correct_answers',"To'g'ri javoblarni ko'rsatish",true],
                            ['allow_review',"Natijalarni ko'rib chiqishga ruxsat",true],
                            ['auto_grade','Avtomatik baholash',true],
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
                </div>
            </div>

            <div class="card">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('lms.tests.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Bekor qilish
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<template id="questionTemplate">
    <div class="question-item">
        <div class="question-header">
            <span style="font-weight:600;font-size:14px;color:var(--c-text)">
                <i class="fas fa-circle me-2" style="color:var(--c-teal);font-size:8px"></i>Savol #<span class="question-number"></span>
            </span>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion(this)" style="padding:3px 8px">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        <div class="mb-3">
            <label class="form-label" style="font-size:13px">Savol matni <span class="text-danger">*</span></label>
            <textarea class="form-control question-text" name="questions[][question]" rows="2" required></textarea>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label" style="font-size:13px">Savol turi <span class="text-danger">*</span></label>
                <select class="form-select question-type" name="questions[][type]" onchange="updateQuestionType(this)" required>
                    <option value="multiple_choice">Ko'p tanlovli</option>
                    <option value="true_false">To'g'ri/Noto'g'ri</option>
                    <option value="short_answer">Qisqa javob</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" style="font-size:13px">Ball <span class="text-danger">*</span></label>
                <input type="number" class="form-control question-points" name="questions[][points]" value="1" min="1" required>
            </div>
        </div>

        <div class="options-container mb-3">
            <label class="form-label" style="font-size:13px">Javob variantlari</label>
            <div class="options-list">
                @foreach(['A','B','C','D'] as $i => $letter)
                <div class="input-group mb-2">
                    <span class="input-group-text" style="width:40px;justify-content:center">
                        <input type="radio" name="questions[][correct]" value="{{ $i }}" class="correct-option">
                    </span>
                    <input type="text" class="form-control option-text" placeholder="{{ $letter }} variant">
                </div>
                @endforeach
            </div>
        </div>

        <div>
            <label class="form-label" style="font-size:13px">Tushuntirish (ixtiyoriy)</label>
            <textarea class="form-control question-explanation" name="questions[][explanation]" rows="2"></textarea>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
let questionCount = 0;

function addQuestion() {
    document.getElementById('emptyMsg')?.remove();
    const container = document.getElementById('questionsContainer');
    const template = document.getElementById('questionTemplate');
    const clone = template.content.cloneNode(true);
    questionCount++;
    clone.querySelector('.question-number').textContent = questionCount;
    const index = questionCount - 1;
    clone.querySelectorAll('[name*="questions[]"]').forEach(el => {
        el.name = el.name.replace('[]', '[' + index + ']');
    });
    clone.querySelectorAll('.correct-option').forEach(radio => {
        radio.name = 'questions[' + index + '][correct]';
    });
    container.appendChild(clone);
}

function removeQuestion(button) {
    button.closest('.question-item').remove();
    updateQuestionNumbers();
    if (document.querySelectorAll('.question-item').length === 0) {
        const container = document.getElementById('questionsContainer');
        const msg = document.createElement('div');
        msg.id = 'emptyMsg';
        msg.className = 'text-center py-4';
        msg.style = 'color:var(--c-text-3);font-size:13px';
        msg.innerHTML = '<i class="fas fa-question-circle mb-2" style="font-size:28px;display:block"></i>Hali savollar qo\'shilmagan';
        container.appendChild(msg);
    }
}

function updateQuestionNumbers() {
    const questions = document.querySelectorAll('.question-item');
    questions.forEach((q, index) => {
        q.querySelector('.question-number').textContent = index + 1;
        q.querySelectorAll('[name*="questions["]').forEach(el => {
            el.name = el.name.replace(/questions\[\d+\]/, 'questions[' + index + ']');
        });
    });
    questionCount = questions.length;
}

function updateQuestionType(select) {
    const container = select.closest('.question-item');
    const optionsContainer = container.querySelector('.options-container');
    const type = select.value;
    const index = Array.from(container.parentNode.children).indexOf(container);

    if (type === 'true_false') {
        optionsContainer.innerHTML = `
            <label class="form-label" style="font-size:13px">To'g'ri javob</label>
            <select class="form-select" name="questions[${index}][correct_answer]" required>
                <option value="true">To'g'ri</option>
                <option value="false">Noto'g'ri</option>
            </select>`;
    } else if (type === 'short_answer') {
        optionsContainer.innerHTML = `
            <label class="form-label" style="font-size:13px">To'g'ri javob</label>
            <input type="text" class="form-control" name="questions[${index}][correct_answer]" required>`;
    } else {
        location.reload();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    addQuestion();
});

document.getElementById('testForm').addEventListener('submit', function(e) {
    const questions = document.querySelectorAll('.question-item');
    if (questions.length === 0) {
        e.preventDefault();
        alert('Kamida bitta savol qo\'shishingiz kerak!');
        return false;
    }
    questions.forEach((question, index) => {
        const type = question.querySelector('.question-type').value;
        if (type === 'multiple_choice') {
            const options = [];
            question.querySelectorAll('.option-text').forEach(input => {
                if (input.value) options.push(input.value);
            });
            const optionsInput = document.createElement('input');
            optionsInput.type = 'hidden';
            optionsInput.name = `questions[${index}][options]`;
            optionsInput.value = JSON.stringify(options);
            question.appendChild(optionsInput);
            const correctRadio = question.querySelector('.correct-option:checked');
            if (correctRadio) {
                const correctInput = document.createElement('input');
                correctInput.type = 'hidden';
                correctInput.name = `questions[${index}][correct_answer]`;
                correctInput.value = correctRadio.value;
                question.appendChild(correctInput);
            }
        }
    });
});
</script>
@endpush
