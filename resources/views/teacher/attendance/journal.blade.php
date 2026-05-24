@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-book me-2"></i>
                        {{ $groupSubject->subject->name }}
                    </h4>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-users me-2"></i>{{ $groupSubject->group->name }}
                        <span class="ms-3"><i class="fas fa-door-open me-1"></i>{{ $groupSubject->room ?? 'N/A' }}</span>
                        <span class="ms-3"><i class="fas fa-calendar me-1"></i>{{ $groupSubject->semester }}-semestr</span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('teacher.attendance.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                    <a href="{{ route('teacher.attendance.create', $groupSubject->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-plus me-1"></i>Davomat qilish
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-users fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Talabalar</h6>
                            <h3 class="mb-0 fw-bold">{{ $students->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-chalkboard-teacher fa-lg text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Darslar</h6>
                            <h3 class="mb-0 fw-bold">{{ $journalEntries->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="fas fa-check-circle fa-lg text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Jami davomatlar</h6>
                            <h3 class="mb-0 fw-bold">
                                {{ $journalEntries->sum(function($entry) { return $entry->grades->count(); }) }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="fas fa-star fa-lg text-warning"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">O'rtacha ball</h6>
                            <h3 class="mb-0 fw-bold">
                                @php
                                    $allGrades = $journalEntries->flatMap(function($entry) { return $entry->grades; });
                                    $avgScore = $allGrades->whereNotNull('score')->avg('score');
                                @endphp
                                {{ $avgScore ? number_format($avgScore, 1) : '0.0' }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Journal Entries -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>
                Darslar tarixi
            </h5>
        </div>
        <div class="card-body p-0">
            @if($journalEntries->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">#</th>
                            <th>Sana</th>
                            <th>Mavzu</th>
                            <th>Dars turi</th>
                            <th>Ishtirok etdi</th>
                            <th>O'rtacha ball</th>
                            <th class="text-end px-4">Harakat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journalEntries as $index => $entry)
                        <tr>
                            <td class="px-4">{{ $journalEntries->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <span>{{ $entry->created_at->format('d.m.Y') }}</span>
                                </div>
                                <small class="text-muted">{{ $entry->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <strong>{{ $entry->grades->first()->topic ?? 'Mavzu ko\'rsatilmagan' }}</strong>
                            </td>
                            <td>
                                @php
                                    $gradeType = $entry->grades->first()->grade_type ?? 'joriy';
                                    $badgeClass = $gradeType == 'joriy' ? 'bg-info' : ($gradeType == 'oraliq' ? 'bg-warning' : 'bg-danger');
                                    $typeLabel = $gradeType == 'joriy' ? 'Joriy nazorat' : ($gradeType == 'oraliq' ? 'Oraliq nazorat' : 'Yakuniy nazorat');
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $typeLabel }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $entry->grades->count() }}</span>
                                <small class="text-muted">/ {{ $students->count() }}</small>
                            </td>
                            <td>
                                @php
                                    $avgScore = $entry->grades->whereNotNull('score')->avg('score');
                                @endphp
                                @if($avgScore)
                                <span class="badge bg-{{ $avgScore >= 86 ? 'success' : ($avgScore >= 71 ? 'primary' : ($avgScore >= 56 ? 'warning' : 'danger')) }} fs-6">
                                    {{ number_format($avgScore, 1) }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('teacher.attendance.show', $entry->id) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-3 border-top">
                {{ $journalEntries->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Hali darslar o'tilmagan</h5>
                <p class="text-muted mb-3">Birinchi davomatni qilish uchun tugmani bosing</p>
                <a href="{{ route('teacher.attendance.create', $groupSubject->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Davomat qilish
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
