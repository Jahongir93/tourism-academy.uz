<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Talaba ID Card - {{ $student->full_name_latin }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #ffffff;
        }
        
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .id-card {
            width: 350px;
            height: 220px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 20px;
            color: white;
            position: relative;
            margin: 0 auto 30px;
            page-break-inside: avoid;
        }
        
        .id-card-back {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .university-name {
            display: table-cell;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            vertical-align: middle;
        }
        
        .logo {
            display: table-cell;
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            text-align: center;
            vertical-align: middle;
            color: #667eea;
            font-weight: bold;
            font-size: 18px;
            line-height: 40px;
        }
        
        .main-content {
            display: table;
            width: 100%;
        }
        
        .photo-section {
            display: table-cell;
            width: 80px;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .photo {
            width: 80px;
            height: 100px;
            background: white;
            border-radius: 8px;
            border: 2px solid rgba(255,255,255,0.5);
            text-align: center;
            padding: 10px;
            color: #999;
            font-size: 10px;
        }
        
        .photo-placeholder {
            width: 60px;
            height: 60px;
            margin: 10px auto;
            background: #f0f0f0;
            border-radius: 50%;
        }
        
        .info-section {
            display: table-cell;
            vertical-align: top;
        }
        
        .student-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .info-item {
            font-size: 11px;
            margin-bottom: 4px;
        }
        
        .info-label {
            font-weight: 600;
            display: inline-block;
            width: 70px;
        }
        
        .info-value {
            font-weight: 400;
        }
        
        .id-number {
            position: absolute;
            bottom: 15px;
            right: 20px;
            font-size: 12px;
            font-weight: bold;
            background: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        .back-content {
            height: 180px;
            position: relative;
        }
        
        .qr-section {
            text-align: center;
            padding: 20px 0;
        }
        
        .qr-code {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 10px;
            padding: 5px;
            margin: 0 auto;
        }
        
        .qr-code img {
            width: 90px;
            height: 90px;
        }
        
        .contact-info {
            padding: 15px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            margin: 10px 0;
        }
        
        .contact-item {
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .validity {
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
        }
        
        .card-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Front Side -->
        <div class="card-title">OLD TOMONI</div>
        <div class="id-card">
            <div class="header">
                <div class="university-name">Tashkent University</div>
                <div class="logo">TU</div>
            </div>
            
            <div class="main-content">
                <div class="photo-section">
                    <div class="photo">
                        @if($student->photo_url)
                            <img src="{{ public_path('storage/' . $student->photo_url) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px;">
                        @else
                            <div class="photo-placeholder"></div>
                            <div>NO PHOTO</div>
                        @endif
                    </div>
                </div>
                
                <div class="info-section">
                    <div class="student-name">{{ $student->full_name_latin }}</div>
                    
                    <div class="info-item">
                        <span class="info-label">Fakultet:</span>
                        <span class="info-value">{{ optional(optional(optional($student->group)->specialty)->faculty)->name_uz ?? 'Axborot texnologiyalari' }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Yo'nalish:</span>
                        <span class="info-value">{{ optional(optional($student->group)->specialty)->name_uz ?? 'Dasturiy injiniring' }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Guruh:</span>
                        <span class="info-value">{{ optional($student->group)->name ?? 'DI-201' }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Kurs:</span>
                        <span class="info-value">{{ optional($student->group)->course_year ?? '2' }}-kurs</span>
                    </div>
                </div>
            </div>
            
            <div class="id-number">ID: {{ $student->student_id }}</div>
        </div>
        
        <!-- Back Side -->
        <div class="card-title">ORQA TOMONI</div>
        <div class="id-card id-card-back">
            <div class="back-content">
                <div class="qr-section">
                    <div class="qr-code">
                        @if(isset($qrCodeDataUri))
                            <img src="{{ $qrCodeDataUri }}" alt="QR Code">
                        @else
                            <div style="padding: 30px; color: #999;">QR CODE</div>
                        @endif
                    </div>
                </div>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <strong>Tel:</strong> {{ $student->phone_primary }}
                    </div>
                    
                    @if($student->email)
                    <div class="contact-item">
                        <strong>Email:</strong> {{ $student->email }}
                    </div>
                    @endif
                    
                    <div class="contact-item">
                        <strong>Manzil:</strong> Toshkent, O'zbekiston
                    </div>
                </div>
                
                <div class="validity">
                    Amal qilish muddati: {{ now()->year }} - {{ now()->addYears(4)->year }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>