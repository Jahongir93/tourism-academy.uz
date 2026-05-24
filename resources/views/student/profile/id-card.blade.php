<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talaba ID Kartasi - {{ $user->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .id-card-container {
            max-width: 450px;
            width: 100%;
        }

        .id-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .card-header-custom {
            text-align: center;
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        .university-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .university-logo i {
            font-size: 40px;
            color: #667eea;
        }

        .university-name {
            color: white;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .card-type {
            color: #ffd700;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .profile-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #667eea;
            margin: 0 auto 15px;
            display: block;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .profile-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            margin: 0 auto 15px;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .student-name {
            font-size: 22px;
            font-weight: 700;
            color: #2d3748;
            text-align: center;
            margin-bottom: 5px;
        }

        .student-id {
            font-size: 14px;
            color: #718096;
            text-align: center;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            position: relative;
            z-index: 1;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.9);
            padding: 12px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .info-label {
            font-size: 11px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .info-value {
            font-size: 13px;
            color: #2d3748;
            font-weight: 600;
        }

        .qr-section {
            background: white;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .qr-code {
            width: 100px;
            height: 100px;
            background: white;
            border: 3px solid #667eea;
            border-radius: 10px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-code i {
            font-size: 60px;
            color: #667eea;
        }

        .qr-text {
            font-size: 10px;
            color: #718096;
            margin-top: 10px;
        }

        .status-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #48bb78;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(72, 187, 120, 0.4);
            z-index: 2;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }

        .btn-custom {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-print {
            background: white;
            color: #667eea;
        }

        .btn-print:hover {
            background: #f7fafc;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-download {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
        }

        .btn-download:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
        }

        @media print {
            body {
                background: white;
            }
            .action-buttons {
                display: none;
            }
            .id-card {
                box-shadow: none;
            }
        }

        @media (max-width: 576px) {
            .id-card {
                padding: 20px;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="id-card-container">
        <div class="id-card">
            <div class="status-badge">
                <i class="fas fa-check-circle me-1"></i>FAOL
            </div>

            <!-- Header -->
            <div class="card-header-custom">
                <div class="university-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="university-name">Tourism Academy Samarkand</div>
                <div class="card-type">Talaba ID Kartasi</div>
            </div>

            <!-- Profile Section -->
            <div class="profile-section">
                @if($user->profile_photo_path)
                <img src="{{ Storage::url($user->profile_photo_path) }}"
                     alt="Profile Photo"
                     class="profile-photo">
                @else
                <div class="profile-placeholder">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                @endif

                <div class="student-name">{{ $user->name }}</div>
                <div class="student-id">ID: {{ $academicInfo['student_id'] }}</div>
            </div>

            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-building me-1"></i>Fakultet
                    </div>
                    <div class="info-value">{{ Str::limit($academicInfo['faculty'], 20) }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-book me-1"></i>Yo'nalish
                    </div>
                    <div class="info-value">{{ Str::limit($academicInfo['specialty'], 20) }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-users me-1"></i>Guruh
                    </div>
                    <div class="info-value">{{ $academicInfo['group'] }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-layer-group me-1"></i>Kurs
                    </div>
                    <div class="info-value">{{ $academicInfo['course'] }}-kurs</div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-calendar me-1"></i>Qabul yili
                    </div>
                    <div class="info-value">
                        {{ $academicInfo['admitted_on'] ? \Carbon\Carbon::parse($academicInfo['admitted_on'])->format('Y') : 'N/A' }}
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-clock me-1"></i>Amal qilish
                    </div>
                    <div class="info-value">{{ date('Y') }}-{{ date('Y') + 1 }}</div>
                </div>
            </div>

            <!-- QR Section -->
            <div class="qr-section mt-3">
                <div class="qr-code">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="qr-text">
                    Scan to verify • {{ $academicInfo['student_id'] }}
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn-custom btn-print" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Chop etish
                </button>
                <a href="{{ route('student.profile.index') }}" class="btn-custom btn-download">
                    <i class="fas fa-arrow-left me-2"></i>Orqaga
                </a>
            </div>
        </div>
    </div>

</body>
</html>
