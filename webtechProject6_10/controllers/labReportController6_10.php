<?php
// controllers/labReportController6_10.php
session_start();
require_once '../models/labModel6_10.php';
require_once '../models/auditModel6_10.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../views/login6_10.php");
    exit();
}

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = "";
}

$dateFilter = "";
if (isset($_POST['date_filter']))
    $dateFilter = $_POST['date_filter'];

$testType = "";
if (isset($_POST['test_type_filter']))
    $testType = $_POST['test_type_filter'];

$patientFil = "";
if (isset($_POST['patient_filter']))
    $patientFil = $_POST['patient_filter'];


if ($action === "download") {
    // Basic CSV logic
    $rows = filterLabResults($dateFilter, $testType, $patientFil);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="lab_report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ResultID', 'PatientID', 'PatientName', 'TestType', 'Result', 'TestDate', 'Status'));
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// Default View
$rows = filterLabResults($dateFilter, $testType, $patientFil);
$_SESSION['lab_report_rows'] = $rows;
header("Location: ../views/reportManagement6_10.php");
?>
