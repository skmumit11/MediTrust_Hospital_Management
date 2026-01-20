<?php
require_once('db16_18.php');

function getOperationalReports($doctorID = null, $deptID = null, $dateFrom = null, $dateTo = null)
{
    $con = getConnection();
    $where = [];

    if ($doctorID)
        $where[] = "a.DoctorID=" . (int) $doctorID;
    if ($deptID)
        $where[] = "a.DepartmentID=" . (int) $deptID;
    if ($dateFrom)
        $where[] = "a.Slot >= '" . mysqli_real_escape_string($con, $dateFrom) . "'";
    if ($dateTo)
        $where[] = "a.Slot <= '" . mysqli_real_escape_string($con, $dateTo) . "'";

    $filter = count($where) ? " WHERE " . implode(" AND ", $where) : "";

    $sql = " SELECT a.AppointmentID, p.Name AS Patient, u.Name AS Doctor, d.Name AS Department, a.Slot, a.Status FROM
    Appointment a LEFT JOIN Patient p ON p.PatientID=a.PatientID LEFT JOIN Doctor doc ON doc.DoctorID=a.DoctorID LEFT
    JOIN User u ON u.UserID=doc.DoctorID LEFT JOIN Department d ON d.DepartmentID=a.DepartmentID $filter ORDER BY a.Slot
    ASC";
    return mysqli_query($con, $sql);
}