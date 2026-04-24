<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');

$result = $conn->query("
    SELECT
        r.roomId,
        r.roomno,
        r.roomcost,
        i.patientName,
        i.admissionDate
    FROM Room r
    LEFT JOIN InPatient i ON r.admissionID = i.admissionID
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
