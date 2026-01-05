<?php
// controllers/staffController.php
session_start();
require_once __DIR__ . '/authCheck.php';
require_once __DIR__ . '/../models/staffModel.php';

// Check login
if (!isset($_SESSION['user'])) {
    header("Location: ../views/login.php");
    exit();
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// Handle Actions
switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleCreate();
        } else {
            // If accessed via GET, redirect to view
            header("Location: ../views/staff_add.php");
        }
        break;

    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleUpdate();
        } else {
            // List for GET: Show Edit Form
            showEditForm();
        }
        break;

    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleDelete();
        }
        break;

    default:
        // Default to list
        header("Location: ../views/stafflist.php");
        break;
}

function handleCreate()
{
    $staffId = isset($_POST['staff_id']) ? $_POST['staff_id'] : '';
    $departmentId = isset($_POST['department_id']) ? $_POST['department_id'] : '';
    $role = isset($_POST['role_assignment']) ? trim($_POST['role_assignment']) : '';
    $email = isset($_POST['contact']) ? trim($_POST['contact']) : '';

    $errors = [];
    if (!$staffId || !$departmentId || !$role || !$email) {
        $errors[] = "All fields are required.";
    }

    if (!empty($errors)) {
        $_SESSION['staff_errors'] = $errors;
        $_SESSION['staff_old'] = $_POST;
        header("Location: ../views/staff_add.php");
        exit();
    }

    $ok = createStaffProfile((int) $staffId, (int) $departmentId, $role, $email);

    if ($ok) {
        $_SESSION['staff_success'] = "Staff profile created successfully.";
        header("Location: ../views/stafflist.php");
    } else {
        $_SESSION['staff_errors'] = ["Database error. User might already be assigned or ID invalid."];
        header("Location: ../views/staff_add.php");
    }
    exit();
}

function handleUpdate()
{
    $staffId = isset($_POST['staff_id']) ? $_POST['staff_id'] : '';
    $departmentId = isset($_POST['department_id']) ? $_POST['department_id'] : '';
    $role = isset($_POST['role_assignment']) ? trim($_POST['role_assignment']) : '';
    $email = isset($_POST['contact']) ? trim($_POST['contact']) : '';

    // validation...
    if (!$staffId) {
        echo "Invalid ID";
        exit();
    }

    $ok = updateStaffProfile((int) $staffId, (int) $departmentId, $role, $email);

    if ($ok) {
        $_SESSION['staff_success'] = "Staff profile updated.";
        header("Location: ../views/stafflist.php");
    } else {
        $_SESSION['staff_errors'] = ["Update failed."];
        header("Location: ../controllers/staffController.php?action=edit&id=" . $staffId);
    }
    exit();
}

function handleDelete()
{
    $staffId = isset($_POST['staff_id']) ? $_POST['staff_id'] : '';
    if ($staffId) {
        deleteStaffProfile((int) $staffId);
        $_SESSION['staff_success'] = "Staff profile deleted.";
    }
    header("Location: ../views/stafflist.php");
    exit();
}

function showEditForm()
{
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    if (!$id) {
        header("Location: ../views/stafflist.php");
        exit();
    }

    $staffProfile = getStaffProfileById((int) $id);
    if (!$staffProfile) {
        $_SESSION['staff_errors'] = ["Staff not found."];
        header("Location: ../views/stafflist.php");
        exit();
    }

    // Load deps for dropdown
    $departments = getDepartmentList();
    // For editing, we might not need user list if we can't change the USER itself, only role/dept/contact.
    // Assuming staffID is fixed once created.

    // Pass to view
    include __DIR__ . '/../views/staff_edit.php';
}
