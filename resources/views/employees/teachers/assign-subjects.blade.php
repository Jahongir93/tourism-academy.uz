@extends('layouts.dashboard-new')

@section('title', 'O\'qituvchi fanlari - ' . $teacher->full_name)
@section('page-title', 'O\'qituvchi fanlari')

@section('styles')
<style>
    :root {
        --primary-green: #0d4f3c;
        --secondary-green: #16a085;
        --light-green: #e8f5f0;
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
            <a href="{{ route('employees.show', $teacher) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-2"></i>Orqaga
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Current Assignments -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-4 py-3 border-b" style="background: var(--light-green);">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-book mr-2" style="color: var(--secondary-green);"></i>
                        Biriktirilgan fanlar
                    </h3>
                </div>
                <div class="p-4">
                    @if($currentAssignments->count() > 0)
                        <div class="space-y-3">
                            @foreach($currentAssignments as $assignment)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-grow">
                                            <h4 class="font-semibold text-gray-800">
                                                {{ $assignment->subject->name_uz ?? $assignment->subject->name ?? 'Noma\'lum fan' }}
                                            </h4>
                                            <p class="text-sm text-gray-500 mt-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                Ma'ruza: {{ $assignment->lecture_hours ?? 0 }} soat |
                                                Amaliy: {{ $assignment->practice_hours ?? 0 }} soat |
                                                Lab: {{ $assignment->lab_hours ?? 0 }} soat
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $assignment->academic_year_id ?? date('Y') }}-yil, {{ $assignment->semester_id ?? 1 }}-semestr
                                            </p>

                                            {{-- Show assigned groups with links --}}
                                            @if($assignment->groups && $assignment->groups->count() > 0)
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    <span class="text-xs text-gray-500"><i class="fas fa-users mr-1"></i>Guruhlar:</span>
                                                    @foreach($assignment->groups as $group)
                                                        <a href="{{ route('student-contingent.groups.show', $group->id) }}"
                                                           class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full transition"
                                                           style="background: var(--light-green); color: var(--primary-green);"
                                                           title="Guruh sahifasiga o'tish">
                                                            <i class="fas fa-external-link-alt mr-1"></i>
                                                            {{ $group->name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @elseif($assignment->group_ids && count($assignment->group_ids) > 0)
                                                <div class="mt-2">
                                                    <span class="text-xs text-gray-400">
                                                        <i class="fas fa-users mr-1"></i>{{ count($assignment->group_ids) }} ta guruh biriktirilgan
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex gap-2 ml-2">
                                            <form action="{{ route('employees.teachers.subjects.destroy', [$teacher->id, $assignment->id]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Haqiqatan ham o\'chirmoqchimisiz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 p-2" title="O'chirish">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-book-open text-4xl mb-3 opacity-30"></i>
                            <p>Hozircha biriktirilgan fan yo'q</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add New Assignment -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="px-4 py-3 border-b" style="background: var(--primary-green);">
                    <h3 class="font-semibold text-white">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Yangi fan biriktirish
                    </h3>
                </div>
                <div class="p-4">
                    <form action="{{ route('employees.teachers.subjects.store', $teacher->id) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fan</label>
                            <select name="subject_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">-- Tanlang --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name_uz ?? $subject->name ?? $subject->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">O'quv yili</label>
                                <select name="academic_year_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                                    @forelse($academicYears ?? [] as $year)
                                        <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                                            {{ $year->year ?? $year->name }}
                                        </option>
                                    @empty
                                        <option value="">O'quv yili topilmadi</option>
                                    @endforelse
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Semestr</label>
                                <select name="semester_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                                    <option value="1">1-semestr</option>
                                    <option value="2">2-semestr</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Guruhlar</label>
                            <select name="group_ids[]" multiple required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500" style="min-height: 100px;">
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Ctrl tugmasini bosib bir nechta tanlang</p>
                        </div>

                        <div class="grid grid-cols-3 gap-2 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Ma'ruza (soat)</label>
                                <input type="number" name="lecture_hours" value="0" min="0" required
                                       class="w-full px-2 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Amaliy (soat)</label>
                                <input type="number" name="practice_hours" value="0" min="0" required
                                       class="w-full px-2 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Lab (soat)</label>
                                <input type="number" name="lab_hours" value="0" min="0" required
                                       class="w-full px-2 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 text-sm">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">O'qitish tili</label>
                            <select name="language" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                                <option value="uz">O'zbekcha</option>
                                <option value="ru">Ruscha</option>
                                <option value="en">Inglizcha</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-2 px-4 text-white rounded-md transition"
                                style="background: var(--primary-green);"
                                onmouseover="this.style.background='var(--secondary-green)'"
                                onmouseout="this.style.background='var(--primary-green)'">
                            <i class="fas fa-plus mr-2"></i>Biriktirish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
