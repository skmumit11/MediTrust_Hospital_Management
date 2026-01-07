<?php
//session_start();

// Redirect if neither session nor cookie exists
if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header('Location: ../views/login11_15.php');
    exit();
}

// Restore session from cookie if needed
if (!isset($_SESSION['username']) && isset($_COOKIE['username'])) {
    $_SESSION['username'] = $_COOKIE['username'];
}
?>
