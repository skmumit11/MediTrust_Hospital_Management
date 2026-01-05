<?php
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
        exit();
    }
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();
?>
