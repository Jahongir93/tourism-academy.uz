@extends('layouts.dashboard-new')

@section('title', 'Elektron jurnal - HEMIS')
@section('page-title', 'Elektron jurnal')

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
            <h1 class="h2 text-white">Elektron jurnal tizimi</h1>
            <p class="text-white opacity-90">
                Dars jurnallari, davomat va baholarni boshqarish
            </p>
        </div>
        <div class="col-md-4 text-end">
            <span class="badge" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); font-size: 1rem; padding: 10px 15px;">
                <i class="fas fa-calendar-alt me-2"></i>
                {{ $currentSemester }}-semestr ({{ $currentYear->year ?? 'N/A' }})
            </span>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important;">
        <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                    <i class="fas fa-book" style="color: var(--secondary-green);"></i>
                    Jurnallar ro'yxati
                </h5>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="journalsTable">
                    <thead style="background: var(--very-light-green);">
                        <tr>
                            <th style="color: var(--text-dark); font-weight: 600;">№</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Fan</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Guruh</th>
                            <th style="color: var(--text-dark); font-weight: 600;">O'qituvchi</th>
                            <th style="color: var(--text-dark); font-weight: 600;">O'quv yili</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Semestr</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Holat</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($journals as $journal)
                        <tr onmouseover="this.style.background='var(--very-light-green)'" onmouseout="this.style.background='white'">
                            <td style="color: var(--text-dark);">{{ $loop->iteration }}</td>
                            <td>
                                <strong style="color: var(--text-dark);">{{ $journal->subject->name_uz ?? $journal->subject->name ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--light-green); color: var(--primary-dark-green);">
                                    {{ $journal->group->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="color: var(--text-dark);">
                                @if($journal->teacher)
                                    {{ $journal->teacher_name }}
                                @else
                                    <span class="badge bg-warning text-dark">Vakant</span>
                                @endif
                            </td>
                            <td style="color: var(--text-dark);">{{ $journal->academicYear->year ?? $journal->academicYear->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge" style="background: var(--accent-green); color: white;">
                                    {{ $journal->semester_id }}-semestr
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--secondary-green); color: white;">
                                    <i class="fas fa-check-circle"></i> Faol
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('journal.show', $journal) }}" class="btn btn-sm"
                                       style="border: 1px solid var(--secondary-green); color: var(--secondary-green);"
                                       onmouseover="this.style.background='var(--light-green)'"
                                       onmouseout="this.style.background='transparent'"
                                       title="Ko'rish">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('attendance.index', $journal) }}" class="btn btn-sm"
                                       style="border: 1px solid var(--accent-green); color: var(--accent-green);"
                                       onmouseover="this.style.background='var(--light-green)'"
                                       onmouseout="this.style.background='transparent'"
                                       title="Davomat">
                                        <i class="fas fa-user-check"></i>
                                    </a>
                                    <a href="{{ route('grades.index', $journal) }}" class="btn btn-sm"
                                       style="border: 1px solid var(--primary-dark-green); color: var(--primary-dark-green);"
                                       onmouseover="this.style.background='var(--light-green)'"
                                       onmouseout="this.style.background='transparent'"
                                       title="Baholar">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    <a href="{{ route('journal.analytics', $journal) }}" class="btn btn-sm"
                                       style="border: 1px solid var(--secondary-green); color: var(--secondary-green);"
                                       onmouseover="this.style.background='var(--light-green)'"
                                       onmouseout="this.style.background='transparent'"
                                       title="Tahlil">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    @can('update', $journal)
                                    <a href="{{ route('journal.edit', $journal) }}" class="btn btn-sm"
                                       style="border: 1px solid #6c757d; color: #6c757d;"
                                       onmouseover="this.style.background='#f0f0f0'"
                                       onmouseout="this.style.background='transparent'"
                                       title="Tahrirlash">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete', $journal)
                                    <form action="{{ route('journal.destroy', $journal) }}" method="POST" class="d-inline" onsubmit="return confirm('Haqiqatan ham o\'chirmoqchimisiz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm"
                                                style="border: 1px solid #dc3545; color: #dc3545;"
                                                onmouseover="this.style.background='#fef0f0'"
                                                onmouseout="this.style.background='transparent'"
                                                title="O'chirish">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div style="color: #7f8c8d;">
                                    <i class="fas fa-book-open fa-3x mb-3" style="color: var(--secondary-green);"></i>
                                    <p>Jurnallar topilmadi</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($journals) && $journals->hasPages())
            <div class="px-4 py-3" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                {{ $journals->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important; background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $journals->total() }}</h3>
                    <p class="mb-0 opacity-90">Jami jurnallar</p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between" style="background: rgba(255,255,255,0.1); border-top: 1px solid rgba(255,255,255,0.2);">
                    <a class="small text-white stretched-link" href="#">Batafsil</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important; background: linear-gradient(135deg, var(--secondary-green), var(--accent-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">0</h3>
                    <p class="mb-0 opacity-90">Bugungi darslar</p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between" style="background: rgba(255,255,255,0.1); border-top: 1px solid rgba(255,255,255,0.2);">
                    <a class="small text-white stretched-link" href="#">Batafsil</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: var(--light-green);">
                            <i class="fas fa-star" style="color: var(--primary-dark-green);"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: var(--primary-dark-green);">0</h3>
                    <p class="mb-0" style="color: #7f8c8d;">Baholangan talabalar</p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                    <a class="small" style="color: var(--secondary-green);" href="#">Batafsil</a>
                    <div class="small" style="color: var(--secondary-green);"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: #fef0f0;">
                            <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: #dc3545;">0</h3>
                    <p class="mb-0" style="color: #7f8c8d;">Qarzdor talabalar</p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                    <a class="small" style="color: var(--secondary-green);" href="#">Batafsil</a>
                    <div class="small" style="color: var(--secondary-green);"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#journalsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Uzbek.json'
        },
        pageLength: 25,
        order: [[0, 'asc']]
    });
});
</script>
@endpush
@endsection