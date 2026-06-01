@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Yuzni Tanish - Real-time Monitoring</h1>
        </div>
    </div>

    {{-- Camera Controls --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Kamera Ko'rinishi</h5>
                </div>
                <div class="card-body">
                    <div class="video-container position-relative">
                        <video id="cameraFeed" class="w-100" style="max-height: 500px;" autoplay></video>
                        <canvas id="detectionCanvas" class="position-absolute top-0 left-0 w-100 h-100" style="pointer-events: none;"></canvas>
                    </div>
                    <div class="mt-3">
                        <button id="startBtn" class="btn btn-success">
                            <i class="fas fa-play"></i> Boshlash
                        </button>
                        <button id="stopBtn" class="btn btn-danger" disabled>
                            <i class="fas fa-stop"></i> To'xtatish
                        </button>
                        <select id="cameraSelect" class="form-select d-inline-block w-auto ms-3">
                            <option value="entrance_main">Asosiy kirish</option>
                            <option value="entrance_secondary">Ikkinchi kirish</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Statistika</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h3 id="totalPresent" class="text-primary">0</h3>
                            <small class="text-muted">Jami hozir</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 id="currentlyIn" class="text-success">0</h3>
                            <small class="text-muted">Ichkarida</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 id="checkedOut" class="text-warning">0</h3>
                            <small class="text-muted">Chiqib ketgan</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 id="avgConfidence" class="text-info">0%</h3>
                            <small class="text-muted">O'rtacha aniqlik</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Camera Status --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Kamera Holati</h5>
                </div>
                <div class="card-body">
                    <div id="cameraStatusList">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Asosiy kirish</span>
                            <span class="badge bg-success">Faol</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Ikkinchi kirish</span>
                            <span class="badge bg-success">Faol</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">So'nggi Faoliyat</h5>
                </div>
                <div class="card-body">
                    <div id="activityList" class="activity-list" style="max-height: 400px; overflow-y: auto;">
                        {{-- Activity items will be added here dynamically --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Soatlik Taqsimot</h5>
                </div>
                <div class="card-body">
                    <canvas id="hourlyChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Notifications --}}
    <div id="notifications" class="position-fixed top-0 end-0 p-3" style="z-index: 11"></div>
</div>

@push('styles')
<style>
    .video-container {
        background: #000;
        border-radius: 8px;
        overflow: hidden;
    }

    .activity-item {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.3s;
    }

    .activity-item.new {
        animation: highlightNew 3s ease-out;
    }

    .activity-item.check_in {
        border-left: 4px solid #28a745;
    }

    .activity-item.check_out {
        border-left: 4px solid #ffc107;
    }

    .activity-item:hover {
        background-color: #f8f9fa;
    }

    .activity-user {
        font-weight: 600;
        color: #495057;
        margin-bottom: 4px;
    }

    .activity-time {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .activity-action {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 8px;
    }

    .notification {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 16px;
        margin-bottom: 12px;
        min-width: 300px;
        animation: slideIn 0.5s ease-out;
    }

    .notification.fade-out {
        animation: fadeOut 0.5s ease-out;
        opacity: 0;
    }

    @keyframes highlightNew {
        0% {
            background-color: #d1f2eb;
        }
        100% {
            background-color: transparent;
        }
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    .status {
        color: #28a745;
        font-weight: 600;
    }

    .check-in, .check-out {
        display: block;
        margin: 2px 0;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script src="{{ asset('js/faceStreamProcessor.js') }}"></script>
<script>
    let streamProcessor = null;
    let monitoringDashboard = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize components
        initializeStreamProcessor();
        initializeMonitoringDashboard();
        initializeHourlyChart();

        // Setup event listeners
        document.getElementById('startBtn').addEventListener('click', startMonitoring);
        document.getElementById('stopBtn').addEventListener('click', stopMonitoring);
        document.getElementById('cameraSelect').addEventListener('change', changeCameraSource);
    });

    function initializeStreamProcessor() {
        streamProcessor = new FaceStreamProcessor({
            apiUrl: '/api/face/stream',
            cameraId: document.getElementById('cameraSelect').value,
            frameInterval: 500, // 2 FPS
            batchSize: 3
        });
    }

    function initializeMonitoringDashboard() {
        monitoringDashboard = new MonitoringDashboard({
            updateInterval: 5000,
            apiUrl: '/api/face/stream/monitoring'
        });

        monitoringDashboard.initialize({
            stats: {
                totalPresent: document.getElementById('totalPresent'),
                currentlyIn: document.getElementById('currentlyIn'),
                checkedOut: document.getElementById('checkedOut'),
                avgConfidence: document.getElementById('avgConfidence')
            },
            activityList: document.getElementById('activityList'),
            notifications: document.getElementById('notifications')
        });
    }

    function initializeHourlyChart() {
        const ctx = document.getElementById('hourlyChart').getContext('2d');

        monitoringDashboard.charts.hourly = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Kirishlar',
                    data: [],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    async function startMonitoring() {
        try {
            const video = document.getElementById('cameraFeed');
            const canvas = document.getElementById('detectionCanvas');

            // Initialize camera stream
            await streamProcessor.initialize(video, canvas);

            // Start processing
            streamProcessor.start();

            // Update UI
            document.getElementById('startBtn').disabled = true;
            document.getElementById('stopBtn').disabled = false;
            document.getElementById('cameraSelect').disabled = true;

            showNotification('Monitoring boshlandi', 'success');
        } catch (error) {
            console.error('Failed to start monitoring:', error);
            showNotification('Kamera ishga tushmadi: ' + error.message, 'error');
        }
    }

    function stopMonitoring() {
        if (streamProcessor) {
            streamProcessor.stop();

            // Stop camera stream
            const video = document.getElementById('cameraFeed');
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
                video.srcObject = null;
            }

            // Update UI
            document.getElementById('startBtn').disabled = false;
            document.getElementById('stopBtn').disabled = true;
            document.getElementById('cameraSelect').disabled = false;

            showNotification('Monitoring to\'xtatildi', 'info');
        }
    }

    function changeCameraSource() {
        if (streamProcessor) {
            streamProcessor.config.cameraId = document.getElementById('cameraSelect').value;

            // If currently monitoring, restart with new camera
            if (streamProcessor.isProcessing) {
                stopMonitoring();
                setTimeout(startMonitoring, 500);
            }
        }
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <span>${message}</span>
                <button type="button" class="btn-close btn-sm" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

        document.getElementById('notifications').appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 500);
        }, 5000);
    }

    // Listen for face detection events
    window.addEventListener('faceDetected', function(e) {
        console.log('Face detected:', e.detail);

        // Update UI with detection info
        const activityItem = `
            <div class="activity-item new ${e.detail.action}">
                <div class="activity-user">${e.detail.name}</div>
                <div class="activity-time">${e.detail.time}</div>
                <div class="activity-action badge bg-${e.detail.action === 'check_in' ? 'success' : 'warning'}">
                    ${e.detail.action === 'check_in' ? 'Kirdi' : 'Chiqdi'}
                </div>
                <small class="text-muted ms-2">Aniqlik: ${e.detail.confidence}%</small>
            </div>
        `;

        const activityList = document.getElementById('activityList');
        activityList.insertAdjacentHTML('afterbegin', activityItem);

        // Keep only latest 20 items
        const items = activityList.querySelectorAll('.activity-item');
        if (items.length > 20) {
            items[items.length - 1].remove();
        }
    });
</script>
@endpush
@endsection