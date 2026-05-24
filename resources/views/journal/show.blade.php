@extends('layouts.dashboard-new')

@section('title', 'Jurnal - ' . ($groupSubject->subject->name_uz ?? 'N/A'))
@section('page-title', 'Jurnal')

@section('styles')
<style>
    :root {
        --primary-dark-green: #0d4f3c;
        --secondary-green: #16a085;
        --light-green: #e8f5f0;
        --accent-green: #48c9b0;
        --border-green: #c3e6d8;
        --text-dark: #2c3e50;
        --hover-green: #0a3d2e;
        --very-light-green: #f0f9f6;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 p-4 rounded-lg" style="background: linear-gradient(135deg, var(--primary-dark-green) 0%, var(--secondary-green) 100%);">
        <div class="col-md-8">
            <h1 class="h2 text-white">{{ $groupSubject->subject->name_uz ?? 'N/A' }}</h1>
            <p class="text-white opacity-90 mb-2">
                <i class="fas fa-users me-2"></i>Guruh: <strong>{{ $groupSubject->studentGroup->name ?? 'N/A' }}</strong>
                <span class="mx-3">|</span>
                <i class="fas fa-calendar me-2"></i>{{ $groupSubject->academicYear->year ?? 'N/A' }} - {{ $groupSubject->semester }}-semestr
            </p>
            @if($groupSubject->teacher)
                <p class="text-white opacity-90 mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>O'qituvchi:
                    <strong>{{ $groupSubject->teacher->first_name }} {{ $groupSubject->teacher->last_name }}</strong>
                </p>
            @else
                <p class="text-white opacity-90 mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span class="badge bg-warning text-dark">O'qituvchi biriktirilmagan (Vakant)</span>
                </p>
            @endif
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('journal.index') }}" class="btn text-white"
               style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-arrow-left me-1"></i> Orqaga
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid var(--secondary-green) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Talabalar soni</h6>
                            <h3 class="mb-0" style="color: var(--primary-dark-green);">{{ $students->count() }}</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: var(--light-green);">
                            <i class="fas fa-users fa-2x" style="color: var(--secondary-green);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid var(--accent-green) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Darslar</h6>
                            <h3 class="mb-0" style="color: var(--primary-dark-green);">0</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: var(--light-green);">
                            <i class="fas fa-book-open fa-2x" style="color: var(--accent-green);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #f39c12 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">O'rtacha baho</h6>
                            <h3 class="mb-0" style="color: var(--primary-dark-green);">-</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: #fef5e7;">
                            <i class="fas fa-star fa-2x" style="color: #f39c12;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #e74c3c !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Davomat %</h6>
                            <h3 class="mb-0" style="color: var(--primary-dark-green);">-</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: #fadbd8;">
                            <i class="fas fa-user-check fa-2x" style="color: #e74c3c;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Talabalar ro'yxati -->
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important;">
        <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
            <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                <i class="fas fa-users" style="color: var(--secondary-green);"></i>
                Talabalar ro'yxati
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: var(--very-light-green);">
                        <tr>
                            <th style="color: var(--text-dark); font-weight: 600; width: 50px;">№</th>
                            <th style="color: var(--text-dark); font-weight: 600;">F.I.O</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Talaba ID</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Email</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Telefon</th>
                            <th style="color: var(--text-dark); font-weight: 600; text-align: center;">Holat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr onmouseover="this.style.background='var(--very-light-green)'" onmouseout="this.style.background='white'">
                            <td style="color: var(--text-dark);">{{ $loop->iteration }}</td>
                            <td>
                                <strong style="color: var(--text-dark);">{{ $student->full_name }}</strong>
                            </td>
                            <td style="color: var(--text-dark);">{{ $student->student_no ?? 'N/A' }}</td>
                            <td style="color: var(--text-dark);">{{ $student->email ?? 'N/A' }}</td>
                            <td style="color: var(--text-dark);">{{ $student->phone ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge" style="background: var(--secondary-green); color: white;">
                                    {{ $student->status ?? 'active' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color: var(--text-dark);">
                                <i class="fas fa-inbox fa-3x mb-3 d-block" style="color: var(--border-green);"></i>
                                Guruhda talabalar yo'q
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Davomat va Baholar tugmalari -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body text-center p-5">
                    <i class="fas fa-user-check fa-4x mb-3" style="color: var(--accent-green);"></i>
                    <h4 style="color: var(--text-dark);">Davomat</h4>
                    <p class="text-muted">Talabalar davomatini boshqarish</p>
                    <a href="#" class="btn btn-lg" style="background: var(--accent-green); color: white; border: none;"
                       onmouseover="this.style.background='var(--secondary-green)'"
                       onmouseout="this.style.background='var(--accent-green)'">
                        <i class="fas fa-clipboard-check me-2"></i> Davomatni ko'rish
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body text-center p-5">
                    <i class="fas fa-star fa-4x mb-3" style="color: #f39c12;"></i>
                    <h4 style="color: var(--text-dark);">Baholar</h4>
                    <p class="text-muted">Talabalar baholarini boshqarish</p>
                    <a href="#" class="btn btn-lg" style="background: #f39c12; color: white; border: none;"
                       onmouseover="this.style.background='#e67e22'"
                       onmouseout="this.style.background='#f39c12'">
                        <i class="fas fa-trophy me-2"></i> Baholarni ko'rish
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
