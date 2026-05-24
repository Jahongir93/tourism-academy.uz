@extends('layouts.dashboard-new')

@section('title', 'Savollar — ' . $exam->title)
@section('page-title', $exam->title . ' — Savollar')

@section('styles')
<style>
.action-btn { display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;border:none;background:transparent;cursor:pointer;transition:all .15s;font-size:13px;padding:0; }
.action-btn:hover { background:var(--c-bg); }
.question-item { border-bottom:1px solid var(--c-border);padding:16px 20px;transition:background .15s; }
.question-item:hover { background:var(--c-bg); }
.option-row { display:flex;align-items:center;gap:8px;margin-bottom:4px; }
</style>
@endsection

@section('content')

<x-lms-alerts />

{{-- Action buttons --}}
<div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
    @if(!$exam->is_published)
    <form method="POST" action="{{ route('lms.exams.publish', $exam) }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm" style="background:var(--c-emerald);color:#fff">
            <i class="fas fa-check-circle me-1"></i>E'lon qilish
        </button>
    </form>
    @endif
    <button class="btn btn-sm" style="background:var(--c-violet);color:#fff"
            data-bs-toggle="modal" data-bs-target="#questionModal" onclick="openAddModal()">
        <i class="fas fa-plus me-1"></i>Savol qo'shish
    </button>
    <a href="{{ route('lms.exams.show', $exam) }}" class="btn btn-sm btn-outline-secondary ms-auto">
        <i class="fas fa-arrow-left me-1"></i>Ortga
    </a>
</div>

{{-- Summary --}}
<div class="d-flex align-items-center gap-3 mb-3" style="font-size:13px;color:var(--c-text-3)">
    <span><i class="fas fa-question-circle me-1" style="color:var(--c-violet)"></i>{{ $exam->questions->count() }} ta savol</span>
    <span><i class="fas fa-star me-1" style="color:var(--c-amber)"></i>{{ $exam->getTotalPoints() }} ball</span>
</div>

