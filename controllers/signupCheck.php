<?php
session_start();
require_once('../models/userModel.php');

if(isset($_POST['submit'])){
    $name     = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $dob      = $_POST['dob'];
    $gender   = $_POST['gender'] ?? '';
    $address  = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirmpassword'];

    $errors = [];

    // Preserve old input
    $_SESSION['old'] = [
        'fullname' => $name,
        'username' => $username,
        'email' => $email,
        'dob' => $dob,
        'gender' => $gender,
        'address' => $address
    ];

    // Check if ALL fields are empty
    if($name=='' && $username=='' && $email=='' && $dob=='' && $gender=='' && $address=='' && $password=='' && $confirm==''){
        $errors[] = "All fields are required.";
    } 
    else if($name == ''){
        $errors[] = "Full Name is required.";
    } 
    else if($username == ''){
        $errors[] = "Username is required.";
    } 
    else if($email == ''){
        $errors[] = "Email is required.";
    } 
    else if($dob == ''){
        $errors[] = "Date of Birth is required.";
    } 
    else if($gender == ''){
        $errors[] = "Gender is required.";
    } 
    else if($address == ''){
        $errors[] = "Address is required.";
    } 
    else if($password == ''){
        $errors[] = "Password is required.";
    } 
    else if($confirm == ''){
        $errors[] = "Confirm Password is required.";
    } 
    else if($password !== $confirm){
        $errors[] = "Passwords do not match.";
    }

    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
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
        'password' => $password // plain text for testing
    ];

    // 🔴 Uncomment below line to enable hashing for signup
    // $userArray['password'] = password_hash($password, PASSWORD_DEFAULT);

    if(addUser($userArray)){
        unset($_SESSION['old']);
        header("Location: ../views/login.php?signup=success");
        exit();
    } else {
        $_SESSION['errors'] = ["Username or Email already exists!"];
        header("Location: ../views/signup.php");
        exit();
    }

} else {
    header("Location: ../views/signup.php");
    exit();
}
?>
