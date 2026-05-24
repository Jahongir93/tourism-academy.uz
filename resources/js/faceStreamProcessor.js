/**
 * Face Stream Processor - Avtomatik yuzni tanish tizimi
 * Kamera orqali real-time yuzlarni aniqlash va attendance qayd qilish
 */

class FaceStreamProcessor {
    constructor(config = {}) {
        this.config = {
            apiUrl: config.apiUrl || '/api/face/stream',
            cameraId: config.cameraId || 'entrance_main',
            frameInterval: config.frameInterval || 500, // 500ms = 2 FPS
            batchSize: config.batchSize || 5,
            detectionConfidence: config.detectionConfidence || 0.5,
            ...config
        };

        this.video = null;
        this.canvas = null;
        this.context = null;
        this.isProcessing = false;
        this.frameBuffer = [];
        this.detectionResults = [];
        this.stats = {
            totalFrames: 0,
            detectedFaces: 0,
            recognizedUsers: 0
        };
    }

    /**
     * Initialize camera stream
     */
    async initialize(videoElement, canvasElement) {
        this.video = videoElement;
        this.canvas = canvasElement || document.createElement('canvas');
        this.context = this.canvas.getContext('2d');

        try {
            // Camera stream olish
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                    facingMode: 'environment'
                }
            });

            this.video.srcObject = stream;
            await this.video.play();

            // Canvas o'lchamlarini sozlash
            this.canvas.width = this.video.videoWidth;
            this.canvas.height = this.video.videoHeight;

            console.log('Camera initialized successfully');
            return true;
        } catch (error) {
            console.error('Camera initialization failed:', error);
            throw error;
        }
    }

    /**
     * Start processing stream
     */
    start() {
        if (this.isProcessing) {
            console.warn('Already processing');
            return;
        }

        this.isProcessing = true;
        this.processLoop();

        // Batch processor
        this.batchInterval = setInterval(() => {
            this.processBatch();
        }, this.config.frameInterval * this.config.batchSize);

        console.log('Stream processing started');
    }

    /**
     * Stop processing
     */
    stop() {
        this.isProcessing = false;

        if (this.batchInterval) {
            clearInterval(this.batchInterval);
            this.batchInterval = null;
        }

        console.log('Stream processing stopped');
    }

    /**
     * Main processing loop
     */
    async processLoop() {
        if (!this.isProcessing) return;

        // Capture frame
        this.captureFrame();

        // Schedule next frame
        setTimeout(() => {
            requestAnimationFrame(() => this.processLoop());
        }, this.config.frameInterval);
    }

    /**
     * Capture current frame
     */
    captureFrame() {
        if (!this.video || this.video.readyState !== 4) return;

        // Draw video frame to canvas
        this.context.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);

        // Convert to base64
        const frameData = this.canvas.toDataURL('image/jpeg', 0.8).split(',')[1];

        // Add to buffer
        this.frameBuffer.push({
            frame: frameData,
            timestamp: Date.now()
        });

        this.stats.totalFrames++;

        // Limit buffer size
        if (this.frameBuffer.length > this.config.batchSize * 2) {
            this.frameBuffer = this.frameBuffer.slice(-this.config.batchSize);
        }
    }

    /**
     * Process batch of frames
     */
    async processBatch() {
        if (this.frameBuffer.length === 0) return;

        const framesToProcess = this.frameBuffer.splice(0, this.config.batchSize);

        try {
            const response = await this.sendFramesToAPI(framesToProcess);

            if (response.success) {
                this.handleDetectionResults(response.results || [response]);
            }
        } catch (error) {
            console.error('Batch processing error:', error);
        }
    }

    /**
     * Send frames to API
     */
    async sendFramesToAPI(frames) {
        const endpoint = frames.length === 1
            ? `${this.config.apiUrl}/process`
            : `${this.config.apiUrl}/batch`;

        const payload = frames.length === 1
            ? {
                frame: frames[0].frame,
                timestamp: frames[0].timestamp,
                camera_id: this.config.cameraId
            }
            : {
                frames: frames,
                camera_id: this.config.cameraId
            };

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(payload)
        });

        return await response.json();
    }

    /**
     * Handle detection results
     */
    handleDetectionResults(results) {
        results.forEach(result => {
            if (result.processed_users && result.processed_users.length > 0) {
                result.processed_users.forEach(user => {
                    this.stats.recognizedUsers++;
                    this.onUserDetected(user);
                });
            }
        });
    }

    /**
     * User detected callback
     */
    onUserDetected(user) {
        // Emit custom event
        const event = new CustomEvent('faceDetected', {
            detail: user
        });
        window.dispatchEvent(event);

        // Log detection
        console.log(`User detected: ${user.name} - ${user.action} at ${user.time}`);

        // Store in results
        this.detectionResults.push({
            ...user,
            detectedAt: new Date()
        });

        // Limit results history
        if (this.detectionResults.length > 100) {
            this.detectionResults = this.detectionResults.slice(-50);
        }
    }

    /**
     * Get statistics
     */
    getStats() {
        return {
            ...this.stats,
            recentDetections: this.detectionResults.slice(-10),
            isProcessing: this.isProcessing,
            bufferSize: this.frameBuffer.length
        };
    }

    /**
     * Draw detection overlay
     */
    drawOverlay(detections) {
        if (!this.context) return;

        detections.forEach(detection => {
            const { x, y, width, height, label, confidence } = detection;

            // Draw bounding box
            this.context.strokeStyle = '#00ff00';
            this.context.lineWidth = 2;
            this.context.strokeRect(x, y, width, height);

            // Draw label
            if (label) {
                this.context.fillStyle = '#00ff00';
                this.context.fillRect(x, y - 25, width, 25);
                this.context.fillStyle = '#ffffff';
                this.context.font = '14px Arial';
                this.context.fillText(
                    `${label} (${Math.round(confidence * 100)}%)`,
                    x + 5,
                    y - 7
                );
            }
        });
    }
}

