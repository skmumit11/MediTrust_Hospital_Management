<?php
// models/monitoringReportingModel.php
require_once "../config/db.php"; // Include your database connection

// Fetch all users
function getAllUsers() {
    $conn = dbConnection();
    $query = "SELECT UserID, Name, Username, Role, Status FROM User";
    $result = mysqli_query($conn, $query);
    $users = [];
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    mysqli_close($conn);
    return $users;
}

// Fetch last 50 audit logs
function getAuditLogs($limit = 50) {
    $conn = dbConnection();
    $query = "SELECT a.*, u.Name as UserName FROM AuditLog a 
              LEFT JOIN User u ON a.UserID = u.UserID
              ORDER BY a.Timestamp DESC LIMIT $limit";
    $result = mysqli_query($conn, $query);
    $logs = [];
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $logs[] = $row;
        }
    }
    mysqli_close($conn);
    return $logs;
}

// Default session timeout
function getSessionTimeout() {
    return 10; // minutes
}

// Roles
function getRoles() {
    return ['Admin','Doctor','Patient','Staff','Cashier'];
}
?>
