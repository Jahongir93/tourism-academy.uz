<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat</title>
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
        .certificate-container {
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            padding: 40px;
        }
        .certificate-border {
            border: 15px solid gold;
            border-radius: 10px;
            background: white;
            padding: 40px;
            height: 100%;
            position: relative;
        }
        .inner-border {
            border: 2px solid #ddd;
            padding: 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .university-name {
            font-size: 16pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .certificate-title {
            text-align: center;
            margin: 30px 0;
        }
        .title-main {
            font-size: 36pt;
            font-weight: bold;
            color: #764ba2;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }
        .title-sub {
            font-size: 14pt;
            color: #666;
            font-style: italic;
        }
        .content {
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .awarded-to {
            font-size: 12pt;
            color: #666;
            margin-bottom: 15px;
        }
        .student-name {
            font-size: 28pt;
            font-weight: bold;
            color: #000;
            margin: 20px 0;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
            display: inline-block;
        }
        .completion-text {
            font-size: 12pt;
            line-height: 1.8;
            color: #333;
            margin: 20px 0;
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        .course-name {
            font-size: 16pt;
            font-weight: bold;
            color: #667eea;
            margin: 15px 0;
        }
        .details {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 20px 0;
            font-size: 10pt;
            color: #666;
        }
        .detail-item {
            text-align: center;
        }
        .detail-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .footer {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .signature-block {
            text-align: center;
            width: 30%;
        }
        .signature-line {
            border-bottom: 2px solid #000;
            margin: 30px auto 10px auto;
            width: 150px;
        }
        .signature-title {
            font-size: 9pt;
            color: #666;
            margin-bottom: 5px;
        }
        .signature-name {
            font-size: 10pt;
            font-weight: bold;
        }
        .certificate-id {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 8pt;
            color: #999;
        }
        .seal {
            position: absolute;
            bottom: 80px;
            left: 80px;
            width: 100px;
            height: 100px;
            border: 3px solid #764ba2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            text-align: center;
            color: #764ba2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="inner-border">
                <!-- Header -->
                <div class="header">
                    <div class="logo">HEMIS</div>
                    <div class="university-name">{{ $university ?? 'Turizm Akademiyasi' }}</div>
                </div>

                <!-- Certificate Title -->
                <div class="certificate-title">
                    <div class="title-main">SERTIFIKAT</div>
                    <div class="title-sub">Certificate of Completion</div>
                </div>

                <!-- Content -->
                <div class="content">
                    <div class="awarded-to">
                        Ushbu sertifikat quyidagi shaxsga berildi:<br>
                        <em>This certificate is awarded to:</em>
                    </div>

                    <div class="student-name">
                        {{ $student->user->name }}
                    </div>

                    <div class="completion-text">
                        Quyidagi o'quv kursini muvaffaqiyatli yakunlagani uchun<br>
                        <em>For successfully completing the course:</em>
                    </div>

                    <div class="course-name">
                        "{{ $courseName }}"
                    </div>

                    <div class="details">
                        <div class="detail-item">
                            <div class="detail-label">Davomiyligi / Duration:</div>
                            <div>{{ $courseDuration }} soat</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Natija / Score:</div>
                            <div>{{ $finalScore }}/100</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Sana / Date:</div>
                            <div>{{ $completionDate }}</div>
                        </div>
                    </div>
                </div>

                <!-- Footer with Signatures -->
                <div class="footer">
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-title">Rektor / Rector</div>
                        <div class="signature-name">{{ $rectorName ?? '_____________' }}</div>
                    </div>

                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-title">O'quv ishlari bo'yicha prorektor</div>
                        <div class="signature-name">{{ $vicerectorName ?? '_____________' }}</div>
                    </div>

                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-title">Kurs rahbari / Course Director</div>
                        <div class="signature-name">{{ $instructorName ?? '_____________' }}</div>
                    </div>
                </div>

                <!-- Seal -->
                <div class="seal">
                    M.O.<br>
                    (Muhr)
                </div>

                <!-- Certificate ID -->
                <div class="certificate-id">
                    Sertifikat ID: {{ $certificateId }}<br>
                    Tasdiqlash: {{ $verificationUrl }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
