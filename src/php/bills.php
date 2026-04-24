<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');

$result = $conn->query("
    SELECT
        b.paymentid,
        b.total,
        i.patientName,
        i.admissionDate,
        d.doctorname
    FROM InPatientBill b
    LEFT JOIN InPatient i ON b.admissionID = i.admissionID
    LEFT JOIN Doctor    d ON i.doctorid    = d.doctorid
");

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
