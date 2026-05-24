@extends('layouts.dashboard-new')

@section('title', 'O\'qituvchi yuklamasi - ' . $teacher->full_name)
@section('page-title', 'O\'qituvchi yuklamasi')

@section('styles')
<style>
    :root {
        --primary-green: #0d4f3c;
        --secondary-green: #16a085;
        --light-green: #e8f5f0;
    }
    .progress-bar {
        height: 20px;
        border-radius: 10px;
        background: #e2e8f0;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.5s ease;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background: var(--light-green);">
                    @if($teacher->photo)
                        <img src="{{ asset('storage/' . $teacher->photo) }}" class="w-16 h-16 rounded-full object-cover" alt="{{ $teacher->full_name }}">
                    @else
                        <i class="fas fa-user text-2xl" style="color: var(--secondary-green);"></i>
                    @endif
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $teacher->full_name }}</h2>
                    <p class="text-gray-500 text-sm">
                        {{ $teacher->employee_code }} |
                        {{ $teacher->employmentDetail->position->name_uz ?? 'O\'qituvchi' }}
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('employees.teachers.subjects', $teacher) }}" class="px-4 py-2 text-white rounded-lg transition"
                   style="background: var(--secondary-green);">
                    <i class="fas fa-book mr-2"></i>Fanlar
                </a>
                <a href="{{ route('employees.show', $teacher) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Orqaga
                </a>
            </div>
        </div>
    </div>

    <!-- Workload Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full" style="background: var(--light-green);">
                    <i class="fas fa-clock text-xl" style="color: var(--primary-green);"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Rejadagi soatlar</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $workload->planned_hours ?? 680 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-chalkboard text-xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">O'qitish soatlari</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $workload->teaching_hours ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <i class="fas fa-user-graduate text-xl text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Tarbiyaviy soatlar</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $workload->educational_hours ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100">
                    <i class="fas fa-percentage text-xl text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Bajarilish</p>
                    @php
                        $totalHours = ($workload->teaching_hours ?? 0) + ($workload->educational_hours ?? 0);
                        $percentage = $workload->planned_hours > 0 ? round(($totalHours / $workload->planned_hours) * 100) : 0;
                    @endphp
                    <p class="text-2xl font-bold text-gray-800">{{ $percentage }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <h3 class="font-semibold text-gray-800 mb-3">Yuklama bajarilishi</h3>
        <div class="progress-bar">
            @php
                $fillColor = $percentage < 50 ? '#e74c3c' : ($percentage < 80 ? '#f39c12' : '#27ae60');
            @endphp
            <div class="progress-fill" style="width: {{ min($percentage, 100) }}%; background: {{ $fillColor }};"></div>
        </div>
        <div class="flex justify-between text-sm text-gray-500 mt-2">
            <span>0 soat</span>
            <span>{{ $totalHours }} / {{ $workload->planned_hours ?? 680 }} soat</span>
            <span>{{ $workload->planned_hours ?? 680 }} soat</span>
        </div>
    </div>

    <!-- Subject Assignments -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b" style="background: var(--light-green);">
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-list mr-2" style="color: var(--secondary-green);"></i>
                Fan bo'yicha taqsimot
            </h3>
        </div>
        <div class="p-4">
            @if($assignments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Fan nomi</th>
                                <th class="px-4 py-2 text-center font-medium text-gray-600">Ma'ruza</th>
                                <th class="px-4 py-2 text-center font-medium text-gray-600">Amaliy</th>
                                <th class="px-4 py-2 text-center font-medium text-gray-600">Lab</th>
                                <th class="px-4 py-2 text-center font-medium text-gray-600">Jami</th>
                                <th class="px-4 py-2 text-center font-medium text-gray-600">Semestr</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assignment)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $assignment->subject->name_uz ?? $assignment->subject->name ?? 'Noma\'lum' }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ $assignment->lecture_hours ?? 0 }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ $assignment->practice_hours ?? 0 }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ $assignment->lab_hours ?? 0 }}</td>
                                    <td class="px-4 py-3 text-center font-semibold" style="color: var(--primary-green);">
                                        {{ ($assignment->lecture_hours ?? 0) + ($assignment->practice_hours ?? 0) + ($assignment->lab_hours ?? 0) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">
                                            {{ $assignment->semester_id ?? 1 }}-semestr
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 font-semibold">
                                <td class="px-4 py-3">Jami:</td>
                                <td class="px-4 py-3 text-center">{{ $assignments->sum('lecture_hours') }}</td>
                                <td class="px-4 py-3 text-center">{{ $assignments->sum('practice_hours') }}</td>
                                <td class="px-4 py-3 text-center">{{ $assignments->sum('lab_hours') }}</td>
                                <td class="px-4 py-3 text-center" style="color: var(--primary-green);">
                                    {{ $assignments->sum('lecture_hours') + $assignments->sum('practice_hours') + $assignments->sum('lab_hours') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-bar text-4xl mb-3 opacity-30"></i>
                    <p>Hozircha biriktirilgan fan yo'q</p>
                    <a href="{{ route('employees.teachers.subjects', $teacher) }}" class="mt-3 inline-block px-4 py-2 text-white rounded-lg"
                       style="background: var(--secondary-green);">
                        <i class="fas fa-plus mr-2"></i>Fan biriktirish
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
