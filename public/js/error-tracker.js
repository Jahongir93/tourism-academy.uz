/**
 * Frontend Error Tracking System
 * Automatically captures and reports JavaScript errors
 */

(function() {
    'use strict';

    const ErrorTracker = {
        endpoint: '/api/log-frontend-error',
        maxErrors: 10,
        errorCount: 0,
        reportedErrors: new Set(),

        /**
         * Initialize error tracking
         */
        init() {
            this.setupGlobalErrorHandler();
            this.setupUnhandledRejectionHandler();
            this.setupResourceErrorHandler();
            this.checkConsoleErrors();
        },

        /**
         * Setup global error handler
         */
        setupGlobalErrorHandler() {
            window.addEventListener('error', (event) => {
                this.handleError({
                    type: 'JavaScript Error',
                    message: event.message,
                    source: event.filename,
                    line: event.lineno,
                    column: event.colno,
                    stack: event.error?.stack,
                    timestamp: new Date().toISOString()
                });
            }, true);
        },

        /**
         * Setup unhandled promise rejection handler
         */
        setupUnhandledRejectionHandler() {
            window.addEventListener('unhandledrejection', (event) => {
                this.handleError({
                    type: 'Unhandled Promise Rejection',
                    message: event.reason?.message || event.reason,
                    stack: event.reason?.stack,
                    timestamp: new Date().toISOString()
                });
            });
        },

        /**
         * Setup resource loading error handler
         */
        setupResourceErrorHandler() {
            window.addEventListener('error', (event) => {
                if (event.target !== window) {
                    const element = event.target;
                    this.handleError({
                        type: 'Resource Loading Error',
                        message: `Failed to load: ${element.tagName}`,
                        source: element.src || element.href,
                        timestamp: new Date().toISOString()
                    });
                }
            }, true);
        },

        /**
         * Check for console errors
         */
        checkConsoleErrors() {
            const originalError = console.error;
            console.error = (...args) => {
                this.handleError({
                    type: 'Console Error',
                    message: args.join(' '),
                    timestamp: new Date().toISOString()
                });
                originalError.apply(console, args);
            };
        },

        /**
         * Handle error
         */
        handleError(errorData) {
            // Prevent duplicate reports
            const errorKey = `${errorData.type}-${errorData.message}-${errorData.source}`;
            if (this.reportedErrors.has(errorKey)) {
                return;
            }

            // Limit number of errors reported
            if (this.errorCount >= this.maxErrors) {
                console.warn('Maximum error reports reached');
                return;
            }

            this.reportedErrors.add(errorKey);
            this.errorCount++;

            // Add additional context
            errorData.url = window.location.href;
            errorData.userAgent = navigator.userAgent;
            errorData.viewport = {
                width: window.innerWidth,
                height: window.innerHeight
            };

            // Display error in development mode
            if (this.isDevMode()) {
                this.displayError(errorData);
            }

            // Report error to backend
            this.reportError(errorData);
        },

        /**
         * Check if in development mode
         */
        isDevMode() {
            return window.location.hostname === 'localhost' ||
                   window.location.hostname === '127.0.0.1' ||
                   window.location.hostname.includes('local');
        },

        /**
         * Display error notification
         */
        displayError(errorData) {
            // Create error notification
            const notification = document.createElement('div');
            notification.className = 'error-tracker-notification';
            notification.innerHTML = `
                <div style="position: fixed; bottom: 20px; right: 20px; background: #ef4444; color: white;
                            padding: 16px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                            max-width: 400px; z-index: 99999; font-family: monospace; font-size: 12px;">
                    <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 8px;">
                        <strong style="flex: 1;">⚠️ ${errorData.type}</strong>
                        <button onclick="this.parentElement.parentElement.remove()"
                                style="background: none; border: none; color: white; cursor: pointer; font-size: 18px; padding: 0; margin-left: 10px;">×</button>
                    </div>
                    <div style="margin-bottom: 4px;"><strong>Message:</strong> ${errorData.message}</div>
                    ${errorData.source ? `<div style="margin-bottom: 4px;"><strong>Source:</strong> ${errorData.source}</div>` : ''}
                    ${errorData.line ? `<div><strong>Line:</strong> ${errorData.line}:${errorData.column || 0}</div>` : ''}
                </div>
            `;
            document.body.appendChild(notification);

            // Auto-remove after 10 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 10000);
        },

        /**
         * Report error to backend
         */
        reportError(errorData) {
            // Only log to console in development mode
            if (this.isDevMode()) {
                console.group('🐛 Error Tracker');
                console.error('Type:', errorData.type);
                console.error('Message:', errorData.message);
                if (errorData.source) console.error('Source:', errorData.source);
                if (errorData.line) console.error('Line:', `${errorData.line}:${errorData.column || 0}`);
                if (errorData.stack) console.error('Stack:', errorData.stack);
                console.groupEnd();
            }

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!csrfToken) {
                return; // Silently fail if no CSRF token
            }

            // Try to send to backend, but don't show errors if it fails
            fetch(this.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(errorData)
            }).catch(() => {
                // Silently fail - endpoint may not exist
            });
        }
    };

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => ErrorTracker.init());
    } else {
        ErrorTracker.init();
    }

    // Expose to window for manual error reporting
    window.ErrorTracker = ErrorTracker;
})();
