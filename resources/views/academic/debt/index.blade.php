@extends('layouts.dashboard-new')

@section('title', 'Akademik Qarzdorlik')
@section('page-title', 'Akademik Qarzdorlik')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <p class="text-muted">Talabalarning akademik qarzdorliklarini kuzatish va boshqarish</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-exclamation-triangle fs-3 text-danger"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Jami qarzdor talabalar</h6>
                            <h3 class="mb-0">{{ $stats['total_students'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-book fs-3 text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Jami qarzlar</h6>
                            <h3 class="mb-0">{{ $stats['total_debts'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-redo fs-3 text-info"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Qayta topshirish rejalashtirilgan</h6>
                            <h3 class="mb-0">{{ $stats['retakes_scheduled'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle fs-3 text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Yopilgan qarzlar (bu oy)</h6>
                            <h3 class="mb-0">{{ $stats['closed_this_month'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtrlash
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('academic.debt.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Fakultet</label>
                        <select name="faculty_id" class="form-select">
                            <option value="">Barchasi</option>
                            @if(isset($faculties))
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Guruh</label>
                        <select name="group_id" class="form-select">
                            <option value="">Barchasi</option>
                            @if(isset($groups))
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Semestr</label>
                        <select name="semester" class="form-select">
                            <option value="">Barchasi</option>
                            <option value="1" {{ request('semester') == '1' ? 'selected' : '' }}>1-semestr</option>
                            <option value="2" {{ request('semester') == '2' ? 'selected' : '' }}>2-semestr</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search me-2"></i>Qidirish
                            </button>
                            <a href="{{ route('academic.debt.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Debts Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Qarzdorliklar ro'yxati
            </h5>
            <div>
                <a href="{{ route('academic.debt.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-2"></i>Excel
                </a>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#notifyModal">
                    <i class="fas fa-bell me-2"></i>Xabarnoma yuborish
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Talaba</th>
                            <th>Guruh</th>
                            <th>Fan</th>
                            <th>Semestr</th>
                            <th>Baho</th>
                            <th>Qayta topshirish</th>
                            <th>Holat</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debts ?? [] as $debt)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div>
                                    <strong>{{ $debt->student->full_name ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $debt->student->student_id ?? '' }}</small>
                                </div>
                            </td>
                            <td>{{ $debt->student->group->name ?? 'N/A' }}</td>
                            <td>{{ $debt->subject->name ?? 'N/A' }}</td>
                            <td>{{ $debt->semester ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-danger">{{ $debt->grade ?? 'F' }}</span>
                            </td>
                            <td>
                                @if($debt->retake_date)
                                    <span class="badge bg-info">{{ $debt->retake_date }}</span>
                                @else
                                    <span class="badge bg-secondary">Rejalashtirilmagan</span>
                                @endif
                            </td>
                            <td>
                                @if($debt->status == 'active')
                                    <span class="badge bg-warning">Faol</span>
                                @elseif($debt->status == 'resolved')
                                    <span class="badge bg-success">Yopilgan</span>
                                @else
                                    <span class="badge bg-danger">Muddati o'tgan</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-primary" onclick="scheduleRetake({{ $debt->id }})">
                                        <i class="fas fa-calendar-plus"></i>
                                    </button>
                                    <a href="{{ route('academic.debt.student', $debt->student_id) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="fas fa-inbox fs-3 d-block mb-2"></i>
                                Hech qanday qarzdorlik topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($debts) && method_exists($debts, 'links'))
                <div class="mt-3">
                    {{ $debts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Notify Modal -->
<div class="modal fade" id="notifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xabarnoma yuborish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('academic.debt.notify') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kimga yuborish</label>
                        <select name="target" class="form-select" required>
                            <option value="all">Barcha qarzdor talabalar</option>
                            <option value="faculty">Fakultet bo'yicha</option>
                            <option value="group">Guruh bo'yicha</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Xabar matni</label>
                        <textarea name="message" class="form-control" rows="4" required>Hurmatli talaba, sizda akademik qarzlar mavjud. Iltimos, o'z vaqtida qayta topshiring.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                    <button type="submit" class="btn btn-primary">Yuborish</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function scheduleRetake(debtId) {
    // TODO: Implement retake scheduling modal
    alert('Qayta topshirish rejalashtirish funksiyasi');
}
</script>
@endpush
@endsection
