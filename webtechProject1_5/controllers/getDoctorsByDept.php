<?php
require_once('../models/doctorModel.php');

$dept = $_REQUEST['dept'] ?? '';

if ($dept === '') {
    // If no department selected, return all doctors (or empty, user requirement says 'filter out', implies showing all if nothing selected)
    // The requirement says: "otherwise will show all doctor name"
    $doctors = getAllDoctors();
} else {
    $doctors = getDoctorsBySpecialty($dept);
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($doctors);
?>
