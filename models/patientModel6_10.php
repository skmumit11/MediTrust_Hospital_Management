
<?php
// models/patientModel.php
require_once 'db6_10.php';

function getPatientById($patientId) {
    $conn = getConnection();
    mysqli_set_charset($conn, "utf8mb4");

    $sql = "SELECT PatientID, Name, Contact, Gender, Age, Address 
            FROM Patient 
            WHERE PatientID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $patientId);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $row;
}

function searchPatients($keyword, $limit = 20) {
    $conn = getConnection();
    mysqli_set_charset($conn, "utf8mb4");

    $limit = (int)$limit;
    if ($limit < 1) { $limit = 20; }
    if ($limit > 100) { $limit = 100; }

    if ($keyword !== "" && ctype_digit($keyword)) {
        $pid = (int)$keyword;
        $sql = "SELECT PatientID, Name, Contact
                FROM Patient 
                WHERE PatientID = ?
                LIMIT $limit";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $pid);
    } else {
        $kw = "%" . $keyword . "%";
        $sql = "SELECT PatientID, Name, Contact
                FROM Patient
                WHERE Name LIKE ? OR Contact LIKE ?
                ORDER BY PatientID DESC
                LIMIT $limit";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $kw, $kw);
    }

    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $rows;
}
