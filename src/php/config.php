<?php
// Load .env file from project root (two levels up from src/php/)
$envPath = __DIR__ . '/../../.env';
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $val] = array_map('trim', explode('=', $line, 2));
        $_ENV[$key] = $val;
    }
}

$host     = $_ENV['DB_HOST']     ?? 'localhost';
$user     = $_ENV['DB_USER']     ?? '';
$password = $_ENV['DB_PASS']     ?? '';
$database = $_ENV['DB_NAME']     ?? '';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed']));
}

$conn->set_charset('utf8mb4');

// ── Session authentication helper ──────────────────────────────────────────
function requireAuth(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['authenticated'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}
