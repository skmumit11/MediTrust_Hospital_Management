<?php
require_once('../models/analyticsReportModel16_18.php');

$doctorID = $_GET['doctorID'] ?? null;
$deptID = $_GET['deptID'] ?? null;
$dateFrom = $_GET['dateFrom'] ?? null;
$dateTo = $_GET['dateTo'] ?? null;

$res = getOperationalReports($doctorID, $deptID, $dateFrom, $dateTo);

include('../views/analytics_report_list16_18.php');
