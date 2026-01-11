<?php
// controllers/staffController6_10.php
session_start();
require_once 'authCheck6_10.php';
require_once '../models/staffModel6_10.php';

// Check login
if (!isset($_SESSION['username'])) {
    header("Location: ../views/login6_10.php");
    exit();
}

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = "";
}

if ($action == 'add') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $staffId = $_REQUEST['staff_id'];
        $departmentId = $_REQUEST['department_id'];
        $role = $_REQUEST['role_assignment'];
        $email = $_REQUEST['contact'];

        if ($staffId == "" || $departmentId == "" || $role == "" || $email == "") {
            $_SESSION['staff_errors'] = ["null value!"];
            header("Location: ../views/staff_add6_10.php");
        } else {
            // Simple validation without regex
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['staff_errors'] = ["invalid email!"];
                header("Location: ../views/staff_add6_10.php");
            } else {
                $status = createStaffProfile($staffId, $departmentId, $role, $email);
                if ($status === true) {
                    $_SESSION['staff_success'] = "success";
                    header("Location: ../views/stafflist6_10.php");
                } else {
                    if ($status == "DUPLICATE_ID") {
                        $_SESSION['staff_errors'] = ["duplicate id!"];
                    } else {
                        $_SESSION['staff_errors'] = ["error!"];
                    }
                    header("Location: ../views/staff_add6_10.php");
                }
            }
        }
    } else {
        header("Location: ../views/staff_add6_10.php");
    }

} elseif ($action == 'edit') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $staffId = $_REQUEST['staff_id'];
        $departmentId = $_REQUEST['department_id'];
        $role = $_REQUEST['role_assignment'];
        $email = $_REQUEST['contact'];

        if ($staffId == "") {
            header("Location: ../views/stafflist6_10.php");
        } else {
            $status = updateStaffProfile($staffId, $departmentId, $role, $email);
            if ($status) {
                header("Location: ../views/stafflist6_10.php");
            } else {
                echo "error";
            }
        }
    } else {
        $id = $_REQUEST['id'];
        if ($id == "") {
            header("Location: ../views/stafflist6_10.php");
        } else {
            $staffProfile = getStaffProfileById($id);
            $departments = getDepartmentList();
            include '../views/staff_edit6_10.php';
        }
    }

} elseif ($action == 'delete') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $staffId = $_REQUEST['staff_id'];
        if (deleteStaffProfile($staffId)) {
            header("Location: ../views/stafflist6_10.php");
        } else {
            echo "error";
        }
    }

} else {
    header("Location: ../views/stafflist6_10.php");
}
?>