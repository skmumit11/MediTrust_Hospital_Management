<?php
require_once('../models/reportModel16_18.php');

$doctorID = $_GET['doctorID'] ?? null;
$deptID = $_GET['deptID'] ?? null;
$dateFrom = $_GET['dateFrom'] ?? null;
$dateTo = $_GET['dateTo'] ?? null;

$res = report_get($doctorID, $deptID, $dateFrom, $dateTo);

include('../views/report_list16_18.php');
