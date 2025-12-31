<?php
<<<<<<< HEAD
$inactive_limit = 3600;

// Check if last activity time exists
if (isset($_SESSION['last_activity'])) {

    $inactive_time = time() - $_SESSION['last_activity'];

    if ($inactive_time > $inactive_limit) {
        // Destroy session
        session_unset();
        session_destroy();

        // Remove remember-me cookie
        if (isset($_COOKIE['username'])) {
            setcookie("username", "", time() - 3600, "/");
        }

        // Redirect after logout
        header("Location: ../views/home.php");
=======
session_start();

// Define inactivity time limit (1 hour = 3600 seconds)
$inactive_limit = 3600; // 1 hour

// Check if user is logged in
if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];

    if ($inactive_time > $inactive_limit) {
        // User inactive for too long, destroy session and cookie
        session_unset();
        session_destroy();

        if (isset($_COOKIE['username'])) {
            setcookie("username", '', time() - 3600, "/");
        }

        // Redirect to home page
        header("Location: home.php");
>>>>>>> 398e55f6f2dbf4b37aaf57e9117711dbcccecfcd
        exit();
    }
}

<<<<<<< HEAD
// Update last activity timestamp
=======
// Update last activity time
>>>>>>> 398e55f6f2dbf4b37aaf57e9117711dbcccecfcd
$_SESSION['last_activity'] = time();
?>
