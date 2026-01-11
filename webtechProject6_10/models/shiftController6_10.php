
<?php
require_once '../controllers/authCheck.php';
require_once 'shiftModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $staffId   = isset($_POST['staff_id']) ? trim($_POST['staff_id']) : '';
    $shiftType = isset($_POST['shift_type']) ? trim($_POST['shift_type']) : '';
    $start     = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
    $end       = isset($_POST['end_time']) ? trim($_POST['end_time']) : '';

    $errors = [];

    if ($staffId === '' || !ctype_digit($staffId) || (int)$staffId <= 0) {
        $errors[] = "Select a valid staff.";
    }
    if ($shiftType === '' || strlen($shiftType) < 2 || strlen($shiftType) > 50) {
        $errors[] = "Select a valid shift type.";
    }

    // datetime-local: Y-m-d\TH:i  (no regex used)
    $startDT = DateTime::createFromFormat('Y-m-d\TH:i', $start);
    $endDT   = DateTime::createFromFormat('Y-m-d\TH:i', $end);

    if (!($startDT && $startDT->format('Y-m-d\TH:i') === $start)) {
        $errors[] = "Start time is invalid.";
    }
    if (!($endDT && $endDT->format('Y-m-d\TH:i') === $end)) {
        $errors[] = "End time is invalid.";
    }

    if (empty($errors)) {
        if ($endDT <= $startDT) {
            $errors[] = "End time must be after start time.";
        } else {
            $startSQL = $startDT->format('Y-m-d H:i:s');
            $endSQL   = $endDT->format('Y-m-d H:i:s');

            $ok = createShiftAssignment((int)$staffId, $shiftType, $startSQL, $endSQL);

            if ($ok) {
                // Create a simple notification record
                createShiftNotification((int)$staffId);

                $_SESSION['shift_success'] = "Shift assigned successfully (notification created).";
                header("Location: shiftController.php");
                exit();
            } else {
                $errors[] = "Failed to assign shift.";
            }
        }
    }

    $_SESSION['shift_errors'] = $errors;
    $_SESSION['shift_old'] = $_POST;
    header("Location: shiftController.php");
    exit();

} else {
    $staffList = getStaffSelectorList();
    $recentShifts = getRecentShifts(20);
    include '../views/shift_scheduler.php';
    exit();
}
