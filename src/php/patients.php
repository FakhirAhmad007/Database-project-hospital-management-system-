<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');

$result = $conn->query("
    SELECT
        i.admissionID,
        i.patientName,
        i.admissionDate,
        i.gender,
        i.staffid,
        i.doctorid,
        d.doctorname,
        d.specialization,
        r.roomno,
        r.roomcost
    FROM InPatient i
    LEFT JOIN Doctor d ON i.doctorid    = d.doctorid
    LEFT JOIN Room   r ON i.admissionID = r.admissionID
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
