<?php
// controllers/insuranceUpdate6_10.php
session_start();
require_once '../models/insuranceModel6_10.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../views/login6_10.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $pid = $_POST['patient_id'];
    $company = $_POST['insurance_company'];
    $policy = $_POST['policy_id'];
    $vdate = $_POST['validity_date'];

    if ($id == "" || $pid == "" || $company == "") {
        echo "Error: Null value";
    } else {
        $status = updateInsuranceRecord($id, $pid, $company, $policy, $vdate);
        if ($status) {
            header("Location: ../views/insurance_create6_10.php");
        } else {
            echo "Error updating record!";
        }
    }
} else {
    header("Location: ../views/insurance_create6_10.php");
}
?>