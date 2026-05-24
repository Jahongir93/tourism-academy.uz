<?php
// Security check file - prevents direct access to installation files after installation

// Check if system is already installed
if (file_exists('../../.env') && filesize('../../.env') > 100) {
    // System is installed, prevent access to install directory
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Access Denied</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
            }
            .container {
                background: white;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                max-width: 500px;
                text-align: center;
            }
            h1 {
                color: #e74c3c;
                margin-bottom: 20px;
            }
            .warning {
                background: #fff3cd;
                border: 1px solid #ffc107;
                color: #856404;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
            }
            .instructions {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 4px;
                text-align: left;
                margin-top: 20px;
            }
            code {
                background: #e9ecef;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: monospace;
            }
            a {
                display: inline-block;
                margin-top: 20px;
                padding: 10px 20px;
                background: #3498db;
                color: white;
                text-decoration: none;
                border-radius: 4px;
            }
            a:hover {
                background: #2980b9;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🚫 Access Denied</h1>

            <div class="warning">
                <strong>Security Warning:</strong> The system is already installed!
            </div>

            <p>The installation directory should be deleted for security reasons.</p>

            <div class="instructions">
                <strong>Please delete the following directory:</strong>
                <p><code>/public/install/</code></p>

                <p style="margin-top: 15px;"><strong>Or run this command:</strong></p>
                <p><code>rm -rf public/install</code></p>
            </div>

            <a href="../../">Go to Homepage</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Function to check system requirements
function checkRequirements() {
    $requirements = [];

    // PHP Version
    $requirements['php_version'] = [
        'label' => 'PHP Version >= 8.1',
        'current' => PHP_VERSION,
        'required' => '8.1.0',
        'status' => version_compare(PHP_VERSION, '8.1.0', '>=')
    ];

    // Required extensions
    $extensions = [
        'pdo' => 'PDO Extension',
        'pdo_mysql' => 'PDO MySQL Extension',
        'mbstring' => 'Mbstring Extension',
        'openssl' => 'OpenSSL Extension',
        'json' => 'JSON Extension',
        'fileinfo' => 'Fileinfo Extension',
        'curl' => 'CURL Extension',
        'gd' => 'GD Extension',
        'zip' => 'ZIP Extension'
    ];

    foreach ($extensions as $ext => $label) {
        $requirements[$ext] = [
            'label' => $label,
            'status' => extension_loaded($ext)
        ];
    }

    // Writable directories
    $directories = [
        '../../storage/app' => 'storage/app',
        '../../storage/framework' => 'storage/framework',
        '../../storage/logs' => 'storage/logs',
        '../../bootstrap/cache' => 'bootstrap/cache',
        '../../public' => 'public'
    ];

    foreach ($directories as $path => $label) {
        $requirements['dir_' . str_replace('/', '_', $label)] = [
            'label' => $label . ' writable',
            'status' => is_writable($path) || @mkdir($path, 0755, true)
        ];
    }

    return $requirements;
}

// Function to validate database connection
function validateDatabase($host, $port, $name, $user, $pass) {
    try {
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Try to select the database
        $pdo->exec("USE `$name`");

        return ['success' => true, 'message' => 'Database connection successful'];
    } catch (PDOException $e) {
        // Try to create database if it doesn't exist
        try {
            $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            return ['success' => true, 'message' => 'Database created successfully'];
        } catch (PDOException $e2) {
            return ['success' => false, 'message' => 'Database error: ' . $e2->getMessage()];
        }
    }
}

// Function to generate secure random key
function generateSecureKey($length = 32) {
    return bin2hex(random_bytes($length));
}

// Function to sanitize input
function sanitizeInput($input, $type = 'string') {
    $input = trim($input);

    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        default:
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

// Export functions for use in other installation files
if (!function_exists('installationSecurityCheck')) {
    function installationSecurityCheck() {
        // Check if running from install directory
        if (!strpos($_SERVER['REQUEST_URI'], '/install/')) {
            return false;
        }

        // Check if already installed
        if (file_exists('../../.env') && filesize('../../.env') > 100) {
            header('Location: security_check.php');
            exit;
        }

        return true;
    }
}
?>