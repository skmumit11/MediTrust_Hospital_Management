<?php
require_once('db11_15.php');

function getAllPatients() {
    $con = getConnection();
    // Patient table has Name, Age, Gender, Contact, Address
    // User table has Email. 
    // We'll join them to get Email if needed, or just use Patient table data.
    // The requirement is "Show patient list (ID, Name, Gender, Contact)".
    // Patient table has all these. Contact is phone. User table has Email.
    // Let's fetch both.
    $sql = "SELECT p.*, u.Email FROM Patient p LEFT JOIN User u ON p.UserID = u.UserID"; 
    $result = mysqli_query($con, $sql);
    
    $patients = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $patients[] = $row;
        }
    }
    return $patients;
}

function searchPatients($term) {
    $con = getConnection();
    $term = mysqli_real_escape_string($con, $term);
    $sql = "SELECT p.*, u.Email FROM Patient p LEFT JOIN User u ON p.UserID = u.UserID WHERE p.Name LIKE '%$term%' OR p.PatientID LIKE '%$term%'";
    $result = mysqli_query($con, $sql);
    
    $patients = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $patients[] = $row;
        }
    }
    return $patients;
}

function getPatientById($id) {
    $con = getConnection();
    $id = (int)$id;
    $sql = "SELECT p.*, u.Email FROM Patient p LEFT JOIN User u ON p.UserID = u.UserID WHERE p.PatientID=$id";
    $result = mysqli_query($con, $sql);
    return mysqli_fetch_assoc($result);
}

function updatePatientContact($id, $contact, $address) {
    $con = getConnection();
    $id = (int)$id;
    $contact = mysqli_real_escape_string($con, $contact); // 'Contact' in Patient table (Phone)
    $address = mysqli_real_escape_string($con, $address); // 'Address' in Patient table
    
    // Requirement says "contact/address". In previous view this was Email/Address.
    // Patient table has 'Contact' (Phone). 'User' has 'Email'.
    // If input is email-like, maybe update User email? 
    // But usually 'Contact' implies Phone in this schema (+880...).
    // I'll update Patient.Contact and Patient.Address.
    
    $sql = "UPDATE Patient SET Contact='$contact', Address='$address' WHERE PatientID=$id";
    return mysqli_query($con, $sql);
}
?>
