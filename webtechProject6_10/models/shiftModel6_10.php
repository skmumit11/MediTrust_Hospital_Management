
<?php
// models/shiftModel.php
require_once 'db6_10.php';

/**
 * Staff selector list using StaffProfile + User + Department [1](https://aiubedu60714-my.sharepoint.com/personal/23-50435-1_student_aiub_edu/Documents/Microsoft%20Copilot%20Chat%20Files/Table_list_meditrust_db%203.docx)
 */
function getStaffSelectorList() {
    $con = getConnection();

    $sql = "SELECT sp.StaffID, u.Name AS StaffName, u.Email AS ContactEmail, d.Name AS DepartmentName
            FROM StaffProfile sp
            JOIN `User` u ON u.UserID = sp.StaffID
            LEFT JOIN Department d ON d.DepartmentID = sp.DepartmentID
            ORDER BY u.Name ASC";

    $result = mysqli_query($con, $sql);

    $list = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
    }

    mysqli_close($con);
    return $list;
}

/**
 * Create shift assignment (ShiftAssignment table exists) [1](https://aiubedu60714-my.sharepoint.com/personal/23-50435-1_student_aiub_edu/Documents/Microsoft%20Copilot%20Chat%20Files/Table_list_meditrust_db%203.docx)
 */
function createShiftAssignment($staffId, $shiftType, $startTime, $endTime) {
    $con = getConnection();

    $sql = "INSERT INTO ShiftAssignment (StaffID, ShiftType, StartTime, EndTime)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "isss", $staffId, $shiftType, $startTime, $endTime);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $ok;
}

/**
 * Create simple notification record (Notification table exists) [1](https://aiubedu60714-my.sharepoint.com/personal/23-50435-1_student_aiub_edu/Documents/Microsoft%20Copilot%20Chat%20Files/Table_list_meditrust_db%203.docx)
 */
function createShiftNotification($recipientUserId) {
    $con = getConnection();

    $sql = "INSERT INTO Notification (RecipientUserID, Channel, ScheduledAt, SentAt)
            VALUES (?, 'App', NOW(), NULL)";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $recipientUserId);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $ok;
}

/**
 * Recent shifts list
 */
function getRecentShifts($limit = 20) {
    $con = getConnection();

    $limit = (int)$limit;
    if ($limit <= 0) { $limit = 20; }

    $sql = "SELECT sa.ShiftID, sa.StaffID, sa.ShiftType, sa.StartTime, sa.EndTime,
                   u.Name AS StaffName
            FROM ShiftAssignment sa
            JOIN `User` u ON u.UserID = sa.StaffID
            ORDER BY sa.ShiftID DESC
            LIMIT $limit";

    $result = mysqli_query($con, $sql);

    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }

    mysqli_close($con);
    return $rows;
}
