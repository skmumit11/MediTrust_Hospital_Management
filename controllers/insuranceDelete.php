<?php
// controllers/insuranceDelete.php
session_start();
require_once __DIR__ . '/../models/insuranceModel.php';

// Ensure user is logged in (add stricter role check if needed)
if (!isset($_SESSION['user'])) {
    header("Location: ../views/login.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id === '' || !ctype_digit($id)) {
    $_SESSION['insurance_errors'] = ["Invalid Record ID."];
} else {
    $ok = deleteInsuranceRecord((int) $id);
    if ($ok) {
        $_SESSION['insurance_success'] = "Record deleted successfully.";
    } else {
        $_SESSION['insurance_errors'] = ["Failed to delete record."];
    }
}

header("Location: ../views/insurance_create.php");
exit();
