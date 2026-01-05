
<?php
// controllers/doctorController.php
require_once __DIR__ . "/../models/doctorModel.php";

function cleanText($s) { return trim($s); }

function handleDoctorCreate() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $doctorId = isset($_POST["DoctorID"]) ? (int)$_POST["DoctorID"] : 0;
    $spec     = isset($_POST["Specialty"]) ? cleanText($_POST["Specialty"]) : "";
    $avail    = isset($_POST["Availability"]) ? cleanText($_POST["Availability"]) : "";

    if($doctorId <= 0) { return "Invalid DoctorID(UserID)"; }
    if(strlen($spec) < 2) { return "Specialty too short"; }
    if(strlen($avail) < 3) { return "Availability too short"; }

    $ok = createDoctor($doctorId, $spec, $avail);
    if($ok) { header("Location: ../views/admindashboard.php"); exit(); }
    return "Failed to create";
}

function handleDoctorUpdate() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $doctorId = isset($_POST["DoctorID"]) ? (int)$_POST["DoctorID"] : 0;
    $spec     = isset($_POST["Specialty"]) ? cleanText($_POST["Specialty"]) : "";
    $avail    = isset($_POST["Availability"]) ? cleanText($_POST["Availability"]) : "";

    if($doctorId <= 0) { return "Invalid DoctorID"; }
    if(strlen($spec) < 2) { return "Specialty too short"; }
    if(strlen($avail) < 3) { return "Availability too short"; }

    $ok = updateDoctor($doctorId, $spec, $avail);
    if($ok) { header("Location: ../views/admindashboard.php"); exit(); }
    return "Failed to update";
}

function handleDoctorDelete() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $doctorId = isset($_POST["DoctorID"]) ? (int)$_POST["DoctorID"] : 0;
    if($doctorId <= 0) { return "Invalid DoctorID"; }

    $ok = deleteDoctor($doctorId);
    if($ok) { header("Location: ../views/admindashboard.php"); exit(); }
    return "Failed to delete";
}