{{-- Questions list --}}
<div class="card">
    @if($exam->questions->count() > 0)
    <div>
        @foreach($exam->questions as $index => $question)
        <div class="question-item">
            <div class="d-flex align-items-start gap-3">
                <div style="width:32px;height:32px;background:rgba(124,58,237,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--c-violet);flex-shrink:0">
                    {{ $index + 1 }}
                </div>
                <div style="flex:1;min-width:0">
                    <p style="font-size:13px;color:var(--c-text);margin-bottom:8px">{!! nl2br(e($question->question_text)) !!}</p>
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge" style="background:var(--c-bg);color:var(--c-text-2);border:1px solid var(--c-border);font-size:10px">{{ $question->getQuestionTypeLabel() }}</span>
                        <span class="badge" style="background:rgba(124,58,237,.1);color:var(--c-violet);font-size:10px">{{ $question->points }} ball</span>
                        <span class="badge" style="background:var(--c-bg);color:var(--c-text-3);border:1px solid var(--c-border);font-size:10px">{{ $question->getDifficultyLabel() }}</span>
                        @if($question->category)
                        <span class="badge" style="background:rgba(20,184,166,.1);color:var(--c-teal);font-size:10px">{{ $question->category }}</span>
                        @endif
                    </div>
                    @if(in_array($question->question_type, ['single_choice', 'multiple_choice']) && $question->options)
                    <div>
                        @foreach($question->options as $key => $option)
                        @php $isCorrect = in_array($key, (array)$question->correct_answer); @endphp
                        <div class="option-row">
                            <i class="fas {{ $isCorrect ? 'fa-check-circle' : 'fa-circle' }}"
                               style="color:{{ $isCorrect ? 'var(--c-emerald)' : 'var(--c-border)' }};font-size:12px;flex-shrink:0"></i>
                            <span style="font-size:12px;color:{{ $isCorrect ? 'var(--c-emerald)' : 'var(--c-text-2)' }};font-weight:{{ $isCorrect ? '600' : '400' }}">{{ $option }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="d-flex gap-1 flex-shrink-0">
                    <button class="action-btn" title="Tahrirlash"
                            onclick="openEditModal({{ json_encode($question) }})"
                            data-bs-toggle="modal" data-bs-target="#questionModal"
                            style="color:var(--c-amber)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form method="POST" action="{{ route('lms.exams.questions.destroy', $question) }}"
                          onsubmit="return confirm('Savolni o\'chirmoqchimisiz?')" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn" style="color:var(--c-rose)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-5" style="color:var(--c-text-3)">
        <i class="fas fa-question-circle mb-2" style="display:block;font-size:36px"></i>
        <div style="font-size:14px;font-weight:600;color:var(--c-text-2);margin-bottom:4px">Savollar yo'q</div>
        <div style="font-size:12px;margin-bottom:16px">Imtihonga savollar qo'shishni boshlang</div>
        <button class="btn btn-sm" style="background:var(--c-violet);color:#fff"
                data-bs-toggle="modal" data-bs-target="#questionModal" onclick="openAddModal()">
            <i class="fas fa-plus me-1"></i>Birinchi savolni qo'shing
        </button>
    </div>
    @endif
</div>

{{-- Question Modal --}}
<div class="modal fade" id="questionModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Yangi savol qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="questionForm" method="POST" action="{{ route('lms.exams.questions.store', $exam) }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Savol turi</label>
                        <select name="question_type" id="questionType" class="form-select" onchange="updateQuestionType()">
                            <option value="single_choice">Bir to'g'ri javob</option>
                            <option value="multiple_choice">Ko'p javobli</option>
                            <option value="true_false">Ha/Yo'q</option>
                            <option value="text">Qisqa javob</option>
                            <option value="essay">Insho</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Savol matni <span class="text-danger">*</span></label>
                        <textarea name="question_text" id="questionText" class="form-control" rows="4" required
                                  placeholder="Savol matnini yozing..."></textarea>
                    </div>

                    {{-- Choice options --}}
                    <div id="choiceOptions" class="mb-3">
                        <label class="form-label">Javob variantlari</label>
                        <div id="optionsList"></div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="addOption()">
                            <i class="fas fa-plus me-1"></i>Variant qo'shish
                        </button>
                    </div>

                    {{-- True/False --}}
                    <div id="trueFalseOptions" class="mb-3 d-none">
                        <label class="form-label">To'g'ri javob</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_answer[]" value="0" id="answerYes">
                                <label class="form-check-label" for="answerYes">Ha</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_answer[]" value="1" id="answerNo">
                                <label class="form-check-label" for="answerNo">Yo'q</label>
                            </div>
                        </div>
                    </div>

                    {{-- Text answer --}}
                    <div id="textAnswer" class="mb-3 d-none">
                        <label class="form-label">To'g'ri javob(lar)</label>
                        <input type="text" name="correct_answer[]" class="form-control"
                               placeholder="To'g'ri javob (vergul bilan ajratsangiz, bir nechta variant)">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Ball</label>
                            <input type="number" name="points" id="questionPoints" class="form-control"
                                   required min="0.5" max="100" step="0.5" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Qiyinlik</label>
                            <select name="difficulty" id="questionDifficulty" class="form-select">
                                <option value="easy">Oson</option>
                                <option value="medium" selected>O'rtacha</option>
                                <option value="hard">Qiyin</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategoriya</label>
                            <input type="text" name="category" id="questionCategory" class="form-control"
                                   placeholder="Mavzu/bo'lim">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Javob izohi (ixtiyoriy)</label>
                        <textarea name="explanation" id="questionExplanation" class="form-control" rows="2"
                                  placeholder="To'g'ri javobni tushuntirish..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn" style="background:var(--c-violet);color:#fff">
                        <i class="fas fa-save me-1"></i><span id="submitBtnText">Qo'shish</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let optionCount = 4;
let currentType = 'single_choice';

function updateQuestionType() {
    currentType = document.getElementById('questionType').value;
    const choiceDiv = document.getElementById('choiceOptions');
    const tfDiv = document.getElementById('trueFalseOptions');
    const textDiv = document.getElementById('textAnswer');

    choiceDiv.classList.add('d-none');
    tfDiv.classList.add('d-none');
    textDiv.classList.add('d-none');

    if (currentType === 'single_choice' || currentType === 'multiple_choice') {
        choiceDiv.classList.remove('d-none');
    } else if (currentType === 'true_false') {
        tfDiv.classList.remove('d-none');
    } else if (currentType === 'text') {
        textDiv.classList.remove('d-none');
    }
}

function buildOptionsList(options, correctAnswers) {
    const list = document.getElementById('optionsList');
    list.innerHTML = '';
    optionCount = options ? options.length : 4;
    const opts = options || ['', '', '', ''];
    opts.forEach((val, i) => {
        const isCorrect = correctAnswers && correctAnswers.includes(i);
        addOptionRow(val, i, isCorrect);
    });
}

function addOption() {
    addOptionRow('', optionCount, false);
    optionCount++;
}

function addOptionRow(val, i, isCorrect) {
    const list = document.getElementById('optionsList');
    const div = document.createElement('div');
    div.className = 'option-row mb-2';
    div.dataset.index = i;
    const inputType = currentType === 'multiple_choice' ? 'checkbox' : 'radio';
    div.innerHTML = `
        <input type="${inputType}" name="correct_answer[]" value="${i}" class="form-check-input flex-shrink-0" ${isCorrect ? 'checked' : ''}>
        <input type="text" name="options[]" class="form-control form-control-sm" value="${val}" placeholder="Variant ${i+1}" required>
        <button type="button" class="action-btn flex-shrink-0" style="color:var(--c-rose)" onclick="removeOption(this)">
            <i class="fas fa-times"></i>
        </button>`;
    list.appendChild(div);
}

function removeOption(btn) {
    const row = btn.closest('.option-row');
    const list = document.getElementById('optionsList');
    if (list.children.length > 2) row.remove();
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = "Yangi savol qo'shish";
    document.getElementById('submitBtnText').textContent = "Qo'shish";
    document.getElementById('questionForm').action = "{{ route('lms.exams.questions.store', $exam) }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('questionType').value = 'single_choice';
    document.getElementById('questionText').value = '';
    document.getElementById('questionPoints').value = 1;
    document.getElementById('questionDifficulty').value = 'medium';
    document.getElementById('questionCategory').value = '';
    document.getElementById('questionExplanation').value = '';
    currentType = 'single_choice';
    updateQuestionType();
    buildOptionsList(null, []);
}

function openEditModal(question) {
    document.getElementById('modalTitle').textContent = 'Savolni tahrirlash';
    document.getElementById('submitBtnText').textContent = 'Saqlash';
    document.getElementById('questionForm').action = '/lms/exams/questions/' + question.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('questionType').value = question.question_type;
    document.getElementById('questionText').value = question.question_text;
    document.getElementById('questionPoints').value = question.points;
    document.getElementById('questionDifficulty').value = question.difficulty;
    document.getElementById('questionCategory').value = question.category || '';
    document.getElementById('questionExplanation').value = question.explanation || '';
    currentType = question.question_type;
    updateQuestionType();

    const correctArr = Array.isArray(question.correct_answer)
        ? question.correct_answer.map(Number)
        : (question.correct_answer !== null ? [Number(question.correct_answer)] : []);

    if (currentType === 'single_choice' || currentType === 'multiple_choice') {
        buildOptionsList(question.options, correctArr);
    } else if (currentType === 'true_false') {
        const val = correctArr[0];
        document.getElementById('answerYes').checked = (val === 0);
        document.getElementById('answerNo').checked = (val === 1);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    buildOptionsList(null, []);
});
</script>
@endpush
