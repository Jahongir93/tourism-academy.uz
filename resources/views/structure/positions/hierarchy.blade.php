@extends('layouts.dashboard-new')

@section('title', 'Lavozimlar ierarxiyasi - HEMIS')

@section('page-title', 'Lavozimlar ierarxiyasi')

@push('styles')
<style>
    .hierarchy-container {
        padding: 20px;
        overflow-x: auto;
    }
    .hierarchy-tree {
        min-width: 800px;
    }
    .hierarchy-node {
        display: inline-block;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 20px;
        margin: 5px;
        text-align: center;
        min-width: 150px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .hierarchy-node:hover {
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    .hierarchy-node.leadership {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }
    .hierarchy-node.academic {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
        border: none;
    }
    .hierarchy-node.administrative {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border: none;
    }
    .hierarchy-node.support {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
        border: none;
    }
    .node-title {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 4px;
    }
    .node-subtitle {
        font-size: 11px;
        opacity: 0.9;
    }
    .hierarchy-level {
        margin-bottom: 30px;
        position: relative;
    }
    .level-title {
        font-weight: bold;
        color: #6b7280;
        margin-bottom: 15px;
        padding-left: 10px;
        border-left: 3px solid #3b82f6;
    }
    .hierarchy-line {
        position: absolute;
        width: 2px;
        background: #d1d5db;
        left: 50%;
        transform: translateX(-50%);
    }
    .hierarchy-connector {
        position: relative;
    }
    .hierarchy-connector::before {
        content: '';
        position: absolute;
        top: -15px;
        left: 50%;
        width: 2px;
        height: 15px;
        background: #d1d5db;
        transform: translateX(-50%);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <p class="text-muted">Universitet lavozimlarining ierarxik tuzilmasi</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('structure.positions.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Ro'yxat ko'rinishi
            </a>
            <a href="{{ route('structure.positions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi lavozim
            </a>
        </div>
    </div>

    <!-- Hierarchy Levels -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="hierarchy-container">
                <div class="hierarchy-tree">
                    
                    <!-- Level 1 - Top Leadership -->
                    <div class="hierarchy-level">
                        <h5 class="level-title">1-daraja: Yuqori rahbariyat</h5>
                        <div class="text-center">
                            @foreach($positions->where('level', 1)->where('category', 'leadership') as $position)
                            <div class="hierarchy-node leadership">
                                <div class="node-title">{{ $position->name_uz ?? $position->name }}</div>
                                <div class="node-subtitle">{{ $position->code }}</div>
                                @if($position->orgUnitPositions->where('is_active', true)->count() > 0)
                                    <div class="node-subtitle mt-1">
                                        <small>{{ $position->orgUnitPositions->where('is_active', true)->count() }} xodim</small>
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Level 2 - Middle Leadership -->
                    <div class="hierarchy-level">
                        <h5 class="level-title">2-daraja: O'rta rahbariyat</h5>
                        <div class="text-center">
                            @foreach($positions->where('level', 2) as $position)
                            <div class="hierarchy-node hierarchy-connector {{ $position->category }}">
                                <div class="node-title">{{ $position->name_uz ?? $position->name }}</div>
                                <div class="node-subtitle">{{ $position->code }}</div>
                                @if($position->orgUnitPositions->where('is_active', true)->count() > 0)
                                    <div class="node-subtitle mt-1">
                                        <small>{{ $position->orgUnitPositions->where('is_active', true)->count() }} xodim</small>
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Level 3 - Department Heads -->
                    <div class="hierarchy-level">
                        <h5 class="level-title">3-daraja: Bo'lim boshliqlari</h5>
                        <div class="text-center">
                            @foreach($positions->where('level', 3) as $position)
                            <div class="hierarchy-node hierarchy-connector {{ $position->category }}">
                                <div class="node-title">{{ $position->name_uz ?? $position->name }}</div>
                                <div class="node-subtitle">{{ $position->code }}</div>
                                @if($position->orgUnitPositions->where('is_active', true)->count() > 0)
                                    <div class="node-subtitle mt-1">
                                        <small>{{ $position->orgUnitPositions->where('is_active', true)->count() }} xodim</small>
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Other Levels -->
                    @for($level = 4; $level <= 10; $level++)
                        @if($positions->where('level', $level)->count() > 0)
                        <div class="hierarchy-level">
                            <h5 class="level-title">{{ $level }}-daraja</h5>
                            <div class="text-center">
                                @foreach($positions->where('level', $level) as $position)
                                <div class="hierarchy-node {{ $position->category }}">
                                    <div class="node-title">{{ $position->name_uz ?? $position->name }}</div>
                                    <div class="node-subtitle">{{ $position->code }}</div>
                                    @if($position->orgUnitPositions->where('is_active', true)->count() > 0)
                                        <div class="node-subtitle mt-1">
                                            <small>{{ $position->orgUnitPositions->where('is_active', true)->count() }} xodim</small>
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics by Category -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Rahbariyat lavozimlar</h6>
                    <h3 class="mb-0 text-primary">{{ $positions->where('category', 'leadership')->count() }}</h3>
                    <small class="text-muted">
                        {{ $positions->where('category', 'leadership')->sum(function($p) { 
                            return $p->orgUnitPositions->where('is_active', true)->count(); 
                        }) }} xodim
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Akademik lavozimlar</h6>
                    <h3 class="mb-0 text-success">{{ $positions->where('category', 'academic')->count() }}</h3>
                    <small class="text-muted">
                        {{ $positions->where('category', 'academic')->sum(function($p) { 
                            return $p->orgUnitPositions->where('is_active', true)->count(); 
                        }) }} xodim
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Ma'muriy lavozimlar</h6>
                    <h3 class="mb-0 text-info">{{ $positions->where('category', 'administrative')->count() }}</h3>
                    <small class="text-muted">
                        {{ $positions->where('category', 'administrative')->sum(function($p) { 
                            return $p->orgUnitPositions->where('is_active', true)->count(); 
                        }) }} xodim
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Yordamchi lavozimlar</h6>
                    <h3 class="mb-0 text-warning">{{ $positions->where('category', 'support')->count() }}</h3>
                    <small class="text-muted">
                        {{ $positions->where('category', 'support')->sum(function($p) { 
                            return $p->orgUnitPositions->where('is_active', true)->count(); 
                        }) }} xodim
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Reporting Structure Table -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Bo'ysunish tuzilmasi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Lavozim</th>
                            <th>Kategoriya</th>
                            <th>Daraja</th>
                            <th>Kimga bo'ysunadi</th>
                            <th>Kimlar bo'ysunadi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions->sortBy('level') as $position)
                        <tr>
                            <td>
                                <strong>{{ $position->name_uz ?? $position->name }}</strong>
                                <br><small class="text-muted">{{ $position->code }}</small>
                            </td>
                            <td>
                                @switch($position->category)
                                    @case('leadership')
                                        <span class="badge bg-primary">Rahbariyat</span>
                                        @break
                                    @case('academic')
                                        <span class="badge bg-success">Akademik</span>
                                        @break
                                    @case('administrative')
                                        <span class="badge bg-info">Ma'muriy</span>
                                        @break
                                    @case('support')
                                        <span class="badge bg-warning">Yordamchi</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $position->level }}-daraja</td>
                            <td>
                                @if($position->reportsTo->count() > 0)
                                    @foreach($position->reportsTo as $superior)
                                        <span class="badge bg-secondary">{{ $superior->name_uz ?? $superior->name }}</span>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($position->subordinates->count() > 0)
                                    <span class="badge bg-info">{{ $position->subordinates->count() }} lavozim</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection