<?php
require_once('db16_18.php');

function patient_get($patientID)
{
    $con = getConnection();
    $pid = (int) $patientID;
    $sql = "SELECT PatientID, Name, Age, Gender, Contact FROM Patient WHERE PatientID=$pid LIMIT 1";
    return mysqli_query($con, $sql);
}

function encounter_save($patientID, $doctorID, $vitals, $diagnosisICD, $allergies, $prescriptions, $attachments)
{
    $con = getConnection();
    $pid = (int) $patientID;
    $did = (int) $doctorID;
    $vitals = mysqli_real_escape_string($con, $vitals);
    $diagnosisICD = mysqli_real_escape_string($con, $diagnosisICD);
    $attachments = mysqli_real_escape_string($con, $attachments);

    mysqli_query($con, "INSERT INTO Encounter (PatientID, DoctorID, Vitals, DiagnosisICD, Attachments)
VALUES ($pid,$did,'$vitals','$diagnosisICD','$attachments')");
    $encID = mysqli_insert_id($con);

    foreach ($allergies as $a) {
        $a = mysqli_real_escape_string($con, $a);
        mysqli_query($con, "INSERT INTO Consent (PatientID,Purpose,GivenAt,PolicyVersion) VALUES
($pid,'Allergy:$a',NOW(),'1.0')");
    }

    foreach ($prescriptions as $p) {
        $med = mysqli_real_escape_string($con, $p['medicine']);
        $dos = mysqli_real_escape_string($con, $p['dosage']);
        $dur = mysqli_real_escape_string($con, $p['duration']);
        mysqli_query($con, "INSERT INTO Prescription (EncounterID,Medicine,Dosage,Duration)
VALUES ($encID,'$med','$dos','$dur')");
    }
    return $encID;
}