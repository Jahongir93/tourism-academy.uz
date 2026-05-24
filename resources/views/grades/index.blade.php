@extends('layouts.dashboard-new')

@section('title', 'Baholar - ' . ($groupSubject->subject->name_uz ?? 'HEMIS'))

@section('content')
@php
    $journal = $groupSubject ?? null;
@endphp
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 p-4 rounded-lg" style="background: linear-gradient(135deg, #0d4f3c 0%, #16a085 100%);">
        <div class="col-md-8">
            <h1 class="h2 text-white">Baholar jurnali</h1>
            <p class="text-white opacity-90">
                <strong>{{ $journal->subject->name_uz ?? 'Fan' }}</strong> |
                {{ $journal->studentGroup->name ?? 'Guruh' }} |
                {{ $journal->semester ?? '1' }}-semestr
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('journal.show', $journal->id) }}" class="btn text-white"
               style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-arrow-left me-1"></i> Orqaga
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Add Column Form -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('grades.addColumn', $journal->id) }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-plus-circle me-1"></i>Qo'shimcha ustun qo'shish
                    </label>
                    <input type="text" name="column_name" class="form-control"
                           placeholder="Masalan: Lab 1, Amaliy ish 1, Nazorat ishi" required>
                    <small class="text-muted">Bu ustun barcha talabalar uchun qo'shiladi</small>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-1"></i>Qo'shish
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <form action="{{ route('grades.store', $journal->id) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="min-width: 200px;">Talaba F.I.O</th>
                                <th style="width: 100px;">Joriy (30%)</th>
                                <th style="width: 100px;">Oraliq (30%)</th>
                                <th style="width: 100px;">Yakuniy (40%)</th>
                                @foreach($additionalColumns as $column)
                                    <th style="width: 100px;">{{ $column }}</th>
                                @endforeach
                                <th style="width: 100px;">Umumiy ball</th>
                                <th style="width: 80px;">Harf baho</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php
                                    $grade = $grades->get($student->id);
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $student->full_name }}</strong>
                                        <input type="hidden" name="grades[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="100"
                                               class="form-control form-control-sm"
                                               name="grades[{{ $loop->index }}][current_grade]"
                                               value="{{ $grade->current_grade ?? '' }}"
                                               placeholder="0-100">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="100"
                                               class="form-control form-control-sm"
                                               name="grades[{{ $loop->index }}][midterm_grade]"
                                               value="{{ $grade->midterm_grade ?? '' }}"
                                               placeholder="0-100">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="100"
                                               class="form-control form-control-sm"
                                               name="grades[{{ $loop->index }}][final_grade]"
                                               value="{{ $grade->final_grade ?? '' }}"
                                               placeholder="0-100">
                                    </td>
                                    @foreach($additionalColumns as $column)
                                        <td>
                                            <input type="number" step="0.01" min="0" max="100"
                                                   class="form-control form-control-sm"
                                                   name="grades[{{ $loop->parent->index }}][additional][{{ $column }}]"
                                                   value="{{ $grade && $grade->additional_grades ? ($grade->additional_grades[$column] ?? '') : '' }}"
                                                   placeholder="0-100">
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        <strong class="text-primary">{{ $grade->total_score ?? '-' }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($grade && $grade->letter_grade)
                                            <span class="badge
                                                @if($grade->letter_grade == 'A') bg-success
                                                @elseif($grade->letter_grade == 'B') bg-info
                                                @elseif($grade->letter_grade == 'C') bg-warning
                                                @elseif($grade->letter_grade == 'D') bg-secondary
                                                @else bg-danger
                                                @endif">
                                                {{ $grade->letter_grade }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        Guruhda talabalar yo'q
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->isNotEmpty())
                <div class="card-footer bg-light">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Baholarni saqlash
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

@endsection
