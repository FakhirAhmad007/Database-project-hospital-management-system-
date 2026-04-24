<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data = json_decode(file_get_contents('php://input'), true);

// Server-side validation
$admissionDate = $data['admissionDate'] ?? '';
$patientName   = trim($data['patientName'] ?? '');
$gender        = $data['gender'] ?? '';
$staffid       = (int)($data['staffid'] ?? 0);
$doctorid      = (int)($data['doctorid'] ?? 0);

if (!$patientName || !$admissionDate || !in_array($gender, ['Male', 'Female']) || !$staffid || !$doctorid) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing fields']);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $admissionDate)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    exit;
}

// admissionID is now AUTO_INCREMENT — we don't accept it from client
$stmt = $conn->prepare("
    INSERT INTO InPatient (admissionDate, patientName, gender, staffid, doctorid)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("sssii", $admissionDate, $patientName, $gender, $staffid, $doctorid);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Patient admitted successfully', 'id' => $conn->insert_id]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
