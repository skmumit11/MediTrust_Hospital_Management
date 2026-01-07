<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: ../views/login16_18.php");
    exit;
}
require_once('../models/dashboardModel16_18.php');

$kpis = kpi_counts();
include('../views/dashboard16_18.php');
