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
    $doctorId = (int)$doctorId;
    $sql = "SELECT d.DoctorID, u.Name, u.Username, d.Specialty, d.Availability
            FROM Doctor d
            JOIN `User` u ON u.UserID = d.DoctorID
            WHERE d.DoctorID = $doctorId";
    $res = mysqli_query($conn, $sql);
    $row = null;
    if ($res) { $row = mysqli_fetch_assoc($res); }
    closeConnection($conn);
    return $row;
}

/** Insert a doctor by existing user’s username (FK requires user to exist). */
function addDoctorByUsername($username, $specialty, $availability)
{
    $conn = getConnection();
    $username = trim($username);
    $specialty = trim($specialty);
    $availability = trim($availability);

    // Resolve UserID
    $uSql = "SELECT UserID FROM `User` WHERE Username = '$username'";
    $ur = mysqli_query($conn, $uSql);
    $row = ($ur) ? mysqli_fetch_assoc($ur) : null;
    
    if (!$row) { closeConnection($conn); return 0; }
    $userId = (int)$row['UserID'];

    // Insert doctor (DoctorID = UserID)
    $sql = "INSERT INTO Doctor (DoctorID, Specialty, Availability) VALUES ($userId, '$specialty', '$availability')";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);
    closeConnection($conn);
    return $ok ? $userId : 0;
}
//
function updateDoctor($doctorId, $specialty, $availability)
{
    $conn = getConnection();
    $doctorId = (int)$doctorId;
    $specialty = trim($specialty);
    $availability = trim($availability);
    
    $sql = "UPDATE Doctor SET Specialty = '$specialty', Availability = '$availability' WHERE DoctorID = $doctorId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) >= 0);
    closeConnection($conn);
    return $ok;
}
//
function deleteDoctor($doctorId)
{
    $conn = getConnection();
    $doctorId = (int)$doctorId;
    $sql = "DELETE FROM Doctor WHERE DoctorID = $doctorId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);
    closeConnection($conn);
    return $ok;
}



function createDoctorUser($name, $username, $password, $email, $dob, $gender, $address)
{
    $conn = getConnection();
    
    $username = trim($username);

    // Ensure username is unique (simple check; no regex)
    $chkSql = "SELECT UserID FROM `User` WHERE Username = '$username'";
    $res = mysqli_query($conn, $chkSql);
    if ($res && mysqli_fetch_assoc($res)) {
        closeConnection($conn);
        return 0; // username already exists
    }

    // Insert new user with Role='Doctor'
    $name = trim($name);
    $password = trim($password);
    $email = trim($email);
    $dob = trim($dob);
    $gender = trim($gender);
    $address = trim($address);

    $sql = "INSERT INTO `User` (Name, Username, Password, Email, DOB, Gender, Address, Status, Role)
            VALUES ('$name', '$username', '$password', '$email', '$dob', '$gender', '$address', 'Active', 'Doctor')";
    
    mysqli_query($conn, $sql);
    $newUserId = mysqli_insert_id($conn);
    closeConnection($conn);

    return ($newUserId > 0) ? $newUserId : 0;
}

/** After user exists, insert the doctor row. Returns DoctorID or 0. */
function ensureDoctorRow($userId, $specialty, $availability)
{
    $conn = getConnection();
    $userId = (int)$userId;
    $specialty = trim($specialty);
    $availability = trim($availability);

    // If Doctor already exists for this user, just update specialty/availability
    $chkSql = "SELECT DoctorID FROM Doctor WHERE DoctorID = $userId";
    $res = mysqli_query($conn, $chkSql);
    if ($res && mysqli_fetch_assoc($res)) {
        $updSql = "UPDATE Doctor SET Specialty = '$specialty', Availability = '$availability' WHERE DoctorID = $userId";
        mysqli_query($conn, $updSql);
        closeConnection($conn);
        return $userId; // already existed; now updated
    }

    // Insert new doctor row (DoctorID = UserID)
    $insSql = "INSERT INTO Doctor (DoctorID, Specialty, Availability) VALUES ($userId, '$specialty', '$availability')";
    mysqli_query($conn, $insSql);
    $ok = (mysqli_affected_rows($conn) > 0);
    closeConnection($conn);

    return $ok ? $userId : 0;
}



/** Resolve a user's UserID by username (helper). */
function getUserIdByUsername($username)
{
    $conn = getConnection();
    $username = trim($username);
    $sql = "SELECT UserID FROM `User` WHERE Username = '$username'";
    $res = mysqli_query($conn, $sql);
    $uid = 0;
    if ($res && ($row = mysqli_fetch_assoc($res))) { $uid = (int)$row['UserID']; }
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
    $doctorId = (int)$doctorId;
    $limit = (int)$limit;
    
    $sql = "SELECT 
                a.AppointmentID,
                a.PatientID,
                p.Name AS PatientName,
                a.Slot,
                a.Status
            FROM Appointment a
            JOIN Patient p ON p.PatientID = a.PatientID
            WHERE a.DoctorID = $doctorId
              AND a.Slot >= NOW()
              AND a.Status IN ('Pending','Confirmed')
            ORDER BY a.Slot ASC
            LIMIT $limit";
            
    $res = mysqli_query($conn, $sql);
    $rows = [];
    if($res){
        while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
    }
    closeConnection($conn);
    return $rows;
}


function getDoctorPatients($doctorId, $limit = 500)
{
    $conn = getConnection();
    $doctorId = (int)$doctorId;
    
    // From appointments
    $sqlAppt = "SELECT 
                    p.PatientID, p.Name, p.Gender, p.Contact,
                    MAX(a.Slot) AS LastSeen
                FROM Appointment a
                JOIN Patient p ON p.PatientID = a.PatientID
                WHERE a.DoctorID = $doctorId
                GROUP BY p.PatientID, p.Name, p.Gender, p.Contact";

    $resA = mysqli_query($conn, $sqlAppt);
    $map = []; // PatientID -> row
    if($resA){
        while ($r = mysqli_fetch_assoc($resA)) { $map[(int)$r['PatientID']] = $r; }
    }

    // From encounters (Encounter.DoctorID references User(UserID))
    $sqlEnc = "SELECT 
                    p.PatientID, p.Name, p.Gender, p.Contact,
                    MAX(e.EncounterID) AS LastSeenEncounterId
               FROM Encounter e
               JOIN Patient p ON p.PatientID = e.PatientID
               WHERE e.DoctorID = $doctorId
               GROUP BY p.PatientID, p.Name, p.Gender, p.Contact";
    $resE = mysqli_query($conn, $sqlEnc);
    
    if($resE){
        while ($r = mysqli_fetch_assoc($resE)) {
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
    }
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
    $specialty = trim($specialty);
    
    $sql = "SELECT u.Name, d.Specialty, u.Username, d.DoctorID
            FROM Doctor d
            JOIN `User` u ON u.UserID = d.DoctorID
            WHERE d.Specialty = '$specialty'
            ORDER BY u.Name ASC";
            
    $res = mysqli_query($conn, $sql);
    $rows = [];
    if($res){
        while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
    }
    closeConnection($conn);
    return $rows;
}

