<?php
session_start();
require_once('../models/patientModel11_15.php');

// Define errors array
$errors = [];

// Handle Patient Search
if (isset($_POST['search'])) {
    $term = trim($_POST['search_term']);
    if (!empty($term)) {
        $patients = searchPatients($term);
    } else {
        $patients = getAllPatients();
    }
    // Store in session or pass via include? 
    // Procedural MVC often uses include at the end or session flash data.
    // For listing, I'll assume the view calls the controller or the controller loads the view.
    // Given the structure, usually view is accessed directly or via controller?
    // Let's assume view includes controller logic or controller sets variables and includes view.
    // Actually, typical simple PHP MVC: Controller handles POST, redirects. View handles GET display.
    // But for Search, it's often a POST back to same page or GET with params.
    // If I stick to "Controller handles request", then:
    
    $_SESSION['patients'] = $patients;
    header("Location: ../views/nurse_patient_list11_15.php");
    exit();
}

// Handle Update Contact
if (isset($_POST['update_contact'])) {
    $id = $_POST['id'];
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    
    // Validation
    if (empty($contact) || empty($address)) {
        $_SESSION['errors'] = ["Contact and Address are required."];
        header("Location: ../views/nurse_patient_edit11_15.php?id=$id");
        exit();
    }
    
    $status = updatePatientContact($id, $contact, $address);
    if ($status) {
        $_SESSION['success'] = "Patient details updated successfully.";
        header("Location: ../views/nurse_patient_list11_15.php");
    } else {
        $_SESSION['errors'] = ["Failed to update details."];
        header("Location: ../views/nurse_patient_edit11_15.php?id=$id");
    }
    exit();
}

// Default Redirect
if (!isset($_SESSION['patients']) && basename($_SERVER['PHP_SELF']) == 'patientController11_15.php') {
     // If accessed directly, maybe fetch all
     // But usually views call model directly or controller is entry point.
     // In this project style, views seem to be entry points calling controllers for actions.
     // `signup11_15.php` -> `signupCheck.php`.
     // So `nurse_patient_list11_15.php` should likely fetch data itself using Model functions.
     // The Controller is likely for Form Actions (POST).
}

?>
