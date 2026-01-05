
<?php
// models/auditModel.php
require_once __DIR__ . '/db.php';

function addAuditLog($userId, $action, $tableAffected, $recordId, $details) {
    $conn = getConnection();
    mysqli_set_charset($conn, "utf8mb4");

    $sql = "INSERT INTO AuditLog (UserID, Action, TableAffected, RecordID, Timestamp, Details)
            VALUES (?, ?, ?, ?, NOW(), ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issis", $userId, $action, $tableAffected, $recordId, $details);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
