<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Yuzni Tanish Tizimi - Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .camera-section {
            position: relative;
            margin-bottom: 20px;
            background: #f5f5f5;
            border-radius: 10px;
            overflow: hidden;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #video {
            width: 100%;
            height: auto;
            max-height: 500px;
            display: none;
        }

        #canvas {
            position: absolute;
            top: 0;
            left: 0;
            display: none;
        }

        .captured-image {
            width: 100%;
            height: auto;
            border-radius: 10px;
            display: none;
        }

        .placeholder {
            text-align: center;
            color: #999;
            padding: 100px 20px;
        }

        .placeholder svg {
            width: 100px;
            height: 100px;
            fill: #ddd;
            margin-bottom: 20px;
        }

        .controls {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        button {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: #48bb78;
            color: white;
        }

        .btn-success:hover {
            background: #38a169;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(72, 187, 120, 0.4);
        }

        .btn-danger {
            background: #f56565;
            color: white;
        }

        .btn-danger:hover {
            background: #e53e3e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 101, 101, 0.4);
        }

        .btn-warning {
            background: #ed8936;
            color: white;
        }

        .btn-warning:hover {
            background: #dd6b20;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(237, 137, 54, 0.4);
        }

        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .message.success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .message.error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #fc8181;
        }

        .message.info {
            background: #bee3f8;
            color: #2c5282;
            border: 1px solid #90cdf4;
        }

        .message.show {
            display: block;
        }

        .result-section {
            margin-top: 20px;
            padding: 20px;
            background: #f7fafc;
            border-radius: 10px;
            display: none;
        }

        .result-section.show {
            display: block;
        }

        .result-section h3 {
            color: #4a5568;
            margin-bottom: 15px;
        }

        .result-item {
            padding: 10px;
            background: white;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
            display: none;
        }

        .loader.show {
            display: block;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .info-box {
            background: #edf2f7;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-box h4 {
            color: #4a5568;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #718096;
            line-height: 1.6;
        }

        @media (max-width: 600px) {
            .controls {
                flex-direction: column;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎥 Yuzni Tanish Tizimi</h1>

        <div class="message" id="message"></div>

        <div class="info-box">
            <h4>📌 Qo'llanma:</h4>
            <p>1. "Kamerani Yoqish" tugmasini bosing</p>
            <p>2. Yuzingiz kameraga to'g'ri ko'rinishini ta'minlang</p>
            <p>3. "Suratga Olish" tugmasini bosing</p>
            <p>4. Kerakli amalni tanlang (Ro'yxatdan o'tish, Kirish, Chiqish)</p>
        </div>

        @php
            // HikVision kameralar ro'yxati - .env dan o'qiladi
            $hikCameras = [];
            if ($url = config('services.hikvision.snapshot_url')) {
                $hikCameras[] = ['name' => config('services.hikvision.name', 'HikVision (Asosiy)'), 'url' => $url];
            }
            // Qo'shimcha kameralar HIKVISION_CAMERAS env (JSON formatda)
            $extraJson = env('HIKVISION_CAMERAS');
            if ($extraJson) {
                $extra = json_decode($extraJson, true);
                if (is_array($extra)) {
                    foreach ($extra as $cam) {
                        if (!empty($cam['url'])) {
                            $hikCameras[] = ['name' => $cam['name'] ?? 'HikVision', 'url' => $cam['url']];
                        }
                    }
                }
            }
        @endphp

        <div style="margin-bottom: 15px;">
            <label for="cameraSelect" style="display:block; font-weight:600; margin-bottom:6px;">📹 Kamerani tanlang:</label>
            <select id="cameraSelect" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px; font-size:1rem;">
                <option value="">-- Kameralarni yuklash uchun "Kamerani Yoqish" ni bosing --</option>
                @foreach($hikCameras as $i => $cam)
                    <option value="ip:{{ $i }}" data-url="{{ $cam['url'] }}">🎥 {{ $cam['name'] }} (HikVision IP)</option>
                @endforeach
            </select>
            <button type="button" id="refreshCamerasBtn" onclick="loadCameras()" style="margin-top:6px; padding:6px 12px; background:#e5e7eb; border:none; border-radius:6px; cursor:pointer; font-size:0.875rem;">
                🔄 Kameralarni yangilash
            </button>

            <div style="margin-top:10px; padding:8px; background:#f9fafb; border-radius:6px; font-size:0.875rem;">
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                    <input type="checkbox" id="useCustomIpCheckbox" onchange="toggleCustomIpInput()">
                    <span><strong>Boshqa HikVision URL kiritish</strong></span>
                </label>
                <div id="customIpBox" style="display:none; margin-top:8px;">
                    <input type="text" id="customIpUrl" placeholder="http://user:pass@192.168.1.64/ISAPI/Streaming/channels/101/picture"
                           style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px; font-size:0.875rem;">
                    <small style="color:#6b7280;">Snapshot URL yoki MJPEG stream URL</small>
                </div>
            </div>
        </div>

        <div class="camera-section">
            <video id="video" autoplay playsinline></video>
            <img id="ipCameraStream" style="display:none; width:100%; max-height:480px; object-fit:contain; background:#000;" alt="IP Camera Stream">
            <canvas id="canvas"></canvas>
            <img id="capturedImage" class="captured-image" alt="Captured">
            <div class="placeholder" id="placeholder">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <p>Kamera yoqilmagan</p>
                <p>Boshlash uchun "Kamerani Yoqish" tugmasini bosing</p>
            </div>
        </div>

        <div class="controls">
            <button id="startBtn" class="btn-primary" onclick="startCamera()">
                📷 Kamerani Yoqish
            </button>
            <button id="captureBtn" class="btn-success" onclick="capturePhoto()" disabled>
                📸 Suratga Olish
            </button>
            <button id="stopBtn" class="btn-danger" onclick="stopCamera()" disabled>
                ⏹️ To'xtatish
            </button>
        </div>

        <div class="controls" id="actionControls" style="display: none;">
            <button class="btn-primary" onclick="registerFace()">
                👤 Ro'yxatdan O'tish
            </button>
            <button class="btn-success" onclick="checkIn()">
                ✅ Kirish (Check In)
            </button>
            <button class="btn-warning" onclick="checkOut()">
                🚪 Chiqish (Check Out)
            </button>
        </div>

        <div class="loader" id="loader"></div>

        <div class="result-section" id="resultSection">
            <h3>📊 Natija:</h3>
            <div id="resultContent"></div>
        </div>
    </div>

    <script>
        let video = document.getElementById('video');
        let canvas = document.getElementById('canvas');
        let capturedImage = document.getElementById('capturedImage');
        let placeholder = document.getElementById('placeholder');
        let context = canvas.getContext('2d');
        let stream = null;
        let capturedImageData = null;

        // CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function showMessage(text, type) {
            const messageEl = document.getElementById('message');
            messageEl.textContent = text;
            messageEl.className = `message ${type} show`;

            setTimeout(() => {
                messageEl.className = 'message';
            }, 5000);
        }

        function showLoader(show) {
            document.getElementById('loader').className = show ? 'loader show' : 'loader';
        }

        let ipStreamPolling = null;

        function toggleCustomIpInput() {
            const use = document.getElementById('useCustomIpCheckbox').checked;
            document.getElementById('customIpBox').style.display = use ? 'block' : 'none';
            document.getElementById('cameraSelect').disabled = use;
        }

        async function loadCameras() {
            try {
                // Permission talab — quruq enumerateDevices label qaytarmaydi
                const tmp = await navigator.mediaDevices.getUserMedia({ video: true });
                tmp.getTracks().forEach(t => t.stop());

                const devices = await navigator.mediaDevices.enumerateDevices();
                const cams = devices.filter(d => d.kind === 'videoinput');
                const select = document.getElementById('cameraSelect');

                // Mavjud HikVision IP optionlarini saqlab qolish
                const ipOptions = Array.from(select.querySelectorAll('option[value^="ip:"]')).map(o => o.outerHTML).join('');

                let html = '<option value="">-- Kamerani tanlang --</option>';
                cams.forEach((cam, idx) => {
                    let label = cam.label || ('USB Kamera ' + (idx + 1));
                    if (/hik\s*vision|hikvision|ds-/i.test(label)) {
                        label = '🎥 ' + label + ' (HikVision USB)';
                    } else {
                        label = '📷 ' + label;
                    }
                    html += `<option value="${cam.deviceId}">${label}</option>`;
                });
                html += ipOptions;
                select.innerHTML = html;

                showMessage('Kameralar yuklandi: ' + cams.length + ' ta USB' + (ipOptions ? ' + IP kameralar' : ''), 'success');
            } catch (e) {
                console.error('enumerateDevices error:', e);
                showMessage('Kameralarni yuklab bo\'lmadi: ' + e.message, 'error');
            }
        }

        async function startCamera() {
            // Agar custom IP URL kiritilgan bo'lsa
            if (document.getElementById('useCustomIpCheckbox').checked) {
                const url = document.getElementById('customIpUrl').value.trim();
                if (!url) { showMessage('URL ni kiriting!', 'error'); return; }
                return startIpCamera(url);
            }

            const selectedValue = document.getElementById('cameraSelect').value;

            // Agar dropdown'dan IP kamera tanlangan bo'lsa
            if (selectedValue.startsWith('ip:')) {
                const opt = document.querySelector(`#cameraSelect option[value="${selectedValue}"]`);
                const url = opt ? opt.getAttribute('data-url') : '';
                if (!url) { showMessage('URL topilmadi', 'error'); return; }
                return startIpCamera(url);
            }

            try {
                const constraints = {
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                    }
                };
                if (selectedValue) {
                    constraints.video.deviceId = { exact: selectedValue };
                } else {
                    constraints.video.facingMode = 'user';
                }

                stream = await navigator.mediaDevices.getUserMedia(constraints);

                video.srcObject = stream;
                video.style.display = 'block';
                document.getElementById('ipCameraStream').style.display = 'none';
                placeholder.style.display = 'none';

                // Kamera ro'yxatini yangilash (label endi mavjud)
                if (!document.getElementById('cameraSelect').value) {
                    loadCameras();
                }

                // Canvas o'lchamini sozlash
                video.addEventListener('loadedmetadata', () => {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                });

                // Tugmalarni yangilash
                document.getElementById('startBtn').disabled = true;
                document.getElementById('captureBtn').disabled = false;
                document.getElementById('stopBtn').disabled = false;

                showMessage('Kamera muvaffaqiyatli yoqildi!', 'success');
            } catch (error) {
                console.error('Kamera xatosi:', error);
                showMessage('Kamera yoqishda xatolik: ' + error.message, 'error');
            }
        }

        function startIpCamera(url) {
            if (!url) {
                showMessage('HikVision URL topilmadi!', 'error');
                return;
            }
            window._currentIpUrl = url;

            const ipImg = document.getElementById('ipCameraStream');
            // Cache buster qo'shib har 1 sekundda yangilanish
            const refresh = () => { ipImg.src = url + (url.includes('?') ? '&' : '?') + 't=' + Date.now(); };
            refresh();
            ipStreamPolling = setInterval(refresh, 1000);

            ipImg.style.display = 'block';
            video.style.display = 'none';
            placeholder.style.display = 'none';

            // Canvas o'lchamini sozlash
            ipImg.onload = () => {
                canvas.width = ipImg.naturalWidth || 1280;
                canvas.height = ipImg.naturalHeight || 720;
            };
            ipImg.onerror = () => {
                showMessage('HikVision kamerasiga ulana olmadi. URL ni va tarmoqni tekshiring.', 'error');
                stopCamera();
            };

            document.getElementById('startBtn').disabled = true;
            document.getElementById('captureBtn').disabled = false;
            document.getElementById('stopBtn').disabled = false;

            showMessage('HikVision kamerasi ulandi!', 'success');
        }

        function capturePhoto() {
            const ipImg = document.getElementById('ipCameraStream');
            const useIp = ipImg.style.display !== 'none' && ipImg.src;

            if (!useIp && !video.srcObject) {
                showMessage('Avval kamerani yoqing!', 'error');
                return;
            }

            // Canvas ga frame chizish — IP yoki webcam dan
            const source = useIp ? ipImg : video;
            const w = useIp ? (ipImg.naturalWidth || 1280) : video.videoWidth;
            const h = useIp ? (ipImg.naturalHeight || 720) : video.videoHeight;
            canvas.width = w;
            canvas.height = h;
            context.drawImage(source, 0, 0, w, h);

            // Base64 formatga o'tkazish
            capturedImageData = canvas.toDataURL('image/jpeg', 0.8).split(',')[1];

            // Suratni ko'rsatish
            capturedImage.src = canvas.toDataURL('image/jpeg', 0.8);
            capturedImage.style.display = 'block';
            video.style.display = 'none';
            ipImg.style.display = 'none';

            // Action tugmalarini ko'rsatish
            document.getElementById('actionControls').style.display = 'flex';

            showMessage('Surat muvaffaqiyatli olindi! Endi amalni tanlang.', 'success');
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
                stream = null;
            }
            if (ipStreamPolling) {
                clearInterval(ipStreamPolling);
                ipStreamPolling = null;
            }
            const ipImg = document.getElementById('ipCameraStream');
            ipImg.src = '';
            ipImg.style.display = 'none';
            video.style.display = 'none';
            capturedImage.style.display = 'none';
            placeholder.style.display = 'flex';

            // Tugmalarni yangilash
            document.getElementById('startBtn').disabled = false;
            document.getElementById('captureBtn').disabled = true;
            document.getElementById('stopBtn').disabled = true;
            document.getElementById('actionControls').style.display = 'none';

            showMessage('Kamera to\'xtatildi', 'info');
        }

        async function registerFace() {
            if (!capturedImageData) {
                showMessage('Avval suratga oling!', 'error');
                return;
            }

            showLoader(true);

            try {
                const response = await fetch('/api/face/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        image: capturedImageData,
                        user_id: 1 // Test uchun
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showMessage('Yuz muvaffaqiyatli ro\'yxatdan o\'tkazildi!', 'success');
                    showResult(data);
                } else {
                    showMessage('Xatolik: ' + (data.message || 'Noma\'lum xatolik'), 'error');
                }
            } catch (error) {
                console.error('Xatolik:', error);
                showMessage('Server xatosi: ' + error.message, 'error');
            } finally {
                showLoader(false);
            }
        }

        async function checkIn() {
            if (!capturedImageData) {
                showMessage('Avval suratga oling!', 'error');
                return;
            }

            showLoader(true);

            try {
                const response = await fetch('/api/face/check-in', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        image: capturedImageData,
                        location: 'main_entrance'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showMessage('Kirish muvaffaqiyatli qayd qilindi!', 'success');
                    showResult(data.data);
                } else {
                    showMessage('Xatolik: ' + (data.message || 'Yuz aniqlanmadi'), 'error');
                }
            } catch (error) {
                console.error('Xatolik:', error);
                showMessage('Server xatosi: ' + error.message, 'error');
            } finally {
                showLoader(false);
            }
        }

        async function checkOut() {
            if (!capturedImageData) {
                showMessage('Avval suratga oling!', 'error');
                return;
            }

            showLoader(true);

            try {
                const response = await fetch('/api/face/check-out', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        image: capturedImageData,
                        location: 'main_entrance'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showMessage('Chiqish muvaffaqiyatli qayd qilindi!', 'success');
                    showResult(data.data);
                } else {
                    showMessage('Xatolik: ' + (data.message || 'Yuz aniqlanmadi'), 'error');
                }
            } catch (error) {
                console.error('Xatolik:', error);
                showMessage('Server xatosi: ' + error.message, 'error');
            } finally {
                showLoader(false);
            }
        }

        function showResult(data) {
            const resultSection = document.getElementById('resultSection');
            const resultContent = document.getElementById('resultContent');

            let html = '<div class="result-item">';

            if (data.user_name) {
                html += `<p><strong>Foydalanuvchi:</strong> ${data.user_name}</p>`;
            }
            if (data.check_in_time) {
                html += `<p><strong>Kirish vaqti:</strong> ${data.check_in_time}</p>`;
            }
            if (data.check_out_time) {
                html += `<p><strong>Chiqish vaqti:</strong> ${data.check_out_time}</p>`;
            }
            if (data.total_hours) {
                html += `<p><strong>Jami soat:</strong> ${data.total_hours}</p>`;
            }
            if (data.confidence) {
                html += `<p><strong>Aniqlik:</strong> ${data.confidence}%</p>`;
            }
            if (data.message) {
                html += `<p><strong>Xabar:</strong> ${data.message}</p>`;
            }
            if (data.images_saved) {
                html += `<p><strong>Saqlangan rasmlar:</strong> ${data.images_saved}</p>`;
            }

            html += '</div>';

            resultContent.innerHTML = html;
            resultSection.className = 'result-section show';
        }

        // Sahifa yuklanganda kamera permission tekshirish
        window.addEventListener('load', () => {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                showMessage('Sizning brauzeringiz kamerani qo\'llab-quvvatlamaydi!', 'error');
                document.getElementById('startBtn').disabled = true;
            }
        });
    </script>
</body>
</html>