@extends('layouts.dashboard-new')

@section('title', 'Dars jadvali')
@section('page-title', 'Dars jadvali')

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-calendar-alt me-2"></i>Dars jadvali</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }} dars jadvali</p>
                        </div>
                        <div>
                            <a href="{{ route('dean.schedule.exams') }}" class="btn btn-light me-2">
                                <i class="fas fa-file-alt me-1"></i> Imtihonlar
                            </a>
                            <a href="{{ route('dean.schedule.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Yangi jadval
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtrlar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('dean.schedule.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Guruh</label>
                    <select name="group_id" class="form-select">
                        <option value="">Barcha guruhlar</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Qoralama</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Arxiv</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Qidirish
                    </button>
                    <a href="{{ route('dean.schedule.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Jadvallar ro'yxati -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list text-primary me-2"></i>Jadvallar ro'yxati</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Guruh</th>
                            <th class="border-0">O'quv yili</th>
                            <th class="border-0">Semestr</th>
                            <th class="border-0">Darslar soni</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Yaratilgan</th>
                            <th class="border-0 text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                        <tr>
                            <td>
                                <span class="badge bg-primary fs-6">{{ $schedule->group?->name ?? '-' }}</span>
                            </td>
                            <td>{{ $schedule->academicYear?->name ?? '-' }}</td>
                            <td>{{ $schedule->semester_id }}-semestr</td>
                            <td>
                                <span class="badge bg-info">{{ $schedule->slots->count() }} ta</span>
                            </td>
                            <td>
                                @switch($schedule->status)
                                    @case('draft')
                                        <span class="badge bg-warning text-dark">Qoralama</span>
                                        @break
                                    @case('active')
                                        <span class="badge bg-success">Faol</span>
                                        @break
                                    @case('archived')
                                        <span class="badge bg-secondary">Arxiv</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $schedule->status }}</span>
                                @endswitch
                            </td>
                            <td>{{ $schedule->created_at->format('d.m.Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('dean.schedule.show', $schedule) }}" class="btn btn-sm btn-outline-info" title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('dean.schedule.edit', $schedule) }}" class="btn btn-sm btn-outline-primary" title="Tahrirlash">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('dean.schedule.destroy', $schedule) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Jadvalni o\'chirishni tasdiqlaysizmi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="O'chirish">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-alt fa-3x mb-3 d-block opacity-50"></i>
                                Jadvallar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($schedules->hasPages())
        <div class="card-footer bg-white">{{ $schedules->withQueryString()->links() }}</div>
        @endif
    </div>
</div>

<style>.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }</style>
@endsection
