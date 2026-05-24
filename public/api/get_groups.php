<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get specialty_id from request
$specialtyId = isset($_GET['specialty_id']) ? intval($_GET['specialty_id']) : 0;

if (!$specialtyId) {
    echo json_encode(['error' => 'specialty_id required']);
    exit;
}

// Database connection - read from environment
$envFile = __DIR__ . '/../../.env';
$dbConfig = [
    'host' => 'localhost',
    'database' => 'tourism_academy',
    'username' => 'root',
    'password' => ''
];

// Try to read .env file if exists
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (preg_match('/DB_HOST=(.+)/', $envContent, $matches)) {
        $dbConfig['host'] = trim($matches[1]);
    }
    if (preg_match('/DB_DATABASE=(.+)/', $envContent, $matches)) {
        $dbConfig['database'] = trim($matches[1]);
    }
    if (preg_match('/DB_USERNAME=(.+)/', $envContent, $matches)) {
        $dbConfig['username'] = trim($matches[1]);
    }
    if (preg_match('/DB_PASSWORD=(.*)/', $envContent, $matches)) {
        $dbConfig['password'] = trim($matches[1]);
    }
}

$host = $dbConfig['host'];
$db = $dbConfig['database'];
$user = $dbConfig['username'];
$pass = $dbConfig['password'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT id, name, code, course FROM `groups` WHERE specialty_id = ? AND is_active = 1 AND name NOT LIKE 'Test%' ORDER BY name");
    $stmt->execute([$specialtyId]);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($groups, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
