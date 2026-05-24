@extends('layouts.dashboard-new')

@section('title', 'Kelib tushgan arizalar - Onlayn Qabul')

@section('content')
<style>
    :root {
        --primary-green: #16a085;
        --dark-green: #0d4f3c;
        --light-green: #e8f5f0;
        --accent-green: #48c9b0;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-reviewing {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-accepted {
        background: #d1fae5;
        color: #065f46;
    }

    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-waitlist {
        background: #e5e7eb;
        color: #374151;
    }

    .table-row-hover:hover {
        background: var(--light-green);
        cursor: pointer;
    }
</style>

<div class="container-fluid px-6 py-4">
    <!-- Header -->
    <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Kelib tushgan arizalar</h2>
                        <p class="text-gray-600 mt-1">Onlayn qabul arizalarini boshqarish</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admission.statistics') }}" class="bg-white px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                            <i class="fas fa-chart-bar mr-2"></i> Statistika
                        </a>
                        <a href="{{ route('admission.export') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-download mr-2"></i> Eksport
                        </a>
                    </div>
                </div>
            </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        @php
            $stats = [
                'pending' => \App\Models\AdmissionApplication::where('status', 'pending')->count(),
                'reviewing' => \App\Models\AdmissionApplication::where('status', 'reviewing')->count(),
                'accepted' => \App\Models\AdmissionApplication::where('status', 'accepted')->count(),
                'rejected' => \App\Models\AdmissionApplication::where('status', 'rejected')->count(),
                'total' => \App\Models\AdmissionApplication::count()
            ];
        @endphp

        <div class="bg-white rounded-lg p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Kutilmoqda</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</p>
                </div>
                <i class="fas fa-clock text-3xl text-yellow-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Ko'rilmoqda</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['reviewing'] }}</p>
                </div>
                <i class="fas fa-search text-3xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Qabul qilindi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['accepted'] }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Rad etildi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['rejected'] }}</p>
                </div>
                <i class="fas fa-times-circle text-3xl text-red-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Jami</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <i class="fas fa-file-alt text-3xl text-gray-500"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admission.applications') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Holat</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                    <option value="">Barchasi</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                    <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>Ko'rilmoqda</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Qabul qilindi</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rad etildi</option>
                    <option value="waitlist" {{ request('status') == 'waitlist' ? 'selected' : '' }}>Kutish ro'yxati</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fakultet</label>
                <select name="faculty_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                    <option value="">Barchasi</option>
                    @foreach($faculties as $faculty)
                        <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                            {{ $faculty->name_uz }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ta'lim shakli</label>
                <select name="education_form" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                    <option value="">Barchasi</option>
                    <option value="kunduzgi" {{ request('education_form') == 'kunduzgi' ? 'selected' : '' }}>Kunduzgi</option>
                    <option value="sirtqi" {{ request('education_form') == 'sirtqi' ? 'selected' : '' }}>Sirtqi</option>
                    <option value="kechki" {{ request('education_form') == 'kechki' ? 'selected' : '' }}>Kechki</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Qidirish</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                    placeholder="Ism, telefon, ariza raqami...">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-search mr-2"></i> Qidirish
                </button>
                <a href="{{ route('admission.applications') }}" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ariza №
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            F.I.O
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fakultet / Yo'nalish
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ta'lim
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Telefon
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sana
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Holat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amallar
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applications as $application)
                    <tr class="table-row-hover" onclick="window.location='{{ route('admission.view-application', $application) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $application->application_number }}
                            </div>
                            <div class="text-xs text-gray-500">
                                ID: {{ $application->id }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $application->full_name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $application->birth_date->format('d.m.Y') }} ({{ $application->age }} yosh)
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $application->faculty->name_uz ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $application->specialty->name_uz ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $application->education_form_text }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $application->education_language_text }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $application->phone }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $application->region }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $application->applied_at->format('d.m.Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $application->applied_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-badge status-{{ $application->status }}">
                                {{ $application->status_text }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                            <div class="flex gap-2">
                                <a href="{{ route('admission.view-application', $application) }}"
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="updateStatus({{ $application->id }})"
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteApplication({{ $application->id }})"
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Arizalar topilmadi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
        <div class="bg-gray-50 px-6 py-3 border-t">
            {{ $applications->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Ariza holatini yangilash</h3>
        <form id="statusForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Holat</label>
                <select id="statusSelect" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="pending">Kutilmoqda</option>
                    <option value="reviewing">Ko'rilmoqda</option>
                    <option value="accepted">Qabul qilindi</option>
                    <option value="rejected">Rad etildi</option>
                    <option value="waitlist">Kutish ro'yxati</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Izoh</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-lg">
                    Bekor qilish
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Saqlash
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentApplicationId = null;

    function updateStatus(id) {
        currentApplicationId = id;
        document.getElementById('statusModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('statusModal').classList.add('hidden');
    }

    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(`/marketing/admission/applications/${currentApplicationId}/status`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });

    function deleteApplication(id) {
        if (confirm('Haqiqatan ham bu arizani o\'chirmoqchimisiz?')) {
            fetch(`/marketing/admission/applications/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }
</script>
@endsection