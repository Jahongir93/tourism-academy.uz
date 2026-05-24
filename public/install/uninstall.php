<?php
session_start();

// Security check - only allow uninstall from local or with authentication
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    // Require password for remote uninstall
    if (!isset($_POST['confirm_password']) || $_POST['confirm_password'] !== 'uninstall_system_2024') {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Uninstall System</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
                .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 400px; }
                h2 { color: #d73502; }
                input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
                button { background: #d73502; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
                button:hover { background: #c23616; }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>⚠️ Uninstall System</h2>
                <p><strong>Warning:</strong> This will delete all data and configuration files!</p>
                <form method="POST">
                    <input type="password" name="confirm_password" placeholder="Enter uninstall password" required>
                    <button type="submit">Confirm Uninstall</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Uninstall process
try {
    // Remove .env file
    if (file_exists('../../.env')) {
        unlink('../../.env');
    }

    // Clear Laravel cache if exists
    if (file_exists('../../artisan')) {
        exec('cd ../.. && php artisan config:clear 2>&1');
        exec('cd ../.. && php artisan cache:clear 2>&1');
        exec('cd ../.. && php artisan view:clear 2>&1');
    }

    // Clear sessions
    $session_path = '../../storage/framework/sessions';
    if (is_dir($session_path)) {
        $files = glob($session_path . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    // Success message
    $message = 'System has been uninstalled successfully. Database was not modified for safety.';
    $success = true;

} catch (Exception $e) {
    $message = 'Error during uninstall: ' . $e->getMessage();
    $success = false;
}

session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Uninstall Complete</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 500px; text-align: center; }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
        a { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="<?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $success ? '✓ Uninstall Complete' : '✗ Uninstall Failed'; ?>
        </h2>
        <p><?php echo htmlspecialchars($message); ?></p>
        <p><strong>Note:</strong> To completely remove the system, manually drop the database.</p>
        <a href="index.php">Reinstall System</a>
    </div>
</body>
</html>