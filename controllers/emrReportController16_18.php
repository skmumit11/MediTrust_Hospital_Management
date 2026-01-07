<?php
require_once('../models/emrReportModel16_18.php');

$patientID = $_GET['patientID'] ?? 0;
$dateFrom = $_GET['dateFrom'] ?? null;
$dateTo = $_GET['dateTo'] ?? null;

$res = emr_timeline($patientID, $dateFrom, $dateTo);

include('../views/emr_report16_18.php');
