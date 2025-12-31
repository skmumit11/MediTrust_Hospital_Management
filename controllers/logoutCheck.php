<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Remove cookie if exists
if (isset($_COOKIE['username'])) {
    setcookie("username", '', time() - 3600, "/");
}

// Redirect to login page
header("Location: ../views/login.php");
exit();
