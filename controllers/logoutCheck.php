
<?php
session_start();

/* Destroy all session data */
session_unset();
session_destroy();

/* Remove remember-me cookie (your login uses remember_identity) */
if (isset($_COOKIE['remember_identity'])) {
    setcookie("remember_identity", '', time() - 3600, "/");
}

/* Remove username cookie if you used it somewhere */
if (isset($_COOKIE['username'])) {
    setcookie("username", '', time() - 3600, "/");
}

/* Optional: remove PHP session cookie */
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, "/");
}

/* Redirect to login page */
header("Location: ../views/login.php");
exit();


?>