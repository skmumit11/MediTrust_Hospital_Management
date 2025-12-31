<?php
<<<<<<< HEAD
session_start();
require_once('../models/userModel.php');

// Clear previous errors
unset($_SESSION['errors']);

if(isset($_POST['submit'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Initialize error array
    $errors = [];

    // Field validation
    if($username == ""){
        $errors['username'] = "Username/Email is required.";
    }
    if($password == ""){
        $errors['password'] = "Password is required.";
    }

    // If there are errors, redirect back to login page
    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
        header("Location: ../views/login.php");
        exit();
    }

    // Attempt login
    $userArray = ['username'=>$username, 'password'=>$password];
    $user = login($userArray);

    if($user){
        $_SESSION['username'] = $user['Username'];
        $_SESSION['role']     = $user['Role'];

        if(isset($_POST['remember_me'])){
            setcookie('username', $user['Username'], time() + (86400*2), '/'); // 2 days
        }

        // Redirect based on role
        if($user['Role'] == "Admin"){
            header("Location: ../views/admin.php");
        } elseif($user['Role'] == "Doctor"){
            header("Location: ../views/doctordashboard.php");
        } else {
            header("Location: ../views/patientdashboard.php");
        }
        exit();
    } else {
        // Invalid login
        $_SESSION['errors'] = ['login' => 'Invalid username/email or password.'];
        header("Location: ../views/login.php");
        exit();
    }

} else {
    header("Location: ../views/login.php");
    exit();
}
?>
=======
    session_start();
    if(isset($_POST['submit'])){
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];

        if($username == "null" || $password == ""){
            echo "null value!";
        }else{

            //if($username == $_SESSION['user']['username'] && $password == $_SESSION['user']['password']){
              if($username==$password){  
                setcookie('status', 'true', time()+3000, '/');
                //$_SESSION['status'] = true;
                $_SESSION['username'] = $username;

                header('location: ../views/patientdashboard.php');
            }else{
                echo "invalid user!";
            }
        }
    }else{
        header('location: ../views/login.php');
    }
?>
>>>>>>> 398e55f6f2dbf4b37aaf57e9117711dbcccecfcd
