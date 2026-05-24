@extends('layouts.dashboard-new')

@section('title', 'Imtihon — ' . $exam->title)
@section('page-title', $exam->title)

@section('styles')
<style>
.answer-label { display:flex;align-items:center;padding:12px 16px;border-radius:10px;border:2px solid var(--c-border);cursor:pointer;transition:all .15s;gap:10px;margin-bottom:8px; }
.answer-label:hover { border-color:var(--c-violet);background:rgba(124,58,237,.04); }
.answer-label.selected { border-color:var(--c-violet);background:rgba(124,58,237,.06); }
.answer-label input { flex-shrink:0; }
.q-nav-btn { width:36px;height:36px;border-radius:8px;border:none;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;background:var(--c-bg);color:var(--c-text-2); }
.q-nav-btn.answered { background:var(--c-emerald);color:#fff; }
.q-nav-btn.flagged { outline:2px solid var(--c-amber);outline-offset:1px; }
.q-card { border:2px solid var(--c-border);border-radius:12px;padding:20px;margin-bottom:16px;background:var(--c-surface);transition:border-color .15s; }
.q-card.current { border-color:var(--c-violet); }
.timer-normal { background:rgba(16,185,129,.1);color:var(--c-emerald); }
.timer-warning { background:rgba(244,63,94,.1);color:var(--c-rose); }
</style>
@endsection

@section('content')

{{-- Sticky header bar --}}
<div class="card mb-4 sticky-top" style="top:0;z-index:100">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="font-size:14px;font-weight:700;color:var(--c-text)">{{ $exam->title }}</div>
                <span class="badge" style="background:rgba(124,58,237,.12);color:var(--c-violet)">{{ $exam->getExamTypeLabel() }}</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div style="font-size:13px;color:var(--c-text-3)">
                    <span id="answeredCount">0</span> / {{ $questions->count() }} javob
                </div>
                <div id="timerBox" class="d-flex align-items-center gap-2 px-3 py-1 rounded timer-normal">
                    <i class="fas fa-clock"></i>
                    <span id="timerDisplay" style="font-family:monospace;font-weight:700;font-size:16px">--:--</span>
                </div>
                <button onclick="confirmSubmit()" class="btn btn-sm" style="background:var(--c-violet);color:#fff">
                    <i class="fas fa-paper-plane me-1"></i>Topshirish
                </button>
            </div>
        </div>
        <div class="mt-2" style="height:4px;background:var(--c-border);border-radius:2px;overflow:hidden">
            <div id="progressBar" style="height:100%;background:var(--c-violet);width:0%;transition:width .3s"></div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Questions --}}
    <div class="col-lg-9">
        @foreach($questions as $index => $question)
        <div class="q-card" id="question-{{ $question->id }}" data-index="{{ $index }}">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div id="qnum-{{ $question->id }}"
                         style="width:32px;height:32px;border-radius:8px;background:var(--c-bg);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--c-text-2);flex-shrink:0">
                        {{ $index + 1 }}
                    </div>
                    <div>
                        <span style="font-size:11px;color:var(--c-text-3)">{{ $question->getQuestionTypeLabel() }}</span>
                        <span style="margin:0 6px;color:var(--c-border)">|</span>
                        <span style="font-size:11px;font-weight:600;color:var(--c-violet)">{{ $question->points }} ball</span>
                    </div>
                </div>
                <button onclick="toggleFlag({{ $question->id }})" id="flag-{{ $question->id }}"
                        class="action-btn" title="Belgilash"
                        style="border:1px solid var(--c-border);border-radius:8px;padding:0;width:30px;height:30px;background:transparent;cursor:pointer;color:var(--c-text-3);display:inline-flex;align-items:center;justify-content:center">
                    <i class="fas fa-flag"></i>
                </button>
            </div>

            <div style="font-size:14px;color:var(--c-text);line-height:1.6;margin-bottom:16px">
                {!! nl2br(e($question->question_text)) !!}
            </div>

            {{-- Options --}}
            @if(in_array($question->question_type, ['single_choice', 'multiple_choice']))
            @php $options = $exam->shuffle_answers ? collect($question->options)->shuffle()->all() : $question->options; @endphp
            <div>
                @foreach($options as $key => $option)
                @php $inputType = $question->question_type == 'single_choice' ? 'radio' : 'checkbox'; @endphp
                <label class="answer-label" id="lbl-{{ $question->id }}-{{ $key }}"
                       onclick="handleChoice({{ $question->id }}, {{ $key }}, '{{ $question->question_type }}')">
                    <input type="{{ $inputType }}" name="answer_{{ $question->id }}" value="{{ $key }}"
                           {{ (isset($answers[$question->id]) && in_array($key, (array)$answers[$question->id])) ? 'checked' : '' }}
                           style="accent-color:var(--c-violet);width:16px;height:16px">
                    <span style="font-size:13px;color:var(--c-text)">{{ $option }}</span>
                </label>
                @endforeach
            </div>
            @elseif($question->question_type == 'true_false')
            <div class="row g-2">
                <div class="col-6">
                    <label class="answer-label justify-content-center" id="lbl-{{ $question->id }}-0"
                           onclick="handleChoice({{ $question->id }}, 0, 'single_choice')">
                        <input type="radio" name="answer_{{ $question->id }}" value="0"
                               {{ (isset($answers[$question->id]) && in_array(0, (array)$answers[$question->id])) ? 'checked' : '' }}
                               style="accent-color:var(--c-violet)">
                        <span style="font-size:13px;font-weight:600;color:var(--c-text)">Ha</span>
                    </label>
                </div>
                <div class="col-6">
                    <label class="answer-label justify-content-center" id="lbl-{{ $question->id }}-1"
                           onclick="handleChoice({{ $question->id }}, 1, 'single_choice')">
                        <input type="radio" name="answer_{{ $question->id }}" value="1"
                               {{ (isset($answers[$question->id]) && in_array(1, (array)$answers[$question->id])) ? 'checked' : '' }}
                               style="accent-color:var(--c-violet)">
                        <span style="font-size:13px;font-weight:600;color:var(--c-text)">Yo'q</span>
                    </label>
                </div>
            </div>
            @elseif(in_array($question->question_type, ['text', 'fill_blank']))
            <input type="text" class="form-control" placeholder="Javobingizni yozing..."
                   id="text-{{ $question->id }}"
                   value="{{ $textAnswers[$question->id] ?? '' }}"
                   oninput="saveTextAnswer({{ $question->id }}, this.value)">
            @elseif($question->question_type == 'essay')
            <textarea class="form-control" rows="6" placeholder="Javobingizni yozing..."
                      id="text-{{ $question->id }}"
                      oninput="saveTextAnswer({{ $question->id }}, this.value)">{{ $textAnswers[$question->id] ?? '' }}</textarea>
            @endif

            @if($question->question_hint)
            <div class="mt-3 p-2 rounded" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2)">
                <span style="font-size:12px;color:var(--c-amber)"><i class="fas fa-lightbulb me-1"></i>{{ $question->question_hint }}</span>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Navigator sidebar --}}
    <div class="col-lg-3">
        <div class="card sticky-top" style="top:80px">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-th" style="color:var(--c-violet)"></i>
                <span>Savollar</span>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach($questions as $index => $question)
                    <button class="q-nav-btn" id="nav-{{ $question->id }}"
                            onclick="scrollToQuestion({{ $index }}, {{ $question->id }})">
                        {{ $index + 1 }}
                    </button>
                    @endforeach
                </div>
                <div style="font-size:11px;color:var(--c-text-3)">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div style="width:14px;height:14px;background:var(--c-emerald);border-radius:3px"></div>
                        <span>Javob berilgan</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div style="width:14px;height:14px;background:var(--c-bg);border:1px solid var(--c-border);border-radius:3px"></div>
                        <span>Javob berilmagan</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:14px;height:14px;background:var(--c-bg);border:1px solid var(--c-border);border-radius:3px;outline:2px solid var(--c-amber);outline-offset:1px"></div>
                        <span>Belgilangan</span>
                    </div>
                </div>
                @if($exam->instructions)
                <div class="mt-3 p-3 rounded" style="background:rgba(14,165,233,.08);border:1px solid rgba(14,165,233,.15)">
                    <div style="font-size:11px;font-weight:600;color:var(--c-sky);margin-bottom:4px">Ko'rsatmalar</div>
                    <p style="font-size:12px;color:var(--c-text-2);margin:0">{{ $exam->instructions }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Submit confirmation modal --}}
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div style="width:56px;height:56px;background:rgba(245,158,11,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                    <i class="fas fa-exclamation-triangle" style="font-size:22px;color:var(--c-amber)"></i>
                </div>
                <h5 style="color:var(--c-text)">Imtihonni topshirmoqchimisiz?</h5>
                <p style="font-size:13px;color:var(--c-text-2);margin-bottom:4px">
                    Jami <strong id="modalAnswered">0</strong> ta savolga javob berdingiz.
                </p>
                <p id="modalUnanswered" style="font-size:12px;color:var(--c-rose);margin-bottom:16px"></p>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">Orqaga</button>
                    <form method="POST" action="{{ route('lms.exams.submit', $attempt) }}" class="flex-fill" id="submitForm">
                        @csrf
                        <button type="submit" class="btn w-100" style="background:var(--c-violet);color:#fff">
                            <i class="fas fa-paper-plane me-1"></i>Topshirish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const TOTAL_QUESTIONS = {{ $questions->count() }};
