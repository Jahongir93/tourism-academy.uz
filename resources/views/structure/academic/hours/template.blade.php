@extends('layouts.dashboard-new')

@section('title', 'Soatlar taqsimoti shablonlari - HEMIS')
@section('page-title', "Soatlar taqsimoti shablonlari")

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Soatlar taqsimoti shablonlari</h5>
                    <p class="text-muted mb-0">Fanlar uchun soatlar taqsimotini tezkor belgilash uchun shablonlar</p>
                </div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                    <i class="fas fa-plus me-1"></i> Yangi shablon
                </button>
            </div>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="row">
        @forelse($templates as $template)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100 {{ $template->is_default ? 'border-primary' : '' }}">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                {{ $template->name }}
                                @if($template->is_default)
                                    <span class="badge bg-primary ms-2">Standart</span>
                                @endif
                            </h6>
                            <span class="badge bg-{{ $template->subject_type == 'majburiy' ? 'info' : 'secondary' }}">
                                {{ ucfirst($template->subject_type) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($template->description)
                            <p class="text-muted small mb-3">{{ $template->description }}</p>
                        @endif
                        
                        <div class="mb-3">
                            <div class="text-center mb-2">
                                <span class="badge bg-primary fs-6">{{ $template->credits }} kredit = {{ $template->credits * 30 }} soat</span>
                            </div>
                            <div class="progress" style="height: 30px;">
                                @php
                                    $total = $template->lecture_hours + $template->practice_hours + $template->seminar_hours + $template->lab_hours + $template->independent_hours;
                                    $lecturePercent = $total > 0 ? round(($template->lecture_hours / $total) * 100) : 0;
                                    $practicePercent = $total > 0 ? round(($template->practice_hours / $total) * 100) : 0;
                                    $seminarPercent = $total > 0 ? round(($template->seminar_hours / $total) * 100) : 0;
                                    $labPercent = $total > 0 ? round(($template->lab_hours / $total) * 100) : 0;
                                    $independentPercent = $total > 0 ? round(($template->independent_hours / $total) * 100) : 0;
                                @endphp
                                @if($template->lecture_hours > 0)
                                <div class="progress-bar bg-primary" style="width: {{ $lecturePercent }}%">
                                    {{ $template->lecture_hours }}s
                                </div>
                                @endif
                                @if($template->practice_hours > 0)
                                <div class="progress-bar bg-info" style="width: {{ $practicePercent }}%">
                                    {{ $template->practice_hours }}s
                                </div>
                                @endif
                                @if($template->seminar_hours > 0)
                                <div class="progress-bar bg-warning" style="width: {{ $seminarPercent }}%">
                                    {{ $template->seminar_hours }}s
                                </div>
                                @endif
                                @if($template->lab_hours > 0)
                                    <div class="progress-bar bg-danger" style="width: {{ $labPercent }}%">
                                        {{ $template->lab_hours }}s
                                    </div>
                                @endif
                                <div class="progress-bar bg-success" style="width: {{ $independentPercent }}%">
                                    {{ $template->independent_hours }}s
                                </div>
                            </div>
                        </div>
                        
                        <table class="table table-sm">
                            <tr>
                                <td><i class="fas fa-chalkboard-teacher text-primary"></i> Ma'ruza:</td>
                                <td class="text-end"><strong>{{ $template->lecture_hours }} soat</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-users text-info"></i> Amaliyot:</td>
                                <td class="text-end"><strong>{{ $template->practice_hours }} soat</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-comments text-warning"></i> Seminar:</td>
                                <td class="text-end"><strong>{{ $template->seminar_hours }} soat</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-flask text-danger"></i> Laboratoriya:</td>
                                <td class="text-end"><strong>{{ $template->lab_hours }} soat</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-book-reader text-success"></i> Mustaqil ta'lim:</td>
                                <td class="text-end"><strong>{{ $template->independent_hours }} soat</strong></td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>Auditoriya soatlari:</strong></td>
                                <td class="text-end">
                                    <strong>{{ $template->lecture_hours + $template->practice_hours + $template->seminar_hours + $template->lab_hours }} soat</strong>
                                </td>
                            </tr>
                        </table>
                        
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="editTemplate({{ $template->id }})">
                                <i class="fas fa-edit"></i> Tahrirlash
                            </button>
                            @if(!$template->is_default)
                                <form action="#" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Shablonni o\'chirmoqchimisiz?')">
                                        <i class="fas fa-trash"></i> O'chirish
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    Hozircha shablonlar mavjud emas. Yangi shablon yarating.
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Create Template Modal -->
<div class="modal fade" id="createTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('structure.academic.hours.saveTemplate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Yangi shablon yaratish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Shablon nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Fan turi <span class="text-danger">*</span></label>
                        <select name="subject_type" class="form-select" required>
                            <option value="majburiy">Majburiy</option>
                            <option value="tanlov">Tanlov</option>
                            <option value="umumkasbiy">Umumkasbiy</option>
                            <option value="mutaxassislik">Mutaxassislik</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-1"></i>
                        Foizlar yig'indisi 100% ga teng bo'lishi kerak
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ma'ruza (%)</label>
                            <input type="number" name="lecture_percent" class="form-control percent-input" min="0" max="100" value="30" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amaliyot (%)</label>
                            <input type="number" name="practice_percent" class="form-control percent-input" min="0" max="100" value="20" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Seminar (%)</label>
                            <input type="number" name="seminar_percent" class="form-control percent-input" min="0" max="100" value="10" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Laboratoriya (%)</label>
                            <input type="number" name="lab_percent" class="form-control percent-input" min="0" max="100" value="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mustaqil ta'lim (%)</label>
                            <input type="number" name="independent_percent" class="form-control percent-input" min="0" max="100" value="40" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jami</label>
                            <div class="form-control-plaintext">
                                <span id="total-percent" class="badge bg-success fs-6">100%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const percentInputs = document.querySelectorAll('.percent-input');
    const totalSpan = document.getElementById('total-percent');
    
    function updateTotal() {
        let total = 0;
        percentInputs.forEach(input => {
            total += parseInt(input.value) || 0;
        });
        
        totalSpan.textContent = total + '%';
        if (total === 100) {
            totalSpan.classList.remove('bg-danger');
            totalSpan.classList.add('bg-success');
        } else {
            totalSpan.classList.remove('bg-success');
            totalSpan.classList.add('bg-danger');
        }
    }
    
    percentInputs.forEach(input => {
        input.addEventListener('input', updateTotal);
    });
    
    updateTotal();
});

function editTemplate(id) {
    // Implementation for editing template
    alert('Tahrirlash funksiyasi tez orada qo\'shiladi');
}
</script>
@endpush
@endsection