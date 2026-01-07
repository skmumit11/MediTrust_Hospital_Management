<?php
require_once ('db.php') ;

function getAllDoctors()
{
    $conn = getConnection();
    $sql = "SELECT u.Name, d.Specialty, u.Username, d.DoctorID
            FROM Doctor d
            JOIN `User` u ON u.UserID = d.DoctorID
            ORDER BY u.Name ASC";
    $res = $conn->query($sql);
    $rows = [];
    if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } $res->free(); }
    closeConnection($conn);
    return $rows;
}

function getDoctorById($doctorId)
{
    $conn = getConnection();
    $sql = "SELECT d.DoctorID, u.Name, u.Username, d.Specialty, d.Availability
            FROM Doctor d
            JOIN `User` u ON u.UserID = d.DoctorID
            WHERE d.DoctorID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $doctorId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = null;
    if ($res) { $row = $res->fetch_assoc(); }
    $stmt->close();
    closeConnection($conn);
    return $row;
}

/** Insert a doctor by existing user’s username (FK requires user to exist). */
function addDoctorByUsername($username, $specialty, $availability)
{
    $conn = getConnection();

    // Resolve UserID
    $u = $conn->prepare("SELECT UserID FROM `User` WHERE Username = ?");
    $u->bind_param('s', $username);
    $u->execute();
    $ur = $u->get_result();
    $row = $ur->fetch_assoc();
    $u->close();
    if (!$row) { closeConnection($conn); return 0; }
    $userId = (int)$row['UserID'];

    // Insert doctor (DoctorID = UserID)
    $stmt = $conn->prepare("INSERT INTO Doctor (DoctorID, Specialty, Availability) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $userId, $specialty, $availability);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok ? $userId : 0;
}
//
function updateDoctor($doctorId, $specialty, $availability)
{
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE Doctor SET Specialty = ?, Availability = ? WHERE DoctorID = ?");
    $stmt->bind_param('ssi', $specialty, $availability, $doctorId);
    $stmt->execute();
    $ok = ($stmt->affected_rows >= 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}
//
function deleteDoctor($doctorId)
{
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM Doctor WHERE DoctorID = ?");
    $stmt->bind_param('i', $doctorId);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}



function createDoctorUser($name, $username, $password, $email, $dob, $gender, $address)
{
    $conn = getConnection();

    // Ensure username is unique (simple check; no regex)
    $chk = $conn->prepare("SELECT UserID FROM `User` WHERE Username = ?");
    $chk->bind_param('s', $username);
    $chk->execute();
    $res = $chk->get_result();
    if ($res && $res->fetch_assoc()) {
        $chk->close();
        closeConnection($conn);
        return 0; // username already exists
    }
    $chk->close();

    // Insert new user with Role='Doctor'
    $sql = "INSERT INTO `User` (Name, Username, Password, Email, DOB, Gender, Address, Status, Role)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Active', 'Doctor')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssss', $name, $username, $password, $email, $dob, $gender, $address);
    $stmt->execute();
    $newUserId = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);

    return ($newUserId > 0) ? $newUserId : 0;
}

/** After user exists, insert the doctor row. Returns DoctorID or 0. */
function ensureDoctorRow($userId, $specialty, $availability)
{
    $conn = getConnection();

    // If Doctor already exists for this user, just update specialty/availability
    $chk = $conn->prepare("SELECT DoctorID FROM Doctor WHERE DoctorID = ?");
    $chk->bind_param('i', $userId);
    $chk->execute();
    $res = $chk->get_result();
    if ($res && $res->fetch_assoc()) {
        $chk->close();
        $upd = $conn->prepare("UPDATE Doctor SET Specialty = ?, Availability = ? WHERE DoctorID = ?");
        $upd->bind_param('ssi', $specialty, $availability, $userId);
        $upd->execute();
        $upd->close();
        closeConnection($conn);
        return $userId; // already existed; now updated
    }
    $chk->close();

    // Insert new doctor row (DoctorID = UserID)
    $ins = $conn->prepare("INSERT INTO Doctor (DoctorID, Specialty, Availability) VALUES (?, ?, ?)");
    $ins->bind_param('iss', $userId, $specialty, $availability);
    $ins->execute();
    $ok = ($ins->affected_rows > 0);
    $ins->close();
    closeConnection($conn);

    return $ok ? $userId : 0;
}



/** Resolve a user's UserID by username (helper). */
function getUserIdByUsername($username)
{
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT UserID FROM `User` WHERE Username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $uid = 0;
    if ($res && ($row = $res->fetch_assoc())) { $uid = (int)$row['UserID']; }
    $stmt->close();
    closeConnection($conn);
    return $uid;
}


function getUsersNotInDoctor()
{
    $conn = getConnection();
    $sql = "SELECT u.UserID, u.Username, u.Name, u.Role
            FROM `User` u
            WHERE u.UserID NOT IN (SELECT d.DoctorID FROM Doctor d)
            ORDER BY u.Name ASC, u.Username ASC";
    $res = $conn->query($sql);
    $rows = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) { $rows[] = $r; }
        $res->free();
    }
    closeConnection($conn);
    return $rows; // each row: UserID, Username, Name, Role
}


function getSpecialtyOptions()
{
    $conn = getConnection();
    $res = $conn->query("SELECT Name FROM Department ORDER BY Name ASC");

    $opts = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $name = trim($r['Name']);
            if ($name !== '') { $opts[] = $name; }
        }
        $res->free();
    }


    closeConnection($conn);
    return $opts;
}


function getDoctorAppointments($doctorId, $limit = 200)
{
    $conn = getConnection();
    $sql = "SELECT 
                a.AppointmentID,
                a.PatientID,
                p.Name AS PatientName,
                a.Slot,
                a.Status
            FROM Appointment a
            JOIN Patient p ON p.PatientID = a.PatientID
            WHERE a.DoctorID = ?
              AND a.Slot >= NOW()
              AND a.Status IN ('Pending','Confirmed')
            ORDER BY a.Slot ASC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $doctorId, $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
    $stmt->close();
    closeConnection($conn);
    return $rows;
}


function getDoctorPatients($doctorId, $limit = 500)
{
    $conn = getConnection();

    // From appointments
    $sqlAppt = "SELECT 
                    p.PatientID, p.Name, p.Gender, p.Contact,
                    MAX(a.Slot) AS LastSeen
                FROM Appointment a
                JOIN Patient p ON p.PatientID = a.PatientID
                WHERE a.DoctorID = ?
                GROUP BY p.PatientID, p.Name, p.Gender, p.Contact";

    $stmtA = $conn->prepare($sqlAppt);
    $stmtA->bind_param('i', $doctorId);
    $stmtA->execute();
    $resA = $stmtA->get_result();
    $map = []; // PatientID -> row
    while ($r = $resA->fetch_assoc()) { $map[(int)$r['PatientID']] = $r; }
    $stmtA->close();

    // From encounters (Encounter.DoctorID references User(UserID))
    $sqlEnc = "SELECT 
                    p.PatientID, p.Name, p.Gender, p.Contact,
                    MAX(e.EncounterID) AS LastSeenEncounterId
               FROM Encounter e
               JOIN Patient p ON p.PatientID = e.PatientID
               WHERE e.DoctorID = ?
               GROUP BY p.PatientID, p.Name, p.Gender, p.Contact";
    $stmtE = $conn->prepare($sqlEnc);
    $stmtE->bind_param('i', $doctorId);
    $stmtE->execute();
    $resE = $stmtE->get_result();
    while ($r = $resE->fetch_assoc()) {
        $pid = (int)$r['PatientID'];
        if (!isset($map[$pid])) {
            // create a synthetic LastSeen using EncounterID as a proxy
            $map[$pid] = [
                'PatientID' => $pid,
                'Name'      => $r['Name'],
                'Gender'    => $r['Gender'],
                'Contact'   => $r['Contact'],
                'LastSeen'  => 'Encounter#' . (int)$r['LastSeenEncounterId'],
            ];
        } else {
            // if appointment-based LastSeen is empty, set encounter info
            if ($map[$pid]['LastSeen'] === null || $map[$pid]['LastSeen'] === '') {
                $map[$pid]['LastSeen'] = 'Encounter#' . (int)$r['LastSeenEncounterId'];
            }
        }
    }
    $stmtE->close();
    closeConnection($conn);

    // Flatten & limit
    $rows = array_values($map);
    if (count($rows) > $limit) { $rows = array_slice($rows, 0, $limit); }
    return $rows;
}

function getAllDepartments()
{
    $conn = getConnection();
    $res = $conn->query("SELECT * FROM Department ORDER BY Name ASC");
    $rows = [];
    if ($res) { 
        while ($r = $res->fetch_assoc()) { 
            $rows[] = $r; 
        } 
        $res->free(); 
    }
    closeConnection($conn);
    return $rows;
}

function getDoctorsBySpecialty($specialty)
{
    $conn = getConnection();
    $sql = "SELECT u.Name, d.Specialty, u.Username, d.DoctorID
            FROM Doctor d
            JOIN `User` u ON u.UserID = d.DoctorID
            WHERE d.Specialty = ?
            ORDER BY u.Name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $specialty);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
    $stmt->close();
    closeConnection($conn);
    return $rows;
}