const QUESTION_IDS = @json($questions->pluck('id')->toArray());
let timeRemaining = {{ $attempt->getRemainingTime() }};
let answers = @json($answers ?? []);
let textAnswers = @json($textAnswers ?? []);
let flaggedQuestions = [];
let saveTimeout = null;

// Timer
function formatTime(s) {
    const h = Math.floor(s / 3600);
    const m = Math.floor((s % 3600) / 60);
    const sec = s % 60;
    if (h > 0) return `${pad(h)}:${pad(m)}:${pad(sec)}`;
    return `${pad(m)}:${pad(sec)}`;
}
function pad(n) { return n.toString().padStart(2, '0'); }

const timerInterval = setInterval(() => {
    timeRemaining--;
    document.getElementById('timerDisplay').textContent = formatTime(timeRemaining);
    const box = document.getElementById('timerBox');
    box.className = 'd-flex align-items-center gap-2 px-3 py-1 rounded ' + (timeRemaining < 300 ? 'timer-warning' : 'timer-normal');
    if (timeRemaining <= 0) {
        clearInterval(timerInterval);
        document.getElementById('submitForm').submit();
    }
}, 1000);
document.getElementById('timerDisplay').textContent = formatTime(timeRemaining);

// Answered count
function updateStats() {
    const choiceCount = Object.keys(answers).length;
    const textCount = Object.keys(textAnswers).filter(k => textAnswers[k] && textAnswers[k].trim()).length;
    const total = choiceCount + textCount;
    document.getElementById('answeredCount').textContent = total;
    document.getElementById('progressBar').style.width = (total / TOTAL_QUESTIONS * 100) + '%';
}

