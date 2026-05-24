<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if already installed
if (file_exists('../../.env') && filesize('../../.env') > 100) {
    header('Location: ../');
    exit('System is already installed. Please delete .env file to reinstall.');
}

// Installation steps
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 1) {
        // Requirements check passed
        header('Location: ?step=2');
        exit;
    } elseif ($step === 2) {
        // Database configuration
        $db_host = trim($_POST['db_host'] ?? '');
        $db_port = trim($_POST['db_port'] ?? '3306');
        $db_name = trim($_POST['db_name'] ?? '');
        $db_user = trim($_POST['db_user'] ?? '');
        $db_pass = $_POST['db_pass'] ?? '';

        // Validate inputs
        if (empty($db_host) || empty($db_name) || empty($db_user)) {
            $error = 'Please fill all required fields';
        } else {
            // Test database connection
            try {
                $dsn = "mysql:host=$db_host;port=$db_port;charset=utf8mb4";
                $pdo = new PDO($dsn, $db_user, $db_pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Create database if not exists
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                // Save to session
                $_SESSION['db_config'] = [
                    'host' => $db_host,
                    'port' => $db_port,
                    'name' => $db_name,
                    'user' => $db_user,
                    'pass' => $db_pass
                ];

                header('Location: ?step=3');
                exit;
            } catch (PDOException $e) {
                $error = 'Database connection failed: ' . $e->getMessage();
            }
        }
    } elseif ($step === 3) {
        // Site configuration
        $app_name = trim($_POST['app_name'] ?? '');
        $app_url = trim($_POST['app_url'] ?? '');
        $admin_email = trim($_POST['admin_email'] ?? '');
        $admin_password = $_POST['admin_password'] ?? '';

        if (empty($app_name) || empty($app_url) || empty($admin_email) || empty($admin_password)) {
            $error = 'Please fill all required fields';
        } elseif (strlen($admin_password) < 8) {
            $error = 'Password must be at least 8 characters';
        } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address';
        } else {
            $_SESSION['app_config'] = [
                'name' => $app_name,
                'url' => rtrim($app_url, '/'),
                'admin_email' => $admin_email,
                'admin_password' => $admin_password
            ];

            header('Location: ?step=4');
            exit;
        }
    } elseif ($step === 4) {
        // Execute installation
        require_once 'install_process.php';
        exit;
    }
}

