@extends('layouts.dashboard-new')

@section('title', "Soatlar taqsimoti - " . $subject->name_uz)
@section('page-title', "Soatlar taqsimoti")

@section('content')
<div class="container-fluid">
    <!-- Subject Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-2">{{ $subject->code }} - {{ $subject->name_uz }}</h4>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2">{{ $subject->credits }} kredit</span>
                        <span class="badge bg-secondary me-2">{{ $subject->total_hours }} soat</span>
                        <span class="badge bg-info">{{ $subject->subject_type_text }}</span>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('structure.academic.subjects.show', $subject) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Fanga qaytish
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Info Alert -->
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <div>
            <strong>Muhim:</strong> 1 kredit = 30 soat. Sizning fanda {{ $subject->credits }} kredit = {{ $subject->total_hours }} soat.
            Auditoriya soatlari (ma'ruza + amaliyot + seminar + laboratoriya) umumiy soatlarning 50% dan oshmasligi kerak.
            <br>Maksimal auditoriya soatlari: <strong>{{ floor($subject->total_hours * 0.5) }} soat</strong>
        </div>
    </div>

    <div class="row">
        <!-- New Distribution Form -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Yangi taqsimot yaratish</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('structure.academic.hours.saveDistribution', $subject) }}" method="POST" id="distributionForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Ta'lim yo'nalishi</label>
                            <select name="program_id" class="form-select">
                                <option value="">Barcha yo'nalishlar uchun (standart)</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}">
                                        {{ $program->code }} - {{ $program->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Agar tanlanmasa, standart taqsimot yaratiladi</small>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Auditoriya soatlari</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-chalkboard-teacher text-primary"></i> Ma'ruza soatlari
                                </label>
                                <input type="number" name="lecture_hours" class="form-control auditory-hours" 
                                       min="0" max="{{ $subject->total_hours }}" value="{{ $defaultDistribution['lecture'] }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-users text-info"></i> Amaliyot soatlari
                                </label>
                                <input type="number" name="practice_hours" class="form-control auditory-hours" 
                                       min="0" max="{{ $subject->total_hours }}" value="{{ $defaultDistribution['practice'] }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-comments text-warning"></i> Seminar soatlari
                                </label>
                                <input type="number" name="seminar_hours" class="form-control auditory-hours" 
                                       min="0" max="{{ $subject->total_hours }}" value="{{ $defaultDistribution['seminar'] }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-flask text-danger"></i> Laboratoriya soatlari
                                </label>
                                <input type="number" name="lab_hours" class="form-control auditory-hours" 
                                       min="0" max="{{ $subject->total_hours }}" value="{{ $defaultDistribution['lab'] }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-success mb-3">Mustaqil ta'lim</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-book-reader text-success"></i> Mustaqil ta'lim soatlari
                                </label>
                                <input type="number" name="independent_hours" class="form-control independent-hours" 
                                       min="0" max="{{ $subject->total_hours }}" value="{{ $defaultDistribution['independent'] }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-pen text-secondary"></i> Kurs ishi soatlari
                                </label>
                                <input type="number" name="course_work_hours" class="form-control" 
                                       min="0" max="{{ $subject->total_hours }}" value="0">
                                <small class="text-muted">Agar mavjud bo'lsa</small>
                            </div>
                        </div>

                        <!-- Hour Summary -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small class="text-muted">Auditoriya</small>
                                        <h5 id="auditoryTotal" class="mb-0">0</h5>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Mustaqil</small>
                                        <h5 id="independentTotal" class="mb-0">0</h5>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Jami</small>
                                        <h5 id="totalHours" class="mb-0">0</h5>
                                    </div>
                                </div>
                                <div class="progress mt-3" style="height: 25px;">
                                    <div id="auditoryProgress" class="progress-bar bg-primary" style="width: 0%">
                                        Auditoriya 0%
                                    </div>
                                    <div id="independentProgress" class="progress-bar bg-success" style="width: 0%">
                                        Mustaqil 0%
                                    </div>
                                </div>
                                <div id="validationMessage" class="mt-2"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Taqsimotni saqlash
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Existing Distributions -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Mavjud taqsimotlar</h5>
                </div>
                <div class="card-body">
                    @if($distributions->count() > 0)
                        @foreach($distributions as $programId => $programDistributions)
                            @foreach($programDistributions as $distribution)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        @if($distribution->program)
                                            <strong>{{ $distribution->program->name_uz }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $distribution->program->code }}</small>
                                        @else
                                            <strong>Standart taqsimot</strong>
                                            <small class="text-muted">(Barcha yo'nalishlar uchun)</small>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <div class="progress mb-3" style="height: 30px;">
                                            @php
                                                $total = $distribution->total_hours;
                                                $lecturePercent = $total > 0 ? round(($distribution->lecture_hours / $total * 100)) : 0;
                                                $practicePercent = $total > 0 ? round(($distribution->practice_hours / $total * 100)) : 0;
                                                $seminarPercent = $total > 0 ? round(($distribution->seminar_hours / $total * 100)) : 0;
                                                $labPercent = $total > 0 ? round(($distribution->lab_hours / $total * 100)) : 0;
                                                $independentPercent = $total > 0 ? round(($distribution->independent_hours / $total * 100)) : 0;
                                            @endphp
                                            @if($distribution->lecture_hours > 0)
                                            <div class="progress-bar bg-primary" style="width: {{ $lecturePercent }}%" 
                                                 title="Ma'ruza: {{ $distribution->lecture_hours }} soat">
                                                {{ $lecturePercent }}%
                                            </div>
                                            @endif
                                            @if($distribution->practice_hours > 0)
                                            <div class="progress-bar bg-info" style="width: {{ $practicePercent }}%"
                                                 title="Amaliyot: {{ $distribution->practice_hours }} soat">
                                                {{ $practicePercent }}%
                                            </div>
                                            @endif
                                            @if($distribution->seminar_hours > 0)
                                            <div class="progress-bar bg-warning" style="width: {{ $seminarPercent }}%"
                                                 title="Seminar: {{ $distribution->seminar_hours }} soat">
                                                {{ $seminarPercent }}%
                                            </div>
                                            @endif
                                            @if($distribution->lab_hours > 0)
                                            <div class="progress-bar bg-danger" style="width: {{ $labPercent }}%"
                                                 title="Laboratoriya: {{ $distribution->lab_hours }} soat">
                                                {{ $labPercent }}%
                                            </div>
                                            @endif
                                            @if($distribution->independent_hours > 0)
                                            <div class="progress-bar bg-success" style="width: {{ $independentPercent }}%"
                                                 title="Mustaqil ta'lim: {{ $distribution->independent_hours }} soat">
                                                {{ $independentPercent }}%
                                            </div>
                                            @endif
                                        </div>
                                        
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <td width="50%">Ma'ruza:</td>
                                                <td>{{ $distribution->lecture_hours }} soat</td>
                                            </tr>
                                            <tr>
                                                <td>Amaliyot:</td>
                                                <td>{{ $distribution->practice_hours }} soat</td>
                                            </tr>
                                            <tr>
                                                <td>Seminar:</td>
                                                <td>{{ $distribution->seminar_hours }} soat</td>
                                            </tr>
                                            <tr>
                                                <td>Laboratoriya:</td>
                                                <td>{{ $distribution->lab_hours }} soat</td>
                                            </tr>
                                            <tr>
                                                <td>Mustaqil ta'lim:</td>
                                                <td>{{ $distribution->independent_hours }} soat</td>
                                            </tr>
                                            @if($distribution->course_work_hours)
                                            <tr>
                                                <td>Kurs ishi:</td>
                                                <td>{{ $distribution->course_work_hours }} soat</td>
                                            </tr>
                                            @endif
                                            <tr class="table-active">
                                                <td><strong>Auditoriya soatlari:</strong></td>
                                                <td><strong>{{ $distribution->total_auditory_hours }} ({{ $distribution->auditory_percent }}%)</strong></td>
                                            </tr>
                                        </table>
                                        
                                        @if(!$distribution->isValid())
                                            <div class="alert alert-warning mt-2 mb-0">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Auditoriya soatlari 50% dan oshib ketgan!
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Hozircha hech qanday taqsimot mavjud emas. 
                            Chap tomondagi formadan foydalanib yangi taqsimot yarating.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('distributionForm');
    const totalRequiredHours = {{ $subject->total_hours }};
    const maxAuditoryHours = Math.floor(totalRequiredHours * 0.5);
    
    const auditoryInputs = document.querySelectorAll('.auditory-hours');
    const independentInput = document.querySelector('.independent-hours');
    const courseWorkInput = document.querySelector('input[name="course_work_hours"]');
    
    const auditoryTotal = document.getElementById('auditoryTotal');
    const independentTotal = document.getElementById('independentTotal');
    const totalHours = document.getElementById('totalHours');
    const auditoryProgress = document.getElementById('auditoryProgress');
    const independentProgress = document.getElementById('independentProgress');
    const validationMessage = document.getElementById('validationMessage');
    
    function updateCalculations() {
        let auditorySum = 0;
        auditoryInputs.forEach(input => {
            auditorySum += parseInt(input.value) || 0;
        });
        
        const independentSum = parseInt(independentInput.value) || 0;
        const courseWorkSum = parseInt(courseWorkInput.value) || 0;
        const total = auditorySum + independentSum + courseWorkSum;
        
        auditoryTotal.textContent = auditorySum;
        independentTotal.textContent = independentSum + courseWorkSum;
        totalHours.textContent = total;
        
        const auditoryPercent = total > 0 ? Math.round((auditorySum / total) * 100) : 0;
        const independentPercent = total > 0 ? Math.round(((independentSum + courseWorkSum) / total) * 100) : 0;
        
        auditoryProgress.style.width = auditoryPercent + '%';
        auditoryProgress.textContent = 'Auditoriya ' + auditoryPercent + '%';
        
        independentProgress.style.width = independentPercent + '%';
        independentProgress.textContent = 'Mustaqil ' + independentPercent + '%';
        
        // Validation
        validationMessage.innerHTML = '';
        validationMessage.className = 'mt-2';
        
        if (total !== totalRequiredHours) {
            validationMessage.innerHTML = `<div class="alert alert-danger mb-0">
                <i class="fas fa-exclamation-circle"></i> 
                Jami soatlar ${totalRequiredHours} ga teng bo'lishi kerak. Hozir: ${total}
            </div>`;
        } else if (auditorySum > maxAuditoryHours) {
            validationMessage.innerHTML = `<div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle"></i> 
                Auditoriya soatlari ${maxAuditoryHours} dan oshmasligi kerak. Hozir: ${auditorySum}
            </div>`;
        } else {
            validationMessage.innerHTML = `<div class="alert alert-success mb-0">
                <i class="fas fa-check-circle"></i> 
                Taqsimot to'g'ri!
            </div>`;
        }
    }
    
    auditoryInputs.forEach(input => {
        input.addEventListener('input', updateCalculations);
    });
    independentInput.addEventListener('input', updateCalculations);
    courseWorkInput.addEventListener('input', updateCalculations);
    
    updateCalculations();
    
    form.addEventListener('submit', function(e) {
        let auditorySum = 0;
        auditoryInputs.forEach(input => {
            auditorySum += parseInt(input.value) || 0;
        });
        
        const independentSum = parseInt(independentInput.value) || 0;
        const courseWorkSum = parseInt(courseWorkInput.value) || 0;
        const total = auditorySum + independentSum + courseWorkSum;
        
        if (total !== totalRequiredHours) {
            e.preventDefault();
            alert('Jami soatlar ' + totalRequiredHours + ' ga teng bo\'lishi kerak!');
            return false;
        }
        
        // Faqat ogohlantirish, to'xtatmaslik
        if (auditorySum > maxAuditoryHours) {
            if (!confirm('Diqqat! Auditoriya soatlari tavsiya etilgan ' + maxAuditoryHours + ' soatdan oshib ketdi (' + auditorySum + ' soat). Davom etishni xohlaysizmi?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endpush
@endsection