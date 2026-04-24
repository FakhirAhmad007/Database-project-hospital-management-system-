<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

// Load credentials from .env (never hardcoded)
$envPath = __DIR__ . '/../../.env';
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $val] = array_map('trim', explode('=', $line, 2));
        $_ENV[$key] = $val;
    }
}

$validUser = $_ENV['ADMIN_USER'] ?? 'admin';
$validHash = $_ENV['ADMIN_HASH'] ?? '';  // bcrypt hash stored in .env

if ($username === $validUser && password_verify($password, $validHash)) {
    session_regenerate_id(true);
    $_SESSION['authenticated'] = true;
    $_SESSION['username'] = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    echo json_encode(['success' => true]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}
