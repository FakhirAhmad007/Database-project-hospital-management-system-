<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data = json_decode(file_get_contents('php://input'), true);

$patientid   = (int)($data['patientid']   ?? 0);
$patientname = trim($data['patientname']  ?? '');
$staffid     = (int)($data['staffid']     ?? 0);
$doctorid    = (int)($data['doctorid']    ?? 0);

if (!$patientid || !$patientname || !$staffid || !$doctorid) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing fields']);
    exit;
}

// doctorname column removed — only update doctorid
$stmt = $conn->prepare("
    UPDATE OutPatient
    SET patientname = ?,
        staffid     = ?,
        doctorid    = ?
    WHERE patientid = ?
");
$stmt->bind_param("siii", $patientname, $staffid, $doctorid, $patientid);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Out-patient updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
