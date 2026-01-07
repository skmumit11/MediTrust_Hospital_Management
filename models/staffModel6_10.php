
<?php
// models/staffModel.php
require_once 'db6_10.php';

/**
 * Department list (Department table exists) [1](https://aiubedu60714-my.sharepoint.com/personal/23-50435-1_student_aiub_edu/Documents/Microsoft%20Copilot%20Chat%20Files/Table_list_meditrust_db%203.docx)
 */
function getDepartmentList() {
    $con = getConnection();
    $sql = "SELECT DepartmentID, Name FROM Department ORDER BY Name ASC";
    $result = mysqli_query($con, $sql);

    $deps = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $deps[] = $row;
        }
    }

    mysqli_close($con);
    return $deps;
}

/**
 * Eligible staff users (from User table) [1](https://aiubedu60714-my.sharepoint.com/personal/23-50435-1_student_aiub_edu/Documents/Microsoft%20Copilot%20Chat%20Files/Table_list_meditrust_db%203.docx)
 * You can change role filtering if needed.
 */
function getEligibleStaffUsers() {
    $con = getConnection();

    $sql = "SELECT UserID, Name, Email, Role
            FROM `User`
            WHERE Role <> 'Patient'
            ORDER BY Name ASC";

    $result = mysqli_query($con, $sql);

    $users = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }

    mysqli_close($con);
    return $users;
}

/**
 * Staff List = StaffProfile + User + Department [1](https://aiubedu60714-my.sharepoint.com/personal/23-50435-1_student_aiub_edu/Documents/Microsoft%20Copilot%20Chat%20Files/Table_list_meditrust_db%203.docx)
 */
function getStaffProfileList() {
    $con = getConnection();

    $sql = "SELECT sp.StaffID, sp.RoleAssignment, sp.DepartmentID,
                   u.Name AS StaffName, u.Email AS ContactEmail, u.Role AS UserRole,
                   d.Name AS DepartmentName
            FROM StaffProfile sp
            JOIN `User` u ON u.UserID = sp.StaffID
            LEFT JOIN Department d ON d.DepartmentID = sp.DepartmentID
            ORDER BY sp.StaffID DESC";

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
 * Single staff profile
 */
function getStaffProfileById($staffId) {
    $con = getConnection();

    $sql = "SELECT sp.StaffID, sp.RoleAssignment, sp.DepartmentID,
                   u.Name AS StaffName, u.Email AS ContactEmail, u.Role AS UserRole
            FROM StaffProfile sp
            JOIN `User` u ON u.UserID = sp.StaffID
            WHERE sp.StaffID = ? LIMIT 1";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        mysqli_close($con);
        return null;
    }

    mysqli_stmt_bind_param($stmt, "i", $staffId);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $row;
}

/**
 * Create staff profile (StaffProfile table exists) [1](https://aiubedu60714-my.sharepoint.com/personal/23-50435-1_student_aiub_edu/Documents/Microsoft%20Copilot%20Chat%20Files/Table_list_meditrust_db%203.docx)
 * Contact is stored in User.Email (since StaffProfile has no contact column) [1](https://aiubedu60714-my.sharepoint.com/personal/23-50435-1_student_aiub_edu/Documents/Microsoft%20Copilot%20Chat%20Files/Table_list_meditrust_db%203.docx)
 */
function createStaffProfile($staffId, $departmentId, $roleAssignment, $contactEmail) {
    $con = getConnection();

    // Update email/contact in User table
    $sqlU = "UPDATE `User` SET Email=? WHERE UserID=?";
    $stmtU = mysqli_prepare($con, $sqlU);
    if ($stmtU) {
        mysqli_stmt_bind_param($stmtU, "si", $contactEmail, $staffId);
        mysqli_stmt_execute($stmtU);
        mysqli_stmt_close($stmtU);
    }

    // Insert StaffProfile
    $sql = "INSERT INTO StaffProfile (StaffID, DepartmentID, RoleAssignment)
            VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "iis", $staffId, $departmentId, $roleAssignment);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $ok;
}

/**
 * Update staff profile + contact(email)
 */
function updateStaffProfile($staffId, $departmentId, $roleAssignment, $contactEmail) {
    $con = getConnection();

    $sqlU = "UPDATE `User` SET Email=? WHERE UserID=?";
    $stmtU = mysqli_prepare($con, $sqlU);
    if ($stmtU) {
        mysqli_stmt_bind_param($stmtU, "si", $contactEmail, $staffId);
        mysqli_stmt_execute($stmtU);
        mysqli_stmt_close($stmtU);
    }

    $sql = "UPDATE StaffProfile
            SET DepartmentID=?, RoleAssignment=?
            WHERE StaffID=?";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "isi", $departmentId, $roleAssignment, $staffId);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $ok;
}

/**
 * Delete staff profile
 */
function deleteStaffProfile($staffId) {
    $con = getConnection();

    $sql = "DELETE FROM StaffProfile WHERE StaffID=?";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $staffId);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $ok;
}
