<?php
if(!isset($_SESSION)){
    session_start();
}
if(!isset($_SESSION['username']) && !isset($_COOKIE['username'])){
    header("Location: ../views/login.php");
    exit();
}
?>
