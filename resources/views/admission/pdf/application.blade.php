<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ariza - {{ $application->application_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #1f2937;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .application-number {
            font-size: 16px;
            font-weight: 700;
            color: #3b82f6;
            margin-top: 15px;
            padding: 8px 15px;
            background: #eff6ff;
            display: inline-block;
            border-radius: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 11px;
            margin-top: 10px;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-reviewing { background: #dbeafe; color: #1e40af; }
        .status-accepted { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-waitlist { background: #e5e7eb; color: #374151; }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
        }

        .section-icon {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            color: #3b82f6;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            padding: 8px 10px;
            font-weight: 600;
            color: #6b7280;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .info-value {
            display: table-cell;
            padding: 8px 10px;
            color: #1f2937;
            border: 1px solid #e5e7eb;
        }

        .modules-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .module-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
        }

        .documents-list {
            margin-top: 10px;
        }

        .document-item {
            padding: 8px 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .document-icon {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            color: #3b82f6;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }

        .timestamp {
            margin-top: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">Онлайн Қабул Тизими</div>
        <div class="subtitle">Ariza Tafsilotlari</div>
        <div class="application-number">№ {{ $application->application_number }}</div>
        <div>
            <span class="status-badge status-{{ $application->status }}">
                @if($application->status == 'pending')
                    Kutilmoqda
                @elseif($application->status == 'reviewing')
                    Ko'rib chiqilmoqda
                @elseif($application->status == 'accepted')
                    Qabul qilindi
                @elseif($application->status == 'rejected')
                    Rad etildi
                @elseif($application->status == 'waitlist')
                    Kutish ro'yxatida
                @endif
            </span>
        </div>
    </div>

    <!-- Personal Information -->
    <div class="section">
        <div class="section-title">
            <span class="section-icon">👤</span>
            Shaxsiy Ma'lumotlar
        </div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Familiya</div>
                <div class="info-value">{{ $application->last_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Ism</div>
                <div class="info-value">{{ $application->first_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Otasining ismi</div>
                <div class="info-value">{{ $application->middle_name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tug'ilgan sana</div>
                <div class="info-value">{{ $application->birth_date ? \Carbon\Carbon::parse($application->birth_date)->format('d.m.Y') : '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jinsi</div>
                <div class="info-value">{{ $application->gender == 'male' ? 'Erkak' : 'Ayol' }}</div>
            </div>
            @if($application->passport_series || $application->passport_number)
            <div class="info-row">
                <div class="info-label">Pasport</div>
                <div class="info-value">{{ $application->passport_series ?? '' }} {{ $application->passport_number ?? '' }}</div>
            </div>
            @endif
            @if($application->jshshir)
            <div class="info-row">
                <div class="info-label">JSHSHIR</div>
                <div class="info-value">{{ $application->jshshir }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Contact Information -->
    <div class="section">
        <div class="section-title">
            <span class="section-icon">📞</span>
            Aloqa Ma'lumotlari
        </div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Telefon</div>
                <div class="info-value">{{ $application->phone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $application->email }}</div>
            </div>
            @if($application->region)
            <div class="info-row">
                <div class="info-label">Viloyat</div>
                <div class="info-value">{{ $application->region }}</div>
            </div>
            @endif
            @if($application->district)
            <div class="info-row">
                <div class="info-label">Tuman</div>
                <div class="info-value">{{ $application->district }}</div>
            </div>
            @endif
            @if($application->address)
            <div class="info-row">
                <div class="info-label">Manzil</div>
                <div class="info-value">{{ $application->address }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Education Information -->
    @if($application->faculty || $application->specialty || $application->education_type)
    <div class="section">
        <div class="section-title">
            <span class="section-icon">🎓</span>
            Ta'lim Ma'lumotlari
        </div>
        <div class="info-grid">
            @if($application->faculty)
            <div class="info-row">
                <div class="info-label">Fakultet</div>
                <div class="info-value">{{ $application->faculty->name_uz ?? '-' }}</div>
            </div>
            @endif
            @if($application->specialty)
            <div class="info-row">
                <div class="info-label">Mutaxassislik</div>
                <div class="info-value">{{ $application->specialty->name_uz ?? '-' }}</div>
            </div>
            @endif
            @if($application->education_type)
            <div class="info-row">
                <div class="info-label">Oldingi ta'lim turi</div>
                <div class="info-value">{{ $application->education_type }}</div>
            </div>
            @endif
            @if($application->education_name)
            <div class="info-row">
                <div class="info-label">O'quv muassasasi</div>
                <div class="info-value">{{ $application->education_name }}</div>
            </div>
            @endif
            @if($application->graduation_year)
            <div class="info-row">
                <div class="info-label">Bitirgan yili</div>
                <div class="info-value">{{ $application->graduation_year }}</div>
            </div>
            @endif
            @if($application->education_form)
            <div class="info-row">
                <div class="info-label">Ta'lim shakli</div>
                <div class="info-value">{{ ucfirst($application->education_form) }}</div>
            </div>
            @endif
            @if($application->education_language)
            <div class="info-row">
                <div class="info-label">Ta'lim tili</div>
                <div class="info-value">{{ strtoupper($application->education_language) }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Selected Modules (for version 2 forms) -->
    @if($application->form_version == 2 && !empty($application->form_data) && isset($application->form_data['selectedModules']))
    <div class="section">
        <div class="section-title">
            <span class="section-icon">📚</span>
            Tanlangan Modullar
        </div>
        <div class="modules-list">
            @php
                $modules = $application->form_data['selectedModules'] ?? [];
                if (is_string($modules)) {
                    $modules = json_decode($modules, true) ?? [];
                }
            @endphp
            @if(is_array($modules))
                @foreach($modules as $module)
                <span class="module-badge">{{ $module }}</span>
                @endforeach
            @endif
        </div>
    </div>
    @endif

    <!-- Additional Form Data (for version 2 forms) -->
    @if($application->form_version == 2 && !empty($application->form_data))
    <div class="section">
        <div class="section-title">
            <span class="section-icon">📋</span>
            Qo'shimcha Ma'lumotlar
        </div>
        <div class="info-grid">
            @if(isset($application->form_data['degree']))
            <div class="info-row">
                <div class="info-label">Daraja</div>
                <div class="info-value">{{ $application->form_data['degree'] }}</div>
            </div>
            @endif
            @if(isset($application->form_data['lastDegreeInstitution']))
            <div class="info-row">
                <div class="info-label">Oxirgi muassasa</div>
                <div class="info-value">{{ $application->form_data['lastDegreeInstitution'] }}</div>
            </div>
            @endif
            @if(isset($application->form_data['graduationYear']))
            <div class="info-row">
                <div class="info-label">Bitirgan yil</div>
                <div class="info-value">{{ $application->form_data['graduationYear'] }}</div>
            </div>
            @endif
            @if(isset($application->form_data['workExperience']))
            <div class="info-row">
                <div class="info-label">Ish tajribasi</div>
                <div class="info-value">{{ $application->form_data['workExperience'] }}</div>
            </div>
            @endif
            @if(isset($application->form_data['currentPosition']))
            <div class="info-row">
                <div class="info-label">Hozirgi lavozim</div>
                <div class="info-value">{{ $application->form_data['currentPosition'] }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Uploaded Documents -->
    @php
        $documents = [];
        if ($application->passport_copy_path) {
            $documents[] = ['label' => 'Pasport nusxasi', 'file' => basename($application->passport_copy_path)];
        }
        if ($application->diploma_copy_path) {
            $label = $application->form_version == 2 ? 'CV (Resume)' : 'Diplom nusxasi';
            $documents[] = ['label' => $label, 'file' => basename($application->diploma_copy_path)];
        }
        if ($application->certificate_copy_path) {
            $documents[] = ['label' => 'Sertifikat nusxasi', 'file' => basename($application->certificate_copy_path)];
        }
        if ($application->photo_path) {
            $documents[] = ['label' => 'Foto', 'file' => basename($application->photo_path)];
        }
    @endphp

    @if(count($documents) > 0)
    <div class="section">
        <div class="section-title">
            <span class="section-icon">📎</span>
            Yuklangan Hujjatlar
        </div>
        <div class="documents-list">
            @foreach($documents as $doc)
            <div class="document-item">
                <span class="document-icon">📄</span>
                <strong>{{ $doc['label'] }}:</strong>&nbsp;{{ $doc['file'] }}
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Timeline -->
    <div class="section">
        <div class="section-title">
            <span class="section-icon">🕐</span>
            Vaqt Chizig'i
        </div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Ariza topshirildi</div>
                <div class="info-value">{{ $application->created_at->format('d.m.Y H:i') }}</div>
            </div>
            @if($application->updated_at != $application->created_at)
            <div class="info-row">
                <div class="info-label">Oxirgi yangilanish</div>
                <div class="info-value">{{ $application->updated_at->format('d.m.Y H:i') }}</div>
            </div>
            @endif
            @if($application->reviewed_at)
            <div class="info-row">
                <div class="info-label">Ko'rib chiqildi</div>
                <div class="info-value">{{ $application->reviewed_at->format('d.m.Y H:i') }}</div>
            </div>
            @endif
        </div>
    </div>

    @if($application->notes)
    <div class="section">
        <div class="section-title">
            <span class="section-icon">📝</span>
            Izohlar
        </div>
        <div style="padding: 10px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 5px;">
            {{ $application->notes }}
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>Ushbu hujjat avtomatik ravishda yaratilgan</div>
        <div class="timestamp">Yaratilgan sana: {{ \Carbon\Carbon::now()->format('d.m.Y H:i') }}</div>
    </div>
</body>
</html>
