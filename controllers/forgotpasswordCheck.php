<?php
session_start();
require_once('../models/userModel.php');

if(isset($_POST['send_code'])){
    $email = trim($_POST['email']);
    if($email == ""){
        $_SESSION['error'] = "Email cannot be empty.";
        header("Location: ../views/forgotpassword.php");
        exit();
    }

    $code = rand(100000, 999999); // 6-digit code
    if(storeResetCode($email, $code)){
        // SUCCESS
        $_SESSION['TEST_CODE'] = $code; // FOR TESTING: Only store if DB success
        $_SESSION['reset_email'] = $email; // Persist email for the next step
        // Here: send email logic (PHP mail or external service)
        $_SESSION['success'] = "Verification code sent to your email.";
    } else {
        $_SESSION['error'] = "Email not found!";
    }
    header("Location: ../views/forgotpassword.php");
    exit();
}

if(isset($_POST['reset_password'])){
    $email    = trim($_POST['email']);
    $code     = trim($_POST['code']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirmpassword']);

    if($password=="" || $confirm=="" || $code=="" || $email==""){
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../views/forgotpassword.php");
        exit();
    }

    if($password !== $confirm){
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../views/forgotpassword.php");
        exit();
    }

    $resetID = verifyResetCode($email, $code);
    if($resetID){
        if(updatePassword($email, $password) && markCodeUsed($resetID)){
            unset($_SESSION['reset_email']); // Clear the saved email
            header("Location: ../views/login.php?reset=success");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid or expired verification code.";
        header("Location: ../views/forgotpassword.php");
        exit();
    }
}
?>
