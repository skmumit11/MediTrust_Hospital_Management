<?php
// controllers/logoutCheck.php
session_start();
session_unset();
session_destroy();
setcookie('username', '', time() - 3600, '/');
header("Location: ../../webtechProject1_5/views/login.php");
exit();
?>