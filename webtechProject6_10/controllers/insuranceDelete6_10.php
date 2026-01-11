<?php
// controllers/insuranceDelete6_10.php
session_start();
require_once '../models/insuranceModel6_10.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../views/login6_10.php");
    exit();
}

$id = $_REQUEST['id'];

if ($id != "") {
    $status = deleteInsuranceRecord($id);
}

header("Location: ../views/insurance_create6_10.php");
?>