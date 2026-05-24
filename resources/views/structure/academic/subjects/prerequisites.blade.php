@extends('layouts.dashboard-new')

@section('title', "Oldindan o'rganilishi kerak bo'lgan fanlar - " . $subject->name_uz)
@section('page-title', "Oldindan o'rganilishi kerak bo'lgan fanlar")

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('structure.academic.subjects.index') }}">Fanlar</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('structure.academic.subjects.show', $subject) }}">{{ $subject->name_uz }}</a></li>
                    <li class="breadcrumb-item active">Oldindan o'rganilishi kerak bo'lgan fanlar</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Subject Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="text-primary">{{ $subject->code }} - {{ $subject->name_uz }}</h5>
                    <p class="mb-1"><strong>Kategoriya:</strong> {{ ucfirst($subject->category ?? 'umumiy') }}</p>
                    <p class="mb-1"><strong>Kredit:</strong> {{ $subject->credits ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('structure.academic.subjects.show', $subject) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Current Prerequisites -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Oldindan o'rganilishi kerak bo'lgan fanlar</h5>
                </div>
                <div class="card-body">
                    @if($subject->prerequisites && $subject->prerequisites->count() > 0)
                        <ul class="list-group" id="prerequisitesList">
                            @foreach($subject->prerequisites as $prerequisite)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $prerequisite->code }}</strong> - {{ $prerequisite->name_uz }}
                                        <br>
                                        <small class="text-muted">{{ $prerequisite->credits ?? 0 }} kredit</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="removePrerequisite({{ $prerequisite->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted text-center py-4">
                            Bu fan uchun oldindan o'rganilishi kerak bo'lgan fanlar belgilanmagan
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Subjects to Add -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Mavjud fanlar</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('structure.academic.subjects.updatePrerequisites', $subject) }}" method="POST" id="prerequisitesForm">
                        @csrf
                        
                        <!-- Search -->
                        <div class="mb-3">
                            <input type="text" class="form-control" id="searchSubjects" 
                                   placeholder="Fanlarni qidirish..." onkeyup="filterSubjects()">
                        </div>
                        
                        <!-- Subject List -->
                        <div style="max-height: 400px; overflow-y: auto;">
                            <div class="list-group" id="availableSubjects">
                                @foreach($availableSubjects as $availableSubject)
                                    @if($availableSubject->id != $subject->id && !$subject->prerequisites->contains($availableSubject->id))
                                        <label class="list-group-item">
                                            <input type="checkbox" name="prerequisite_ids[]" 
                                                   value="{{ $availableSubject->id }}" class="form-check-input me-2">
                                            <strong>{{ $availableSubject->code }}</strong> - {{ $availableSubject->name_uz }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $availableSubject->credits ?? 0 }} kredit | 
                                                {{ ucfirst($availableSubject->category ?? 'umumiy') }}
                                            </small>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i> Tanlangan fanlarni qo'shish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Dependency Tree -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Bog'liqlik daraxti</h5>
        </div>
        <div class="card-body">
            <div id="dependencyTree">
                <div class="tree">
                    <ul>
                        <li>
                            <span class="badge bg-primary">{{ $subject->code }} - {{ $subject->name_uz }}</span>
                            @if($subject->prerequisites && $subject->prerequisites->count() > 0)
                                <ul>
                                    @foreach($subject->prerequisites as $prerequisite)
                                        <li>
                                            <span class="badge bg-secondary">
                                                {{ $prerequisite->code }} - {{ $prerequisite->name_uz }}
                                            </span>
                                            @if($prerequisite->prerequisites && $prerequisite->prerequisites->count() > 0)
                                                <ul>
                                                    @foreach($prerequisite->prerequisites as $subPrerequisite)
                                                        <li>
                                                            <span class="badge bg-light text-dark">
                                                                {{ $subPrerequisite->code }} - {{ $subPrerequisite->name_uz }}
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.tree ul {
    padding-top: 20px;
    position: relative;
    transition: all 0.5s;
}

.tree li {
    float: left;
    text-align: center;
    list-style-type: none;
    position: relative;
    padding: 20px 5px 0 5px;
    transition: all 0.5s;
}

.tree li::before, .tree li::after {
    content: '';
    position: absolute;
    top: 0;
    right: 50%;
    border-top: 1px solid #ccc;
    width: 50%;
    height: 20px;
}

.tree li::after {
    right: auto;
    left: 50%;
    border-left: 1px solid #ccc;
}

.tree li:only-child::after, .tree li:only-child::before {
    display: none;
}

.tree li:only-child {
    padding-top: 0;
}

.tree li:first-child::before, .tree li:last-child::after {
    border: 0 none;
}

.tree li:last-child::before {
    border-right: 1px solid #ccc;
    border-radius: 0 5px 0 0;
}

.tree li:first-child::after {
    border-radius: 5px 0 0 0;
}

.tree ul ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    border-left: 1px solid #ccc;
    width: 0;
    height: 20px;
}
</style>
@endpush

@push('scripts')
<script>
function filterSubjects() {
    const searchValue = document.getElementById('searchSubjects').value.toLowerCase();
    const subjects = document.querySelectorAll('#availableSubjects label');
    
    subjects.forEach(subject => {
        const text = subject.textContent.toLowerCase();
        if (text.includes(searchValue)) {
            subject.style.display = 'block';
        } else {
            subject.style.display = 'none';
        }
    });
}

function removePrerequisite(prerequisiteId) {
    if (confirm('Bu fanni oldindan o\'rganilishi kerak bo\'lgan fanlar ro\'yxatidan o\'chirishni xohlaysizmi?')) {
        // Create a form to submit the removal
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('structure.academic.subjects.updatePrerequisites', $subject) }}';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add action to remove
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'remove';
        form.appendChild(actionInput);
        
        // Add prerequisite ID to remove
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'remove_prerequisite_id';
        idInput.value = prerequisiteId;
        form.appendChild(idInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-submit form when checkboxes are changed
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="prerequisite_ids[]"]');
    let selectedCount = 0;
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            selectedCount = document.querySelectorAll('input[name="prerequisite_ids[]"]:checked').length;
            
            // Update button text
            const submitBtn = document.querySelector('button[type="submit"]');
            if (selectedCount > 0) {
                submitBtn.innerHTML = `<i class="fas fa-plus"></i> Tanlangan ${selectedCount} ta fanni qo'shish`;
                submitBtn.classList.remove('btn-success');
                submitBtn.classList.add('btn-primary');
            } else {
                submitBtn.innerHTML = '<i class="fas fa-plus"></i> Tanlangan fanlarni qo\'shish';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');
            }
        });
    });
});
</script>
@endpush
@endsection