<?php
session_start();
include 'config.php';
requireAuth();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');

// Use GROUP_CONCAT to collapse multiple medicines into one row per patient
$result = $conn->query("
    SELECT
        o.patientid,
        o.patientname,
        o.staffid,
        o.doctorid,
        d.doctorname,
        d.specialization,
        GROUP_CONCAT(DISTINCT om.medicinename ORDER BY om.medicinename SEPARATOR ', ') AS medicinename,
        ob.total
    FROM OutPatient o
    LEFT JOIN Doctor                d  ON o.doctorid  = d.doctorid
    LEFT JOIN OutPatientMedicalInfo om ON o.patientid = om.patientId
    LEFT JOIN OutPatientBill        ob ON o.patientid = ob.patientId
    GROUP BY o.patientid, o.patientname, o.staffid, o.doctorid,
             d.doctorname, d.specialization, ob.total
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
