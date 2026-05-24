@extends('layouts.dashboard-new')

@section('title', 'Dars jadvali - O\'quv jarayoni - HEMIS')

@section('page-title', 'Dars jadvali')

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
        <div class="col-md-5">
            <h1 class="h2 text-white">Dars jadvali</h1>
            <p class="text-white opacity-90">Haftalik dars jadvalini boshqarish va ko'rish</p>
        </div>
        <div class="col-md-7 text-end d-flex align-items-center justify-content-end flex-wrap gap-2">
            <a href="{{ route('schedule.room-monitoring') }}" class="btn text-white"
               style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); white-space: nowrap;"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-door-open"></i> Xonalar nazorati
            </a>
            <a href="{{ route('schedule.weekly') }}" class="btn text-white"
               style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); white-space: nowrap;"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-calendar-week"></i> Haftalik
            </a>
            <a href="{{ route('schedule.faculty-builder') }}" class="btn text-white"
               style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); white-space: nowrap;"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-layer-group"></i> Fakultet
            </a>
            <a href="{{ route('schedule.create') }}" class="btn text-white"
               style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); white-space: nowrap;"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-plus"></i> Yangi
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1" style="color: #7f8c8d;">Bugungi darslar</h6>
                            <h3 class="mb-0" style="color: var(--text-dark);">24</h3>
                        </div>
                        <div>
                            <div class="d-inline-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background: var(--light-green); border-radius: 10px;">
                                <i class="fas fa-calendar-day fa-xl" style="color: var(--primary-dark-green);"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1" style="color: #7f8c8d;">Haftalik darslar</h6>
                            <h3 class="mb-0" style="color: var(--text-dark);">156</h3>
                        </div>
                        <div>
                            <div class="d-inline-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, #e8f5f0, #c3e6d8); border-radius: 10px;">
                                <i class="fas fa-calendar-week fa-xl" style="color: var(--secondary-green);"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1" style="color: #7f8c8d;">Band xonalar</h6>
                            <h3 class="mb-0" style="color: var(--text-dark);">18</h3>
                        </div>
                        <div>
                            <div class="d-inline-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background: #fff3cd; border-radius: 10px;">
                                <i class="fas fa-door-closed fa-xl" style="color: #f39c12;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1" style="color: #7f8c8d;">Bo'sh xonalar</h6>
                            <h3 class="mb-0" style="color: var(--text-dark);">7</h3>
                        </div>
                        <div>
                            <div class="d-inline-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, #f0f9f6, #d8e9e3); border-radius: 10px;">
                                <i class="fas fa-door-open fa-xl" style="color: var(--accent-green);"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important;">
        <div class="card-body">
            <form method="GET" action="{{ route('schedule.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" style="color: var(--text-dark); font-weight: 600;">Guruh</label>
                    <select name="group_id" class="form-select"
                            style="border: 1px solid var(--border-green);"
                            onfocus="this.style.borderColor='var(--secondary-green)'; this.style.boxShadow='0 0 0 3px rgba(22, 160, 133, 0.1)'"
                            onblur="this.style.borderColor='var(--border-green)'; this.style.boxShadow='none'">
                        <option value="">Barcha guruhlar</option>
                        @foreach($groups ?? [] as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label" style="color: var(--text-dark); font-weight: 600;">O'qituvchi</label>
                    <select name="teacher_id" class="form-select"
                            style="border: 1px solid var(--border-green);"
                            onfocus="this.style.borderColor='var(--secondary-green)'; this.style.boxShadow='0 0 0 3px rgba(22, 160, 133, 0.1)'"
                            onblur="this.style.borderColor='var(--border-green)'; this.style.boxShadow='none'">
                        <option value="">Barcha o'qituvchilar</option>
                        @foreach($teachers ?? [] as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label" style="color: var(--text-dark); font-weight: 600;">Hafta kuni</label>
                    <select name="day_of_week" class="form-select"
                            style="border: 1px solid var(--border-green);"
                            onfocus="this.style.borderColor='var(--secondary-green)'; this.style.boxShadow='0 0 0 3px rgba(22, 160, 133, 0.1)'"
                            onblur="this.style.borderColor='var(--border-green)'; this.style.boxShadow='none'">
                        <option value="">Barcha kunlar</option>
                        <option value="1" {{ request('day_of_week') == 1 ? 'selected' : '' }}>Dushanba</option>
                        <option value="2" {{ request('day_of_week') == 2 ? 'selected' : '' }}>Seshanba</option>
                        <option value="3" {{ request('day_of_week') == 3 ? 'selected' : '' }}>Chorshanba</option>
                        <option value="4" {{ request('day_of_week') == 4 ? 'selected' : '' }}>Payshanba</option>
                        <option value="5" {{ request('day_of_week') == 5 ? 'selected' : '' }}>Juma</option>
                        <option value="6" {{ request('day_of_week') == 6 ? 'selected' : '' }}>Shanba</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn me-2 text-white"
                            style="background: var(--primary-dark-green); padding: 10px 24px;"
                            onmouseover="this.style.background='var(--secondary-green)'"
                            onmouseout="this.style.background='var(--primary-dark-green)'">
                        <i class="fas fa-search"></i> Qidirish
                    </button>

                    @if(request()->hasAny(['group_id', 'teacher_id', 'day_of_week']))
                        <a href="{{ route('schedule.index') }}" class="btn"
                           style="background: var(--light-green); color: var(--text-dark); padding: 10px 24px;"
                           onmouseover="this.style.background='var(--border-green)'"
                           onmouseout="this.style.background='var(--light-green)'">
                            <i class="fas fa-times"></i> Tozalash
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Schedule Table -->
    <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: var(--light-green);">
                        <tr>
                            <th style="color: var(--text-dark); font-weight: 600;">Hafta kuni</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Vaqt</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Fan</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Guruh</th>
                            <th style="color: var(--text-dark); font-weight: 600;">O'qituvchi</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Xona</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Turi</th>
                            <th style="color: var(--text-dark); font-weight: 600;">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $days = [1 => 'Dushanba', 2 => 'Seshanba', 3 => 'Chorshanba', 4 => 'Payshanba', 5 => 'Juma', 6 => 'Shanba'];
                            $types = ['lecture' => 'Ma\'ruza', 'practice' => 'Amaliyot', 'lab' => 'Laboratoriya', 'seminar' => 'Seminar'];
                        @endphp
                        @forelse($schedules ?? [] as $schedule)
                            <tr onmouseover="this.style.background='var(--very-light-green)'" onmouseout="this.style.background='white'">
                                <td>
                                    <span class="badge" style="background: var(--secondary-green); color: white;">
                                        {{ $days[$schedule->day_of_week] ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    @if($schedule->timeSlot ?? null)
                                        <strong style="color: var(--text-dark);">{{ $schedule->timeSlot->name }}</strong><br>
                                        <small style="color: #7f8c8d;">{{ $schedule->timeSlot->start_time }} - {{ $schedule->timeSlot->end_time }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: var(--text-dark);">{{ $schedule->subject->name_uz ?? '' }}</strong>
                                    @if($schedule->subject->name_ru ?? null)
                                        <br><small style="color: #7f8c8d;">{{ $schedule->subject->name_ru }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--light-green); color: var(--primary-dark-green);">
                                        {{ $schedule->group->name ?? '' }}
                                    </span>
                                </td>
                                <td style="color: var(--text-dark);">
                                    {{ $schedule->teacher->full_name ?? '' }}
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--accent-green); color: white;">
                                        <i class="fas fa-door-open me-1"></i>{{ $schedule->classroom->name ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'lecture' => ['bg' => '#e3f2fd', 'color' => '#1976d2'],
                                            'practice' => ['bg' => '#e8f5f0', 'color' => '#0d4f3c'],
                                            'lab' => ['bg' => '#f3e5f5', 'color' => '#7b1fa2'],
                                            'seminar' => ['bg' => '#fff3cd', 'color' => '#f39c12']
                                        ];
                                        $typeStyle = $typeColors[$schedule->lesson_type] ?? ['bg' => '#f0f0f0', 'color' => '#666'];
                                    @endphp
                                    <span class="badge" style="background: {{ $typeStyle['bg'] }}; color: {{ $typeStyle['color'] }};">
                                        {{ $types[$schedule->lesson_type] ?? $schedule->lesson_type }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('schedule.show', $schedule) }}"
                                           class="btn btn-sm"
                                           style="border: 1px solid var(--secondary-green); color: var(--secondary-green);"
                                           onmouseover="this.style.background='var(--light-green)'"
                                           onmouseout="this.style.background='transparent'">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('schedule.edit', $schedule) }}"
                                           class="btn btn-sm"
                                           style="border: 1px solid #f39c12; color: #f39c12;"
                                           onmouseover="this.style.background='#fff3cd'"
                                           onmouseout="this.style.background='transparent'">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('schedule.destroy', $schedule) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm"
                                                    style="border: 1px solid #e74c3c; color: #e74c3c;"
                                                    onmouseover="this.style.background='#fef0f0'"
                                                    onmouseout="this.style.background='transparent'"
                                                    onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div style="color: #7f8c8d;">
                                        <i class="fas fa-calendar-times fa-3x mb-3" style="color: var(--secondary-green);"></i>
                                        <p>Hozircha dars jadvali yo'q</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($schedules) && $schedules->hasPages())
            <div class="px-4 py-3" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                {{ $schedules->links() }}
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
                            <i class="fas fa-magic"></i> Avtomatik jadval yaratish
                        </button>
                        <button class="btn text-white"
                                style="background: var(--secondary-green);"
                                onmouseover="this.style.background='var(--primary-dark-green)'"
                                onmouseout="this.style.background='var(--secondary-green)'">
                            <i class="fas fa-copy"></i> Jadvalni nusxalash
                        </button>
                        <button class="btn"
                                style="background: var(--accent-green); color: white;"
                                onmouseover="this.style.background='var(--secondary-green)'"
                                onmouseout="this.style.background='var(--accent-green)'">
                            <i class="fas fa-file-export"></i> Excel export
                        </button>
                        <button class="btn"
                                style="background: #f39c12; color: white;"
                                onmouseover="this.style.background='#e67e22'"
                                onmouseout="this.style.background='#f39c12'">
                            <i class="fas fa-print"></i> Chop etish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection