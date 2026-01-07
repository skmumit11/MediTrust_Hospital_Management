<?php
require_once('db16_18.php');

function user_get_by_username($username)
{
    $con = getConnection();
    $usernameEsc = mysqli_real_escape_string($con, $username);
    $sql = "SELECT * FROM User WHERE Username='$usernameEsc' LIMIT 1";
    $res = mysqli_query($con, $sql);
    return mysqli_fetch_assoc($res);
}

function doctor_get_all()
{
    $con = getConnection();
    $sql = "SELECT d.DoctorID, u.Name 
            FROM Doctor d
            JOIN User u ON d.DoctorID = u.UserID 
            ORDER BY u.Name ASC";
    return mysqli_query($con, $sql);
}

function doctor_exists($doctorID)
{
    $con = getConnection();
    $did = (int) $doctorID;
    $res = mysqli_query($con, "SELECT DoctorID FROM Doctor WHERE DoctorID=$did");
    return mysqli_num_rows($res) > 0;
}
