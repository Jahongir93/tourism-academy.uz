@extends('layouts.dashboard-new')

@section('title', 'Nomzodlar')
@section('page-title', 'Nomzodlar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Xodimlar</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vacancies.index') }}">Vakansiyalar</a></li>
                    <li class="breadcrumb-item active">Nomzodlar</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-users text-info me-2"></i>
                Nomzodlar (Arizalar)
            </h1>
        </div>
        <div>
            <a href="{{ route('admin.vacancy-applications.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Export
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body text-center py-3">
                    <div class="h4 mb-0">{{ $stats['total'] }}</div>
                    <small class="text-muted">Jami</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body text-center py-3">
                    <div class="h4 mb-0 text-info">{{ $stats['new'] }}</div>
                    <small class="text-muted">Yangi</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body text-center py-3">
                    <div class="h4 mb-0 text-primary">{{ $stats['reviewed'] }}</div>
                    <small class="text-muted">Ko'rilgan</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                <div class="card-body text-center py-3">
                    <div class="h4 mb-0 text-warning">{{ $stats['shortlisted'] }}</div>
                    <small class="text-muted">Tanlangan</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center py-3">
                    <div class="h4 mb-0 text-success">{{ $stats['hired'] }}</div>
                    <small class="text-muted">Qabul</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="F.I.O, Email, Telefon..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="vacancy_id" class="form-select">
                        <option value="">Barcha vakansiyalar</option>
                        @foreach($vacancies as $vacancy)
                            <option value="{{ $vacancy->id }}" {{ request('vacancy_id') == $vacancy->id ? 'selected' : '' }}>
                                {{ $vacancy->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Barcha holatlar</option>
                        @foreach($statuses as $key => $status)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $status['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" placeholder="Sanadan"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" placeholder="Sanagacha"
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($applications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Nomzod</th>
                                <th>Vakansiya</th>
                                <th>Aloqa</th>
                                <th>Ma'lumoti</th>
                                <th>Holat</th>
                                <th>Sana</th>
                                <th width="100">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                <tr class="{{ $application->status === 'new' ? 'table-info' : '' }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input application-checkbox"
                                               value="{{ $application->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($application->photo_url)
                                                <img src="{{ $application->photo_url }}" alt="" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $application->full_name }}</strong>
                                                @if($application->experience_years)
                                                    <br><small class="text-muted">{{ $application->experience_years }} yil tajriba</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $application->vacancy->title ?? '-' }}</td>
                                    <td>
                                        <a href="mailto:{{ $application->email }}" class="text-decoration-none">{{ $application->email }}</a>
                                        <br><a href="tel:{{ $application->phone }}" class="text-muted">{{ $application->phone }}</a>
                                    </td>
                                    <td>{{ $application->education_level_label ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $application->status_color }}">
                                            <i class="fas {{ $application->status_icon }} me-1"></i>
                                            {{ $application->status_label }}
                                        </span>
                                    </td>
                                    <td>{{ $application->created_at->format('d.m.Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.vacancy-applications.show', $application) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <select id="bulkStatus" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Holatni o'zgartirish</option>
                            @foreach($statuses as $key => $status)
                                <option value="{{ $key }}">{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="bulkUpdateBtn" class="btn btn-sm btn-outline-primary" disabled>
                            Qo'llash
                        </button>
                    </div>
                    {{ $applications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h5>Arizalar topilmadi</h5>
                    <p class="text-muted">Hali nomzodlar ariza topshirmagan</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Update Form -->
<form id="bulkUpdateForm" action="{{ route('admin.vacancy-applications.bulk-update-status') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="status" id="bulkStatusInput">
    <div id="bulkIdsContainer"></div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.application-checkbox');
    const bulkUpdateBtn = document.getElementById('bulkUpdateBtn');
    const bulkStatus = document.getElementById('bulkStatus');

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkButton();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkButton);
    });

    function updateBulkButton() {
        const checked = document.querySelectorAll('.application-checkbox:checked');
        bulkUpdateBtn.disabled = checked.length === 0 || !bulkStatus.value;
    }

    bulkStatus.addEventListener('change', updateBulkButton);

    bulkUpdateBtn.addEventListener('click', function() {
        const checked = document.querySelectorAll('.application-checkbox:checked');
        if (checked.length === 0) return;

        if (confirm(checked.length + ' ta ariza holatini o\'zgartirmoqchimisiz?')) {
            const form = document.getElementById('bulkUpdateForm');
            const container = document.getElementById('bulkIdsContainer');
            document.getElementById('bulkStatusInput').value = bulkStatus.value;

            container.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });

            form.submit();
        }
    });
});
</script>
@endpush
@endsection
