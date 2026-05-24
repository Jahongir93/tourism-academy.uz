@extends('layouts.dashboard-new')

@section('title', 'Faoliyat Loglari')
@section('page-title', 'Faoliyat Loglari')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Faoliyat Loglari</h5>
        </div>
        <div class="card-body p-4">
            @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Foydalanuvchi</th>
                            <th>Harakat</th>
                            <th>Tavsif</th>
                            <th>Sana</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->user?->name ?? 'System' }}</td>
                            <td><span class="badge bg-info">{{ $log->action }}</span></td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $logs->links() }}
            @else
            <p class="text-muted">Loglar topilmadi</p>
            @endif
        </div>
    </div>
</div>
@endsection
