<?php
// models/dashboardModel.php
require_once 'db6_10.php';
require_once 'billingModel6_10.php'; // Ensure Bill table schema is updated

function getDashboardStats()
{
    $con = getConnection();
    $stats = [
        'total_patients' => 0,
        'today_revenue' => 0.00,
        'total_tests' => 0
    ];

    // 1. Total Patients
    $res1 = mysqli_query($con, "SELECT COUNT(*) as cnt FROM Patient");
    if ($res1) {
        $row = mysqli_fetch_assoc($res1);
        $stats['total_patients'] = $row['cnt'];
    }

    // 2. Today's Revenue (Sum of GrandTotal from Bills where BillDate is today)
    // Updated to use Date() check for accuracy
    $today = date('Y-m-d');
    $sql2 = "SELECT SUM(GrandTotal) as rev FROM Bill WHERE DATE(BillDate) = '$today'";
    $res2 = mysqli_query($con, $sql2);
    if ($res2) {
        $row = mysqli_fetch_assoc($res2);
        // If null, 0
        $stats['today_revenue'] = $row['rev'] ? $row['rev'] : 0.00;
    }

    // 3. Total Tests Performed
    $res3 = mysqli_query($con, "SELECT COUNT(*) as cnt FROM LabTestResult");
    if ($res3) {
        $row = mysqli_fetch_assoc($res3);
        $stats['total_tests'] = $row['cnt'];
    }

    mysqli_close($con);
    return $stats;
}
