@extends('layouts.dashboard-new')

@section('title', 'Fan ma\'lumotlari')
@section('page-title', 'Fan ma\'lumotlari')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary text-white shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-2">{{ $groupSubject->subject->name }}</h4>
                            <div class="d-flex gap-3 flex-wrap">
                                <span><i class="fas fa-users me-2"></i>{{ $groupSubject->group->name }}</span>
                                <span><i class="fas fa-door-open me-2"></i>{{ $groupSubject->room ?? 'N/A' }}</span>
                                <span><i class="fas fa-calendar me-2"></i>{{ $groupSubject->semester }}-semestr</span>
                                <span><i class="fas fa-book me-2"></i>{{ $groupSubject->subject->credits ?? 4 }} kredit</span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('teacher.subjects.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-2"></i>Orqaga
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-3">
        <div class="col-md-3 mb-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h3 class="mb-0 fw-bold">{{ $studentsCount }}</h3>
                    <small class="text-muted">Jami talabalar</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-male fa-2x text-info mb-2"></i>
                    <h3 class="mb-0 fw-bold">{{ $maleCount }}</h3>
                    <small class="text-muted">O'g'il bolalar</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-female fa-2x text-danger mb-2"></i>
                    <h3 class="mb-0 fw-bold">{{ $femaleCount }}</h3>
                    <small class="text-muted">Qizlar</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-star fa-2x text-warning mb-2"></i>
                    <h3 class="mb-0 fw-bold">{{ $avgGrade ? number_format($avgGrade, 1) : '-' }}</h3>
                    <small class="text-muted">O'rtacha ball</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-success">
                            <i class="fas fa-check-circle me-2"></i>Davomat kiritish
                        </button>
                        <button class="btn btn-warning">
                            <i class="fas fa-star me-2"></i>Baholar kiritish
                        </button>
                        <button class="btn btn-info">
                            <i class="fas fa-tasks me-2"></i>Topshiriq berish
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-file-upload me-2"></i>Material yuklash
                        </button>
                        <button class="btn btn-secondary">
                            <i class="fas fa-file-pdf me-2"></i>Hisobot olish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users text-primary me-2"></i>Talabalar ro'yxati</h5>
                <div>
                    <input type="search" class="form-control form-control-sm" placeholder="Qidirish..." id="searchStudent">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="studentsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>F.I.Sh</th>
                            <th>Talaba ID</th>
                            <th>Jinsi</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th>O'rtacha ball</th>
                            <th>Harakat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2">
                                            {{ strtoupper(substr($student->user->name ?? 'N', 0, 2)) }}
                                        </div>
                                        <strong>{{ $student->user->name ?? 'N/A' }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $student->student_id }}</span>
                                </td>
                                <td>
                                    @if($student->gender == 'male')
                                        <i class="fas fa-male text-info"></i> Erkak
                                    @else
                                        <i class="fas fa-female text-danger"></i> Ayol
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>{{ $student->user->phone ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope me-1"></i>{{ $student->user->email ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    @php
                                        $avgScore = $student->journalGrades->avg('score');
                                    @endphp
                                    @if($avgScore)
                                        <span class="badge bg-{{ $avgScore >= 86 ? 'success' : ($avgScore >= 71 ? 'primary' : ($avgScore >= 56 ? 'warning' : 'danger')) }}">
                                            {{ number_format($avgScore, 1) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" title="Batafsil">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-users-slash fa-3x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Talabalar topilmadi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple search functionality
    const searchInput = document.getElementById('searchStudent');
    const table = document.getElementById('studentsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const text = row.textContent.toLowerCase();

            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});
</script>
@endsection
