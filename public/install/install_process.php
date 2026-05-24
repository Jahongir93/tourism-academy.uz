<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check session data
if (!isset($_SESSION['db_config']) || !isset($_SESSION['app_config'])) {
    header('Location: index.php?step=1');
    exit;
}

$db_config = $_SESSION['db_config'];
$app_config = $_SESSION['app_config'];

try {
    // Connect to database
    $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_config['user'], $db_config['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start transaction
    $pdo->beginTransaction();

    // Import database schema
    $schema_file = __DIR__ . '/schema.sql';
    if (file_exists($schema_file)) {
        $schema = file_get_contents($schema_file);
        $statements = array_filter(array_map('trim', explode(';', $schema)));

        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
    } else {
        // Create basic tables if schema file doesn't exist
        createBasicTables($pdo);
    }

    // Insert admin user
    $admin_password = password_hash($app_config['admin_password'], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, email_verified_at, role, created_at, updated_at)
        VALUES (:name, :email, :password, NOW(), 'admin', NOW(), NOW())
        ON DUPLICATE KEY UPDATE password = :password2, role = 'admin'
    ");
    $stmt->execute([
        'name' => 'Administrator',
        'email' => $app_config['admin_email'],
        'password' => $admin_password,
        'password2' => $admin_password
    ]);

    // Commit transaction
    $pdo->commit();

    // Create .env file
    $env_content = generateEnvFile($db_config, $app_config);
    file_put_contents('../../.env', $env_content);

    // Generate app key if Laravel
    if (file_exists('../../artisan')) {
        exec('cd ../.. && php artisan key:generate --force 2>&1', $output);
        exec('cd ../.. && php artisan config:cache 2>&1', $output);
        exec('cd ../.. && php artisan migrate --force 2>&1', $output);
    }

    // Clear session
    session_destroy();

    // Redirect to success page
    header('Location: index.php?step=4');
    exit;

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['install_error'] = $e->getMessage();
    header('Location: index.php?step=3&error=' . urlencode($e->getMessage()));
    exit;
}

function createBasicTables($pdo) {
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            email_verified_at TIMESTAMP NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) DEFAULT 'user',
            remember_token VARCHAR(100) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_email (email),
            INDEX idx_role (role)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Create password resets table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS password_resets (
            email VARCHAR(255) NOT NULL,
            token VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NULL,
            KEY password_resets_email_index (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Create sessions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sessions (
            id VARCHAR(255) PRIMARY KEY,
            user_id BIGINT UNSIGNED NULL,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            payload TEXT NOT NULL,
            last_activity INT NOT NULL,
            INDEX sessions_user_id_index (user_id),
            INDEX sessions_last_activity_index (last_activity)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Create categories table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            description TEXT NULL,
            parent_id BIGINT UNSIGNED NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_slug (slug),
            INDEX idx_parent (parent_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Create posts table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS posts (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            content LONGTEXT NULL,
            excerpt TEXT NULL,
            category_id BIGINT UNSIGNED NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            featured_image VARCHAR(255) NULL,
            status VARCHAR(50) DEFAULT 'draft',
            published_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_slug (slug),
            INDEX idx_status (status),
            INDEX idx_user (user_id),
            INDEX idx_category (category_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Create settings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `key` VARCHAR(255) UNIQUE NOT NULL,
            `value` TEXT NULL,
            `type` VARCHAR(50) DEFAULT 'string',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_key (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Insert default settings
    $pdo->exec("
        INSERT INTO settings (`key`, `value`, created_at, updated_at) VALUES
        ('site_name', 'Tourism Academy', NOW(), NOW()),
        ('site_description', 'Welcome to Tourism Academy', NOW(), NOW()),
        ('site_keywords', 'tourism, education, academy', NOW(), NOW()),
        ('contact_email', '', NOW(), NOW()),
        ('contact_phone', '', NOW(), NOW()),
        ('contact_address', '', NOW(), NOW())
        ON DUPLICATE KEY UPDATE updated_at = NOW()
    ");
}

function generateEnvFile($db_config, $app_config) {
    $app_key = 'base64:' . base64_encode(random_bytes(32));

    return "APP_NAME=\"{$app_config['name']}\"
APP_ENV=production
APP_KEY={$app_key}
APP_DEBUG=false
APP_URL={$app_config['url']}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST={$db_config['host']}
DB_PORT={$db_config['port']}
DB_DATABASE={$db_config['name']}
DB_USERNAME={$db_config['user']}
DB_PASSWORD={$db_config['pass']}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME=\"{$app_config['name']}\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"
MIX_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"";
}
?>