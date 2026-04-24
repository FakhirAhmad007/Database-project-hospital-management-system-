<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data = json_decode(file_get_contents('php://input'), true);

$patientname = trim($data['patientname'] ?? '');
$staffid     = (int)($data['staffid']    ?? 0);
$doctorid    = (int)($data['doctorid']   ?? 0);

if (!$patientname || !$staffid || !$doctorid) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing fields']);
    exit;
}

// Do NOT store doctorname — fetch via JOIN on doctorid instead
// patientid is AUTO_INCREMENT — not accepted from client
$stmt = $conn->prepare("
    INSERT INTO OutPatient (patientname, staffid, doctorid)
    VALUES (?, ?, ?)
");
$stmt->bind_param("sii", $patientname, $staffid, $doctorid);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Out-patient registered successfully', 'id' => $conn->insert_id]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
