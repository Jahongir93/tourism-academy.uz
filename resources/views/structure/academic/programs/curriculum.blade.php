@extends('layouts.dashboard-new')

@section('title', "O'quv reja - " . $program->name_uz)
@section('page-title', "O'quv reja - " . $program->name_uz)

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('structure.academic.programs.index') }}">Ta'lim yo'nalishlari</a></li>
                    <li class="breadcrumb-item active">O'quv reja</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Program Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ $program->code }} - {{ $program->name_uz }}</h5>
                    <p class="mb-1"><strong>Fakultet:</strong> {{ $program->faculty->name_uz ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Kafedra:</strong> {{ $program->department->name_uz ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-1"><strong>Ta'lim shakli:</strong> {{ ucfirst($program->education_form) }}</p>
                    <p class="mb-1"><strong>Daraja:</strong> {{ ucfirst($program->level) }}</p>
                    <p class="mb-1"><strong>O'quv muddati:</strong> {{ $program->duration_years }} yil</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Year Selector -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">O'quv yili</label>
                    <select name="academic_year" class="form-select" onchange="this.form.submit()">
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-9 text-end">
                    <a href="{{ route('structure.academic.curriculum.builder', $program) }}?academic_year={{ $academicYear }}" 
                       class="btn btn-primary">
                        <i class="fas fa-edit"></i> O'quv rejani tahrirlash
                    </a>
                    <a href="{{ route('structure.academic.curriculum.export', $program) }}?academic_year={{ $academicYear }}" 
                       class="btn btn-success">
                        <i class="fas fa-download"></i> Export
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Curriculum by Semesters -->
    @for($sem = 1; $sem <= $program->duration_years * 2; $sem++)
        @php
            $semesterCurriculum = $curriculum->get($sem, collect());
            $semesterCredits = $semesterCurriculum->sum('credits');
            $semesterHours = $semesterCurriculum->sum('total_hours');
        @endphp
        
        @if($semesterCurriculum->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $sem }}-semestr</h5>
                        <div>
                            <span class="badge bg-white text-primary">{{ $semesterCredits }} kredit</span>
                            <span class="badge bg-white text-primary">{{ $semesterHours }} soat</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">№</th>
                                    <th>Fan nomi</th>
                                    <th width="100">Fan kodi</th>
                                    <th width="80">Kredit</th>
                                    <th width="100">Ma'ruza</th>
                                    <th width="100">Amaliyot</th>
                                    <th width="100">Seminar</th>
                                    <th width="100">Laboratoriya</th>
                                    <th width="100">Mustaqil</th>
                                    <th width="100">Jami soat</th>
                                    <th width="100">Fan turi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($semesterCurriculum as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->subject->name_uz }}</td>
                                        <td>{{ $item->subject->code }}</td>
                                        <td class="text-center">{{ $item->credits }}</td>
                                        <td class="text-center">{{ $item->lecture_hours }}</td>
                                        <td class="text-center">{{ $item->practice_hours }}</td>
                                        <td class="text-center">{{ $item->seminar_hours }}</td>
                                        <td class="text-center">{{ $item->lab_hours }}</td>
                                        <td class="text-center">{{ $item->independent_hours }}</td>
                                        <td class="text-center"><strong>{{ $item->total_hours }}</strong></td>
                                        <td>
                                            <span class="badge {{ $item->subject_type == 'majburiy' ? 'bg-primary' : 'bg-warning' }}">
                                                {{ ucfirst($item->subject_type) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="3">Jami:</th>
                                    <th class="text-center">{{ $semesterCredits }}</th>
                                    <th class="text-center">{{ $semesterCurriculum->sum('lecture_hours') }}</th>
                                    <th class="text-center">{{ $semesterCurriculum->sum('practice_hours') }}</th>
                                    <th class="text-center">{{ $semesterCurriculum->sum('seminar_hours') }}</th>
                                    <th class="text-center">{{ $semesterCurriculum->sum('lab_hours') }}</th>
                                    <th class="text-center">{{ $semesterCurriculum->sum('independent_hours') }}</th>
                                    <th class="text-center"><strong>{{ $semesterHours }}</strong></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endfor

    <!-- Overall Statistics -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Umumiy statistika</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h3>{{ $stats['total_credits'] }}</h3>
                        <p class="text-muted">Jami kredit</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3>{{ $stats['total_hours'] }}</h3>
                        <p class="text-muted">Jami soat</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3>{{ $stats['auditory_hours'] }}</h3>
                        <p class="text-muted">Auditoriya soatlari</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3>{{ $stats['independent_hours'] }}</h3>
                        <p class="text-muted">Mustaqil ta'lim</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection