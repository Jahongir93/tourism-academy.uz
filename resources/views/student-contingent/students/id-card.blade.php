<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talaba ID Card - {{ $student->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            display: flex;
            flex-direction: column;
            gap: 40px;
            align-items: center;
        }

        .id-card {
            width: 420px;
            height: 260px;
            background: linear-gradient(135deg, #16a085 0%, #0d7560 100%);
            border-radius: 20px;
            padding: 25px;
            color: white;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            position: relative;
            overflow: hidden;
        }

        .id-card::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .id-card::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .id-card-back {
            background: linear-gradient(135deg, #0d7560 0%, #16a085 100%);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .university-info {
            flex: 1;
        }

        .university-name {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.3;
            margin-bottom: 3px;
        }

        .university-subtitle {
            font-size: 10px;
            opacity: 0.9;
            font-weight: 400;
        }

        .logo {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900;
            font-size: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
        }
        
        .main-content {
            display: flex;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .photo-section {
            flex-shrink: 0;
        }

        .photo {
            width: 95px;
            height: 120px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid rgba(255,255,255,0.5);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .student-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
            line-height: 1.2;
        }

        .info-item {
            font-size: 12px;
            margin-bottom: 6px;
            display: flex;
            gap: 8px;
            align-items: baseline;
        }

        .info-label {
            font-weight: 600;
            opacity: 0.85;
            min-width: 65px;
        }

        .info-value {
            font-weight: 500;
            flex: 1;
        }

        .id-number {
            position: absolute;
            bottom: 20px;
            right: 25px;
            font-size: 14px;
            font-weight: 700;
            background: rgba(255,255,255,0.25);
            padding: 8px 15px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            letter-spacing: 0.5px;
        }
        
        .back-content {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .qr-section {
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1;
        }

        .qr-code {
            width: 130px;
            height: 130px;
            background: white;
            border-radius: 15px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            border: 3px solid rgba(255,255,255,0.5);
        }

        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .contact-info {
            padding: 18px;
            background: rgba(255,255,255,0.2);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .contact-item {
            font-size: 12px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .contact-item:last-child {
            margin-bottom: 0;
        }

        .validity {
            text-align: center;
            font-size: 12px;
            margin-top: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .container {
                gap: 0;
            }
            
            .actions {
                display: none;
            }
            
            .id-card, .id-card-back {
                page-break-inside: avoid;
                margin: 10mm;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Front Side -->
        <div class="id-card">
            <div class="header">
                <div class="university-info">
                    <div class="university-name">Туризм Академияси</div>
                    <div class="university-subtitle">Tourism Academy of Uzbekistan</div>
                </div>
                <div class="logo">TA</div>
            </div>

            <div class="main-content">
                <div class="photo-section">
                    <div class="photo">
                        @if($student->photo_url)
                            <img src="{{ asset('storage/' . $student->photo_url) }}" alt="Student Photo">
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #e0e0e0 0%, #f5f5f5 100%); display: flex; align-items: center; justify-content: center; color: #999;">
                                <svg width="50" height="50" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="info-section">
                    <div class="student-name">{{ $student->full_name ?? 'N/A' }}</div>

                    <div class="info-item">
                        <span class="info-label">Fakultet:</span>
                        <span class="info-value">{{ optional($student->faculty)->name_uz ?? '-' }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Yo'nalish:</span>
                        <span class="info-value">{{ optional($student->specialty)->name_uz ?? '-' }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Guruh:</span>
                        <span class="info-value">{{ optional($student->group)->name ?? '-' }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Kurs:</span>
                        <span class="info-value">{{ $student->course ?? '1' }}-kurs</span>
                    </div>
                </div>
            </div>

            <div class="id-number">{{ $student->student_id ?? 'N/A' }}</div>
        </div>
        
        <!-- Back Side -->
        <div class="id-card id-card-back">
            <div class="back-content">
                <div class="qr-section">
                    <div class="qr-code">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($student->student_id ?? 'N/A') }}" alt="QR Code">
                    </div>
                </div>

                <div class="contact-info">
                    @if($student->phone)
                    <div class="contact-item">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                        </svg>
                        {{ $student->phone }}
                    </div>
                    @endif

                    @if($student->email)
                    <div class="contact-item">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                        </svg>
                        {{ $student->email }}
                    </div>
                    @endif

                    <div class="contact-item">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg>
                        Toshkent, O'zbekiston
                    </div>
                </div>

                <div class="validity">
                    Amal qilish muddati: {{ now()->year }} - {{ now()->addYears(4)->year }}
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="actions">
            <button class="btn btn-primary" onclick="window.print()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                </svg>
                Chop etish
            </button>
            
            <a href="{{ route('students.id-card', ['student' => $student->id, 'download' => 1]) }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg>
                PDF yuklash
            </a>
            
            <a href="{{ route('students.show', $student) }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Orqaga
            </a>
        </div>
    </div>
</body>
</html>