<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data = json_decode(file_get_contents('php://input'), true);

$admissionID   = (int)($data['admissionID']   ?? 0);
$admissionDate = $data['admissionDate']        ?? '';
$patientName   = trim($data['patientName']     ?? '');
$gender        = $data['gender']               ?? '';
$staffid       = (int)($data['staffid']        ?? 0);
$doctorid      = (int)($data['doctorid']       ?? 0);

if (!$admissionID || !$patientName || !$admissionDate ||
    !in_array($gender, ['Male', 'Female']) || !$staffid || !$doctorid) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing fields']);
    exit;
}

$stmt = $conn->prepare("
    UPDATE InPatient
    SET admissionDate = ?,
        patientName   = ?,
        gender        = ?,
        staffid       = ?,
        doctorid      = ?
    WHERE admissionID = ?
");
$stmt->bind_param("sssiii", $admissionDate, $patientName, $gender, $staffid, $doctorid, $admissionID);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Patient updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
