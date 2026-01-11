<?php
session_start();
require_once('../models/pharmacyModel11_15.php');

if (isset($_POST['add_medicine'])) {
    $name = trim($_POST['name']);
    $batch_no = trim($_POST['batch_no']);
    $expiry_date = trim($_POST['expiry_date']);
    $quantity = trim($_POST['quantity']);
    $price = trim($_POST['price']);
    
    $errors = [];
    
    if (empty($name) || empty($batch_no) || empty($expiry_date) || empty($quantity) || empty($price)) {
        $errors[] = "All fields are required.";
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../views/pharmacist_inventory11_15.php");
        exit();
    }
    
    if (addMedicine($name, $batch_no, $expiry_date, $quantity, $price)) {
        $_SESSION['success'] = "Medicine added successfully.";
    } else {
        $_SESSION['errors'] = ["Failed to add medicine."];
    }
    
    header("Location: ../views/pharmacist_inventory11_15.php");
    exit();
} else {
    header("Location: ../views/pharmacist_inventory11_15.php");
    exit();
}
?>
