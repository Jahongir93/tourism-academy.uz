<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma'lumotnoma</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 18pt;
            font-weight: bold;
            color: #1a56db;
            margin-bottom: 5px;
        }
        .university-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .address {
            font-size: 10pt;
            color: #666;
        }
        .doc-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 30px 0;
            text-transform: uppercase;
        }
        .doc-number {
            text-align: right;
            font-size: 10pt;
            margin-bottom: 20px;
        }
        .content {
            text-align: justify;
            margin: 30px 0;
            line-height: 2;
        }
        .student-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #f5f5f5;
            border-left: 4px solid #1a56db;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 180px;
        }
        .info-value {
            flex: 1;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-block {
            width: 45%;
        }
        .signature-label {
            font-size: 10pt;
            margin-bottom: 30px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }
        .stamp {
            text-align: center;
            margin-top: 30px;
            font-size: 10pt;
            color: #666;
        }
        .qr-code {
            position: absolute;
            bottom: 40px;
            right: 40px;
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">HEMIS</div>
        <div class="university-name">{{ $university ?? 'Turizm Akademiyasi' }}</div>
        <div class="address">{{ $address ?? 'Toshkent sh., Amir Temur ko\'chasi 108' }}</div>
    </div>

    <!-- Document Number -->
    <div class="doc-number">
        № {{ $documentNumber }} <br>
        Sana: {{ $date }}
    </div>

    <!-- Title -->
    <div class="doc-title">MA'LUMOTNOMA</div>

    <!-- Content -->
    <div class="content">
        <p>
            Mazkur ma'lumotnoma <strong>{{ $student->user->name }}</strong>
            {{ date('Y') - \Carbon\Carbon::parse($student->birth_date)->year }} yoshda
            {{ $university ?? 'Turizm Akademiyasi' }}ning {{ $student->course ?? '1' }}-kurs talabasi
            ekanligini tasdiqlash uchun berildi.
        </p>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <div class="info-row">
            <div class="info-label">F.I.Sh.:</div>
            <div class="info-value">{{ $student->user->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tug'ilgan sana:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($student->birth_date)->format('d.m.Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Talaba ID raqami:</div>
            <div class="info-value">{{ $student->student_id }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Ta'lim shakli:</div>
            <div class="info-value">{{ $student->education_form ?? 'Kunduzgi' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Mutaxassislik:</div>
            <div class="info-value">{{ $student->group->specialty->name ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kurs:</div>
            <div class="info-value">{{ $student->group->course_year ?? '1' }}-kurs</div>
        </div>
        <div class="info-row">
            <div class="info-label">O'quv yili:</div>
            <div class="info-value">{{ date('Y') }}-{{ date('Y') + 1 }}</div>
        </div>
    </div>

    <div class="content">
        <p>
            Talaba o'z zimmasiga yuklatilgan barcha majburiyatlarni to'liq bajarmoqda va
            hozirgi kunda {{ $university ?? 'Turizm Akademiyasi' }}da o'qishni davom ettirmoqda.
        </p>
        <p style="margin-top: 20px;">
            Ma'lumotnoma talab qilingan joyga taqdim etish uchun berildi.
        </p>
    </div>

    <!-- Footer with Signatures -->
    <div class="footer">
        <div class="signature-block">
            <div class="signature-label">Rektor:</div>
            <div class="signature-line"></div>
            <div>{{ $rectorName ?? '_______________' }}</div>
        </div>
        <div class="signature-block">
            <div class="signature-label">O'quv ishlari bo'yicha prorektor:</div>
            <div class="signature-line"></div>
            <div>{{ $vicerectorName ?? '_______________' }}</div>
        </div>
    </div>

    <!-- Stamp Notice -->
    <div class="stamp">
        M.O. (Muhr o'rni)
    </div>

    <!-- QR Code Placeholder -->
    <div class="qr-code">
        <!-- QR code would be generated here -->
    </div>
</body>
</html>
