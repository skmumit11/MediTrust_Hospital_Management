<?php
// controllers/insuranceUpdate.php
session_start();
require_once __DIR__ . '/../models/insuranceModel.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $patientId = isset($_POST['patient_id']) ? trim($_POST['patient_id']) : '';
    $company = isset($_POST['insurance_company']) ? trim($_POST['insurance_company']) : '';
    $policyId = isset($_POST['policy_id']) ? trim($_POST['policy_id']) : '';
    $validity = isset($_POST['validity_date']) ? trim($_POST['validity_date']) : '';

    $errors = [];

    if ($id === '' || !ctype_digit($id)) {
        $errors[] = "Invalid ID.";
    }
    if ($patientId === '' || !ctype_digit($patientId)) {
        $errors[] = "Invalid Patient.";
    }
    if ($company === '') {
        $errors[] = "Company Name required.";
    }

    // Simple date check
    $dt = DateTime::createFromFormat('Y-m-d', $validity);
    if (!$dt || $dt->format('Y-m-d') !== $validity) {
        $errors[] = "Invalid Date.";
    }

    if (count($errors) > 0) {
        $_SESSION['insurance_edit_errors'] = $errors;
        header("Location: ../views/insurance_edit.php?id=" . $id);
        exit();
    }

    $ok = updateInsuranceRecord((int) $id, (int) $patientId, $company, $policyId, $validity);

    if ($ok) {
        $_SESSION['insurance_success'] = "Record updated successfully.";
        header("Location: ../views/insurance_create.php");
        exit();
    } else {
        $_SESSION['insurance_edit_errors'] = ["Database error during update."];
        header("Location: ../views/insurance_edit.php?id=" . $id);
        exit();
    }

} else {
    // Redirect if accessed via GET without ID (should be handled by view)
    header("Location: ../views/insurance_create.php");
    exit();
}
