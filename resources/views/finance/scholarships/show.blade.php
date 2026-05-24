@extends('layouts.dashboard-new')

@section('title', 'Grant ma\'lumotlari')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <!-- Grant Info -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $scholarship->name }}</h5>
                    <div>
                        <a href="{{ route('finance.scholarships.edit', $scholarship) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Tahrirlash
                        </a>
                        <a href="{{ route('finance.scholarships.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            @if($scholarship->description)
                            <p class="text-muted">{{ $scholarship->description }}</p>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            @if($scholarship->status === 'active')
                                <span class="badge badge-success fs-6">Faol</span>
                            @else
                                <span class="badge badge-secondary fs-6">Nofaol</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <h6 class="text-muted">Summa</h6>
                            <h4 class="text-primary">{{ number_format($scholarship->amount, 0) }} so'm</h4>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Turi</h6>
                            <p><span class="badge badge-info">{{ $scholarship->type_label }}</span></p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Kategoriya</h6>
                            <p><span class="badge badge-secondary">{{ $scholarship->category_label }}</span></p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Oluvchilar</h6>
                            <p>{{ $scholarship->current_recipients }}
                                @if($scholarship->max_recipients)
                                    / {{ $scholarship->max_recipients }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jami berilgan</div>
                            <div class="h5 mb-0">{{ number_format($stats['total_awarded'], 0) }} so'm</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">To'langan</div>
                            <div class="h5 mb-0">{{ number_format($stats['total_paid'], 0) }} so'm</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Kutilmoqda</div>
                            <div class="h5 mb-0">{{ $stats['pending_payments'] }} ta</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients List -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Grant oluvchilar</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Talaba</th>
                                    <th>Summa</th>
                                    <th>Berilgan sana</th>
                                    <th>Davr</th>
                                    <th>Holat</th>
                                    <th>Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scholarship->studentScholarships as $ss)
                                <tr>
                                    <td>
                                        <strong>{{ $ss->student->user->name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $ss->student->student_id ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ number_format($ss->amount, 0) }} so'm</td>
                                    <td>{{ $ss->awarded_date->format('d.m.Y') }}</td>
                                    <td>
                                        {{ $ss->start_date->format('d.m.Y') }}
                                        @if($ss->end_date)
                                            - {{ $ss->end_date->format('d.m.Y') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($ss->status === 'active')
                                            <span class="badge badge-success">Faol</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $ss->status_label }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ss->status === 'active')
                                        <form method="POST" action="{{ route('finance.scholarships.revoke', $ss) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Rostdan ham bekor qilmoqchimisiz?')">
                                                <i class="fas fa-times"></i> Bekor qilish
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Hozircha grant oluvchilar yo'q
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
