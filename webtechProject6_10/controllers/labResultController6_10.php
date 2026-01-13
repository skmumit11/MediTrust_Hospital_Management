<?php
// controllers/labResultController6_10.php
session_start();
require_once '../models/labModel6_10.php';
require_once '../models/patientModel6_10.php';
require_once '../models/auditModel6_10.php';

// Simple Auth Check
if (!isset($_SESSION['username'])) {
    header("Location: ../views/login6_10.php");
    exit();
}

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = "";
}

if ($action === "save") {
    $patientId = $_REQUEST['patient_id'];
    $testType = $_REQUEST['test_type'];
    $resultTxt = $_REQUEST['result'];

    if ($patientId == "" || $testType == "" || $resultTxt == "") {
        $_SESSION['lab_errors'] = ["null value!"];
        header("Location: ../views/testResultEntry6_10.php");
    } else {
        // Simple numeric check using casting
        if (!is_numeric($patientId)) {
            $_SESSION['lab_errors'] = ["invalid id!"];
            header("Location: ../views/testResultEntry6_10.php");
        } else {
            $newId = createLabTestResult($patientId, $testType, $resultTxt);
            if ($newId) {
                $_SESSION['lab_success'] = "success";
                header("Location: ../views/testResultEntry6_10.php");
            } else {
                $_SESSION['lab_errors'] = ["error!"];
                header("Location: ../views/testResultEntry6_10.php");
            }
        }
    }
} else {
    header("Location: ../views/testResultEntry6_10.php");
}
?>