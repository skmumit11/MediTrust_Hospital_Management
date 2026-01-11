<?php
session_start();
require_once('../models/userModel.php');

unset($_SESSION['errors']);

if(!isset($_POST['submit'])){
    header('Location: ../views/login.php');
    exit;
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

$errors = [];

if($username === ''){
    $errors[] = "Username/Email is required.";
}

if($password === ''){
    $errors[] = "Password is required.";
}

if(!empty($errors)){
    $_SESSION['errors'] = $errors;
    $_SESSION['old_username'] = $username;
    header('Location: ../views/login.php');
    exit;
}

$user = login([
    'username' => $username,
    'password' => $password
]);

if($user === false){
    $_SESSION['errors'] = ["Invalid username/email or password (or account inactive)."];
    $_SESSION['old_username'] = $username;
    header('Location: ../views/login.php');
    exit;
}

/* Remember Me cookie */
if(isset($_POST['remember_me'])){
    setcookie('remember_identity', $username, time() + (30 * 24 * 60 * 60), '/');
} else {
    if(isset($_COOKIE['remember_identity'])){
        setcookie('remember_identity', '', time() - 3600, '/');
    }
}

/* Create session */
$_SESSION['UserID'] = $user['UserID'];
$_SESSION['username'] = $user['Username'];
$_SESSION['name'] = $user['Name'];
$_SESSION['role'] = $user['Role'];
$_SESSION['user'] = $user; // Project 6-10 requires the full user array

/* Optional: store status */
if(isset($user['Status'])){
    $_SESSION['status'] = $user['Status'];
}

/* Redirect by role (safe fallback to home.php) */
$role = isset($user['Role']) ? $user['Role'] : '';


if($role === 'Doctor'){
    header('Location: ../views/doctorDashboard.php');
    exit;
}

if($role === 'Admin1_5'){
    header('Location: ../views/admindashboard.php');
    exit;
}
if($role === 'Admin6_10'){
    header('Location: ../../webtechProject6_10/views/admindashboard6_10.php');
    exit;
}
if($role === 'Admin11_15'){
    header('Location: ../../webtechProject11_15/views/admin_doctor_schedule11_15.php');
    exit;
}
if($role === 'Admin16_18'){
    header('Location: ../../webtechProject16_20/controllers/loginCheck16_18.php');
    exit;
}

if($role === 'Patient'){
    header('Location: ../views/patientdashboard.php');
    exit;
}

if($role === 'ComplianceOfficer'){
    header('Location: ../views/compliance_dashboard.php');
    exit;
}

/* Default fallback */
header('Location: ../views/home.php');
exit;
?>