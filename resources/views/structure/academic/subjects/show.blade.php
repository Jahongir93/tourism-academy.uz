@extends('layouts.dashboard-new')

@section('title', $subject->name_uz)
@section('page-title', $subject->name_uz)

@section('content')
<div class="container-fluid">
    <!-- Subject Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <h4 class="mb-0 me-3">{{ $subject->code }}</h4>
                        <span class="badge bg-{{ $subject->subject_type == 'majburiy' ? 'primary' : 'info' }} me-2">
                            {{ $subject->subject_type_text }}
                        </span>
                        @if($subject->active)
                            <span class="badge bg-success">Faol</span>
                        @else
                            <span class="badge bg-danger">Nofaol</span>
                        @endif
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <th width="200">Fan nomi (O'zbekcha):</th>
                            <td>{{ $subject->name_uz }}</td>
                        </tr>
                        @if($subject->name_ru)
                        <tr>
                            <th>Fan nomi (Ruscha):</th>
                            <td>{{ $subject->name_ru }}</td>
                        </tr>
                        @endif
                        @if($subject->name_en)
                        <tr>
                            <th>Fan nomi (Inglizcha):</th>
                            <td>{{ $subject->name_en }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Kafedra:</th>
                            <td>{{ $subject->department->name ?? 'Belgilanmagan' }}</td>
                        </tr>
                        <tr>
                            <th>Kreditlar:</th>
                            <td><span class="badge bg-primary">{{ $subject->credits }} kredit</span></td>
                        </tr>
                        <tr>
                            <th>Jami soatlar:</th>
                            <td><span class="badge bg-secondary">{{ $subject->total_hours }} soat</span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Amallar</h6>
                            <div class="d-grid gap-2">
                                <a href="{{ route('structure.academic.subjects.edit', $subject) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Tahrirlash
                                </a>
                                <a href="{{ route('structure.academic.subjects.prerequisites', $subject) }}" class="btn btn-info">
                                    <i class="fas fa-link me-1"></i> Bog'liqliklar
                                </a>
                                <a href="{{ route('structure.academic.hours.distribution', $subject) }}" class="btn btn-primary">
                                    <i class="fas fa-clock me-1"></i> Soatlar taqsimoti
                                </a>
                                <a href="{{ route('subjects.topics.index', $subject) }}" class="btn btn-success">
                                    <i class="fas fa-list me-1"></i> Fan mavzulari
                                </a>
                                <form action="{{ route('structure.academic.subjects.destroy', $subject) }}" method="POST" 
                                      onsubmit="return confirm('Fanni o\'chirishni xohlaysizmi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash me-1"></i> O'chirish
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Details Tabs -->
    <div class="card shadow-sm">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#description">Tavsif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#prerequisites">Oldindan talab qilinadigan fanlar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#programs">Ta'lim yo'nalishlari</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#hours">Soatlar taqsimoti</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <h5>Fan tavsifi</h5>
                            <p class="text-muted">{{ $subject->description ?: 'Tavsif kiritilmagan' }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Fan maqsadlari</h5>
                            <p class="text-muted">{{ $subject->objectives ?: 'Maqsadlar kiritilmagan' }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Kutilayotgan natijalar</h5>
                            <p class="text-muted">{{ $subject->outcomes ?: 'Natijalar kiritilmagan' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Prerequisites Tab -->
                <div class="tab-pane fade" id="prerequisites">
                    <h5 class="mb-3">Oldindan o'tilishi kerak bo'lgan fanlar</h5>
                    @if($subject->prerequisiteSubjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fan kodi</th>
                                        <th>Fan nomi</th>
                                        <th>Kafedra</th>
                                        <th>Kreditlar</th>
                                        <th>Turi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subject->prerequisiteSubjects as $prereq)
                                        <tr>
                                            <td>
                                                <a href="{{ route('structure.academic.subjects.show', $prereq) }}">
                                                    {{ $prereq->code }}
                                                </a>
                                            </td>
                                            <td>{{ $prereq->name_uz }}</td>
                                            <td>{{ $prereq->department->name ?? 'N/A' }}</td>
                                            <td>{{ $prereq->credits }} kr</td>
                                            <td>
                                                <span class="badge bg-{{ $prereq->pivot->type == 'required' ? 'danger' : 'warning' }}">
                                                    {{ $prereq->pivot->type == 'required' ? 'Majburiy' : 'Tavsiya etiladi' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Oldindan talab qilinadigan fanlar yo'q</p>
                    @endif
                    
                    <hr>
                    
                    <h5 class="mb-3">Bu fanni talab qiladigan fanlar</h5>
                    @if($subject->dependentSubjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fan kodi</th>
                                        <th>Fan nomi</th>
                                        <th>Kafedra</th>
                                        <th>Kreditlar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subject->dependentSubjects as $dependent)
                                        <tr>
                                            <td>
                                                <a href="{{ route('structure.academic.subjects.show', $dependent) }}">
                                                    {{ $dependent->code }}
                                                </a>
                                            </td>
                                            <td>{{ $dependent->name_uz }}</td>
                                            <td>{{ $dependent->department->name ?? 'N/A' }}</td>
                                            <td>{{ $dependent->credits }} kr</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Bu fanni talab qiladigan fanlar yo'q</p>
                    @endif
                </div>
                
                <!-- Programs Tab -->
                <div class="tab-pane fade" id="programs">
                    <h5 class="mb-3">Ushbu fan quyidagi ta'lim yo'nalishlarida o'qitiladi</h5>
                    @if($programs->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Yo'nalish kodi</th>
                                        <th>Yo'nalish nomi</th>
                                        <th>Fakultet</th>
                                        <th>Daraja</th>
                                        <th>Ta'lim shakli</th>
                                        <th>Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($programs as $program)
                                        <tr>
                                            <td>{{ $program->code }}</td>
                                            <td>{{ $program->name_uz }}</td>
                                            <td>{{ $program->faculty->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($program->level) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($program->education_form) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('structure.academic.programs.show', $program) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Hozircha hech qaysi yo'nalishga biriktirilmagan</p>
                    @endif
                </div>
                
                <!-- Hours Distribution Tab -->
                <div class="tab-pane fade" id="hours">
                    <h5 class="mb-3">Soatlar taqsimoti</h5>
                    @if($hourDistributions->count() > 0)
                        <div class="row">
                            @foreach($hourDistributions as $distribution)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            @if($distribution->program)
                                                <strong>{{ $distribution->program->name_uz }}</strong>
                                            @else
                                                <strong>Standart taqsimot</strong>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <div class="progress mb-3" style="height: 25px;">
                                                @php
                                                    $total = $distribution->total_hours;
                                                    $lecturePercent = $total > 0 ? ($distribution->lecture_hours / $total * 100) : 0;
                                                    $practicePercent = $total > 0 ? ($distribution->practice_hours / $total * 100) : 0;
                                                    $seminarPercent = $total > 0 ? ($distribution->seminar_hours / $total * 100) : 0;
                                                    $labPercent = $total > 0 ? ($distribution->lab_hours / $total * 100) : 0;
                                                    $independentPercent = $total > 0 ? ($distribution->independent_hours / $total * 100) : 0;
                                                @endphp
                                                <div class="progress-bar bg-primary" style="width: {{ $lecturePercent }}%">
                                                    {{ $distribution->lecture_hours }}s
                                                </div>
                                                <div class="progress-bar bg-info" style="width: {{ $practicePercent }}%">
                                                    {{ $distribution->practice_hours }}s
                                                </div>
                                                <div class="progress-bar bg-warning" style="width: {{ $seminarPercent }}%">
                                                    {{ $distribution->seminar_hours }}s
                                                </div>
                                                @if($distribution->lab_hours > 0)
                                                <div class="progress-bar bg-danger" style="width: {{ $labPercent }}%">
                                                    {{ $distribution->lab_hours }}s
                                                </div>
                                                @endif
                                                <div class="progress-bar bg-success" style="width: {{ $independentPercent }}%">
                                                    {{ $distribution->independent_hours }}s
                                                </div>
                                            </div>
                                            
                                            <ul class="list-unstyled small">
                                                <li><i class="fas fa-chalkboard-teacher text-primary"></i> Ma'ruza: {{ $distribution->lecture_hours }} soat</li>
                                                <li><i class="fas fa-users text-info"></i> Amaliyot: {{ $distribution->practice_hours }} soat</li>
                                                <li><i class="fas fa-comments text-warning"></i> Seminar: {{ $distribution->seminar_hours }} soat</li>
                                                <li><i class="fas fa-flask text-danger"></i> Laboratoriya: {{ $distribution->lab_hours }} soat</li>
                                                <li><i class="fas fa-book-reader text-success"></i> Mustaqil: {{ $distribution->independent_hours }} soat</li>
                                                @if($distribution->course_work_hours)
                                                <li><i class="fas fa-pen text-secondary"></i> Kurs ishi: {{ $distribution->course_work_hours }} soat</li>
                                                @endif
                                            </ul>
                                            
                                            <div class="text-muted small">
                                                Auditoriya: {{ $distribution->total_auditory_hours }} soat ({{ $distribution->auditory_percent }}%)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Soatlar taqsimoti belgilanmagan</p>
                        <a href="{{ route('structure.academic.hours.distribution', $subject) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Soatlar taqsimotini belgilash
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection