@extends('layouts.dashboard-new')

@section('title', 'Xonalar nazorati')
@section('page-title', 'Xonalar nazorati')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Xonalar nazorati</h1>
        <p class="page-header-sub">Dars jadvalida ishlatiladigan o'quv xonalari</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('classrooms.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yangi xona
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card" style="color:var(--c-primary)">
            <div class="stat-card-icon" style="background:rgba(79,70,229,.1)"><i class="fas fa-door-open"></i></div>
            <div class="stat-card-value">{{ $stats['total'] }}</div>
            <div class="stat-card-label">Jami xonalar</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card" style="color:var(--c-emerald)">
            <div class="stat-card-icon" style="background:rgba(16,185,129,.1)"><i class="fas fa-check-circle"></i></div>
            <div class="stat-card-value">{{ $stats['active'] }}</div>
            <div class="stat-card-label">Faol xonalar</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.1)"><i class="fas fa-users"></i></div>
            <div class="stat-card-value">{{ $stats['capacity'] }}</div>
            <div class="stat-card-label">Umumiy sig'im</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2 mb-3 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="max-width:260px" placeholder="Nomi yoki kodi...">
            <select name="type" class="form-select" style="max-width:180px">
                <option value="">Barcha turlar</option>
                @foreach(['lecture'=>'Ma\'ruza','practice'=>'Amaliy','lab'=>'Laboratoriya','seminar'=>'Seminar','other'=>'Boshqa'] as $k=>$v)
                    <option value="{{ $k }}" {{ request('type')==$k?'selected':'' }}>{{ $v }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary"><i class="fas fa-search"></i></button>
            @if(request('search') || request('type'))
                <a href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary">Tozalash</a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nomi</th><th>Kodi</th><th>Qavat</th><th>Sig'im</th><th>Turi</th><th>Jihozlar</th><th>Holat</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classrooms as $room)
                    <tr>
                        <td><strong>{{ $room->name }}</strong></td>
                        <td>{{ $room->code ?: '—' }}</td>
                        <td>{{ $room->floor ?? '—' }}</td>
                        <td>{{ $room->capacity ?? '—' }}</td>
                        <td>
                            @php $types=['lecture'=>'Ma\'ruza','practice'=>'Amaliy','lab'=>'Laboratoriya','seminar'=>'Seminar','other'=>'Boshqa']; @endphp
                            <span class="badge bg-secondary">{{ $types[$room->type] ?? ($room->type ?: '—') }}</span>
                        </td>
                        <td style="font-size:14px">
                            @if($room->has_projector)<i class="fas fa-video text-primary" title="Proyektor"></i>@endif
                            @if($room->has_computer)<i class="fas fa-desktop text-info" title="Kompyuter"></i>@endif
                            @if($room->has_whiteboard)<i class="fas fa-chalkboard text-success" title="Doska"></i>@endif
                            @if(!$room->has_projector && !$room->has_computer && !$room->has_whiteboard)—@endif
                        </td>
                        <td>
                            @if($room->is_active)
                                <span class="badge bg-success">Faol</span>
                            @else
                                <span class="badge bg-secondary">Nofaol</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('classrooms.edit', $room) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('classrooms.destroy', $room) }}" method="POST" class="d-inline" onsubmit="return confirm('Xona o\'chirilsinmi?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-door-closed"></i></div>
                            <div class="empty-state-title">Xonalar yo'q</div>
                            <div class="empty-state-sub">"Yangi xona" tugmasi orqali qo'shing</div>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $classrooms->links() }}
    </div>
</div>
@endsection
