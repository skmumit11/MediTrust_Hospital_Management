
<?php
// models/patientModel.php
require_once 'db.php';





/* -----------------------------------------------------------
   COUNTS & UTILITIES
----------------------------------------------------------- */

function countPatients()
{
    $conn = getDBConnection();
    $res = $conn->query("SELECT COUNT(*) AS c FROM Patient");
    $c = 0;
    if ($res && ($row = $res->fetch_assoc())) { $c = (int)$row['c']; }
    if ($res) $res->free();
    $conn->close();
    return $c;
}

/**
 * Get patients by category (OPD/IPD/Emergency/Unknown).
 */
function getPatientsByCategory($category)
{
    $conn = getDBConnection();
    $sql = "SELECT PatientID, Name, Age, Gender, Contact, Address, PatientCategory
            FROM Patient
            WHERE PatientCategory = ?
            ORDER BY PatientID DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $category);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
    $stmt->close();
    $conn->close();
    return $rows;
}

/**
 * Search patients by name (simple LIKE, no regex).
 * Pass a plain substring (e.g., 'Rahman').
 */
function searchPatientsByName($nameSubstr)
{
    $conn = getDBConnection();
    $like = "%" . $nameSubstr . "%";
    $sql = "SELECT PatientID, Name, Age, Gender, Contact, Address, PatientCategory
            FROM Patient
            WHERE Name LIKE ?
            ORDER BY PatientID DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
    $stmt->close();
    $conn->close();
    return $rows;
}


function getAllPatients()
{
    $conn = getConnection();
    $sql = "SELECT p.PatientID, p.UserID, p.Name, p.Age, p.Gender, p.Contact, p.Address,
                   p.PatientCategory, p.CreatedAt, p.LinkedAt, p.Notes, u.Username AS LinkedUsername
            FROM Patient p
            LEFT JOIN `User` u ON u.UserID = p.UserID
            ORDER BY p.PatientID DESC";
    $res = $conn->query($sql);
    $rows = [];
    if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } $res->free(); }
    closeConnection($conn);
    return $rows;
}

function getPatientById($patientId)
{
    $conn = getConnection();
    $sql = "SELECT p.PatientID, p.UserID, p.Name, p.Age, p.Gender, p.Contact, p.Address,
                   p.PatientCategory, p.CreatedAt, p.LinkedAt, p.Notes, u.Username AS LinkedUsername
            FROM Patient p
            LEFT JOIN `User` u ON u.UserID = p.UserID
            WHERE p.PatientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $patientId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = null; if ($res) { $row = $res->fetch_assoc(); }
    $stmt->close();
    closeConnection($conn);
    return $row;
}

function addPatient($userId, $name, $age, $gender, $contact, $address, $patientCategory, $notes)
{
    $conn = getConnection();
    $sql = "INSERT INTO Patient
            (UserID, Name, Age, Gender, Contact, Address, PatientCategory, CreatedAt, Notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $uid = ($userId === null || $userId === '') ? null : (int)$userId;
    $stmt->bind_param('isisssss', $uid, $name, $age, $gender, $contact, $address, $patientCategory, $notes);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    return ($newId > 0) ? $newId : 0;
}

function updatePatient($patientId, $userId, $name, $age, $gender, $contact, $address, $patientCategory, $notes, $setLinkedAtNow)
{
    $conn = getConnection();
    $sql = "UPDATE Patient
            SET UserID = ?, Name = ?, Age = ?, Gender = ?, Contact = ?, Address = ?, PatientCategory = ?, Notes = ?";
    if ($setLinkedAtNow) { $sql .= ", LinkedAt = NOW()"; }
    $sql .= " WHERE PatientID = ?";
    $stmt = $conn->prepare($sql);
    $uid = ($userId === null || $userId === '') ? null : (int)$userId;
    $stmt->bind_param('isisssssi', $uid, $name, $age, $gender, $contact, $address, $patientCategory, $notes, $patientId);
    $stmt->execute();
    $ok = ($stmt->affected_rows >= 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}

function deletePatient($patientId)
{
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM Patient WHERE PatientID = ?");
    $stmt->bind_param('i', $patientId);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}
