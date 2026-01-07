<?php
session_start();
require_once('../models/opdIpdModel11_15.php');

// --- OPD Actions ---

if (isset($_POST['add_opd'])) {
    $patientId = $_POST['patient_id'];
    $doctorId = $_POST['doctor_id'];
    $date = $_POST['visit_date'];
    $status = $_POST['status'];
    $createdBy = $_SESSION['UserID']; // Assuming logged in

    if(addOPD($patientId, $doctorId, $date, $status, $createdBy)){
        $_SESSION['success'] = "OPD Visit added.";
    } else {
        $_SESSION['errors'] = ["Failed to add OPD visit."];
    }
    header("Location: ../views/admin_opd_ipd11_15.php");
    exit();
}

if (isset($_POST['edit_opd'])) {
    $id = $_POST['id'];
    $patientId = $_POST['patient_id'];
    $doctorId = $_POST['doctor_id'];
    $date = $_POST['visit_date'];
    $status = $_POST['status'];

    if(updateOPD($id, $patientId, $doctorId, $date, $status)){
        $_SESSION['success'] = "OPD Visit updated.";
    } else {
        $_SESSION['errors'] = ["Failed to update OPD visit."];
    }
    header("Location: ../views/admin_opd_ipd11_15.php");
    exit();
}

if (isset($_GET['delete_opd'])) {
    $id = $_GET['id'];
    if(deleteOPD($id)){
        $_SESSION['success'] = "OPD Visit deleted.";
    } else {
        $_SESSION['errors'] = ["Failed to delete OPD visit."];
    }
    header("Location: ../views/admin_opd_ipd11_15.php");
    exit();
}

// --- IPD Actions ---

if (isset($_POST['add_ipd'])) {
    $patientId = $_POST['patient_id'];
    $roomNo = $_POST['room_no'];
    $admissionDate = $_POST['admission_date'];
    $dischargeDate = !empty($_POST['discharge_date']) ? $_POST['discharge_date'] : null;
    $status = $_POST['status'];
    $source = $_POST['admission_source'];
    $createdBy = $_SESSION['UserID'];

    if(addIPD($patientId, $roomNo, $admissionDate, $dischargeDate, $status, $source, $createdBy)){
        $_SESSION['success'] = "IPD Admission added.";
    } else {
        $_SESSION['errors'] = ["Failed to add IPD admission."];
    }
    header("Location: ../views/admin_opd_ipd11_15.php");
    exit();
}

if (isset($_POST['edit_ipd'])) {
    $id = $_POST['id'];
    $patientId = $_POST['patient_id'];
    $roomNo = $_POST['room_no'];
    $admissionDate = $_POST['admission_date'];
    $dischargeDate = !empty($_POST['discharge_date']) ? $_POST['discharge_date'] : null;
    $status = $_POST['status'];
    $source = $_POST['admission_source'];

    if(updateIPD($id, $patientId, $roomNo, $admissionDate, $dischargeDate, $status, $source)){
        $_SESSION['success'] = "IPD Admission updated.";
    } else {
        $_SESSION['errors'] = ["Failed to update IPD admission."];
    }
    header("Location: ../views/admin_opd_ipd11_15.php");
    exit();
}

if (isset($_GET['delete_ipd'])) {
    $id = $_GET['id'];
    if(deleteIPD($id)){
        $_SESSION['success'] = "IPD Admission deleted.";
    } else {
        $_SESSION['errors'] = ["Failed to delete IPD admission."];
    }
    header("Location: ../views/admin_opd_ipd11_15.php");
    exit();
}
?>
