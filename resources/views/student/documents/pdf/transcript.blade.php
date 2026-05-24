<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademik ma'lumotnoma</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .logo {
            font-size: 16pt;
            font-weight: bold;
            color: #1a56db;
            margin-bottom: 5px;
        }
        .university-name {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .doc-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .student-info {
            margin: 20px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border-left: 4px solid #1a56db;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 150px;
            padding: 3px 10px 3px 0;
        }
        .info-value {
            display: table-cell;
            padding: 3px 0;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9pt;
        }
        .grades-table th {
            background-color: #1a56db;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        .grades-table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
        }
        .grades-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .semester-header {
            background-color: #e5e7eb !important;
            font-weight: bold;
            padding: 8px 5px !important;
        }
        .summary {
            margin: 20px 0;
            padding: 15px;
            background-color: #f0f9ff;
            border: 1px solid #1a56db;
            border-radius: 5px;
        }
        .summary-grid {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        .summary-item {
            flex: 1;
        }
        .summary-value {
            font-size: 18pt;
            font-weight: bold;
            color: #1a56db;
        }
        .summary-label {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }
        .grading-scale {
            margin: 20px 0;
            font-size: 9pt;
            padding: 10px;
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
        }
        .scale-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .scale-table {
            width: 100%;
            margin-top: 10px;
        }
        .scale-table td {
            padding: 3px 8px;
        }
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-block {
            width: 45%;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            margin: 20px 0 5px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">HEMIS</div>
        <div class="university-name">{{ $university ?? 'Turizm Akademiyasi' }}</div>
    </div>

    <!-- Title -->
    <div class="doc-title">AKADEMIK MA'LUMOTNOMA<br>(Academic Transcript)</div>

    <!-- Student Information -->
    <div class="student-info">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">F.I.Sh. / Full Name:</div>
                <div class="info-value">{{ $student->user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Talaba ID / Student ID:</div>
                <div class="info-value">{{ $student->student_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Mutaxassislik / Major:</div>
                <div class="info-value">{{ $student->group->specialty->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Ta'lim shakli / Form:</div>
                <div class="info-value">{{ $student->education_form ?? 'Kunduzgi / Full-time' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Hujjat sanasi / Date:</div>
                <div class="info-value">{{ $date }}</div>
            </div>
        </div>
    </div>

    <!-- Academic Summary -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ number_format($gpa, 2) }}</div>
                <div class="summary-label">GPA (4.0 shkala)</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $totalCredits }}</div>
                <div class="summary-label">Jami kreditlar</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $completedCourses }}</div>
                <div class="summary-label">Tugatilgan fanlar</div>
            </div>
        </div>
    </div>

    <!-- Grades Table -->
    <table class="grades-table">
        <thead>
            <tr>
                <th style="width: 5%">№</th>
                <th style="width: 40%">Fan nomi / Course Name</th>
                <th style="width: 10%">Kredit / Credit</th>
                <th style="width: 10%">Ball / Score</th>
                <th style="width: 10%">Baho / Grade</th>
                <th style="width: 10%">GPA</th>
                <th style="width: 15%">Sana / Date</th>
            </tr>
        </thead>
        <tbody>
            @php
                $counter = 1;
                $currentSemester = null;
            @endphp
            @foreach($grades as $grade)
                @if($currentSemester !== $grade->semester)
                    @php
                        $currentSemester = $grade->semester;
                    @endphp
                    <tr>
                        <td colspan="7" class="semester-header">
                            {{ $grade->semester }}-semestr / Semester {{ $grade->semester }}
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="text-align: center">{{ $counter++ }}</td>
                    <td>{{ $grade->subject_name }}</td>
                    <td style="text-align: center">{{ $grade->credits ?? 4 }}</td>
                    <td style="text-align: center">{{ $grade->score }}</td>
                    <td style="text-align: center"><strong>{{ $grade->letter_grade }}</strong></td>
                    <td style="text-align: center">{{ number_format($grade->grade_point, 2) }}</td>
                    <td style="text-align: center">{{ \Carbon\Carbon::parse($grade->graded_date)->format('d.m.Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Grading Scale -->
    <div class="grading-scale">
        <div class="scale-title">Baholash tizimi / Grading Scale:</div>
        <table class="scale-table">
            <tr>
                <td><strong>A (90-100)</strong> - A'lo / Excellent - GPA 4.0</td>
                <td><strong>B (80-89)</strong> - Yaxshi / Good - GPA 3.0</td>
            </tr>
            <tr>
                <td><strong>C (70-79)</strong> - Qoniqarli / Satisfactory - GPA 2.0</td>
                <td><strong>D (60-69)</strong> - Qoniqarsiz / Unsatisfactory - GPA 1.0</td>
            </tr>
            <tr>
                <td colspan="2"><strong>F (0-59)</strong> - Qoniqarsiz / Fail - GPA 0.0</td>
            </tr>
        </table>
    </div>

    <!-- Footer with Signatures -->
    <div class="footer">
        <div class="signature-block">
            <div>O'quv ishlari bo'yicha prorektor</div>
            <div>Vice-Rector for Academic Affairs</div>
            <div class="signature-line"></div>
            <div>{{ $vicerectorName ?? '_______________' }}</div>
        </div>
        <div class="signature-block" style="text-align: right;">
            <div>M.O.</div>
            <div>(Muhr o'rni / Seal)</div>
        </div>
    </div>
</body>
</html>
