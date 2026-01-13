<?php
session_start();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/'); // Optional: Clear session cookie
header("Location: ../../webtrchProject1_5/views/login.php");
exit();
?>