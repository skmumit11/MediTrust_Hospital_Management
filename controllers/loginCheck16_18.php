<?php
require_once('../models/userModel16_18.php');
require_once('../models/db16_18.php');

function loginCheck()
{
    session_start();

    // Fix for GET redirect loop: if already logged in from 1_5, go to dashboard
    // Check both Role and role because 1_5 sets 'role' (lowercase)
    $role = $_SESSION['Role'] ?? $_SESSION['role'] ?? null;
    if ($role === 'Admin16_18' || $role === 'Admin16_20') {
        header('Location: ../controllers/dashboardController16_18.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Use model function instead of helpers
        $user = user_get_by_username($username);

        if ($user && $password === $user['Password']) {
            // For production: use password_hash / password_verify
            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['Role'] = $user['Role'];
            header('Location: ../controllers/dashboardController16_18.php');
            exit;
        } else {
            $error = "Invalid username or password";
            require('../../webtechProject1_5/views/login.php');
        }
    } else {
        require('../../webtechProject1_5/views/login.php');
    }
}
loginCheck();
