
<?php
// controllers/appointmentController.php
require_once __DIR__ . "/../models/appointmentModel.php";

function cleanText($s) { return trim($s); }

function isValidAppStatus($s) {
    $allowed = ["Pending","Confirmed","Completed","Cancelled"];
    foreach($allowed as $a) {
        if($a === $s) { return true; }
    }
    return false;
}

function handleAppointmentCreate() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $patientId = isset($_POST["PatientID"]) ? (int)$_POST["PatientID"] : 0;
    $doctorId  = isset($_POST["DoctorID"]) ? (int)$_POST["DoctorID"] : 0;
    $deptId    = isset($_POST["DepartmentID"]) ? (int)$_POST["DepartmentID"] : 0;
    $slot      = isset($_POST["Slot"]) ? cleanText($_POST["Slot"]) : "";
    $status    = isset($_POST["Status"]) ? cleanText($_POST["Status"]) : "Pending";

    if($patientId <= 0 || $doctorId <= 0 || $deptId <= 0) { return "Invalid IDs"; }
    if(strlen($slot) < 10) { return "Invalid Slot"; }
    if(!isValidAppStatus($status)) { return "Invalid Status"; }

    $ok = createAppointment($patientId, $doctorId, $deptId, $slot, $status);
    if($ok) { header("Location: ../views/admindashboard.php"); exit(); }
    return "Failed to create";
}

function handleAppointmentUpdate() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $id        = isset($_POST["AppointmentID"]) ? (int)$_POST["AppointmentID"] : 0;
    $patientId = isset($_POST["PatientID"]) ? (int)$_POST["PatientID"] : 0;
    $doctorId  = isset($_POST["DoctorID"]) ? (int)$_POST["DoctorID"] : 0;
    $deptId    = isset($_POST["DepartmentID"]) ? (int)$_POST["DepartmentID"] : 0;
    $slot      = isset($_POST["Slot"]) ? cleanText($_POST["Slot"]) : "";
    $status    = isset($_POST["Status"]) ? cleanText($_POST["Status"]) : "Pending";

    if($id <= 0) { return "Invalid AppointmentID"; }
    if($patientId <= 0 || $doctorId <= 0 || $deptId <= 0) { return "Invalid IDs"; }
    if(strlen($slot) < 10) { return "Invalid Slot"; }
    if(!isValidAppStatus($status)) { return "Invalid Status"; }

    $ok = updateAppointment($id, $patientId, $doctorId, $deptId, $slot, $status);
    if($ok) { header("Location: ../views/admindashboard.php"); exit(); }
    return "Failed to update";
}

function handleAppointmentDelete() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $id = isset($_POST["AppointmentID"]) ? (int)$_POST["AppointmentID"] : 0;
    if($id <= 0) { return "Invalid AppointmentID"; }

    $ok = deleteAppointment($id);
    if($ok) { header("Location: ../views/admindashboard.php"); exit(); }
    return "Failed to delete";
}
