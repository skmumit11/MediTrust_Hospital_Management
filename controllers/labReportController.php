
<?php
// controllers/labReportController.php
session_start();

require_once __DIR__ . '/../models/labModel.php';
require_once __DIR__ . '/../models/auditModel.php';

function requireRole($allowedRoles) {
    if (!isset($_SESSION['user'])) {
        header("Location: ../views/login.php");
        exit();
    }

    $role = $_SESSION['user']['Role'];
    $ok = false;

    foreach ($allowedRoles as $r) {
        if ($role === $r) {
            $ok = true;
            break;
        }
    }

    if (!$ok) {
        echo "Access denied";
        exit();
    }
}

requireRole(['Lab', 'Staff', 'Admin']);

$action = isset($_GET['action']) ? $_GET['action'] : "view";

$dateFilter = isset($_POST['date_filter']) ? trim($_POST['date_filter']) : "";
$testType   = isset($_POST['test_type_filter']) ? trim($_POST['test_type_filter']) : "";
$patientFil = isset($_POST['patient_filter']) ? trim($_POST['patient_filter']) : "";

// allow GET too
if ($dateFilter === "" && isset($_GET['date_filter'])) { $dateFilter = trim($_GET['date_filter']); }
if ($testType   === "" && isset($_GET['test_type_filter'])) { $testType = trim($_GET['test_type_filter']); }
if ($patientFil === "" && isset($_GET['patient_filter'])) { $patientFil = trim($_GET['patient_filter']); }

$rows = filterLabResults($dateFilter, $testType, $patientFil);
$filtersText = "date=" . $dateFilter . "; test_type=" . $testType . "; patient=" . $patientFil;

if ($action === "download") {
    $rid = createLabReportRecord($filtersText, (int)$_SESSION['user']['UserID']);
    addAuditLog((int)$_SESSION['user']['UserID'], "Downloaded Lab Report", "LabReport", $rid, $filtersText);

    $_SESSION['lab_report_rows'] = $rows;
    $_SESSION['lab_report_filters'] = $filtersText;

    header("Location: ../views/lab/downloadReportCsv.php");
    exit();
}

if ($action === "print") {
    $rid = createLabReportRecord($filtersText, (int)$_SESSION['user']['UserID']);
    addAuditLog((int)$_SESSION['user']['UserID'], "Printed Lab Report", "LabReport", $rid, $filtersText);

    $_SESSION['lab_report_rows'] = $rows;
    $_SESSION['lab_report_filters'] = $filtersText;

    header("Location: ../views/lab/printReport.php");
    exit();
}

// view
$_SESSION['lab_report_rows'] = $rows;
$_SESSION['lab_report_filters'] = $filtersText;

header("Location: ../views/lab/reportManagement.php?keep=1");
exit();
