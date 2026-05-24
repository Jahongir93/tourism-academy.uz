@extends('layouts.dashboard-new')

@section('title', 'Sayt Shablonlari')
@section('page-title', 'Sayt Shablonlari')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">Sayt shablonlari</h2>
                    <p class="text-muted mb-0">Saytning umumiy dizaynini boshqarish</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @foreach($templates as $key => $template)
            <div class="col-md-6">
                <div class="card h-100 shadow-sm {{ $currentTemplate === $key ? 'border-primary border-2' : '' }}">
                    @if($currentTemplate === $key)
                        <div class="position-absolute top-0 end-0 m-3 z-3">
                            <span class="badge bg-primary">
                                <i class="fas fa-check me-1"></i>Faol
                            </span>
                        </div>
                    @endif

                    <!-- Template Preview -->
                    <div class="card-img-top position-relative" style="height: 220px; overflow: hidden;">
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center"
                             style="background: linear-gradient(135deg, {{ $template['colors'][0] }} 0%, {{ $template['colors'][1] ?? $template['colors'][0] }} 100%);">
                            <div class="text-center text-white p-4">
                                <i class="fas fa-palette fa-3x mb-3" style="opacity: 0.8;"></i>
                                <h4 class="mb-0">{{ $template['name'] }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $template['name'] }}</h5>
                        <p class="card-text text-muted">{{ $template['description'] }}</p>

                        <!-- Color Palette -->
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">Ranglar palitrasi:</small>
                            <div class="d-flex gap-2">
                                @foreach($template['colors'] as $color)
                                    <div class="rounded-circle border shadow-sm"
                                         style="width: 32px; height: 32px; background-color: {{ $color }};"
                                         title="{{ $color }}"></div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-top-0 pb-4">
                        @if($currentTemplate === $key)
                            <button class="btn btn-outline-success w-100" disabled>
                                <i class="fas fa-check me-2"></i>Hozirda faol
                            </button>
                        @else
                            <form action="{{ route('cms.templates.activate') }}" method="POST">
                                @csrf
                                <input type="hidden" name="template" value="{{ $key }}">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-check-circle me-2"></i>Faollashtirish
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Template Features Comparison -->
    <div class="card mt-5 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-list-check me-2 text-primary"></i>Shablonlar taqqoslash</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Xususiyat</th>
                            <th class="text-center">Classic (Neon)</th>
                            <th class="text-center">Modern Academic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="fas fa-fill-drip me-2 text-muted"></i>Asosiy rang</td>
                            <td class="text-center">
                                <span class="badge" style="background: #655CFF;">Neon binafsha</span>
                            </td>
                            <td class="text-center">
                                <span class="badge" style="background: #0A1F44;">Chuqur ko'k</span>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-highlighter me-2 text-muted"></i>Aksent rang</td>
                            <td class="text-center">
                                <span class="badge text-dark" style="background: #D9FF5F;">Neon yashil</span>
                            </td>
                            <td class="text-center">
                                <span class="badge" style="background: #2F80ED;">Moviy</span>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-font me-2 text-muted"></i>Tipografiya</td>
                            <td class="text-center">Inter</td>
                            <td class="text-center">Inter / Poppins</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-window-maximize me-2 text-muted"></i>Header uslubi</td>
                            <td class="text-center">Shaffof gradient</td>
                            <td class="text-center">2 qatlamli (utility + main)</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-shoe-prints me-2 text-muted"></i>Footer uslubi</td>
                            <td class="text-center">Qora gradient</td>
                            <td class="text-center">Oq minimalistik</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-bullseye me-2 text-muted"></i>Maqsad</td>
                            <td class="text-center"><span class="badge bg-secondary">Zamonaviy, kreativ</span></td>
                            <td class="text-center"><span class="badge bg-info">Akademik, professional</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Usage Instructions -->
    <div class="card mt-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-info-circle" style="color:var(--c-sky)"></i>
            <span>Qo'llanma</span>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li>Shablonni faollashtirish uchun "Faollashtirish" tugmasini bosing</li>
                <li>Shablon o'zgarishi darhol barcha sahifalarda ko'rinadi</li>
                <li>Sahifadagi mazmun (content) saqlanib qoladi</li>
                <li>Faqat dizayn (ranglar, shriftlar, layout) o'zgaradi</li>
            </ul>
        </div>
    </div>
@endsection
