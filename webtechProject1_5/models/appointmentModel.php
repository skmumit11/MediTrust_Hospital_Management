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

    $sql = "INSERT INTO Appointment (PatientID, DoctorID, DepartmentID, Slot, Status)
            VALUES (?,?,?,?,?)";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iiiss", $patientId, $doctorId, $deptId, $slot, $status);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    closeConnection($con);
    return $ok ? true : false;
}





function getAllAppointments($limit = 200)
{
    $conn = getConnection();
    $sql = "SELECT a.AppointmentID, p.Name AS patient, ud.Name AS doctor, a.Slot, a.Status
            FROM Appointment a
            JOIN Patient p ON p.PatientID = a.PatientID
            JOIN Doctor d ON d.DoctorID = a.DoctorID
            JOIN `User` ud ON ud.UserID = d.DoctorID
            ORDER BY a.Slot DESC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
    $stmt->close();
    closeConnection($conn);
    return $rows;
}

function getAppointmentById($appointmentId)
{
    $conn = getConnection();
    $sql = "SELECT a.AppointmentID, a.PatientID, a.DoctorID, a.DepartmentID, a.Slot, a.Status
            FROM Appointment a
            WHERE a.AppointmentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $appointmentId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = null; if ($res) { $row = $res->fetch_assoc(); }
    $stmt->close();
    closeConnection($conn);
    return $row;
}

function addAppointment($patientId, $doctorId, $departmentId, $slot, $status, $createdByUserId = null)
{
    $conn = getConnection();
    $sql = "INSERT INTO Appointment
            (PatientID, DoctorID, DepartmentID, Slot, Status, CreatedByUserID, CreatedAt)
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $cuid = ($createdByUserId === null) ? null : (int)$createdByUserId;
    $stmt->bind_param('iiissi', $patientId, $doctorId, $departmentId, $slot, $status, $cuid);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    return ($newId > 0) ? $newId : 0;
}

function updateAppointment($appointmentId, $patientId, $doctorId, $departmentId, $slot, $status)
{
    $conn = getConnection();
    $sql = "UPDATE Appointment
            SET PatientID = ?, DoctorID = ?, DepartmentID = ?, Slot = ?, Status = ?
            WHERE AppointmentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiissi', $patientId, $doctorId, $departmentId, $slot, $status, $appointmentId);
    $stmt->execute();
    $ok = ($stmt->affected_rows >= 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}

function deleteAppointment($appointmentId)
{
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM Appointment WHERE AppointmentID = ?");
    $stmt->bind_param('i', $appointmentId);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}



function getUpcomingAppointmentsForUser($username, $fallbackName = '')
{
    $conn = getConnection();

    // Resolve UserID
    $userId = null;
    if ($username !== '') {
        $stmt = $conn->prepare("SELECT UserID, Name FROM `User` WHERE Username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $userId = (int)$row['UserID'];
            // if no explicit fallbackName provided, use the user's Name from table
            if ($fallbackName === '' && isset($row['Name'])) {
                $fallbackName = $row['Name'];
            }
        }
        $stmt->close();
    }

    // Resolve PatientID linked to user (may be NULL)
    $linkedPatientId = null;
    if ($userId !== null) {
        $q = $conn->prepare("SELECT PatientID FROM Patient WHERE UserID = ?");
        $q->bind_param('i', $userId);
        $q->execute();
        $qr = $q->get_result();
        if ($pr = $qr->fetch_assoc()) { $linkedPatientId = (int)$pr['PatientID']; }
        $q->close();
    }

    $rows = [];

    if ($linkedPatientId !== null) {
        // Primary: appointments for the linked patient
        $sql = "SELECT a.AppointmentID, a.Slot, a.Status, u.Name AS DoctorName
                FROM Appointment a
                JOIN Doctor d ON d.DoctorID = a.DoctorID
                JOIN `User` u ON u.UserID = d.DoctorID
                WHERE a.PatientID = ?
                  AND a.Slot >= NOW()
                  AND a.Status IN ('Pending','Confirmed')
                ORDER BY a.Slot ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $linkedPatientId);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) { $rows[] = $r; }
        $stmt->close();
    }

    // If none found yet, try by CreatedByUserID (appointments the user booked)
    if (count($rows) === 0 && $userId !== null) {
        $sql = "SELECT a.AppointmentID, a.Slot, a.Status, u.Name AS DoctorName
                FROM Appointment a
                JOIN Doctor d ON d.DoctorID = a.DoctorID
                JOIN `User` u ON u.UserID = d.DoctorID
                WHERE a.CreatedByUserID = ?
                  AND a.Slot >= NOW()
                  AND a.Status IN ('Pending','Confirmed')
                ORDER BY a.Slot ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) { $rows[] = $r; }
        $stmt->close();
    }

    // Final fallback for guests / non-linked users: match Patient.Name
    if (count($rows) === 0 && $fallbackName !== '') {
        $sql = "SELECT a.AppointmentID, a.Slot, a.Status, u.Name AS DoctorName
                FROM Appointment a
                JOIN Patient p ON p.PatientID = a.PatientID
                JOIN Doctor d ON d.DoctorID = a.DoctorID
                JOIN `User` u ON u.UserID = d.DoctorID
                WHERE p.Name = ?
                  AND a.Slot >= NOW()
                  AND a.Status IN ('Pending','Confirmed')
                ORDER BY a.Slot ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $fallbackName);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) { $rows[] = $r; }
        $stmt->close();
    }

    closeConnection($conn);
    return $rows;
}
