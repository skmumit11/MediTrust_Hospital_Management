<?php
session_start();
require_once('../models/doctorModel11_15.php');

if (isset($_POST['assign_duty'])) {
    $id = $_POST['id'];
    $department = trim($_POST['department']);
    $duty_hours = trim($_POST['duty_hours']); // e.g., "9:00 AM - 5:00 PM"
    
    if (empty($department) || empty($duty_hours)) {
        $_SESSION['errors'] = ["Department and Duty Hours are required."];
    } else {
        if (updateDoctorDuty($id, $department, $duty_hours)) {
            $_SESSION['success'] = "Doctor duty updated successfully.";
        } else {
            $_SESSION['errors'] = ["Failed to update duty."];
        }
    }
    
    header("Location: ../views/admin_doctor_schedule11_15.php");
    exit();
} else {
    header("Location: ../views/admin_doctor_schedule11_15.php");
    exit();
}
?>
