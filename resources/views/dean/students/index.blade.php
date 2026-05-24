@extends('layouts.dashboard-new')

@section('title', 'Talabalar ro\'yxati')
@section('page-title', 'Talabalar ro\'yxati')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-user-graduate me-2"></i>Talabalar ro'yxati</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }} talabalari</p>
                        </div>
                        <a href="{{ route('dean.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtrlar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('dean.students.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Qidiruv</label>
                    <input type="text" name="search" class="form-control" placeholder="Ism, familiya, ID..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Guruh</label>
                    <select name="group_id" class="form-select">
                        <option value="">Barchasi</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kurs</label>
                    <select name="course" class="form-select">
                        <option value="">Barchasi</option>
                        @for($i = 1; $i <= 4; $i++)
                        <option value="{{ $i }}" {{ request('course') == $i ? 'selected' : '' }}>{{ $i }}-kurs</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="academic_leave" {{ request('status') == 'academic_leave' ? 'selected' : '' }}>Akademik ta'til</option>
                        <option value="expelled" {{ request('status') == 'expelled' ? 'selected' : '' }}>Chetlatilgan</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Qidirish
                    </button>
                    <a href="{{ route('dean.students.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Talabalar jadvali -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">ID</th>
                            <th class="border-0">Talaba</th>
                            <th class="border-0">Guruh</th>
                            <th class="border-0">Kurs</th>
                            <th class="border-0">GPA</th>
                            <th class="border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td><span class="badge bg-light text-dark">{{ $student->student_id }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $student->last_name }} {{ $student->first_name }}</h6>
                                        <small class="text-muted">{{ $student->specialty?->name_uz ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-success">{{ $student->group?->name ?? '-' }}</span></td>
                            <td>{{ $student->course }}-kurs</td>
                            <td>
                                @if($student->gpa)
                                    <span class="badge {{ $student->gpa >= 3.5 ? 'bg-success' : ($student->gpa >= 2.5 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ number_format($student->gpa, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($student->status == 'active')
                                    <span class="badge bg-success">Faol</span>
                                @elseif($student->status == 'academic_leave')
                                    <span class="badge bg-warning">Akademik ta'til</span>
                                @else
                                    <span class="badge bg-danger">Chetlatilgan</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-user-graduate fa-3x text-muted mb-3 d-block opacity-50"></i>
                                <p class="text-muted mb-0">Talabalar topilmadi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($students->hasPages())
        <div class="card-footer bg-white">
            {{ $students->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.avatar-sm { width: 40px; height: 40px; }
</style>
@endsection
