
<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once('../models/userModel.php');
require_once('../models/doctorModel.php');

/* Restore username from cookie if needed */
if (!isset($_SESSION['username']) && isset($_COOKIE['username'])) {
    $_SESSION['username'] = $_COOKIE['username'];
}

/* Restore user_id + role from DB if session is missing user_id */
if (isset($_SESSION['username']) && !isset($_SESSION['user_id'])) {
    $u = getUserByUsername($_SESSION['username']);
    if ($u) {
        $_SESSION['user_id'] = $u['UserID'];
        if (!isset($_SESSION['role'])) $_SESSION['role'] = $u['Role'];
        if (!isset($_SESSION['name'])) $_SESSION['name'] = $u['Name'];
    }
}

/* Must be logged in as Doctor */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../views/login.php");
    exit();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

/* Ensure CSRF token */
if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* Flash message */
$flash = '';
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

/* Load data */
$doctorId = (int)$_SESSION['user_id'];
$appointments = getDoctorAppointments($doctorId);
$patients     = getDoctorPatients($doctorId);
