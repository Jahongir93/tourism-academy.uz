@extends('layouts.dashboard-new')

@section('title', 'Sozlamalar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Tizim sozlamalari</h2>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jami sozlamalar</div>
                    <div class="h5 mb-0">{{ $stats['total_settings'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">O'quv yillari</div>
                    <div class="h5 mb-0">{{ $stats['academic_years'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Joriy o'quv yili</div>
                    <div class="h5 mb-0">{{ $stats['current_year'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Joriy semestr</div>
                    <div class="h5 mb-0">{{ $stats['current_semester'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Categories -->
    <div class="row">
        <!-- General Settings -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-cog fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Umumiy sozlamalar</h5>
                        <p class="card-text text-muted small">Sayt nomi, logo, til va boshqa umumiy sozlamalar</p>
                        <a href="{{ route('settings.general') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-edit"></i> Sozlash
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Settings -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-graduation-cap fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Akademik sozlamalar</h5>
                        <p class="card-text text-muted small">O'quv yili, semestr, davomat va baho sozlamalari</p>
                        <a href="{{ route('settings.academic') }}" class="btn btn-success btn-sm mt-2">
                            <i class="fas fa-edit"></i> Sozlash
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finance Settings -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-money-bill-wave fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Moliya sozlamalari</h5>
                        <p class="card-text text-muted small">Valyuta, to'lov muddati va jarima sozlamalari</p>
                        <a href="{{ route('settings.finance') }}" class="btn btn-info btn-sm mt-2">
                            <i class="fas fa-edit"></i> Sozlash
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-server fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Tizim sozlamalari</h5>
                        <p class="card-text text-muted small">Zaxira nusxa, kesh va texnik ishlar rejimi</p>
                        <a href="{{ route('settings.system') }}" class="btn btn-warning btn-sm mt-2">
                            <i class="fas fa-edit"></i> Sozlash
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-shield-alt fa-3x text-danger mb-3"></i>
                        <h5 class="card-title">Xavfsizlik</h5>
                        <p class="card-text text-muted small">Parol, sessiya va kirish sozlamalari</p>
                        <a href="{{ route('settings.security') }}" class="btn btn-danger btn-sm mt-2">
                            <i class="fas fa-edit"></i> Sozlash
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Logs -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-left-dark shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-history fa-3x text-dark mb-3"></i>
                        <h5 class="card-title">Faoliyat jurnali</h5>
                        <p class="card-text text-muted small">Tizim faoliyati va o'zgarishlar tarixini ko'rish</p>
                        <a href="{{ route('settings.activity-logs') }}" class="btn btn-dark btn-sm mt-2">
                            <i class="fas fa-eye"></i> Ko'rish
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Integrations -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-plug fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Integratsiyalar</h5>
                        <p class="card-text text-muted small">HEMIS, Telegram Bot va boshqa tizimlar bilan integratsiya</p>
                        <a href="{{ route('settings.integrations') }}" class="btn btn-info btn-sm mt-2">
                            <i class="fas fa-cog"></i> Sozlash
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- OTP Settings -->
        @can('manage_settings')
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-mobile-alt fa-3x text-success mb-3"></i>
                        <h5 class="card-title">OTP Sozlamalari</h5>
                        <p class="card-text text-muted small">SMS va Email OTP tasdiqlash sozlamalari</p>
                        <a href="{{ route('admin.settings.otp.index') }}" class="btn btn-success btn-sm mt-2">
                            <i class="fas fa-cog"></i> Sozlash
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <!-- Recent Activity -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">So'nggi faoliyat</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Foydalanuvchi</th>
                            <th>Harakat</th>
                            <th>Tavsif</th>
                            <th>Sana</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentActivities as $activity)
                        <tr>
                            <td>{{ $activity->user->name ?? 'Sistema' }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $activity->action }}</span>
                            </td>
                            <td>{{ $activity->description }}</td>
                            <td>{{ $activity->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
