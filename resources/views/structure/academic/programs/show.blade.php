@extends('layouts.dashboard-new')

@section('title', $program->name_uz)
@section('page-title', $program->name_uz)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('structure.academic.programs.index') }}">Ta'lim yo'nalishlari</a></li>
                    <li class="breadcrumb-item active">{{ $program->name_uz }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <div>
                    <a href="{{ route('structure.academic.programs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Orqaga
                    </a>
                </div>
                <div>
                    <a href="{{ route('structure.academic.programs.edit', $program) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                    <a href="{{ route('structure.academic.programs.curriculum', $program) }}" class="btn btn-info">
                        <i class="fas fa-book"></i> O'quv reja
                    </a>
                    <a href="{{ route('structure.academic.curriculum.builder', $program) }}" class="btn btn-primary">
                        <i class="fas fa-tools"></i> O'quv reja tuzish
                    </a>
                    <form action="{{ route('structure.academic.programs.destroy', $program) }}" method="POST" class="d-inline" 
                          onsubmit="return confirm('Bu yo\'nalishni o\'chirishni xohlaysizmi?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> O'chirish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Asosiy ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Yo'nalish kodi</label>
                            <p class="mb-0"><strong>{{ $program->code }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Holati</label>
                            <p class="mb-0">
                                @if($program->active ?? true)
                                    <span class="badge bg-success">Faol</span>
                                @else
                                    <span class="badge bg-danger">Nofaol</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">Yo'nalish nomi (O'zbek)</label>
                            <p class="mb-0"><strong>{{ $program->name_uz }}</strong></p>
                        </div>
                    </div>
                    
                    @if($program->name_ru)
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="text-muted small">Yo'nalish nomi (Rus)</label>
                                <p class="mb-0">{{ $program->name_ru }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($program->name_en)
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="text-muted small">Yo'nalish nomi (Ingliz)</label>
                                <p class="mb-0">{{ $program->name_en }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($program->description)
                        <div class="row">
                            <div class="col-md-12">
                                <label class="text-muted small">Tavsif</label>
                                <p class="mb-0">{{ $program->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Academic Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Ta'lim ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Fakultet</label>
                            <p class="mb-0">
                                @if($program->faculty)
                                    <a href="{{ route('structure.faculties.show', $program->faculty) }}">
                                        {{ $program->faculty->name_uz }}
                                    </a>
                                @else
                                    <span class="text-muted">Belgilanmagan</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Kafedra</label>
                            <p class="mb-0">
                                @if($program->department)
                                    <a href="{{ route('structure.departments.show', $program->department) }}">
                                        {{ $program->department->name_uz }}
                                    </a>
                                @else
                                    <span class="text-muted">Belgilanmagan</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Daraja</label>
                            <p class="mb-0">
                                <span class="badge bg-info">
                                    {{ ucfirst($program->degree ?? $program->level ?? 'bachelor') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Ta'lim shakli</label>
                            <p class="mb-0">
                                <span class="badge bg-secondary">
                                    {{ ucfirst($program->education_form ?? 'kunduzgi') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Ta'lim tili</label>
                            <p class="mb-0">
                                <span class="badge bg-warning text-dark">
                                    O'zbek
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">O'quv muddati</label>
                            <p class="mb-0"><strong>{{ $program->duration_years ?? 4 }}</strong> yil</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Jami kredit</label>
                            <p class="mb-0"><strong>{{ $program->total_credits ?? 240 }}</strong> kredit</p>
                        </div>
                    </div>
                    
                    @if($program->qualification)
                        <div class="row">
                            <div class="col-md-12">
                                <label class="text-muted small">Kvalifikatsiya</label>
                                <p class="mb-0">{{ $program->qualification }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics and Quick Actions -->
        <div class="col-md-4">
            <!-- Statistics -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Statistika</h5>
                </div>
                <div class="card-body">
                    @if($program->id < 10000)
                    <div class="d-flex justify-content-between mb-2">
                        <span>O'quv rejadagi fanlar:</span>
                        <strong>{{ $program->curricula()->distinct('subject_id')->count() }}</strong>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span>Semestrlar soni:</span>
                        <strong>{{ ($program->duration_years ?? 4) * 2 }}</strong>
                    </div>
                    @if($program->id < 10000)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Majburiy fanlar:</span>
                        <strong>{{ $program->curricula()->where('subject_type', 'majburiy')->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Tanlov fanlar:</span>
                        <strong>{{ $program->curricula()->where('subject_type', 'tanlov')->count() }}</strong>
                    </div>
                    @else
                    <div class="d-flex justify-content-between mb-2">
                        <span>Jami kredit:</span>
                        <strong>{{ $program->total_credits ?? 0 }}</strong>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Tezkor amallar</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('structure.academic.curriculum.topics') }}?program_id={{ $program->id }}" 
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-list me-2"></i> Mavzularni boshqarish
                    </a>
                    <a href="{{ route('structure.academic.curriculum.export', $program) }}" 
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-download me-2"></i> O'quv rejani eksport qilish
                    </a>
                    <a href="{{ route('structure.academic.curriculum.import') }}?program_id={{ $program->id }}" 
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-upload me-2"></i> O'quv rejani import qilish
                    </a>
                    <button type="button" class="list-group-item list-group-item-action" 
                            onclick="copyProgram({{ $program->id }})">
                        <i class="fas fa-copy me-2"></i> Yo'nalishni nusxalash
                    </button>
                </div>
            </div>

            <!-- Recent Changes -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">So'nggi o'zgarishlar</h5>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt me-1"></i> Yaratilgan:
                        {{ $program->created_at?->format('d.m.Y H:i') ?? 'N/A' }}
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-edit me-1"></i> Yangilangan:
                        {{ $program->updated_at?->format('d.m.Y H:i') ?? 'N/A' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Curriculum Preview -->
    @if($program->id < 10000 && $program->curricula()->count() > 0)
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Joriy o'quv reja ({{ $currentYear ?? date('Y') . '-' . (date('Y') + 1) }})</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Semestr</th>
                                <th>Fan nomi</th>
                                <th>Fan kodi</th>
                                <th>Kredit</th>
                                <th>Jami soat</th>
                                <th>Fan turi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($program->getCurrentCurriculum()->take(10) as $curriculum)
                                <tr>
                                    <td>{{ $curriculum->semester_number }}</td>
                                    <td>{{ $curriculum->subject->name_uz ?? 'N/A' }}</td>
                                    <td>{{ $curriculum->subject->code ?? 'N/A' }}</td>
                                    <td>{{ $curriculum->credits }}</td>
                                    <td>{{ $curriculum->total_hours }}</td>
                                    <td>
                                        <span class="badge {{ $curriculum->subject_type == 'majburiy' ? 'bg-primary' : 'bg-warning' }}">
                                            {{ ucfirst($curriculum->subject_type) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($program->curricula()->count() > 10)
                    <div class="text-center mt-3">
                        <a href="{{ route('structure.academic.programs.curriculum', $program) }}" class="btn btn-primary">
                            To'liq o'quv rejani ko'rish
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Copy Program Modal -->
<div class="modal fade" id="copyProgramModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="copyProgramForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Ta'lim yo'nalishini nusxalash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Yangi yo'nalish kodi</label>
                        <input type="text" name="new_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yangi yo'nalish nomi</label>
                        <input type="text" name="new_name" class="form-control" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="copy_curriculum" class="form-check-input" id="copyCurriculum" value="1">
                        <label class="form-check-label" for="copyCurriculum">
                            O'quv rejani ham nusxalash
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Nusxalash</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyProgram(programId) {
    const modal = new bootstrap.Modal(document.getElementById('copyProgramModal'));
    const form = document.getElementById('copyProgramForm');
    form.action = `/structure/academic/programs/${programId}/copy`;
    modal.show();
}
</script>
@endpush
@endsection