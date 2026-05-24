@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-user-graduate me-2"></i>
                        {{ $student->user->name }}
                    </h4>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-book me-2"></i>{{ $groupSubject->subject->name }}
                        <span class="ms-3"><i class="fas fa-users me-1"></i>{{ $groupSubject->group->name }}</span>
                        <span class="ms-3"><i class="fas fa-id-badge me-1"></i>{{ $student->student_id }}</span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('teacher.grades.show', $groupSubject->id) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Left Column: Statistics -->
        <div class="col-lg-4 mb-4">
            <!-- Student Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 2rem;">
                        {{ strtoupper(substr($student->user->name, 0, 2)) }}
                    </div>
                    <h5 class="mb-1">{{ $student->user->name }}</h5>
                    <p class="text-muted mb-2">{{ $student->user->email }}</p>
                    <span class="badge bg-secondary">{{ $student->student_id }}</span>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistika
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Total Average -->
                    <div class="text-center mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2">Umumiy o'rtacha ball</small>
                        @php
                            $totalAvg = $statistics['total_avg'];
                        @endphp
                        <h1 class="mb-2">
                            <span class="badge bg-{{ $totalAvg >= 86 ? 'success' : ($totalAvg >= 71 ? 'primary' : ($totalAvg >= 56 ? 'warning' : 'danger')) }}">
                                {{ $totalAvg ? number_format($totalAvg, 1) : '0.0' }}
                            </span>
                        </h1>
                        @if($totalAvg >= 86)
                            <span class="badge bg-success">A (5) - A'lo</span>
                        @elseif($totalAvg >= 71)
                            <span class="badge bg-primary">B (4) - Yaxshi</span>
                        @elseif($totalAvg >= 56)
                            <span class="badge bg-warning">C (3) - Qoniqarli</span>
                        @else
                            <span class="badge bg-danger">F (2) - Qoniqarsiz</span>
                        @endif
                    </div>

                    <!-- Joriy -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Joriy nazorat</span>
                            <span class="badge bg-info">{{ $statistics['joriy_count'] }} ta</span>
                        </div>
                        @if($statistics['joriy_avg'])
                        <h4 class="mb-2 text-info">{{ number_format($statistics['joriy_avg'], 1) }}</h4>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info"
                                 style="width: {{ $statistics['joriy_avg'] }}%">
                            </div>
                        </div>
                        @else
                        <p class="text-muted mb-0">Baholar yo'q</p>
                        @endif
                    </div>

                    <!-- Oraliq -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Oraliq nazorat</span>
                            <span class="badge bg-warning">{{ $statistics['oraliq_count'] }} ta</span>
                        </div>
                        @if($statistics['oraliq_avg'])
                        <h4 class="mb-2 text-warning">{{ number_format($statistics['oraliq_avg'], 1) }}</h4>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning"
                                 style="width: {{ $statistics['oraliq_avg'] }}%">
                            </div>
                        </div>
                        @else
                        <p class="text-muted mb-0">Baholar yo'q</p>
                        @endif
                    </div>

                    <!-- Yakuniy -->
                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Yakuniy nazorat</span>
                            <span class="badge bg-danger">{{ $statistics['yakuniy_count'] }} ta</span>
                        </div>
                        @if($statistics['yakuniy_avg'])
                        <h4 class="mb-2 text-danger">{{ number_format($statistics['yakuniy_avg'], 1) }}</h4>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger"
                                 style="width: {{ $statistics['yakuniy_avg'] }}%">
                            </div>
                        </div>
                        @else
                        <p class="text-muted mb-0">Baholar yo'q</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Grades List -->
        <div class="col-lg-8 mb-4">
            <!-- All Grades -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Barcha baholar ({{ $grades->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($grades->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4">#</th>
                                    <th>Sana</th>
                                    <th>Mavzu</th>
                                    <th>Nazorat turi</th>
                                    <th class="text-center">Ball</th>
                                    <th class="text-center">Baho</th>
                                    <th class="text-end px-4">Harakat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grades as $index => $grade)
                                <tr>
                                    <td class="px-4">{{ $index + 1 }}</td>
                                    <td>
                                        <div>
                                            <i class="fas fa-calendar text-muted me-1"></i>
                                            {{ $grade->graded_date ? \Carbon\Carbon::parse($grade->graded_date)->format('d.m.Y') : $grade->journalEntry->created_at->format('d.m.Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $grade->graded_date ? \Carbon\Carbon::parse($grade->graded_date)->format('H:i') : $grade->journalEntry->created_at->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong>{{ $grade->topic ?? 'Mavzu ko\'rsatilmagan' }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = $grade->grade_type == 'joriy' ? 'bg-info' : ($grade->grade_type == 'oraliq' ? 'bg-warning' : 'bg-danger');
                                            $typeLabel = $grade->grade_type == 'joriy' ? 'Joriy' : ($grade->grade_type == 'oraliq' ? 'Oraliq' : 'Yakuniy');
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $typeLabel }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($grade->score !== null)
                                        <span class="badge bg-{{ $grade->score >= 86 ? 'success' : ($grade->score >= 71 ? 'primary' : ($grade->score >= 56 ? 'warning' : 'danger')) }} fs-6">
                                            {{ number_format($grade->score, 1) }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($grade->score !== null)
                                            @if($grade->score >= 86)
                                                <span class="badge bg-success">A (5)</span>
                                            @elseif($grade->score >= 71)
                                                <span class="badge bg-primary">B (4)</span>
                                            @elseif($grade->score >= 56)
                                                <span class="badge bg-warning">C (3)</span>
                                            @else
                                                <span class="badge bg-danger">F (2)</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button"
                                                    class="btn btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editGradeModal"
                                                    onclick="editGrade({{ $grade->id }}, '{{ $grade->score }}', '{{ $grade->topic }}', '{{ $grade->grade_type }}')"
                                                    title="Tahrirlash">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-danger"
                                                    onclick="deleteGrade({{ $grade->id }})"
                                                    title="O'chirish">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Hali baholar yo'q</h5>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Grade Modal -->
<div class="modal fade" id="editGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editGradeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Bahoni tahrirlash
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ball (0-100)</label>
                        <input type="number"
                               name="score"
                               id="editScore"
                               class="form-control"
                               min="0"
                               max="100"
                               step="0.01"
                               required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mavzu</label>
                        <input type="text"
                               name="topic"
                               id="editTopic"
                               class="form-control"
                               placeholder="Dars mavzusi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nazorat turi</label>
                        <select name="grade_type" id="editGradeType" class="form-select" required>
                            <option value="joriy">Joriy nazorat</option>
                            <option value="oraliq">Oraliq nazorat</option>
                            <option value="yakuniy">Yakuniy nazorat</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<script>
function editGrade(id, score, topic, gradeType) {
    const form = document.getElementById('editGradeForm');
    form.action = `/teacher/grades/${id}/update`;

    document.getElementById('editScore').value = score;
    document.getElementById('editTopic').value = topic || '';
    document.getElementById('editGradeType').value = gradeType;
}

function deleteGrade(id) {
    if (confirm('Bu bahoni o\'chirishni xohlaysizmi?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/teacher/grades/${id}/delete`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
        `;

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
