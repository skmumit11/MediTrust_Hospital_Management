
<?php
session_start();
require_once('../models/doctorModel.php');

function ensureDoctor(){
    if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor'){
        header("Location: ../views/login.php");
        exit();
    }
    if(!isset($_SESSION['user_id'])){
        header("Location: ../views/login.php");
        exit();
    }
}

function checkCsrf($token){
    if(!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === ''){
        return false;
    }
    if($token === '') return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

ensureDoctor();

$doctorId = (int)$_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $csrf   = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    if(!checkCsrf($csrf)){
        $_SESSION['flash'] = "Invalid request (CSRF).";
        header("Location: ../views/doctordashboard.php");
        exit();
    }

    if($action === 'update_appointment_purpose'){
        $appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
        $purpose = isset($_POST['purpose']) ? trim($_POST['purpose']) : '';

        if($appointmentId > 0){
            $ok = updateAppointmentPurpose($doctorId, $appointmentId, $purpose);
            $_SESSION['flash'] = $ok ? "Appointment purpose updated." : "Failed to update purpose.";
        } else {
            $_SESSION['flash'] = "Invalid appointment.";
        }

        header("Location: ../views/doctordashboard.php");
        exit();
    }

    if($action === 'update_patient_contact'){
        $patientId = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
        $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';

        if($patientId > 0 && $contact !== ''){
            $ok = updatePatientContact($patientId, $contact);
            $_SESSION['flash'] = $ok ? "Patient contact updated." : "Failed to update contact.";
        } else {
            $_SESSION['flash'] = "Invalid patient/contact.";
        }

        header("Location: ../views/doctordashboard.php");
        exit();
    }

    if($action === 'upload_prescription'){
        $patientId = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
        $medicine  = isset($_POST['medicine']) ? trim($_POST['medicine']) : '';
        $dosage    = isset($_POST['dosage']) ? trim($_POST['dosage']) : '';
        $duration  = isset($_POST['duration']) ? trim($_POST['duration']) : '';
        $notes     = isset($_POST['notes']) ? trim($_POST['notes']) : '';

        if($patientId > 0 && $medicine !== '' && $dosage !== '' && $duration !== '' && $notes !== ''){
            $ok = uploadPrescriptionByDoctor($doctorId, $patientId, $medicine, $dosage, $duration, $notes);
            $_SESSION['flash'] = $ok ? "Prescription uploaded successfully." : "Failed to upload prescription.";
        } else {
            $_SESSION['flash'] = "All prescription fields are required.";
        }

        header("Location: ../views/doctordashboard.php");
        exit();
    }

    $_SESSION['flash'] = "Unknown action.";
    header("Location: ../views/doctordashboard.php");
    exit();
}

header("Location: ../views/doctordashboard.php");
exit();
?>
