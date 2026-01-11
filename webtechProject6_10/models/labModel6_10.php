<?php
// models/labModel6_10.php
require_once 'db6_10.php';

function createLabTestResult($patientId, $testType, $resultText)
{
    $conn = getConnection();
    $sql = "INSERT INTO LabTestResult VALUES (NULL, {$patientId}, '{$testType}', '{$resultText}', NOW())"; // Assumes auto-increment ID first
    // Note: If schema is different, adjust. Assuming typical strict procedural school project often creates tables with simple auto-inc.
    // Safety check: The user's snippet uses `insert into table values(...)`. I will follow that pattern but ensure column count matches or use valid SQL.
    // Better strictly safe approach for 'simple' code:
    $sql = "INSERT INTO LabTestResult (PatientID, TestType, Result, Timestamp) VALUES ('{$patientId}', '{$testType}', '{$resultText}', NOW())";

    if (mysqli_query($conn, $sql)) {
        return mysqli_insert_id($conn);
    } else {
        return 0;
    }
}

function getDistinctTestTypes()
{
    $conn = getConnection();
    $sql = "SELECT DISTINCT TestType FROM LabTestResult";
    $res = mysqli_query($conn, $sql);
    $types = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $types[] = $r['TestType'];
    }
    return $types;
}

function filterLabResults($dateFilter, $testType, $patientFilter)
{
    $conn = getConnection();
    $sql = "SELECT l.ResultID, l.PatientID, p.Name AS PatientName, p.Contact AS PatientContact,
                   l.TestType, l.Timestamp, l.Result
            FROM LabTestResult l, Patient p 
            WHERE l.PatientID = p.PatientID";

    if ($dateFilter != "") {
        $sql .= " AND DATE(l.Timestamp) = '{$dateFilter}'";
    }
    if ($testType != "") {
        $sql .= " AND l.TestType = '{$testType}'";
    }
    if ($patientFilter != "") {
        $sql .= " AND p.Name LIKE '%{$patientFilter}%'";
    }

    $res = mysqli_query($conn, $sql);
    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }
    return $rows;
}

function createLabReportRecord($filtersText, $generatedByUserId)
{
    $conn = getConnection();
    $sql = "INSERT INTO LabReport (Filters, GeneratedBy, GeneratedAt) VALUES ('{$filtersText}', '{$generatedByUserId}', NOW())";
    mysqli_query($conn, $sql);
    return mysqli_insert_id($conn);
}
?>