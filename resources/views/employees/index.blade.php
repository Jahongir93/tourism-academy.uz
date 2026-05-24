@extends('layouts.dashboard-new')

@section('title', 'Xodimlar - HEMIS')
@section('page-title', 'Xodimlar ro\'yxati')

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

    /* Custom styles for select dropdowns */
    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2316a085' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid var(--border-green);
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(13, 79, 60, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-6 p-6 rounded-lg"
         style="background: linear-gradient(135deg, var(--primary-dark-green) 0%, var(--secondary-green) 100%);">
        <h1 class="text-2xl font-bold text-white">Xodimlar ro'yxati</h1>
        <a href="{{ route('employees.create') }}"
           class="text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-all"
           style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);"
           onmouseover="this.style.background='rgba(255,255,255,0.3)'"
           onmouseout="this.style.background='rgba(255,255,255,0.2)'">
            <i class="fas fa-plus mr-2"></i> Yangi xodim qo'shish
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card">
            <div class="flex items-center">
                <div class="p-3 rounded-full" style="background: var(--light-green);">
                    <i class="fas fa-users text-2xl" style="color: var(--primary-dark-green);"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm" style="color: #7f8c8d;">Jami xodimlar</p>
                    <p class="text-2xl font-bold" style="color: var(--text-dark);">{{ $employees->total() }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center">
                <div class="p-3 rounded-full" style="background: var(--light-green);">
                    <i class="fas fa-chalkboard-teacher text-2xl" style="color: var(--secondary-green);"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm" style="color: #7f8c8d;">O'qituvchilar</p>
                    <p class="text-2xl font-bold" style="color: var(--text-dark);">{{ \App\Models\Employee::where('employee_type', 'teacher')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center">
                <div class="p-3 rounded-full" style="background: var(--light-green);">
                    <i class="fas fa-user-tie text-2xl" style="color: var(--accent-green);"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm" style="color: #7f8c8d;">Ma'muriy xodimlar</p>
                    <p class="text-2xl font-bold" style="color: var(--text-dark);">{{ \App\Models\Employee::where('employee_type', 'admin')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center">
                <div class="p-3 rounded-full" style="background: #fff3cd;">
                    <i class="fas fa-calendar-times text-2xl" style="color: #f39c12;"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm" style="color: #7f8c8d;">Ta'tilda</p>
                    <p class="text-2xl font-bold" style="color: var(--text-dark);">{{ \App\Models\Employee::where('status', 'leave')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6" style="border: 1px solid var(--border-green);">
        <h3 class="text-lg font-semibold mb-4" style="color: var(--text-dark); border-bottom: 2px solid var(--light-green); padding-bottom: 10px;">
            <i class="fas fa-filter mr-2" style="color: var(--secondary-green);"></i>Filtrlar
        </h3>
        <form method="GET" action="{{ route('employees.index') }}">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-medium mb-1" style="color: var(--text-dark);">Qidirish</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Ism, JSHSHIR..."
                           class="w-full px-2 py-1.5 text-sm rounded-md transition-all"
                           style="border: 1px solid var(--border-green);"
                           onfocus="this.style.borderColor='var(--secondary-green)'; this.style.boxShadow='0 0 0 3px rgba(22, 160, 133, 0.1)'"
                           onblur="this.style.borderColor='var(--border-green)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--text-dark);">Xodim turi</label>
                    <select name="type"
                            class="w-full px-2 py-1.5 text-sm rounded-md transition-all appearance-none bg-white"
                            style="border: 1px solid var(--border-green);"
                            onfocus="this.style.borderColor='var(--secondary-green)'; this.style.boxShadow='0 0 0 3px rgba(22, 160, 133, 0.1)'"
                            onblur="this.style.borderColor='var(--border-green)'; this.style.boxShadow='none'">
                        <option value="">Barchasi</option>
                        <option value="teacher" {{ request('type') == 'teacher' ? 'selected' : '' }}>O'qituvchi</option>
                        <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Ma'muriy</option>
                        <option value="support" {{ request('type') == 'support' ? 'selected' : '' }}>Yordamchi</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--text-dark);">Holati</label>
                    <select name="status"
                            class="w-full px-2 py-1.5 text-sm rounded-md transition-all appearance-none bg-white"
                            style="border: 1px solid var(--border-green);"
                            onfocus="this.style.borderColor='var(--secondary-green)'; this.style.boxShadow='0 0 0 3px rgba(22, 160, 133, 0.1)'"
                            onblur="this.style.borderColor='var(--border-green)'; this.style.boxShadow='none'">
                        <option value="">Barchasi</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ishlayapti</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nofaol</option>
                        <option value="leave" {{ request('status') == 'leave' ? 'selected' : '' }}>Ta'tilda</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Bo'shagan</option>
                    </select>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-medium mb-1" style="color: var(--text-dark);">Fakultet</label>
                    <select name="faculty_id"
                            class="w-full px-2 py-1.5 text-sm rounded-md transition-all appearance-none bg-white"
                            style="border: 1px solid var(--border-green);"
                            onfocus="this.style.borderColor='var(--secondary-green)'; this.style.boxShadow='0 0 0 3px rgba(22, 160, 133, 0.1)'"
                            onblur="this.style.borderColor='var(--border-green)'; this.style.boxShadow='none'">
                        <option value="">Barchasi</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name_uz }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2 md:col-span-1 flex gap-2">
                    <a href="{{ route('employees.index') }}"
                       class="flex-1 text-center font-medium py-1.5 px-2 text-sm rounded inline-flex items-center justify-center transition-all"
                       style="background: var(--light-green); color: var(--text-dark);"
                       onmouseover="this.style.background='#d1ebe3'"
                       onmouseout="this.style.background='var(--light-green)'">
                        <i class="fas fa-redo mr-1 text-xs"></i>Tozalash
                    </a>
                    <button type="submit"
                            class="flex-1 text-white font-medium py-1.5 px-2 text-sm rounded inline-flex items-center justify-center transition-all"
                            style="background: var(--primary-dark-green);"
                            onmouseover="this.style.background='var(--secondary-green)'"
                            onmouseout="this.style.background='var(--primary-dark-green)'">
                        <i class="fas fa-search mr-1 text-xs"></i>Qidirish
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-lg shadow" style="border: 1px solid var(--border-green);">
        <div class="overflow-x-auto">
            <table class="w-full divide-y" style="border-color: var(--border-green); min-width: 900px;">
                <thead style="background: var(--light-green);">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark); width: 80px;">
                            Kod
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark); min-width: 200px;">
                            F.I.O
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark);">
                            Lavozim
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark);">
                            Bo'lim/Fakultet
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark); width: 120px;">
                            Telefon
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark); width: 100px;">
                            Holati
                        </th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark); width: 160px; background: var(--primary-dark-green); color: white;">
                            Amallar
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y" style="border-color: var(--border-green);">
                    @forelse($employees as $employee)
                        <tr class="transition-all hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium" style="color: var(--secondary-green);">
                                {{ $employee->employee_code }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($employee->photo)
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                 src="{{ asset('storage/'.$employee->photo) }}"
                                                 alt="{{ $employee->full_name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full flex items-center justify-center" style="background: var(--light-green);">
                                                <i class="fas fa-user" style="color: var(--secondary-green);"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium" style="color: var(--text-dark);">
                                            {{ $employee->full_name }}
                                        </div>
                                        @if($employee->is_teacher)
                                            <span class="px-2 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full"
                                                  style="background: var(--light-green); color: var(--primary-dark-green);">
                                                O'qituvchi
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm" style="color: var(--text-dark);">
                                @if($employee->employmentDetail)
                                    <div>{{ $employee->employmentDetail->position->name_uz ?? $employee->employmentDetail->position->name ?? '-' }}</div>
                                    <div class="text-xs" style="color: #7f8c8d;">({{ $employee->employmentDetail->stavka }} stavka)</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm" style="color: var(--text-dark);">
                                @if($employee->employmentDetail)
                                    {{ $employee->employmentDetail->faculty->name_uz ?? $employee->employmentDetail->department->name_uz ?? '-' }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm" style="color: var(--text-dark);">
                                {{ $employee->phone ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @switch($employee->status)
                                    @case('active')
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full"
                                              style="background: var(--light-green); color: var(--primary-dark-green);">
                                            Ishlayapti
                                        </span>
                                        @break
                                    @case('inactive')
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full"
                                              style="background: #f5f5f5; color: #7f8c8d;">
                                            Nofaol
                                        </span>
                                        @break
                                    @case('leave')
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full"
                                              style="background: #fff3cd; color: #f39c12;">
                                            Ta'tilda
                                        </span>
                                        @break
                                    @case('terminated')
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full"
                                              style="background: #fef0f0; color: #e74c3c;">
                                            Bo'shagan
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center" style="background: #f8fdfb;">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('employees.show', $employee) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-md transition-all"
                                       style="background: var(--light-green); color: var(--secondary-green);"
                                       onmouseover="this.style.background='var(--secondary-green)'; this.style.color='white'"
                                       onmouseout="this.style.background='var(--light-green)'; this.style.color='var(--secondary-green)'"
                                       title="Ko'rish">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('employees.edit', $employee) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-md transition-all"
                                       style="background: #fef3e2; color: #f39c12;"
                                       onmouseover="this.style.background='#f39c12'; this.style.color='white'"
                                       onmouseout="this.style.background='#fef3e2'; this.style.color='#f39c12'"
                                       title="Tahrirlash">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    @if($employee->is_teacher)
                                        <a href="{{ route('employees.teachers.subjects', $employee) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md transition-all"
                                           style="background: #e8f4fd; color: #3498db;"
                                           onmouseover="this.style.background='#3498db'; this.style.color='white'"
                                           onmouseout="this.style.background='#e8f4fd'; this.style.color='#3498db'"
                                           title="Fanlar">
                                            <i class="fas fa-book text-sm"></i>
                                        </a>
                                        <a href="{{ route('employees.teachers.workload', $employee) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md transition-all"
                                           style="background: #f3e8fd; color: #9b59b6;"
                                           onmouseover="this.style.background='#9b59b6'; this.style.color='white'"
                                           onmouseout="this.style.background='#f3e8fd'; this.style.color='#9b59b6'"
                                           title="Yuklama">
                                            <i class="fas fa-chart-bar text-sm"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-4xl mb-3" style="color: var(--border-green);"></i>
                                    <p style="color: #7f8c8d;">Xodimlar topilmadi</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
            <div class="px-4 py-3" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection