<?php
// models/insuranceModel.php
require_once 'db6_10.php';

function getPatientList()
{
    $con = getConnection();

    $sql = "SELECT PatientID, Name, Contact 
            FROM Patient 
            ORDER BY PatientID DESC";

    $result = mysqli_query($con, $sql);

    $patients = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $patients[] = $row;
        }
    }

    mysqli_close($con);
    return $patients;
}

function createInsuranceRecord($patientId, $company, $policyId, $validityDate)
{
    $con = getConnection();

    $sql = "INSERT INTO InsuranceRecord (PatientID, Company, PolicyID, ValidityDate)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "isss", $patientId, $company, $policyId, $validityDate);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $ok;
}

function getAllInsuranceRecords()
{
    $con = getConnection();
    $sql = "SELECT i.*, p.Name as PatientName, p.Contact as PatientContact 
            FROM InsuranceRecord i
            JOIN Patient p ON i.PatientID = p.PatientID
            ORDER BY i.InsuranceID DESC";

    $result = mysqli_query($con, $sql);
    $records = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }
    }

    mysqli_close($con);
    return $records;
}

function getInsuranceRecordById($id)
{
    $con = getConnection();
    $sql = "SELECT * FROM InsuranceRecord WHERE InsuranceID = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $row;
}

function updateInsuranceRecord($id, $patientId, $company, $policyId, $validityDate)
{
    $con = getConnection();
    $sql = "UPDATE InsuranceRecord 
            SET PatientID=?, Company=?, PolicyID=?, ValidityDate=?
            WHERE InsuranceID=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "isssi", $patientId, $company, $policyId, $validityDate, $id);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $ok;
}

function deleteInsuranceRecord($id)
{
    $con = getConnection();
    $sql = "DELETE FROM InsuranceRecord WHERE InsuranceID = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $ok;
}
