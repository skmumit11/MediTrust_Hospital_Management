<?php
// models/patientModel.php
require_once 'db.php';





/* -----------------------------------------------------------
   COUNTS & UTILITIES
----------------------------------------------------------- */

function countPatients()
{
    $conn = getConnection();
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
    $conn = getConnection();
    $category = trim($category);
    $sql = "SELECT PatientID, Name, Age, Gender, Contact, Address, PatientCategory
            FROM Patient
            WHERE PatientCategory = '$category'
            ORDER BY PatientID DESC";
    $res = mysqli_query($conn, $sql);
    $rows = [];
    if($res){
        while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
    }
    $conn->close();
    return $rows;
}

/**
 * Search patients by name (simple LIKE, no regex).
 * Pass a plain substring (e.g., 'Rahman').
 */
function searchPatientsByName($nameSubstr)
{
    $conn = getConnection();
    $nameSubstr = trim($nameSubstr);
    
    $sql = "SELECT PatientID, Name, Age, Gender, Contact, Address, PatientCategory
            FROM Patient
            WHERE Name LIKE '%$nameSubstr%'
            ORDER BY PatientID DESC";
    $res = mysqli_query($conn, $sql);
    $rows = [];
    if($res){
        while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
    }
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
    $patientId = (int)$patientId;
    $sql = "SELECT p.PatientID, p.UserID, p.Name, p.Age, p.Gender, p.Contact, p.Address,
                   p.PatientCategory, p.CreatedAt, p.LinkedAt, p.Notes, u.Username AS LinkedUsername
            FROM Patient p
            LEFT JOIN `User` u ON u.UserID = p.UserID
            WHERE p.PatientID = $patientId";
    $res = mysqli_query($conn, $sql);
    $row = null; if ($res) { $row = mysqli_fetch_assoc($res); }
    closeConnection($conn);
    return $row;
}

function addPatient($userId, $name, $age, $gender, $contact, $address, $patientCategory, $notes)
{
    $conn = getConnection();
    
    $uid = ($userId === null || $userId === '') ? 'NULL' : (int)$userId;
    $name = trim($name);
    $age = (int)$age;
    $gender = trim($gender);
    $contact = trim($contact);
    $address = trim($address);
    $patientCategory = trim($patientCategory);
    $notes = trim($notes);

    $sql = "INSERT INTO Patient
            (UserID, Name, Age, Gender, Contact, Address, PatientCategory, CreatedAt, Notes)
            VALUES ($uid, '$name', $age, '$gender', '$contact', '$address', '$patientCategory', NOW(), '$notes')";
    
    mysqli_query($conn, $sql);
    $newId = mysqli_insert_id($conn);
    closeConnection($conn);
    return ($newId > 0) ? $newId : 0;
}

function updatePatient($patientId, $userId, $name, $age, $gender, $contact, $address, $patientCategory, $notes, $setLinkedAtNow)
{
    $conn = getConnection();
    $patientId = (int)$patientId;
    $uid = ($userId === null || $userId === '') ? 'NULL' : (int)$userId;
    $name = trim($name);
    $age = (int)$age;
    $gender = trim($gender);
    $contact = trim($contact);
    $address = trim($address);
    $patientCategory = trim($patientCategory);
    $notes = trim($notes);
    
    $sql = "UPDATE Patient
            SET UserID = $uid, Name = '$name', Age = $age, Gender = '$gender', Contact = '$contact', Address = '$address', PatientCategory = '$patientCategory', Notes = '$notes'";
    if ($setLinkedAtNow) { $sql .= ", LinkedAt = NOW()"; }
    $sql .= " WHERE PatientID = $patientId";
    
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) >= 0);
    closeConnection($conn);
    return $ok;
}

function deletePatient($patientId)
{
    $conn = getConnection();
    $patientId = (int)$patientId;
    $sql = "DELETE FROM Patient WHERE PatientID = $patientId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);
    closeConnection($conn);
    return $ok;
}
