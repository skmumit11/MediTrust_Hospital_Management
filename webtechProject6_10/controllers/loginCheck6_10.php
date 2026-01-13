<?php
session_start();
require_once('../models/userModel6_10.php');

// Clear previous errors
unset($_SESSION['errors']);

if (isset($_POST['submit'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $errors = [];

    // If all fields empty
    if ($username == '' && $password == '') {
        $errors[] = "All fields are required.";
    } else if ($username == '') {
        $errors[] = "Username/Email is required.";
    } else if ($password == '') {
        $errors[] = "Password is required.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../views/login6_10.php");
        exit();
    }

    $userArray = ['username' => $username, 'password' => $password];

    // 🔴 Uncomment below line to enable hash verification
    // $userArray['password'] = $password; // keep plain for testing
    $user = login($userArray);

    if ($user) {
        // Fix: Store full user array in 'user' key for Lab Controller compatibility
        $_SESSION['user'] = $user;
        $_SESSION['username'] = $user['Username'];
        $_SESSION['role'] = $user['Role'];

        if (isset($_POST['remember_me'])) {
            setcookie('username', $user['Username'], time() + (86400 * 2), '/'); // 2 days
        }

        header("Location: ../views/admindashboard6_10.php");
        exit();
    } else {
        $_SESSION['errors'] = ['Invalid username/email or password.'];
        header("Location: ../views/login6_10.php");
        exit();
    }

} else {
    header("Location: ../views/login6_10.php");
    exit();
}
?>