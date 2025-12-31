<?php
session_start();
require_once('../models/userModel.php');

if(isset($_POST['submit'])){
    $name     = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $dob      = $_POST['dob'];
    $gender   = $_POST['gender'];
    $address  = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirmpassword'];

    // Validation (can also be done via JS)
    if($name=="" || $username=="" || $email=="" || $password=="" || $confirm=="" || $gender==""){
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../views/signup.php");
        exit();
    }

    if($password !== $confirm){
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../views/signup.php");
        exit();
    }

    $userArray = [
        'name' => $name,
        'username' => $username,
        'email' => $email,
        'dob' => $dob,
        'gender' => $gender,
        'address' => $address,
        'password' => $password
    ];

    if(addUser($userArray)){
        header("Location: ../views/login.php?signup=success");
        exit();
    } else {
        $_SESSION['error'] = "Username or Email already exists!";
        header("Location: ../views/signup.php");
        exit();
    }
} else {
    header("Location: ../views/signup.php");
    exit();
}
?>
