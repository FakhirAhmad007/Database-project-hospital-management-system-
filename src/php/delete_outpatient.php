<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data      = json_decode(file_get_contents('php://input'), true);
$patientid = (int)($data['patientid'] ?? 0);

if (!$patientid) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid patient ID']);
    exit;
}

// All cascade deletes use prepared statements — note consistent patientId casing fixed
$stmt = $conn->prepare("DELETE FROM OutPatientBill WHERE patientId = ?");
$stmt->bind_param("i", $patientid);
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM OutPatientMedicalInfo WHERE patientId = ?");
$stmt->bind_param("i", $patientid);
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM OutPatient WHERE patientid = ?");
$stmt->bind_param("i", $patientid);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Out-patient deleted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
