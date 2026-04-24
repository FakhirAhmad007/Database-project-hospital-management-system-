<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');

$result = $conn->query("SELECT staffid, firstname, surname FROM Staff");

if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
