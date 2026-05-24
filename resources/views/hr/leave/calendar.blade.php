@extends('layouts.dashboard-new')

@section('title', 'Ta\'til kalendari')
@section('page-title', 'Ta\'til kalendari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-calendar me-2"></i>Ta'til kalendari</h4>
                            <p class="mb-0 opacity-75">Tasdiqlangan ta'tillarni kalendar ko'rinishida ko'rish</p>
                        </div>
                        <a href="{{ route('hr.leave.requests') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Arizalar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kalendar -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Legendlar -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <h6 class="mb-3">Belgilar:</h6>
            <div class="d-flex gap-4 flex-wrap">
                <span><i class="fas fa-circle text-success me-1"></i> Tasdiqlangan ta'til</span>
                <span><i class="fas fa-circle text-warning me-1"></i> Kutilmoqda</span>
                <span><i class="fas fa-circle text-danger me-1"></i> Rad etilgan</span>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var events = @json($approvedLeaves->map(function($leave) {
        return [
            'title' => ($leave->employee?->last_name ?? '') . ' ' . ($leave->employee?->first_name ?? ''),
            'start' => $leave->start_date?->format('Y-m-d'),
            'end' => $leave->end_date?->addDay()->format('Y-m-d'),
            'backgroundColor' => '#198754',
            'borderColor' => '#198754'
        ];
    }));

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'uz',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        events: events,
        height: 'auto'
    });

    calendar.render();
});
</script>
@endpush

<style>
.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
</style>
@endsection
