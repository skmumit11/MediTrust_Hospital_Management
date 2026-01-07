<?php
session_start();
require_once('../models/appointmentModel11_15.php');
require_once('../models/patientModel11_15.php'); 
// Need to check if patient ID exists

if (isset($_POST['book_appointment'])) {
    $patient_id = trim($_POST['patient_id']);
    $doctor_id = trim($_POST['doctor_id']); // Updated var name
    $department = trim($_POST['department']);
    $date = trim($_POST['date']);
    $time = trim($_POST['time']);
    // $reason = trim($_POST['reason']); // Reason not supported in DB
    
    $errors = [];
    
    // Validation
    if (empty($patient_id) || empty($doctor_id) || empty($department) || empty($date) || empty($time)) {
        $errors[] = "All fields are required.";
    }
    
    // Validate Patient ID
    $patient = getPatientById($patient_id);
    if (!$patient) {
        $errors[] = "Invalid Patient ID.";
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../views/receptionist_appointment11_15.php");
        exit();
    }
    
    $status = createAppointment($patient_id, $doctor_id, $department, $date, $time); // Removed reason
    
    if ($status) {
        $_SESSION['success'] = "Appointment booked successfully!";
    } else {
        $_SESSION['errors'] = ["Failed to book appointment. Please try again."];
    }
    
    header("Location: ../views/receptionist_appointment11_15.php");
    exit();
} else {
    header("Location: ../views/receptionist_appointment11_15.php");
    exit();
}
?>
