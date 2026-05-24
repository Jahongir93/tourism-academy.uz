@extends('layouts.dashboard-new')

@section('title', "O'quv rejalar - O'quv jarayoni - HEMIS")

@section('page-title', "O'quv rejalar")

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
            <h1 class="h2 text-white">O'quv rejalar</h1>
            <p class="text-white opacity-90">
                Joriy o'quv yili: <strong>{{ $currentYear ?? (date('Y') . '-' . (date('Y') + 1)) }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('structure.academic.curriculum.import') }}" class="btn text-white"
               style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-upload"></i> Import qilish
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <h6 class="mb-1" style="color: #7f8c8d;">Jami yo'nalishlar</h6>
                    <h3 class="mb-0" style="color: var(--text-dark);">{{ count($programs ?? []) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <h6 class="mb-1" style="color: #7f8c8d;">Tasdiqlangan rejalar</h6>
                    <h3 class="mb-0" style="color: var(--primary-dark-green);">{{ $programs->where('is_approved', true)->count() ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <h6 class="mb-1" style="color: #7f8c8d;">Tayyorlanmoqda</h6>
                    <h3 class="mb-0" style="color: var(--secondary-green);">{{ $programs->where('is_approved', false)->count() ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <h6 class="mb-1" style="color: #7f8c8d;">Jami kreditlar</h6>
                    <h3 class="mb-0" style="color: var(--accent-green);">{{ $programs->sum('total_credits') ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Programs Table -->
    <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
        <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
            <h5 class="mb-0" style="color: var(--text-dark);">
                <i class="fas fa-book-open" style="color: var(--secondary-green);"></i> Ta'lim yo'nalishlari bo'yicha o'quv rejalar
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: var(--very-light-green);">
                        <tr>
                            <th style="color: var(--text-dark); font-weight: 600;">Kod</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Ta'lim yo'nalishi</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Fakultet</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Daraja</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Ta'lim shakli</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Fanlar</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Kreditlar</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Status</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs ?? [] as $program)
                            <tr onmouseover="this.style.background='var(--very-light-green)'" onmouseout="this.style.background='white'">
                                <td>
                                    <span class="badge" style="background: var(--secondary-green); color: white;">
                                        {{ $program->code }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: var(--text-dark);">{{ $program->name_uz }}</strong>
                                    @if($program->name_ru)
                                        <br><small style="color: #7f8c8d;">{{ $program->name_ru }}</small>
                                    @endif
                                </td>
                                <td style="color: var(--text-dark);">
                                    {{ $program->faculty->short_name ?? $program->faculty->name ?? 'N/A' }}
                                </td>
                                <td>
                                    @php
                                        $levelColors = [
                                            'bakalavr' => ['bg' => '#e8f5f0', 'color' => '#0d4f3c'],
                                            'magistr' => ['bg' => '#e3f2fd', 'color' => '#1976d2'],
                                            'doktorant' => ['bg' => '#f3e5f5', 'color' => '#7b1fa2']
                                        ];
                                        $levelStyle = $levelColors[$program->level] ?? ['bg' => '#f0f0f0', 'color' => '#666'];
                                    @endphp
                                    <span class="badge" style="background: {{ $levelStyle['bg'] }}; color: {{ $levelStyle['color'] }};">
                                        {{ ucfirst($program->level) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $formColors = [
                                            'kunduzgi' => ['bg' => '#48c9b0', 'color' => 'white'],
                                            'sirtqi' => ['bg' => '#fff3cd', 'color' => '#f39c12'],
                                            'kechki' => ['bg' => '#fef0f0', 'color' => '#e74c3c']
                                        ];
                                        $formStyle = $formColors[$program->education_form] ?? ['bg' => '#f0f0f0', 'color' => '#666'];
                                    @endphp
                                    <span class="badge" style="background: {{ $formStyle['bg'] }}; color: {{ $formStyle['color'] }};">
                                        {{ ucfirst($program->education_form) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--border-green); color: var(--text-dark);">
                                        {{ $program->curricula_count ?? 0 }} ta
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: var(--primary-dark-green);">{{ $program->total_credits ?? 0 }}</strong> kr
                                </td>
                                <td>
                                    @if($program->is_approved ?? false)
                                        <span class="badge" style="background: var(--light-green); color: var(--primary-dark-green);">
                                            <i class="fas fa-check-circle"></i> Tasdiqlangan
                                        </span>
                                    @else
                                        <span class="badge" style="background: #fff3cd; color: #f39c12;">
                                            <i class="fas fa-clock"></i> Tayyorlanmoqda
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('structure.academic.curriculum.builder', $program) }}"
                                           class="btn btn-sm"
                                           style="border: 1px solid var(--primary-dark-green); color: var(--primary-dark-green);"
                                           onmouseover="this.style.background='var(--light-green)'"
                                           onmouseout="this.style.background='transparent'"
                                           title="O'quv rejani tahrirlash">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('structure.academic.programs.curriculum', $program) }}"
                                           class="btn btn-sm"
                                           style="border: 1px solid var(--secondary-green); color: var(--secondary-green);"
                                           onmouseover="this.style.background='var(--light-green)'"
                                           onmouseout="this.style.background='transparent'"
                                           title="Ko'rish">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm dropdown-toggle"
                                                    style="border: 1px solid var(--accent-green); color: var(--accent-green);"
                                                    onmouseover="this.style.background='var(--light-green)'"
                                                    onmouseout="this.style.background='transparent'"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('structure.academic.curriculum.export', $program) }}">
                                                        <i class="fas fa-download me-1" style="color: var(--secondary-green);"></i> Export
                                                    </a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" onclick="copyCurriculum({{ $program->id }})">
                                                        <i class="fas fa-copy me-1" style="color: var(--accent-green);"></i> Nusxalash
                                                    </button>
                                                </li>
                                                @if(!($program->is_approved ?? false))
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item" onclick="approveCurriculum({{ $program->id }})">
                                                        <i class="fas fa-check me-1" style="color: var(--primary-dark-green);"></i> Tasdiqlash
                                                    </button>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div style="color: #7f8c8d;">
                                        <i class="fas fa-book-open fa-3x mb-3" style="color: var(--secondary-green);"></i>
                                        <p>Hozircha o'quv rejalar mavjud emas</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($programs) && $programs->hasPages())
            <div class="px-4 py-3" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                {{ $programs->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important; background: linear-gradient(135deg, var(--very-light-green), white);">
                <div class="card-body">
                    <h5 style="color: var(--text-dark); font-weight: 600; margin-bottom: 20px;">
                        <i class="fas fa-bolt" style="color: var(--secondary-green);"></i> Tez harakatlar
                    </h5>
                    <div class="d-flex gap-3">
                        <button class="btn text-white"
                                style="background: var(--primary-dark-green);"
                                onmouseover="this.style.background='var(--secondary-green)'"
                                onmouseout="this.style.background='var(--primary-dark-green)'">
                            <i class="fas fa-plus"></i> Yangi o'quv reja
                        </button>
                        <a href="{{ route('structure.academic.curriculum.topics') }}" class="btn text-white"
                           style="background: var(--secondary-green);"
                           onmouseover="this.style.background='var(--primary-dark-green)'"
                           onmouseout="this.style.background='var(--secondary-green)'">
                            <i class="fas fa-list"></i> Mavzular ro'yxati
                        </a>
                        <a href="{{ route('structure.academic.hours.index') }}" class="btn"
                           style="background: var(--accent-green); color: white;"
                           onmouseover="this.style.background='var(--secondary-green)'"
                           onmouseout="this.style.background='var(--accent-green)'">
                            <i class="fas fa-clock"></i> Soatlar taqsimoti
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyCurriculum(programId) {
    if (confirm('O\'quv rejani nusxalashni xohlaysizmi?')) {
        fetch(`{{ url('/structure/academic/curriculum/copy') }}/${programId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('O\'quv reja muvaffaqiyatli nusxalandi!');
                window.location.reload();
            } else {
                alert('Xatolik yuz berdi!');
            }
        });
    }
}

function approveCurriculum(programId) {
    if (confirm('O\'quv rejani tasdiqlashni xohlaysizmi?')) {
        fetch(`{{ url('/structure/academic/curriculum/approve') }}/${programId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('O\'quv reja tasdiqlandi!');
                window.location.reload();
            } else {
                alert('Xatolik yuz berdi!');
            }
        });
    }
}
</script>
@endsection