// Check PHP version
$php_version_ok = version_compare(PHP_VERSION, '8.1.0', '>=');
$extensions = [
    'pdo' => extension_loaded('pdo'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'mbstring' => extension_loaded('mbstring'),
    'openssl' => extension_loaded('openssl'),
    'json' => extension_loaded('json'),
    'fileinfo' => extension_loaded('fileinfo'),
    'curl' => extension_loaded('curl'),
];

$folders_writable = [
    'storage/app' => is_writable('../../storage/app'),
    'storage/framework' => is_writable('../../storage/framework'),
    'storage/logs' => is_writable('../../storage/logs'),
    'bootstrap/cache' => is_writable('../../bootstrap/cache'),
    'public' => is_writable('../../public'),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Installation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .steps {
            display: flex;
            justify-content: space-between;
            padding: 20px 30px;
            background: #f7f9fc;
            border-bottom: 1px solid #e1e8ed;
        }
        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e1e8ed;
            color: #8798ad;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .step.active .step-number {
            background: #667eea;
            color: white;
        }
        .step.completed .step-number {
            background: #48bb78;
            color: white;
        }
        .step-title {
            font-size: 12px;
            color: #8798ad;
        }
        .step.active .step-title {
            color: #2d3748;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"] {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e1e8ed;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .error {
            background: #fed7d7;
            color: #9b2c2c;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .success {
            background: #c6f6d5;
            color: #22543d;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .requirement {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: #f7f9fc;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        .requirement.ok {
            background: #c6f6d5;
        }
        .requirement.error {
            background: #fed7d7;
        }
        .check-icon {
            color: #48bb78;
        }
        .x-icon {
            color: #f56565;
        }
        .help-text {
            font-size: 13px;
            color: #718096;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>System Installation</h1>
            <p>Welcome to the installation wizard</p>
        </div>

        <div class="steps">
            <div class="step <?php echo $step >= 1 ? ($step > 1 ? 'completed' : 'active') : ''; ?>">
                <div class="step-number">1</div>
                <div class="step-title">Requirements</div>
            </div>
            <div class="step <?php echo $step >= 2 ? ($step > 2 ? 'completed' : 'active') : ''; ?>">
                <div class="step-number">2</div>
                <div class="step-title">Database</div>
            </div>
            <div class="step <?php echo $step >= 3 ? ($step > 3 ? 'completed' : 'active') : ''; ?>">
                <div class="step-number">3</div>
                <div class="step-title">Configuration</div>
            </div>
            <div class="step <?php echo $step >= 4 ? 'active' : ''; ?>">
                <div class="step-number">4</div>
                <div class="step-title">Complete</div>
            </div>
        </div>

        <div class="content">
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if ($step === 1): ?>
                <h2>System Requirements</h2>
                <p style="margin-bottom: 20px;">Please ensure all requirements are met before proceeding.</p>

                <div class="requirement <?php echo $php_version_ok ? 'ok' : 'error'; ?>">
                    <span>PHP Version >= 8.1</span>
                    <span><?php echo $php_version_ok ? '✓' : '✗'; ?> <?php echo PHP_VERSION; ?></span>
                </div>

                <?php foreach ($extensions as $ext => $loaded): ?>
                <div class="requirement <?php echo $loaded ? 'ok' : 'error'; ?>">
                    <span><?php echo ucfirst(str_replace('_', ' ', $ext)); ?> Extension</span>
                    <span><?php echo $loaded ? '✓' : '✗'; ?></span>
                </div>
                <?php endforeach; ?>

                <?php foreach ($folders_writable as $folder => $writable): ?>
                <div class="requirement <?php echo $writable ? 'ok' : 'error'; ?>">
                    <span><?php echo $folder; ?> Writable</span>
                    <span><?php echo $writable ? '✓' : '✗'; ?></span>
                </div>
                <?php endforeach; ?>

                <form method="POST" style="margin-top: 20px;">
                    <button type="submit" class="btn" <?php echo (!$php_version_ok || in_array(false, $extensions) || in_array(false, $folders_writable)) ? 'disabled' : ''; ?>>
                        Continue to Database Setup
                    </button>
                </form>

            <?php elseif ($step === 2): ?>
                <h2>Database Configuration</h2>
                <p style="margin-bottom: 20px;">Enter your database connection details.</p>

                <form method="POST">
                    <div class="form-group">
                        <label for="db_host">Database Host *</label>
                        <input type="text" id="db_host" name="db_host" value="localhost" required>
                        <div class="help-text">Usually 'localhost' for local installations</div>
                    </div>

                    <div class="form-group">
                        <label for="db_port">Database Port</label>
                        <input type="number" id="db_port" name="db_port" value="3306">
                        <div class="help-text">Default MySQL port is 3306</div>
                    </div>

                    <div class="form-group">
                        <label for="db_name">Database Name *</label>
                        <input type="text" id="db_name" name="db_name" required>
                        <div class="help-text">Will be created if it doesn't exist</div>
                    </div>

                    <div class="form-group">
                        <label for="db_user">Database Username *</label>
                        <input type="text" id="db_user" name="db_user" required>
                    </div>

                    <div class="form-group">
                        <label for="db_pass">Database Password</label>
                        <input type="password" id="db_pass" name="db_pass">
                        <div class="help-text">Leave blank if no password</div>
                    </div>

                    <button type="submit" class="btn">Test Connection & Continue</button>
                </form>

            <?php elseif ($step === 3): ?>
                <h2>Site Configuration</h2>
                <p style="margin-bottom: 20px;">Configure your application settings.</p>

                <form method="POST">
                    <div class="form-group">
                        <label for="app_name">Application Name *</label>
                        <input type="text" id="app_name" name="app_name" value="Tourism Academy" required>
                    </div>

                    <div class="form-group">
                        <label for="app_url">Application URL *</label>
                        <input type="text" id="app_url" name="app_url" value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['REQUEST_URI'])); ?>" required>
                        <div class="help-text">Full URL without trailing slash</div>
                    </div>

                    <div class="form-group">
                        <label for="admin_email">Admin Email *</label>
                        <input type="email" id="admin_email" name="admin_email" required>
                        <div class="help-text">This will be your login email</div>
                    </div>

                    <div class="form-group">
                        <label for="admin_password">Admin Password *</label>
                        <input type="password" id="admin_password" name="admin_password" required minlength="8">
                        <div class="help-text">Minimum 8 characters</div>
                    </div>

                    <button type="submit" class="btn">Install System</button>
                </form>

            <?php elseif ($step === 4): ?>
                <h2>Installation Complete!</h2>
                <div class="success" style="margin: 20px 0;">
                    System has been successfully installed!
                </div>

                <p>Your system is now ready to use. You can login with the admin credentials you provided.</p>

                <div style="background: #f7f9fc; padding: 20px; border-radius: 6px; margin: 20px 0;">
                    <h3 style="margin-bottom: 10px;">Login Credentials</h3>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['app_config']['admin_email'] ?? ''); ?></p>
                    <p><strong>Password:</strong> [The password you entered]</p>
                </div>

                <div style="background: #fed7d7; padding: 15px; border-radius: 6px; margin: 20px 0;">
                    <strong>Security Notice:</strong> Please delete the /install directory after installation for security.
                </div>

                <a href="../login" class="btn" style="display: inline-block; text-decoration: none;">Go to Login Page</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>