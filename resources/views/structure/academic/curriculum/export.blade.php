@extends('layouts.dashboard-new')

@section('title', "O'quv reja export - " . $program->name_uz)
@section('page-title', "O'quv reja export")

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('structure.academic.curriculum.index') }}">O'quv rejalar</a></li>
                    <li class="breadcrumb-item active">Export</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Export Options -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Export parametrlari</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>{{ $program->code }} - {{ $program->name_uz }}</h6>
                    <p class="text-muted">O'quv yili: {{ $academicYear }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Chop etish
                    </button>
                    <button onclick="exportToExcel()" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button onclick="exportToPDF()" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Curriculum Preview -->
    <div class="card shadow-sm" id="exportContent">
        <div class="card-body">
            <div class="text-center mb-4">
                <h4>O'ZBEKISTON RESPUBLIKASI OLIY TA'LIM, FAN VA INNOVATSIYALAR VAZIRLIGI</h4>
                <h5>{{ strtoupper($program->faculty->university_name ?? 'UNIVERSITET NOMI') }}</h5>
                <hr>
                <h5>O'QUV REJA</h5>
                <p>
                    <strong>Ta'lim yo'nalishi:</strong> {{ $program->code }} - {{ $program->name_uz }}<br>
                    <strong>Ta'lim shakli:</strong> {{ ucfirst($program->education_form ?? 'kunduzgi') }}<br>
                    <strong>O'quv yili:</strong> {{ $academicYear }}
                </p>
            </div>

            @php
                $totalCredits = 0;
                $totalHours = 0;
                $totalLecture = 0;
                $totalPractice = 0;
                $totalSeminar = 0;
                $totalLab = 0;
                $totalIndependent = 0;
            @endphp

            <table class="table table-bordered table-sm">
                <thead>
                    <tr class="table-light">
                        <th rowspan="2" class="text-center align-middle">№</th>
                        <th rowspan="2" class="text-center align-middle">Fan nomi</th>
                        <th rowspan="2" class="text-center align-middle">Fan kodi</th>
                        <th rowspan="2" class="text-center align-middle">Semestr</th>
                        <th colspan="5" class="text-center">Auditoriya soatlari</th>
                        <th rowspan="2" class="text-center align-middle">Mustaqil ta'lim</th>
                        <th rowspan="2" class="text-center align-middle">Jami soat</th>
                        <th rowspan="2" class="text-center align-middle">Kredit</th>
                    </tr>
                    <tr class="table-light">
                        <th class="text-center">Ma'ruza</th>
                        <th class="text-center">Amaliyot</th>
                        <th class="text-center">Seminar</th>
                        <th class="text-center">Lab</th>
                        <th class="text-center">Jami</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @foreach($curriculum as $item)
                        @php
                            $auditoryHours = $item->lecture_hours + $item->practice_hours + $item->seminar_hours + $item->lab_hours;
                            $totalHoursItem = $auditoryHours + $item->independent_hours;
                            
                            $totalCredits += $item->credits;
                            $totalHours += $totalHoursItem;
                            $totalLecture += $item->lecture_hours;
                            $totalPractice += $item->practice_hours;
                            $totalSeminar += $item->seminar_hours;
                            $totalLab += $item->lab_hours;
                            $totalIndependent += $item->independent_hours;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $counter++ }}</td>
                            <td>{{ $item->subject->name_uz }}</td>
                            <td class="text-center">{{ $item->subject->code }}</td>
                            <td class="text-center">{{ $item->semester_number }}</td>
                            <td class="text-center">{{ $item->lecture_hours }}</td>
                            <td class="text-center">{{ $item->practice_hours }}</td>
                            <td class="text-center">{{ $item->seminar_hours }}</td>
                            <td class="text-center">{{ $item->lab_hours }}</td>
                            <td class="text-center"><strong>{{ $auditoryHours }}</strong></td>
                            <td class="text-center">{{ $item->independent_hours }}</td>
                            <td class="text-center"><strong>{{ $totalHoursItem }}</strong></td>
                            <td class="text-center"><strong>{{ $item->credits }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="4" class="text-end">JAMI:</th>
                        <th class="text-center">{{ $totalLecture }}</th>
                        <th class="text-center">{{ $totalPractice }}</th>
                        <th class="text-center">{{ $totalSeminar }}</th>
                        <th class="text-center">{{ $totalLab }}</th>
                        <th class="text-center">{{ $totalLecture + $totalPractice + $totalSeminar + $totalLab }}</th>
                        <th class="text-center">{{ $totalIndependent }}</th>
                        <th class="text-center">{{ $totalHours }}</th>
                        <th class="text-center">{{ $totalCredits }}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="row mt-5">
                <div class="col-md-6">
                    <p>
                        <strong>Fakultet dekani:</strong> ___________________<br>
                        <small class="text-muted">Imzo, F.I.O</small>
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <p>
                        <strong>O'quv ishlari bo'yicha prorektor:</strong> ___________________<br>
                        <small class="text-muted">Imzo, F.I.O</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style media="print">
    @page {
        size: A4 landscape;
        margin: 1cm;
    }
    
    body * {
        visibility: hidden;
    }
    
    #exportContent, #exportContent * {
        visibility: visible;
    }
    
    #exportContent {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    .btn, .breadcrumb, .card-header {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
function exportToExcel() {
    // Create CSV content
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "№,Fan nomi,Fan kodi,Semestr,Ma'ruza,Amaliyot,Seminar,Laboratoriya,Mustaqil ta'lim,Jami soat,Kredit\n";
    
    // Add data rows
    const table = document.querySelector('#exportContent table');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const rowData = Array.from(cells).map(cell => cell.textContent.trim()).join(',');
        csvContent += rowData + "\n";
    });
    
    // Download CSV
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "curriculum_{{ $program->code }}_{{ $academicYear }}.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToPDF() {
    alert('PDF export funksiyasi tez orada qo\'shiladi');
    // You can integrate with jsPDF or similar library
}
</script>
@endpush
@endsection