<?php
// models/userModel.php
require_once 'db6_10.php';

function login($user)
{
    $con = getConnection();

    $username = $user['username'];
    $password = $user['password'];

    // Check for both Username or Email
    $sql = "SELECT * FROM User WHERE (Username = ? OR Email = ?) AND Password = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $username, $username, $password);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $userRow = mysqli_fetch_assoc($result);

    mysqli_close($con);
    return $userRow;
}
?>