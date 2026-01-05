
<?php
require_once __DIR__ . '/authCheck.php';
require_once __DIR__ . '/../models/staffModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = isset($_GET['action']) ? trim($_GET['action']) : 'list';

/** LIST **/
if ($action === 'list') {
    $staffList = getStaffProfileList();
    include __DIR__ . '/../views/stafflist.php';
    exit();
}

/** ADD (GET) **/
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $departments = getDepartmentList();
    $users = getEligibleStaffUsers();
    include __DIR__ . '/../views/staff_add.php';
    exit();
}

/** ADD (POST) **/
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffId = isset($_POST['staff_id']) ? trim($_POST['staff_id']) : '';
    $deptId  = isset($_POST['department_id']) ? trim($_POST['department_id']) : '';
    $roleAsg = isset($_POST['role_assignment']) ? trim($_POST['role_assignment']) : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';

    $errors = [];

    if ($staffId === '' || !ctype_digit($staffId) || (int)$staffId <= 0) {
        $errors[] = "Select a valid staff user.";
    }
    if ($deptId === '' || !ctype_digit($deptId) || (int)$deptId <= 0) {
        $errors[] = "Select a valid department.";
    }
    if ($roleAsg === '' || strlen($roleAsg) < 2 || strlen($roleAsg) > 100) {
        $errors[] = "Role assignment must be 2-100 characters.";
    }
    // Contact stored as email in User table
    if ($contact === '' || strlen($contact) < 5 || strlen($contact) > 100) {
        $errors[] = "Contact (email) must be 5-100 characters.";
    }

    if (!empty($errors)) {
        $_SESSION['staff_errors'] = $errors;
        $_SESSION['staff_old'] = $_POST;
        header("Location: staffController.php?action=add");
        exit();
    }

    $ok = createStaffProfile((int)$staffId, (int)$deptId, $roleAsg, $contact);

    if ($ok) {
        $_SESSION['staff_success'] = "Staff profile added successfully.";
    } else {
        $_SESSION['staff_errors'] = ["Failed to add staff profile (maybe already exists)."];
    }

    header("Location: staffController.php?action=list");
    exit();
}

/** EDIT (GET) **/
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $staffId = isset($_GET['id']) ? trim($_GET['id']) : '';
    if ($staffId === '' || !ctype_digit($staffId)) {
        header("Location: staffController.php?action=list");
        exit();
    }

    $staff = getStaffProfileById((int)$staffId);
    $departments = getDepartmentList();
    include __DIR__ . '/../views/staff_edit.php';
    exit();
}

/** EDIT (POST) **/
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffId = isset($_POST['staff_id']) ? trim($_POST['staff_id']) : '';
    $deptId  = isset($_POST['department_id']) ? trim($_POST['department_id']) : '';
    $roleAsg = isset($_POST['role_assignment']) ? trim($_POST['role_assignment']) : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';

    $errors = [];

    if ($staffId === '' || !ctype_digit($staffId) || (int)$staffId <= 0) {
        $errors[] = "Invalid staff id.";
    }
    if ($deptId === '' || !ctype_digit($deptId) || (int)$deptId <= 0) {
        $errors[] = "Select a valid department.";
    }
    if ($roleAsg === '' || strlen($roleAsg) < 2 || strlen($roleAsg) > 100) {
        $errors[] = "Role assignment must be 2-100 characters.";
    }
    if ($contact === '' || strlen($contact) < 5 || strlen($contact) > 100) {
        $errors[] = "Contact (email) must be 5-100 characters.";
    }

    if (!empty($errors)) {
        $_SESSION['staff_errors'] = $errors;
        header("Location: staffController.php?action=edit&id=" . urlencode($staffId));
        exit();
    }

    $ok = updateStaffProfile((int)$staffId, (int)$deptId, $roleAsg, $contact);

    if ($ok) {
        $_SESSION['staff_success'] = "Staff profile updated successfully.";
    } else {
        $_SESSION['staff_errors'] = ["Failed to update staff profile."];
    }

    header("Location: staffController.php?action=list");
    exit();
}

/** DELETE (POST) **/
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffId = isset($_POST['staff_id']) ? trim($_POST['staff_id']) : '';

    if ($staffId !== '' && ctype_digit($staffId)) {
        $ok = deleteStaffProfile((int)$staffId);
        if ($ok) {
            $_SESSION['staff_success'] = "Staff profile deleted successfully.";
        } else {
            $_SESSION['staff_errors'] = ["Failed to delete staff profile."];
        }
    }

    header("Location: staffController.php?action=list");
    exit();
}

header("Location: staffController.php?action=list");
exit();
