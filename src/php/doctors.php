<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');

$result = $conn->query("
    SELECT
        d.doctorid,
        d.doctorname,
        d.specialization,
        COUNT(DISTINCT i.admissionID) AS inpatient_count,
        COUNT(DISTINCT o.patientid)   AS outpatient_count
    FROM Doctor d
    LEFT JOIN InPatient  i ON d.doctorid = i.doctorid
    LEFT JOIN OutPatient o ON d.doctorid = o.doctorid
    GROUP BY d.doctorid, d.doctorname, d.specialization
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
