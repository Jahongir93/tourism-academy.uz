<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diplom</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            padding: 0;
            margin: 0;
        }
        .diploma-container {
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 30px;
            position: relative;
        }
        .diploma-border {
            border: 20px solid #fbbf24;
            background: white;
            padding: 40px;
            position: relative;
        }
        .ornament-border {
            border: 3px double #1e3a8a;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #1e3a8a;
            padding-bottom: 20px;
        }
        .coat-of-arms {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px auto;
            border: 2px solid #1e3a8a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            font-size: 24pt;
            font-weight: bold;
        }
        .republic-name {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .ministry-name {
            font-size: 10pt;
            margin-bottom: 10px;
        }
        .university-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a8a;
        }
        .diploma-title {
            text-align: center;
            margin: 30px 0;
        }
        .title-main {
            font-size: 42pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .title-degree {
            font-size: 16pt;
            color: #dc2626;
            font-weight: bold;
            margin-top: 10px;
        }
        .content {
            text-align: center;
            margin: 30px 0;
            line-height: 2;
        }
        .intro-text {
            font-size: 11pt;
            margin-bottom: 20px;
        }
        .student-name {
            font-size: 26pt;
            font-weight: bold;
            color: #000;
            margin: 25px 0;
            border-bottom: 3px double #1e3a8a;
            padding-bottom: 10px;
            display: inline-block;
            min-width: 60%;
        }
        .graduation-text {
            font-size: 11pt;
            line-height: 1.8;
            margin: 20px auto;
            max-width: 80%;
        }
        .specialty-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #3b82f6;
            padding: 20px;
            margin: 25px auto;
            max-width: 70%;
            border-radius: 5px;
        }
        .specialty-label {
            font-size: 10pt;
            color: #666;
            margin-bottom: 8px;
        }
        .specialty-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a8a;
        }
        .qualification {
            font-size: 12pt;
            color: #dc2626;
            font-weight: bold;
            margin-top: 8px;
        }
        .details-grid {
            display: flex;
            justify-content: space-around;
            margin: 25px 0;
            padding: 15px;
            background: #f9fafb;
            border-top: 2px solid #e5e7eb;
            border-bottom: 2px solid #e5e7eb;
        }
        .detail-item {
            text-align: center;
        }
        .detail-label {
            font-size: 9pt;
            color: #666;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 11pt;
            font-weight: bold;
        }
        .diploma-number {
            text-align: center;
            margin: 25px 0;
            font-size: 11pt;
        }
        .number-label {
            color: #666;
        }
        .number-value {
            font-weight: bold;
            color: #1e3a8a;
            font-size: 12pt;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 3px double #1e3a8a;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .signature-block {
            width: 45%;
        }
        .signature-title {
            font-size: 9pt;
            color: #666;
            margin-bottom: 25px;
        }
        .signature-line {
            border-bottom: 2px solid #000;
            margin-bottom: 8px;
        }
        .signature-name {
            font-size: 10pt;
            font-weight: bold;
        }
        .issue-date {
            text-align: center;
            margin-top: 25px;
            font-size: 10pt;
        }
        .seal-position {
            position: absolute;
            bottom: 100px;
            left: 80px;
            width: 120px;
            height: 120px;
            border: 4px solid #1e3a8a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10pt;
            text-align: center;
            color: #1e3a8a;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.9);
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72pt;
            color: rgba(30, 58, 138, 0.05);
            font-weight: bold;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="diploma-container">
        <div class="diploma-border">
            <div class="ornament-border">
                <!-- Watermark -->
                <div class="watermark">DIPLOM</div>

                <!-- Header -->
                <div class="header">
                    <div class="coat-of-arms">UZ</div>
                    <div class="republic-name">O'ZBEKISTON RESPUBLIKASI</div>
                    <div class="ministry-name">Oliy va o'rta maxsus ta'lim vazirligi</div>
                    <div class="university-name">{{ $university ?? 'TURIZM AKADEMIYASI' }}</div>
                </div>

                <!-- Diploma Title -->
                <div class="diploma-title">
                    <div class="title-main">DIPLOM</div>
                    <div class="title-degree">BAKALAVR DARAJASI / BACHELOR'S DEGREE</div>
                </div>

                <!-- Content -->
                <div class="content">
                    <div class="intro-text">
                        Ushbu diplom quyidagilar tomonidan beriladi:<br>
                        <em>This diploma is awarded to:</em>
                    </div>

                    <div class="student-name">
                        {{ strtoupper($student->user->name) }}
                    </div>

                    <div class="graduation-text">
                        {{ $university ?? 'Turizm Akademiyasi' }}da to'liq ta'lim dasturini<br>
                        muvaffaqiyatli yakunlab, barcha talablarni bajarganligini tasdiqlaydi.<br>
                        <em>Successfully completed the full educational program and met all requirements.</em>
                    </div>

                    <!-- Specialty Box -->
                    <div class="specialty-box">
                        <div class="specialty-label">Mutaxassislik / Specialty:</div>
                        <div class="specialty-name">{{ strtoupper($student->group->specialty->name ?? 'TURIZM') }}</div>
                        <div class="specialty-label" style="margin-top: 15px;">Ta'lim yo'nalishi / Field of Study:</div>
                        <div class="specialty-name">{{ $student->group->specialty->code ?? '5230100' }}</div>
                        <div class="qualification">
                            Malaka / Qualification: Turizm bo'yicha bakalavr
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">O'qish davri / Period of Study</div>
                            <div class="detail-value">{{ $studyPeriod ?? '2020-2024' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Ta'lim shakli / Form of Education</div>
                            <div class="detail-value">{{ $student->education_form ?? 'Kunduzgi / Full-time' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">GPA</div>
                            <div class="detail-value">{{ number_format($gpa, 2) }} / 4.0</div>
                        </div>
                    </div>

                    <!-- Diploma Number -->
                    <div class="diploma-number">
                        <span class="number-label">Diplom raqami / Diploma Number:</span><br>
                        <span class="number-value">{{ $diplomaNumber }}</span>
                    </div>
                </div>

                <!-- Footer with Signatures -->
                <div class="footer">
                    <div class="signatures">
                        <div class="signature-block">
                            <div class="signature-title">Rektor / Rector</div>
                            <div class="signature-line"></div>
                            <div class="signature-name">{{ $rectorName ?? '_______________' }}</div>
                        </div>
                        <div class="signature-block" style="text-align: right;">
                            <div class="signature-title">Kengash raisi / Council Chairman</div>
                            <div class="signature-line"></div>
                            <div class="signature-name">{{ $chairmanName ?? '_______________' }}</div>
                        </div>
                    </div>

                    <div class="issue-date">
                        Berilgan sana / Issue Date: <strong>{{ $issueDate }}</strong>
                    </div>
                </div>

                <!-- Seal Position -->
                <div class="seal-position">
                    M.O.<br>
                    (Muhr o'rni)<br>
                    SEAL
                </div>
            </div>
        </div>
    </div>
</body>
</html>
