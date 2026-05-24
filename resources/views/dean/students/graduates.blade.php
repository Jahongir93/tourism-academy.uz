@extends('layouts.dashboard-new')

@section('title', 'Bitiruvchilar')
@section('page-title', 'Bitiruvchilar ro\'yxati')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-graduation-cap me-2"></i>Bitiruvchilar</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }} 4-kurs talabalari</p>
                        </div>
                        <a href="{{ route('dean.students.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Talabalar
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
                            <th class="border-0">ID</th>
                            <th class="border-0">Talaba</th>
                            <th class="border-0">Guruh</th>
                            <th class="border-0">Yo'nalish</th>
                            <th class="border-0">GPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($graduates as $student)
                        <tr>
                            <td><span class="badge bg-light text-dark">{{ $student->student_id }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-success bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-graduation-cap text-success"></i>
                                    </div>
                                    <h6 class="mb-0">{{ $student->last_name }} {{ $student->first_name }}</h6>
                                </div>
                            </td>
                            <td><span class="badge bg-success">{{ $student->group?->name ?? '-' }}</span></td>
                            <td>{{ $student->specialty?->name_uz ?? '-' }}</td>
                            <td>
                                @if($student->gpa)
                                    <span class="badge {{ $student->gpa >= 4.5 ? 'bg-success' : 'bg-primary' }}">{{ number_format($student->gpa, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-graduation-cap fa-3x mb-3 d-block opacity-50"></i>
                                Bitiruvchilar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($graduates->hasPages())
        <div class="card-footer bg-white">{{ $graduates->links() }}</div>
        @endif
    </div>
</div>

<style>
.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.avatar-sm { width: 40px; height: 40px; }
</style>
@endsection