// Answer handlers
function handleChoice(qId, val, type) {
    if (type === 'single_choice' || type === 'true_false') {
        answers[qId] = [val];
        // Deselect other labels
        document.querySelectorAll(`label[id^="lbl-${qId}-"]`).forEach(l => l.classList.remove('selected'));
        const lbl = document.getElementById(`lbl-${qId}-${val}`);
        if (lbl) lbl.classList.add('selected');
    } else {
        if (!answers[qId]) answers[qId] = [];
        const idx = answers[qId].indexOf(val);
        const lbl = document.getElementById(`lbl-${qId}-${val}`);
        if (idx > -1) { answers[qId].splice(idx, 1); if (lbl) lbl.classList.remove('selected'); }
        else { answers[qId].push(val); if (lbl) lbl.classList.add('selected'); }
    }
    updateQNum(qId);
    updateNavBtn(qId);
    updateStats();
    sendAnswer(qId, answers[qId], null);
}

function saveTextAnswer(qId, val) {
    textAnswers[qId] = val;
    updateQNum(qId);
    updateNavBtn(qId);
    updateStats();
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(() => sendAnswer(qId, null, val), 1000);
}

function updateQNum(qId) {
    const el = document.getElementById(`qnum-${qId}`);
    if (!el) return;
    const hasAnswer = (answers[qId] && answers[qId].length > 0) || (textAnswers[qId] && textAnswers[qId].trim());
    el.style.background = hasAnswer ? 'rgba(16,185,129,.15)' : 'var(--c-bg)';
    el.style.color = hasAnswer ? 'var(--c-emerald)' : 'var(--c-text-2)';
}

function updateNavBtn(qId) {
    const btn = document.getElementById(`nav-${qId}`);
    if (!btn) return;
    const hasAnswer = (answers[qId] && answers[qId].length > 0) || (textAnswers[qId] && textAnswers[qId].trim());
    btn.classList.toggle('answered', !!hasAnswer);
}

function toggleFlag(qId) {
    const idx = flaggedQuestions.indexOf(qId);
    if (idx > -1) flaggedQuestions.splice(idx, 1);
    else flaggedQuestions.push(qId);
    const btn = document.getElementById(`flag-${qId}`);
    const navBtn = document.getElementById(`nav-${qId}`);
    if (btn) btn.style.color = flaggedQuestions.includes(qId) ? 'var(--c-amber)' : 'var(--c-text-3)';
    if (navBtn) navBtn.classList.toggle('flagged', flaggedQuestions.includes(qId));
}

function scrollToQuestion(index, qId) {
    const el = document.getElementById(`question-${qId}`);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function sendAnswer(qId, answer, textAnswer) {
    fetch('{{ route('lms.exams.save-answer', $attempt) }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ question_id: qId, answer, text_answer: textAnswer })
    }).catch(() => {});
}

function confirmSubmit() {
    const answered = parseInt(document.getElementById('answeredCount').textContent);
    const unanswered = TOTAL_QUESTIONS - answered;
    document.getElementById('modalAnswered').textContent = answered;
    const uEl = document.getElementById('modalUnanswered');
    uEl.textContent = unanswered > 0 ? `${unanswered} ta savol javobsiz qoldi!` : '';
    const modal = new bootstrap.Modal(document.getElementById('submitModal'));
    modal.show();
}

// Init: restore selections
document.addEventListener('DOMContentLoaded', function() {
    Object.entries(answers).forEach(([qId, vals]) => {
        if (Array.isArray(vals)) vals.forEach(v => {
            const lbl = document.getElementById(`lbl-${qId}-${v}`);
            if (lbl) lbl.classList.add('selected');
        });
        updateQNum(qId);
        updateNavBtn(qId);
    });
    updateStats();
});

// Tab switch tracking
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        fetch('{{ route('lms.exams.tab-switch', $attempt) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        }).catch(() => {});
    }
});

@if($exam->prevent_copy_paste)
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('paste', e => e.preventDefault());
document.addEventListener('contextmenu', e => e.preventDefault());
@endif
</script>
@endpush
