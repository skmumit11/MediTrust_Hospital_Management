<?php
// models/insuranceModel6_10.php
require_once 'db6_10.php';

function getPatientList()
{
    $conn = getConnection();
    $sql = "SELECT * FROM Patient ORDER BY PatientID DESC";
    $result = mysqli_query($conn, $sql);
    $list = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
    }
    return $list;
}

function createInsuranceRecord($patientId, $company, $policyId, $validityDate)
{
    $conn = getConnection();
    $sql = "INSERT INTO InsuranceRecord (PatientID, Company, PolicyID, ValidityDate) 
            VALUES ('{$patientId}', '{$company}', '{$policyId}', '{$validityDate}')";
    return mysqli_query($conn, $sql);
}

function getAllInsuranceRecords()
{
    $conn = getConnection();
    $sql = "SELECT i.*, p.Name as PatientName 
            FROM InsuranceRecord i, Patient p 
            WHERE i.PatientID = p.PatientID 
            ORDER BY i.InsuranceID DESC";
    $result = mysqli_query($conn, $sql);
    $list = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
    }
    return $list;
}

function getInsuranceRecordById($id)
{
    $conn = getConnection();
    $sql = "SELECT * FROM InsuranceRecord WHERE InsuranceID = '{$id}'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

function updateInsuranceRecord($id, $patientId, $company, $policyId, $validityDate)
{
    $conn = getConnection();
    $sql = "UPDATE InsuranceRecord 
            SET PatientID='{$patientId}', Company='{$company}', PolicyID='{$policyId}', ValidityDate='{$validityDate}' 
            WHERE InsuranceID='{$id}'";
    return mysqli_query($conn, $sql);
}

function deleteInsuranceRecord($id)
{
    $conn = getConnection();
    $sql = "DELETE FROM InsuranceRecord WHERE InsuranceID='{$id}'";
    return mysqli_query($conn, $sql);
}
?>