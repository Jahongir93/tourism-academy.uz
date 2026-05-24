@extends('layouts.dashboard-new')

@section('title', 'Dars soatlari taqsimoti - HEMIS')
@section('page-title', "Dars soatlari taqsimoti")

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
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2" style="color: #7f8c8d;">Jami fanlar</h6>
                            <h3 class="mb-0" style="color: var(--text-dark);">{{ $stats['total_subjects'] }}</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green));">
                            <i class="fas fa-book fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2" style="color: #7f8c8d;">Taqsimlangan</h6>
                            <h3 class="mb-0" style="color: var(--primary-dark-green);">{{ $stats['with_distribution'] }}</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: linear-gradient(135deg, var(--secondary-green), var(--accent-green));">
                            <i class="fas fa-check-circle fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2" style="color: #7f8c8d;">Taqsimlanmagan</h6>
                            <h3 class="mb-0" style="color: var(--secondary-green);">{{ $stats['without_distribution'] }}</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: var(--light-green);">
                            <i class="fas fa-exclamation-triangle fa-lg" style="color: var(--primary-dark-green);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert mb-4" style="background: var(--light-green); border: 1px solid var(--border-green); color: var(--text-dark);">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-3" style="background: var(--secondary-green);">
                <i class="fas fa-info-circle text-white"></i>
            </div>
            <div>
                <strong>Eslatma:</strong> Har bir fan uchun dars soatlari taqsimoti 1 kredit = 30 soat formulasi asosida hisoblanadi.
                Auditoriya soatlari (ma'ruza, amaliyot, seminar, laboratoriya) umumiy soatlarning 50% dan oshmasligi kerak.
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important; background: linear-gradient(135deg, var(--very-light-green), white);">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                    <i class="fas fa-bolt" style="color: var(--secondary-green);"></i> Tezkor amallar
                </h5>
                <div>
                    <a href="{{ route('structure.academic.hours.template') }}" class="btn text-white"
                       style="background: var(--primary-dark-green);"
                       onmouseover="this.style.background='var(--secondary-green)'"
                       onmouseout="this.style.background='var(--primary-dark-green)'">
                        <i class="fas fa-file-alt me-1"></i> Shablonlar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects Table -->
    <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
        <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
            <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                <i class="fas fa-clock" style="color: var(--secondary-green);"></i> Fanlar bo'yicha soatlar taqsimoti
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fan kodi</th>
                            <th>Fan nomi</th>
                            <th>Kafedra</th>
                            <th>Kreditlar</th>
                            <th>Jami soatlar</th>
                            <th>Taqsimot holati</th>
                            <th width="150">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                            <tr>
                                <td>
                                    <strong>{{ $subject->code }}</strong>
                                </td>
                                <td>{{ $subject->name_uz }}</td>
                                <td>{{ $subject->department->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $subject->credits }} kr</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $subject->total_hours }} soat</span>
                                </td>
                                <td>
                                    @if($subject->hourDistributions->count() > 0)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>
                                            {{ $subject->hourDistributions->count() }} ta taqsimot
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation me-1"></i>
                                            Taqsimlanmagan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('structure.academic.hours.distribution', $subject) }}" 
                                       class="btn btn-sm btn-primary" title="Soatlar taqsimoti">
                                        <i class="fas fa-clock"></i> Taqsimlash
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="text-muted mb-0">Fanlar topilmadi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $subjects->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Hour Distribution Legend -->
<div class="card shadow-sm mt-4">
    <div class="card-header bg-white">
        <h6 class="mb-0">Soatlar taqsimoti qoidalari</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Auditoriya soatlari (maksimal 50%)</h6>
                <ul class="small">
                    <li><strong>Ma'ruza:</strong> Nazariy materiallarni o'rganish</li>
                    <li><strong>Amaliyot:</strong> Amaliy ko'nikmalarni rivojlantirish</li>
                    <li><strong>Seminar:</strong> Muhokama va tahlil qilish</li>
                    <li><strong>Laboratoriya:</strong> Tajriba va tadqiqot ishlari</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Mustaqil ta'lim (minimal 50%)</h6>
                <ul class="small">
                    <li><strong>Mustaqil o'rganish:</strong> Adabiyotlar bilan ishlash</li>
                    <li><strong>Topshiriqlar:</strong> Uy vazifalar va loyihalar</li>
                    <li><strong>Tayyorgarlik:</strong> Imtihonlarga tayyorlanish</li>
                    <li><strong>Kurs ishi:</strong> Ilmiy-tadqiqot ishlari (agar mavjud bo'lsa)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection