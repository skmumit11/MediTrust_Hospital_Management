<?php
// models/staffModel6_10.php
require_once 'db6_10.php';

function getDepartmentList()
{
    $con = getConnection();
    $sql = "select * from Department";
    $result = mysqli_query($con, $sql);
    $deps = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $deps[] = $row;
    }
    return $deps;
}

function getEligibleStaffUsers()
{
    $con = getConnection();
    $sql = "select * from User where Role != 'Patient'";
    $result = mysqli_query($con, $sql);
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    return $users;
}

function getStaffProfileList()
{
    $con = getConnection();
    $sql = "select StaffProfile.StaffID, User.Name as StaffName, User.Email as ContactEmail, Department.Name as DepartmentName, StaffProfile.RoleAssignment 
            from StaffProfile, User, Department 
            where StaffProfile.StaffID = User.UserID 
            and StaffProfile.DepartmentID = Department.DepartmentID";
    $result = mysqli_query($con, $sql);
    $list = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}

function getStaffProfileById($staffId)
{
    $con = getConnection();
    $sql = "select StaffProfile.*, User.Name as StaffName, User.Email as ContactEmail 
            from StaffProfile, User 
            where StaffProfile.StaffID = User.UserID 
            and StaffProfile.StaffID = {$staffId}";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row;
}

function createStaffProfile($staffId, $departmentId, $roleAssignment, $contactEmail)
{
    $con = getConnection();

    // Check for duplicate ID manually
    $checkSql = "select * from StaffProfile where StaffID = {$staffId}";
    $checkResult = mysqli_query($con, $checkSql);
    $count = mysqli_num_rows($checkResult);

    if ($count > 0) {
        return "DUPLICATE_ID";
    }

    // Insert
    $sql = "insert into StaffProfile values({$staffId}, {$departmentId}, '{$roleAssignment}')";

    if (mysqli_query($con, $sql)) {
        // Update email
        $sql2 = "update User set Email = '{$contactEmail}' where UserID = {$staffId}";
        mysqli_query($con, $sql2);
        return true;
    } else {
        return false;
    }
}

function updateStaffProfile($staffId, $departmentId, $roleAssignment, $contactEmail)
{
    $con = getConnection();

    $sql = "update StaffProfile set DepartmentID = {$departmentId}, RoleAssignment = '{$roleAssignment}' where StaffID = {$staffId}";

    if (mysqli_query($con, $sql)) {
        $sql2 = "update User set Email = '{$contactEmail}' where UserID = {$staffId}";
        mysqli_query($con, $sql2);
        return true;
    } else {
        return false;
    }
}

function deleteStaffProfile($staffId)
{
    $con = getConnection();
    $sql = "delete from StaffProfile where StaffID = {$staffId}";
    if (mysqli_query($con, $sql)) {
        return true;
    } else {
        return false;
    }
}
?>