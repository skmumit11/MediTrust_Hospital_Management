<?php
require_once('db16_18.php');

function emr_timeline($patientID, $dateFrom = null, $dateTo = null)
{
    $con = getConnection();
    $pid = (int) $patientID;
    $where = "WHERE e.PatientID=$pid";
    if ($dateFrom)
        $where .= " AND e.VisitDate>='" . mysqli_real_escape_string($con, $dateFrom) . "'";
    if ($dateTo)
        $where .= " AND e.VisitDate<='" . mysqli_real_escape_string($con, $dateTo) . "'";
    $sql = "SELECT e.EncounterID,e.Vitals,e.DiagnosisICD,e.Attachments,e.VisitDate,
                 p.Medicine,p.Dosage,p.Duration
          FROM Encounter e
          LEFT JOIN Prescription p ON p.EncounterID=e.EncounterID
          $where ORDER BY e.VisitDate ASC";
    return mysqli_query($con, $sql);
}