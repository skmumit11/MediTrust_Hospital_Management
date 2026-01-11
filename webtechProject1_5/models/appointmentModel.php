<?php
require_once "db.php";

function demoAppointments() {
    return [
        ["AppointmentID"=>1, "PatientID"=>201, "DoctorID"=>101, "DepartmentID"=>1, "Slot"=>"2026-01-04 10:00:00", "Status"=>"Pending", "patient"=>"Fatima Noor", "doctor"=>"Dr. Arif Rahman"],
        ["AppointmentID"=>2, "PatientID"=>202, "DoctorID"=>102, "DepartmentID"=>2, "Slot"=>"2026-01-05 11:00:00", "Status"=>"Confirmed", "patient"=>"Rashid Khan", "doctor"=>"Dr. Laila Hassan"],
    ];
}




function createAppointment($patientId, $doctorId, $deptId, $slot, $status) {
    global $USE_DEMO;
    if($USE_DEMO) { return true; }

    $con = getConnection();
    
    $patientId = (int)$patientId;
    $doctorId = (int)$doctorId;
    $deptId = (int)$deptId;
    $slot = trim($slot);
    $status = trim($status);

    $sql = "INSERT INTO Appointment (PatientID, DoctorID, DepartmentID, Slot, Status)
            VALUES ($patientId, $doctorId, $deptId, '$slot', '$status')";

    $ok = mysqli_query($con, $sql);

    closeConnection($con);
    return $ok ? true : false;
}





function getAllAppointments($limit = 200)
{
    $conn = getConnection();
    $limit = (int)$limit;
    $sql = "SELECT a.AppointmentID, p.Name AS patient, ud.Name AS doctor, a.Slot, a.Status
            FROM Appointment a
            JOIN Patient p ON p.PatientID = a.PatientID
            JOIN Doctor d ON d.DoctorID = a.DoctorID
            JOIN `User` ud ON ud.UserID = d.DoctorID
            ORDER BY a.Slot DESC
            LIMIT $limit";
    $res = mysqli_query($conn, $sql);
    $rows = [];
    if($res){
        while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
    }
    closeConnection($conn);
    return $rows;
}

function getAppointmentById($appointmentId)
{
    $conn = getConnection();
    $appointmentId = (int)$appointmentId;
    $sql = "SELECT a.AppointmentID, a.PatientID, a.DoctorID, a.DepartmentID, a.Slot, a.Status
            FROM Appointment a
            WHERE a.AppointmentID = $appointmentId";
    $res = mysqli_query($conn, $sql);
    $row = null; if ($res) { $row = mysqli_fetch_assoc($res); }
    closeConnection($conn);
    return $row;
}

function addAppointment($patientId, $doctorId, $departmentId, $slot, $status, $createdByUserId = null)
{
    $conn = getConnection();
    
    $patientId = (int)$patientId;
    $doctorId = (int)$doctorId;
    $departmentId = (int)$departmentId;
    $slot = trim($slot);
    $status = trim($status);
    $cuid = ($createdByUserId === null) ? 'NULL' : (int)$createdByUserId;
    
    $sql = "INSERT INTO Appointment
            (PatientID, DoctorID, DepartmentID, Slot, Status, CreatedByUserID, CreatedAt)
            VALUES ($patientId, $doctorId, $departmentId, '$slot', '$status', $cuid, NOW())";
            
    mysqli_query($conn, $sql);
    $newId = mysqli_insert_id($conn);
    closeConnection($conn);
    return ($newId > 0) ? $newId : 0;
}

function updateAppointment($appointmentId, $patientId, $doctorId, $departmentId, $slot, $status)
{
    $conn = getConnection();
    
    $appointmentId = (int)$appointmentId;
    $patientId = (int)$patientId;
    $doctorId = (int)$doctorId;
    $departmentId = (int)$departmentId;
    $slot = trim($slot);
    $status = trim($status);
    
    $sql = "UPDATE Appointment
            SET PatientID = $patientId, DoctorID = $doctorId, DepartmentID = $departmentId, Slot = '$slot', Status = '$status'
            WHERE AppointmentID = $appointmentId";
            
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) >= 0);
    closeConnection($conn);
    return $ok;
}

function deleteAppointment($appointmentId)
{
    $conn = getConnection();
    $appointmentId = (int)$appointmentId;
    
    $sql = "DELETE FROM Appointment WHERE AppointmentID = $appointmentId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);
    closeConnection($conn);
    return $ok;
}



function getUpcomingAppointmentsForUser($username, $fallbackName = '')
{
    $conn = getConnection();
    
    $username = trim($username);
    $fallbackName = trim($fallbackName);

    // Resolve UserID
    $userId = null;
    if ($username !== '') {
        $sql = "SELECT UserID, Name FROM `User` WHERE Username = '$username'";
        $res = mysqli_query($conn, $sql);
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $userId = (int)$row['UserID'];
            // if no explicit fallbackName provided, use the user's Name from table
            if ($fallbackName === '' && isset($row['Name'])) {
                $fallbackName = $row['Name'];
            }
        }
    }

    // Resolve PatientID linked to user (may be NULL)
    $linkedPatientId = null;
    if ($userId !== null) {
        $q = "SELECT PatientID FROM Patient WHERE UserID = $userId";
        $qr = mysqli_query($conn, $q);
        if ($qr && $pr = mysqli_fetch_assoc($qr)) { $linkedPatientId = (int)$pr['PatientID']; }
    }

    $rows = [];

    if ($linkedPatientId !== null) {
        // Primary: appointments for the linked patient
        $sql = "SELECT a.AppointmentID, a.Slot, a.Status, u.Name AS DoctorName
                FROM Appointment a
                JOIN Doctor d ON d.DoctorID = a.DoctorID
                JOIN `User` u ON u.UserID = d.DoctorID
                WHERE a.PatientID = $linkedPatientId
                  AND a.Slot >= NOW()
                  AND a.Status IN ('Pending','Confirmed')
                ORDER BY a.Slot ASC";
        $res = mysqli_query($conn, $sql);
        if($res){
             while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
        }
    }

    // If none found yet, try by CreatedByUserID (appointments the user booked)
    if (count($rows) === 0 && $userId !== null) {
        $sql = "SELECT a.AppointmentID, a.Slot, a.Status, u.Name AS DoctorName
                FROM Appointment a
                JOIN Doctor d ON d.DoctorID = a.DoctorID
                JOIN `User` u ON u.UserID = d.DoctorID
                WHERE a.CreatedByUserID = $userId
                  AND a.Slot >= NOW()
                  AND a.Status IN ('Pending','Confirmed')
                ORDER BY a.Slot ASC";
        $res = mysqli_query($conn, $sql);
        if($res){
             while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
        }
    }

    // Final fallback for guests / non-linked users: match Patient.Name
    if (count($rows) === 0 && $fallbackName !== '') {
        $sql = "SELECT a.AppointmentID, a.Slot, a.Status, u.Name AS DoctorName
                FROM Appointment a
                JOIN Patient p ON p.PatientID = a.PatientID
                JOIN Doctor d ON d.DoctorID = a.DoctorID
                JOIN `User` u ON u.UserID = d.DoctorID
                WHERE p.Name = '$fallbackName'
                  AND a.Slot >= NOW()
                  AND a.Status IN ('Pending','Confirmed')
                ORDER BY a.Slot ASC";
        $res = mysqli_query($conn, $sql);
        if($res){
             while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
        }
    }

    closeConnection($conn);
    return $rows;
}
