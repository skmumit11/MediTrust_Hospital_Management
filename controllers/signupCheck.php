
<?php
session_start();
require_once('../models/userModel.php');

unset($_SESSION['errors']);

if(!isset($_POST['submit'])){
    header("Location: ../views/signup.php");
    exit();
}

$name     = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
$dob      = isset($_POST['dob']) ? trim($_POST['dob']) : '';
$gender   = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$address  = isset($_POST['address']) ? trim($_POST['address']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm  = isset($_POST['confirmpassword']) ? $_POST['confirmpassword'] : '';

$errors = [];

/* Preserve old input */
$_SESSION['old'] = [
    'fullname' => $name,
    'username' => $username,
    'email'    => $email,
    'dob'      => $dob,
    'gender'   => $gender,
    'address'  => $address
];

/* Validation (no regex) */
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
else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $errors[] = "Invalid email format.";
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

/* Duplicate check */
if(userExists($username, $email)){
    $_SESSION['errors'] = ["Username or Email already exists!"];
    header("Location: ../views/signup.php");
    exit();
}

$userArray = [
    'name'     => $name,
    'username' => $username,
    'email'    => $email,
    'dob'      => $dob,
    'gender'   => $gender,
    'address'  => $address,
    'password' => $password // plain text for testing
];

/* Insert */
if(addUser($userArray)){
    unset($_SESSION['old']);
    header("Location: ../views/login.php?signup=success");
    exit();
} else {
    $_SESSION['errors'] = ["Signup failed. Please try again."];
    header("Location: ../views/signup.php");
    exit();
}
