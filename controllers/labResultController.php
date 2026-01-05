
<?php
// controllers/labResultController.php
session_start();

require_once __DIR__ . '/../models/labModel.php';
require_once __DIR__ . '/../models/patientModel.php';
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

$action = isset($_GET['action']) ? $_GET['action'] : "";

if ($action === "save") {
    $patientId = isset($_POST['patient_id']) ? trim($_POST['patient_id']) : "";
    $testType  = isset($_POST['test_type']) ? trim($_POST['test_type']) : "";
    $resultTxt = isset($_POST['result']) ? trim($_POST['result']) : "";

    $errors = [];

    if ($patientId === "" || !ctype_digit($patientId)) {
        $errors[] = "Patient ID must be numeric.";
    }

    if ($testType === "") {
        $errors[] = "Test Type is required.";
    } else {
        if (strlen($testType) > 100) {
            $errors[] = "Test Type too long (max 100).";
        }
    }

    if ($resultTxt === "") {
        $errors[] = "Result is required.";
    }

    if (count($errors) === 0) {
        $patient = getPatientById((int)$patientId);
        if (!$patient) {
            $errors[] = "Patient not found.";
        }
    }

    if (count($errors) > 0) {
        $_SESSION['lab_errors'] = $errors;
        $_SESSION['lab_old'] = [
            'patient_id' => $patientId,
            'test_type'  => $testType,
            'result'     => $resultTxt
        ];
        header("Location: ../views/lab/testResultEntry.php");
        exit();
    }

    $newId = createLabTestResult((int)$patientId, $testType, $resultTxt);

    if ($newId > 0) {
        $uid = (int)$_SESSION['user']['UserID'];
        addAuditLog($uid, "Created Lab Result", "LabTestResult", $newId, "TestType=" . $testType);

        $_SESSION['lab_success'] = "Saved successfully. Result ID: " . $newId;
        header("Location: ../views/lab/testResultEntry.php?result_id=" . $newId);
        exit();
    }

    $_SESSION['lab_errors'] = ["Failed to save lab result."];
    header("Location: ../views/lab/testResultEntry.php");
    exit();
}

header("Location: ../views/lab/testResultEntry.php");
exit();
