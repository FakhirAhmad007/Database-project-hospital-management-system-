<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data        = json_decode(file_get_contents('php://input'), true);
$admissionID = (int)($data['admissionID'] ?? 0);

if (!$admissionID) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid admission ID']);
    exit;
}

// All cascade deletes use prepared statements — no interpolation
$tables = ['InPatientBill', 'InPatientService', 'InPatientMedical', 'Room'];
foreach ($tables as $table) {
    $stmt = $conn->prepare("DELETE FROM `$table` WHERE admissionID = ?");
    $stmt->bind_param("i", $admissionID);
    $stmt->execute();
}

// Delete the patient record
$stmt = $conn->prepare("DELETE FROM InPatient WHERE admissionID = ?");
$stmt->bind_param("i", $admissionID);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Patient deleted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
