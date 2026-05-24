<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vedomost - {{ $subject->name ?? 'N/A' }}</title>
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h3 {
            margin: 5px 0;
        }

        .info {
            margin-bottom: 20px;
        }

        .info-item {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        td.left {
            text-align: left;
        }

        .signatures {
            margin-top: 40px;
        }

        .signature-line {
            display: inline-block;
            width: 45%;
            margin-right: 5%;
            margin-top: 20px;
        }

        .signature-line:last-child {
            margin-right: 0;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">
        Chop etish
    </button>

    <div class="header">
        <h3>O'ZBEKISTON RESPUBLIKASI</h3>
        <h3>TURIZM VA SPORT AKADEMIYASI</h3>
        <h2 style="margin-top: 20px;">VEDOMOST</h2>
    </div>

    <div class="info">
        <div class="info-item"><strong>Fan:</strong> {{ $subject->name ?? 'N/A' }}</div>
        <div class="info-item"><strong>Guruh:</strong> {{ $group->name ?? 'N/A' }}</div>
        <div class="info-item"><strong>O'quv yili:</strong> {{ $academicYear->name ?? 'N/A' }}</div>
        <div class="info-item"><strong>Semestr:</strong> {{ $semester ?? 'N/A' }}</div>
        <div class="info-item"><strong>Nazorat turi:</strong> {{ $assessmentType ?? 'N/A' }}</div>
        <div class="info-item"><strong>Sana:</strong> {{ $date ?? date('d.m.Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">№</th>
                <th style="width: 120px;">Talaba ID</th>
                <th>F.I.O</th>
                <th style="width: 80px;">Oraliq 1</th>
                <th style="width: 80px;">Oraliq 2</th>
                <th style="width: 80px;">Yakuniy</th>
                <th style="width: 80px;">Jami</th>
                <th style="width: 60px;">Baho</th>
                <th style="width: 100px;">Imzo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students ?? [] as $student)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $student->student_id }}</td>
                <td class="left">{{ $student->full_name }}</td>
                <td>{{ $student->midterm1_score ?? '-' }}</td>
                <td>{{ $student->midterm2_score ?? '-' }}</td>
                <td>{{ $student->final_score ?? '-' }}</td>
                <td><strong>{{ $student->total_score ?? 0 }}</strong></td>
                <td><strong>{{ $student->letter_grade ?? '-' }}</strong></td>
                <td></td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="left">Ma'lumot topilmadi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(isset($statistics))
    <div class="info">
        <div class="info-item"><strong>Jami talabalar:</strong> {{ $statistics['total'] ?? 0 }}</div>
        <div class="info-item"><strong>O'tganlar:</strong> {{ $statistics['passed'] ?? 0 }}</div>
        <div class="info-item"><strong>O'tmaganlar:</strong> {{ $statistics['failed'] ?? 0 }}</div>
        <div class="info-item"><strong>O'rtacha ball:</strong> {{ number_format($statistics['average'] ?? 0, 1) }}</div>
    </div>
    @endif

    <div class="signatures">
        <div class="signature-line">
            <p><strong>O'qituvchi:</strong> {{ $teacher->name ?? '______________________' }}</p>
            <p style="margin-top: 20px;">Imzo: _______________</p>
        </div>

        <div class="signature-line">
            <p><strong>Kafedra mudiri:</strong> ______________________</p>
            <p style="margin-top: 20px;">Imzo: _______________</p>
        </div>
    </div>

    <script>
        // Auto print on page load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
