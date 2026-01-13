<?php
// controllers/insuranceCreate6_10.php
session_start();
require_once '../models/insuranceModel6_10.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = $_POST['patient_id'];
    $company = $_POST['insurance_company'];
    $policy = $_POST['policy_id'];
    $vdate = $_POST['validity_date'];

    if ($pid == "" || $company == "") {
        echo "Error: Null value";
    } else {
        $status = createInsuranceRecord($pid, $company, $policy, $vdate);
        if ($status) {
            header("Location: ../views/insurance_create6_10.php");
        } else {
            echo "Error!";
        }
    }
} else {
    header("Location: ../views/insurance_create6_10.php");
}
?>