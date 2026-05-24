<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diplom ilovasi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px double #1e3a8a;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 8px;
        }
        .university-name {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            color: #1e3a8a;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .subtitle {
            text-align: center;
            font-size: 9pt;
            color: #666;
            margin-bottom: 20px;
            font-style: italic;
        }
        .section {
            margin: 20px 0;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            background-color: #1e3a8a;
            color: white;
            padding: 8px 12px;
            margin-bottom: 12px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 5px 8px;
            border: 1px solid #ddd;
        }
        .info-label {
            font-weight: bold;
            width: 35%;
            background-color: #f3f4f6;
        }
        .courses-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin: 10px 0;
        }
        .courses-table th {
            background-color: #1e3a8a;
            color: white;
            padding: 8px 5px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .courses-table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
        }
        .courses-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .year-header {
            background-color: #e5e7eb !important;
            font-weight: bold;
            font-size: 10pt;
            padding: 8px 5px !important;
        }
        .summary-box {
            background-color: #eff6ff;
            border: 2px solid #3b82f6;
            padding: 15px;
            margin: 15px 0;
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
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
        }
        .summary-label {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }
        .grading-info {
            font-size: 9pt;
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin: 15px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
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
        .page-number {
            text-align: center;
            font-size: 9pt;
            color: #999;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">HEMIS</div>
        <div class="university-name">{{ $university ?? 'Turizm Akademiyasi' }}</div>
    </div>

    <!-- Document Title -->
    <div class="document-title">
        DIPLOM ILOVASI<br>
        DIPLOMA SUPPLEMENT
    </div>
    <div class="subtitle">
        (Evropa diplomiga ilova namunasi asosida / Based on European Diploma Supplement Model)
    </div>

    <!-- Section 1: Student Information -->
    <div class="section">
        <div class="section-title">1. DIPLOMNI EGASI HAQIDA MA'LUMOT / INFORMATION ABOUT THE HOLDER</div>
        <table class="info-table">
            <tr>
                <td class="info-label">1.1. Familiya, ism, sharif / Full Name:</td>
                <td>{{ strtoupper($student->user->name) }}</td>
            </tr>
            <tr>
                <td class="info-label">1.2. Tug'ilgan sana / Date of Birth:</td>
                <td>{{ \Carbon\Carbon::parse($student->birth_date)->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">1.3. Talaba ID raqami / Student ID:</td>
                <td>{{ $student->student_id }}</td>
            </tr>
        </table>
    </div>

    <!-- Section 2: Diploma Information -->
    <div class="section">
        <div class="section-title">2. DIPLOM HAQIDA MA'LUMOT / INFORMATION ABOUT THE DIPLOMA</div>
        <table class="info-table">
            <tr>
                <td class="info-label">2.1. Diplom raqami / Diploma Number:</td>
                <td>{{ $diplomaNumber }}</td>
            </tr>
            <tr>
                <td class="info-label">2.2. Berilgan sana / Date of Issue:</td>
                <td>{{ $issueDate }}</td>
            </tr>
            <tr>
                <td class="info-label">2.3. Malaka / Qualification:</td>
                <td>{{ $qualification ?? 'Turizm bo\'yicha bakalavr / Bachelor in Tourism' }}</td>
            </tr>
        </table>
    </div>

    <!-- Section 3: Education Information -->
    <div class="section">
        <div class="section-title">3. TA'LIM HAQIDA MA'LUMOT / INFORMATION ABOUT THE EDUCATION</div>
        <table class="info-table">
            <tr>
                <td class="info-label">3.1. Ta'lim darajasi / Level of Education:</td>
                <td>Bakalavr / Bachelor's Degree (1-bosqich / Level 1)</td>
            </tr>
            <tr>
                <td class="info-label">3.2. Ta'lim muddati / Duration of Study:</td>
                <td>{{ $duration ?? '4 yil / 4 years' }} ({{ $totalCredits ?? 240 }} kredit / credits)</td>
            </tr>
            <tr>
                <td class="info-label">3.3. O'qish davri / Period of Study:</td>
                <td>{{ $studyPeriod ?? '2020-2024' }}</td>
            </tr>
            <tr>
                <td class="info-label">3.4. Ta'lim shakli / Form of Study:</td>
                <td>{{ $student->education_form ?? 'Kunduzgi / Full-time' }}</td>
            </tr>
            <tr>
                <td class="info-label">3.5. Ta'lim tili / Language of Instruction:</td>
                <td>O'zbek tili / Uzbek</td>
            </tr>
        </table>
    </div>

    <!-- Section 4: Specialty Information -->
    <div class="section">
        <div class="section-title">4. MUTAXASSISLIK HAQIDA MA'LUMOT / INFORMATION ABOUT THE SPECIALTY</div>
        <table class="info-table">
            <tr>
                <td class="info-label">4.1. Ta'lim yo'nalishi / Field of Study:</td>
                <td>{{ $student->group->specialty->name ?? 'Turizm' }}</td>
            </tr>
            <tr>
                <td class="info-label">4.2. Yo'nalish kodi / Field Code:</td>
                <td>{{ $student->group->specialty->code ?? '5230100' }}</td>
            </tr>
            <tr>
                <td class="info-label">4.3. Fakultet / Faculty:</td>
                <td>{{ $student->group->faculty->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Section 5: Academic Performance Summary -->
    <div class="section">
        <div class="section-title">5. AKADEMIK NATIJALAR / ACADEMIC PERFORMANCE</div>

        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-value">{{ number_format($gpa, 2) }}</div>
                    <div class="summary-label">Umumiy GPA</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">{{ $totalCredits ?? 240 }}</div>
                    <div class="summary-label">Jami kreditlar</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">{{ $totalCourses ?? 60 }}</div>
                    <div class="summary-label">O'zlashtirilgan fanlar</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">{{ number_format($avgScore ?? 85, 0) }}</div>
                    <div class="summary-label">O'rtacha ball</div>
                </div>
            </div>
        </div>

        <div class="grading-info">
            <strong>Baholash tizimi / Grading System:</strong><br>
            A (90-100) - A'lo / Excellent - GPA 4.0 |
            B (80-89) - Yaxshi / Good - GPA 3.0 |
            C (70-79) - Qoniqarli / Satisfactory - GPA 2.0 |
            D (60-69) - Qoniqarsiz / Unsatisfactory - GPA 1.0 |
            F (0-59) - Qoniqarsiz / Fail - GPA 0.0
        </div>
    </div>

    <!-- Section 6: Courses by Year -->
    <div class="section">
        <div class="section-title">6. O'ZLASHTIRILGAN FANLAR RO'YXATI / LIST OF COURSES COMPLETED</div>

        <table class="courses-table">
            <thead>
                <tr>
                    <th style="width: 5%">№</th>
                    <th style="width: 40%">Fan nomi / Course Name</th>
                    <th style="width: 8%">Kredit<br>Credit</th>
                    <th style="width: 8%">Soat<br>Hours</th>
                    <th style="width: 8%">Ball<br>Score</th>
                    <th style="width: 8%">Baho<br>Grade</th>
                    <th style="width: 8%">GPA</th>
                    <th style="width: 15%">Sana / Date</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $counter = 1;
                    $currentYear = null;
                @endphp
                @foreach($courses as $course)
                    @if($currentYear !== $course->year)
                        @php
                            $currentYear = $course->year;
                        @endphp
                        <tr>
                            <td colspan="8" class="year-header">
                                {{ $course->year }}-O'QUV YILI / ACADEMIC YEAR {{ $course->year }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="text-align: center">{{ $counter++ }}</td>
                        <td>{{ $course->name }}</td>
                        <td style="text-align: center">{{ $course->credits ?? 4 }}</td>
                        <td style="text-align: center">{{ ($course->credits ?? 4) * 30 }}</td>
                        <td style="text-align: center">{{ $course->score }}</td>
                        <td style="text-align: center"><strong>{{ $course->grade }}</strong></td>
                        <td style="text-align: center">{{ number_format($course->gpa, 2) }}</td>
                        <td style="text-align: center">{{ $course->date }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Section 7: Additional Information -->
    <div class="section">
        <div class="section-title">7. QO'SHIMCHA MA'LUMOTLAR / ADDITIONAL INFORMATION</div>
        <table class="info-table">
            <tr>
                <td class="info-label">7.1. Ilmiy rahbar / Scientific Advisor:</td>
                <td>{{ $advisor ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">7.2. Bitiruv ishi mavzusi / Thesis Topic:</td>
                <td>{{ $thesisTopic ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">7.3. Bitiruv ishi bahosi / Thesis Grade:</td>
                <td>{{ $thesisGrade ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">7.4. Mukofotlar / Awards:</td>
                <td>{{ $awards ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Section 8: University Information -->
    <div class="section">
        <div class="section-title">8. O'QUV MUASSASASI HAQIDA / INFORMATION ABOUT THE INSTITUTION</div>
        <table class="info-table">
            <tr>
                <td class="info-label">8.1. Nomi / Name:</td>
                <td>{{ $university ?? 'Turizm Akademiyasi' }}</td>
            </tr>
            <tr>
                <td class="info-label">8.2. Turi / Type:</td>
                <td>Oliy ta'lim muassasasi / Higher Education Institution</td>
            </tr>
            <tr>
                <td class="info-label">8.3. Litsenziya / License:</td>
                <td>{{ $licenseNumber ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">8.4. Akkreditatsiya / Accreditation:</td>
                <td>{{ $accreditationNumber ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer with Signatures -->
    <div class="footer">
        <div class="signature-block">
            <div>O'quv ishlari bo'yicha prorektor</div>
            <div><em>Vice-Rector for Academic Affairs</em></div>
            <div class="signature-line"></div>
            <div style="font-weight: bold;">{{ $vicerectorName ?? '_______________' }}</div>
        </div>
        <div class="signature-block" style="text-align: right;">
            <div>M.O.</div>
            <div><em>(Muhr o'rni / Seal)</em></div>
        </div>
    </div>

    <!-- Page Number -->
    <div class="page-number">
        Diplom ilovasi ID: {{ $supplementId }}<br>
        Tasdiqlash / Verification: {{ $verificationUrl ?? 'https://hemis.uz/verify/' . $supplementId }}
    </div>
</body>
</html>