/**
 * Monitoring Dashboard Manager
 */
class MonitoringDashboard {
    constructor(config = {}) {
        this.config = {
            updateInterval: config.updateInterval || 5000,
            apiUrl: config.apiUrl || '/api/face/monitoring',
            ...config
        };

        this.elements = {};
        this.updateTimer = null;
        this.charts = {};
    }

    /**
     * Initialize dashboard
     */
    initialize(elements) {
        this.elements = elements;
        this.setupEventListeners();
        this.startAutoUpdate();
        this.loadInitialData();
    }

    /**
     * Setup WebSocket for real-time updates
     */
    setupEventListeners() {
        // Listen for face detection events via Laravel Echo
        if (window.Echo) {
            window.Echo.channel('face-detection')
                .listen('FaceDetected', (e) => {
                    this.handleRealTimeDetection(e);
                });
        }

        // Listen for custom events
        window.addEventListener('faceDetected', (e) => {
            this.updateRecentActivity(e.detail);
        });
    }

    /**
     * Handle real-time detection
     */
    handleRealTimeDetection(data) {
        // Update UI with new detection
        this.addActivityItem(data);
        this.updateStatistics();

        // Show notification
        this.showNotification(
            `${data.user_name} - ${data.action === 'check_in' ? 'Kirdi' : 'Chiqdi'}`,
            data.time
        );
    }

    /**
     * Load initial dashboard data
     */
    async loadInitialData() {
        try {
            const response = await fetch(`${this.config.apiUrl}/data`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });

            const data = await response.json();
            if (data.success) {
                this.updateDashboard(data);
            }
        } catch (error) {
            console.error('Failed to load dashboard data:', error);
        }
    }

    /**
     * Update dashboard with new data
     */
    updateDashboard(data) {
        // Update statistics
        if (this.elements.stats) {
            this.elements.stats.totalPresent.textContent = data.stats.total_present;
            this.elements.stats.currentlyIn.textContent = data.stats.currently_in;
            this.elements.stats.checkedOut.textContent = data.stats.checked_out;
            this.elements.stats.avgConfidence.textContent =
                `${Math.round(data.stats.average_confidence)}%`;
        }

        // Update activity list
        if (this.elements.activityList && data.recent_activity) {
            this.updateActivityList(data.recent_activity);
        }

        // Update chart
        if (data.hourly_distribution) {
            this.updateHourlyChart(data.hourly_distribution);
        }
    }

    /**
     * Update activity list
     */
    updateActivityList(activities) {
        if (!this.elements.activityList) return;

        this.elements.activityList.innerHTML = activities.map(activity => `
            <div class="activity-item ${activity.status}">
                <div class="activity-user">${activity.user_name}</div>
                <div class="activity-time">
                    <span class="check-in">Kirish: ${activity.check_in}</span>
                    ${activity.check_out ?
                        `<span class="check-out">Chiqish: ${activity.check_out}</span>` :
                        '<span class="status">Hozir ichkarida</span>'
                    }
                </div>
                <div class="activity-location">${activity.location}</div>
            </div>
        `).join('');
    }

    /**
     * Add single activity item
     */
    addActivityItem(data) {
        if (!this.elements.activityList) return;

        const item = document.createElement('div');
        item.className = `activity-item new ${data.action}`;
        item.innerHTML = `
            <div class="activity-user">${data.user_name}</div>
            <div class="activity-time">${data.time}</div>
            <div class="activity-action">${
                data.action === 'check_in' ? 'Kirdi' : 'Chiqdi'
            }</div>
        `;

        this.elements.activityList.prepend(item);

        // Remove old items
        const items = this.elements.activityList.querySelectorAll('.activity-item');
        if (items.length > 20) {
            items[items.length - 1].remove();
        }

        // Remove 'new' class after animation
        setTimeout(() => {
            item.classList.remove('new');
        }, 3000);
    }

    /**
     * Update hourly chart
     */
    updateHourlyChart(data) {
        // Implementation depends on charting library
        // Example with Chart.js
        if (this.charts.hourly && window.Chart) {
            const hours = Array.from({length: 24}, (_, i) => i);
            const counts = hours.map(hour => {
                const item = data.find(d => d.hour === hour);
                return item ? item.count : 0;
            });

            this.charts.hourly.data.labels = hours.map(h => `${h}:00`);
            this.charts.hourly.data.datasets[0].data = counts;
            this.charts.hourly.update();
        }
    }

    /**
     * Show notification
     */
    showNotification(message, time) {
        if (!this.elements.notifications) return;

        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-message">${message}</div>
                <div class="notification-time">${time}</div>
            </div>
        `;

        this.elements.notifications.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 500);
        }, 5000);
    }

    /**
     * Start auto update
     */
    startAutoUpdate() {
        this.updateTimer = setInterval(() => {
            this.loadInitialData();
        }, this.config.updateInterval);
    }

    /**
     * Stop auto update
     */
    stopAutoUpdate() {
        if (this.updateTimer) {
            clearInterval(this.updateTimer);
            this.updateTimer = null;
        }
    }

    /**
     * Update statistics
     */
    async updateStatistics() {
        // Fetch updated stats
        await this.loadInitialData();
    }
}

// Export for use
window.FaceStreamProcessor = FaceStreamProcessor;
window.MonitoringDashboard = MonitoringDashboard;