<?php
require_once('db11_15.php');

function getOPDVisits() {
    $con = getConnection();
    
    $sql = "SELECT o.*, p.Name as patient_name, u.Name as doctor_name 
            FROM OPDRecord o
            JOIN Patient p ON o.PatientID = p.PatientID
            JOIN User u ON o.DoctorID = u.UserID";
    
    $result = mysqli_query($con, $sql);
    $data = [];
    if($result){
        while($row = mysqli_fetch_assoc($result)){
             // Mock notes if missing
            $row['doctor_notes'] = "No notes column in OPDRecord";
            $data[] = $row;
        }
    }
    return $data;
}

function getIPDAdmissions() {
    $con = getConnection();
    
    $sql = "SELECT i.*, p.Name as patient_name 
            FROM IPDRecord i
            JOIN Patient p ON i.PatientID = p.PatientID";

    $result = mysqli_query($con, $sql);
    $data = [];
    if($result){
        while($row = mysqli_fetch_assoc($result)){
             // Mock notes
            $row['doctor_notes'] = "No notes column in IPDRecord";
            $data[] = $row;
        }
    }
    return $data;
}

function updateOPDNote($id, $note) {
    // Cannot update note if column doesn't exist.
    return false; 
}

function updateIPDNoteAndStatus($id, $note, $status) {
    $con = getConnection();
    $id = (int)$id;
    $status = mysqli_real_escape_string($con, $status);
    // Only update status
    $sql = "UPDATE IPDRecord SET Status='$status' WHERE IPDID=$id";
    return mysqli_query($con, $sql);
}

// --- CRUD for OPD ---

function addOPD($patientId, $doctorId, $date, $status, $createdBy) {
    $con = getConnection();
    $patientId = (int)$patientId;
    $doctorId = (int)$doctorId;
    $date = mysqli_real_escape_string($con, $date);
    $status = mysqli_real_escape_string($con, $status);
    $createdBy = (int)$createdBy;

    $sql = "INSERT INTO OPDRecord (PatientID, DoctorID, VisitDate, Status, CreatedByUserID) 
            VALUES ($patientId, $doctorId, '$date', '$status', $createdBy)";
    return mysqli_query($con, $sql);
}

function updateOPD($id, $patientId, $doctorId, $date, $status) {
    $con = getConnection();
    $id = (int)$id;
    $patientId = (int)$patientId;
    $doctorId = (int)$doctorId;
    $date = mysqli_real_escape_string($con, $date);
    $status = mysqli_real_escape_string($con, $status);

    $sql = "UPDATE OPDRecord SET PatientID=$patientId, DoctorID=$doctorId, VisitDate='$date', Status='$status' WHERE OPDID=$id";
    return mysqli_query($con, $sql);
}

function deleteOPD($id) {
    $con = getConnection();
    $id = (int)$id;
    $sql = "DELETE FROM OPDRecord WHERE OPDID=$id";
    return mysqli_query($con, $sql);
}

function getOPDById($id) {
    $con = getConnection();
    $id = (int)$id;
    $sql = "SELECT * FROM OPDRecord WHERE OPDID=$id";
    $result = mysqli_query($con, $sql);
    return mysqli_fetch_assoc($result);
}

// --- CRUD for IPD ---

function addIPD($patientId, $roomNo, $admissionDate, $dischargeDate, $status, $source, $createdBy) {
    $con = getConnection();
    $patientId = (int)$patientId;
    $roomNo = mysqli_real_escape_string($con, $roomNo);
    $admissionDate = mysqli_real_escape_string($con, $admissionDate);
    // Discharge date can be null/empty
    $dischargeSql = $dischargeDate ? "'".mysqli_real_escape_string($con, $dischargeDate)."'" : "NULL";
    $status = mysqli_real_escape_string($con, $status);
    $source = mysqli_real_escape_string($con, $source);
    $createdBy = (int)$createdBy;

    $sql = "INSERT INTO IPDRecord (PatientID, RoomNo, AdmissionDate, DischargeDate, Status, AdmissionSource, CreatedByUserID) 
            VALUES ($patientId, '$roomNo', '$admissionDate', $dischargeSql, '$status', '$source', $createdBy)";
    return mysqli_query($con, $sql);
}

function updateIPD($id, $patientId, $roomNo, $admissionDate, $dischargeDate, $status, $source) {
    $con = getConnection();
    $id = (int)$id;
    $patientId = (int)$patientId;
    $roomNo = mysqli_real_escape_string($con, $roomNo);
    $admissionDate = mysqli_real_escape_string($con, $admissionDate);
    $dischargeSql = $dischargeDate ? "'".mysqli_real_escape_string($con, $dischargeDate)."'" : "NULL";
    $status = mysqli_real_escape_string($con, $status);
    $source = mysqli_real_escape_string($con, $source);

    $sql = "UPDATE IPDRecord SET PatientID=$patientId, RoomNo='$roomNo', AdmissionDate='$admissionDate', DischargeDate=$dischargeSql, Status='$status', AdmissionSource='$source' WHERE IPDID=$id";
    return mysqli_query($con, $sql);
}

function deleteIPD($id) {
    $con = getConnection();
    $id = (int)$id;
    $sql = "DELETE FROM IPDRecord WHERE IPDID=$id";
    return mysqli_query($con, $sql);
}

function getIPDById($id) {
    $con = getConnection();
    $id = (int)$id;
    $sql = "SELECT * FROM IPDRecord WHERE IPDID=$id";
    $result = mysqli_query($con, $sql);
    return mysqli_fetch_assoc($result);
}

// Helper to get dropdowns
function getAllPatients() {
    $con = getConnection();
    $sql = "SELECT PatientID, Name FROM Patient";
    $result = mysqli_query($con, $sql);
    $data = [];
    if($result){
        while($r = mysqli_fetch_assoc($result)) $data[] = $r;
    }
    return $data;
}
