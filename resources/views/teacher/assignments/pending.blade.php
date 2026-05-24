@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-clock me-2"></i>
                        Tekshirish kerak
                    </h4>
                    <p class="mb-0 opacity-75">Baholanmagan topshiriqlar ({{ $submissions->total() }})</p>
                </div>
                <div>
                    <a href="{{ route('teacher.assignments.index') }}" class="btn btn-light btn-sm">
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

    @if($submissions->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">#</th>
                            <th>Talaba</th>
                            <th>Topshiriq</th>
                            <th>Fan</th>
                            <th>Guruh</th>
                            <th>Topshirgan vaqti</th>
                            <th class="text-center px-4">Harakat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $index => $submission)
                        <tr>
                            <td class="px-4">{{ $submissions->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2" style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.8rem;">
                                        {{ strtoupper(substr($submission->student?->user?->name ?? $submission->student?->full_name ?? 'N/A', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $submission->student?->user?->name ?? $submission->student?->full_name ?? 'Noma\'lum' }}</div>
                                        <small class="text-muted">{{ $submission->student?->student_id ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong>{{ Str::limit($submission->assignment?->title ?? 'N/A', 40) }}</strong>
                                @if($submission->assignment?->deadline && $submission->assignment->deadline < now())
                                <br><small class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Muddati o'tgan</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $submission->assignment?->subject?->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $submission->student?->group?->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div>{{ $submission->submitted_at ? $submission->submitted_at->format('d.m.Y') : '-' }}</div>
                                <small class="text-muted">{{ $submission->submitted_at ? $submission->submitted_at->format('H:i') : '' }}</small>
                            </td>
                            <td class="text-center px-4">
                                <a href="{{ route('teacher.assignments.grade', $submission->id) }}"
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-star me-1"></i>Baholash
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top">
            {{ $submissions->links() }}
        </div>
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h5 class="text-success">Barcha topshiriqlar baholangan!</h5>
            <p class="text-muted">Tekshirish kerak bo'lgan topshiriqlar yo'q</p>
        </div>
    </div>
    @endif
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
