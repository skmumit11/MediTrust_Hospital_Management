
<?php
// models/labModel.php
require_once 'db6_10.php';

function createLabTestResult($patientId, $testType, $resultText) {
    $conn = getConnection();
    mysqli_set_charset($conn, "utf8mb4");

    $sql = "INSERT INTO LabTestResult (PatientID, TestType, Result, Timestamp)
            VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $patientId, $testType, $resultText);
    $ok = mysqli_stmt_execute($stmt);

    $newId = 0;
    if ($ok) {
        $newId = mysqli_insert_id($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $newId;
}

function getDistinctTestTypes() {
    $conn = getConnection();
    mysqli_set_charset($conn, "utf8mb4");

    $sql = "SELECT DISTINCT TestType FROM LabTestResult ORDER BY TestType ASC";
    $res = mysqli_query($conn, $sql);

    $types = [];
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $types[] = $r['TestType'];
        }
    }

    mysqli_close($conn);
    return $types;
}

function getLabResultById($resultId) {
    $conn = getConnection();
    mysqli_set_charset($conn, "utf8mb4");

    $sql = "SELECT l.ResultID, l.PatientID, l.TestType, l.Result, l.Timestamp,
                   p.Name AS PatientName, p.Contact AS PatientContact
            FROM LabTestResult l
            JOIN Patient p ON p.PatientID = l.PatientID
            WHERE l.ResultID = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $resultId);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $row;
}

function filterLabResults($dateFilter, $testType, $patientFilter) {
    $conn = getConnection();
    mysqli_set_charset($conn, "utf8mb4");

    $where = [];
    $params = [];
    $types  = "";

    if ($dateFilter !== "") {
        $where[] = "DATE(l.Timestamp) = ?";
        $types .= "s";
        $params[] = $dateFilter;
    }

    if ($testType !== "") {
        $where[] = "l.TestType = ?";
        $types .= "s";
        $params[] = $testType;
    }

    if ($patientFilter !== "") {
        if (ctype_digit($patientFilter)) {
            $where[] = "p.PatientID = ?";
            $types .= "i";
            $params[] = (int)$patientFilter;
        } else {
            $where[] = "(p.Name LIKE ? OR p.Contact LIKE ?)";
            $types .= "ss";
            $kw = "%" . $patientFilter . "%";
            $params[] = $kw;
            $params[] = $kw;
        }
    }

    $sql = "SELECT l.ResultID, l.PatientID, p.Name AS PatientName, p.Contact AS PatientContact,
                   l.TestType, l.Timestamp, l.Result
            FROM LabTestResult l
            JOIN Patient p ON p.PatientID = l.PatientID";

    if (count($where) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY l.Timestamp DESC, l.ResultID DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (count($params) > 0) {
        $bindArgs = [];
        $bindArgs[] = $types;

        for ($i = 0; $i < count($params); $i++) {
            $bindArgs[] = &$params[$i];
        }

        call_user_func_array([$stmt, 'bind_param'], $bindArgs);
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

function createLabReportRecord($filtersText, $generatedByUserId) {
    $conn = getConnection();
    mysqli_set_charset($conn, "utf8mb4");

    $sql = "INSERT INTO LabReport (Filters, GeneratedBy, GeneratedAt)
            VALUES (?, ?, NOW())";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $filtersText, $generatedByUserId);
    mysqli_stmt_execute($stmt);

    $newId = mysqli_insert_id($conn);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $newId;
}
