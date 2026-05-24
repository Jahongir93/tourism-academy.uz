@extends('layouts.dashboard-new')

@section('title', 'Stipendiatlar')
@section('page-title', 'Stipendiatlar ro\'yxati')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-award me-2"></i>Stipendiatlar</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }}</p>
                        </div>
                        <a href="{{ route('dean.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">#</th>
                            <th class="border-0">Talaba</th>
                            <th class="border-0">Guruh</th>
                            <th class="border-0">GPA</th>
                            <th class="border-0">Stipendiya turi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scholars as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-success bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-award text-success"></i>
                                    </div>
                                    <h6 class="mb-0">{{ $student->last_name }} {{ $student->first_name }}</h6>
                                </div>
                            </td>
                            <td><span class="badge bg-primary">{{ $student->group?->name ?? '-' }}</span></td>
                            <td><span class="badge bg-success">{{ number_format($student->gpa ?? 0, 2) }}</span></td>
                            <td>Davlat stipendiyasi</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-award fa-3x mb-3 d-block opacity-50"></i>
                                Stipendiatlar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($scholars->hasPages())
        <div class="card-footer bg-white">{{ $scholars->links() }}</div>
        @endif
    </div>
</div>

<style>
.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.avatar-sm { width: 40px; height: 40px; }
</style>
@endsection
