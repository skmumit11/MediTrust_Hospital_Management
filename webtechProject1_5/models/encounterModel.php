<?php
require_once('db.php');

/* ---------------- GET ALL ---------------- */
function getAllEncounters(){
    $con = getConnection();
    $sql = "SELECT * FROM Encounter";
    $result = mysqli_query($con, $sql);
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
    }
    return $rows;
}

/* ---------------- GET BY ID ---------------- */
function getEncounterById($id){
    $con = getConnection();
    $id = (int)$id;
    $sql = "SELECT * FROM Encounter WHERE EncounterID=$id";
    $result = mysqli_query($con, $sql);
    return mysqli_fetch_assoc($result);
}

/* ---------------- GET BY PATIENT ---------------- */
function getEncountersByPatient($patientID){
    $con = getConnection();
    $patientID = (int)$patientID;
    $sql = "SELECT e.*, u.Name AS DoctorName
            FROM Encounter e
            JOIN User u ON e.DoctorID = u.UserID
            WHERE e.PatientID=$patientID
            ORDER BY e.EncounterID DESC";
    $result = mysqli_query($con, $sql);
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
    }
    return $rows;
}

/* ---------------- GET MEDICAL HISTORY BY PATIENT ---------------- */
function getMedicalHistoryByPatient($patientID){
    $con = getConnection();
    $patientID = (int)$patientID;
    $sql = "SELECT e.EncounterID, e.PatientID, e.DoctorID, e.DiagnosisICD, e.Vitals, e.Attachments,
                   u.Name AS DoctorName,
                   GROUP_CONCAT(p.Medicine SEPARATOR ', ') AS Prescription
            FROM Encounter e
            LEFT JOIN Prescription p ON e.EncounterID = p.EncounterID
            JOIN User u ON e.DoctorID = u.UserID
            WHERE e.PatientID = $patientID
            GROUP BY e.EncounterID
            ORDER BY e.EncounterID DESC";
    $result = mysqli_query($con, $sql);
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
    }
    return $rows;
}

/* ---------------- ADD ---------------- */
function addEncounter($data){
    $con = getConnection();
    $sql = "INSERT INTO Encounter (PatientID, DoctorID, Vitals, DiagnosisICD, Attachments) 
            VALUES ('{$data['PatientID']}', '{$data['DoctorID']}', '{$data['Vitals']}', '{$data['DiagnosisICD']}', '{$data['Attachments']}')";
    return mysqli_query($con, $sql);
}

/* ---------------- UPDATE ---------------- */
function updateEncounter($data){
    $con = getConnection();
    $id = (int)$data['EncounterID'];
    $sql = "UPDATE Encounter SET PatientID='{$data['PatientID']}', DoctorID='{$data['DoctorID']}', Vitals='{$data['Vitals']}', DiagnosisICD='{$data['DiagnosisICD']}', Attachments='{$data['Attachments']}' WHERE EncounterID=$id";
    return mysqli_query($con, $sql);
}

/* ---------------- DELETE ---------------- */
function deleteEncounter($id){
    $con = getConnection();
    $id = (int)$id;
    $sql = "DELETE FROM Encounter WHERE EncounterID=$id";
    return mysqli_query($con, $sql);
}
?>
