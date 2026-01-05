<?php
// controllers/insuranceCreate.php

require_once __DIR__ . '/authCheck.php';
require_once __DIR__ . '/../models/insuranceModel.php';

// authCheck.php starts session, but this is safe if already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $patientId = isset($_POST['patient_id']) ? trim($_POST['patient_id']) : '';
    $company = isset($_POST['insurance_company']) ? trim($_POST['insurance_company']) : '';
    $policyId = isset($_POST['policy_id']) ? trim($_POST['policy_id']) : '';
    $validity = isset($_POST['validity_date']) ? trim($_POST['validity_date']) : '';

    $errors = [];

    // Validation (no regex/preg_match)
    if ($patientId === '' || !ctype_digit($patientId) || (int) $patientId <= 0) {
        $errors[] = "Please select a valid patient.";
    }

    if ($company === '' || strlen($company) < 2 || strlen($company) > 100) {
        $errors[] = "Insurance company must be 2-100 characters.";
    }

    if ($policyId === '' || strlen($policyId) < 2 || strlen($policyId) > 50) {
        $errors[] = "Policy ID must be 2-50 characters.";
    }

    // Date validation (YYYY-MM-DD)
    $dt = DateTime::createFromFormat('Y-m-d', $validity);
    $isValidDate = ($dt && $dt->format('Y-m-d') === $validity);

    if (!$isValidDate) {
        $errors[] = "Validity date must be a valid date (YYYY-MM-DD).";
    }

    // If errors, redirect back with flash data
    if (count($errors) > 0) {
        $_SESSION['insurance_errors'] = $errors;
        $_SESSION['insurance_old'] = [
            'patient_id' => $patientId,
            'insurance_company' => $company,
            'policy_id' => $policyId,
            'validity_date' => $validity
        ];
        header("Location: ../views/insurance_create.php");
        exit();
    }

    $ok = createInsuranceRecord((int) $patientId, $company, $policyId, $validity);

    if ($ok) {
        $_SESSION['insurance_success'] = "Insurance record saved successfully.";
    } else {
        $_SESSION['insurance_errors'] = ["Failed to save insurance record. Please try again."];
    }

    header("Location: insuranceCreate.php");
    exit();

} else {
    // GET request: load view
    $patients = getPatientList();
    $records = getAllInsuranceRecords(); // Fetch recent records
    include __DIR__ . '/../views/insurance_create.php';
}
