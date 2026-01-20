<?php
require_once('db16_18.php');

function preference_get($userID)
{
    $con = getConnection();
    $uid = (int) $userID;
    $res = mysqli_query($con, "SELECT * FROM NotificationPreference WHERE UserID=$uid LIMIT 1");
    return mysqli_fetch_assoc($res);
}

function preference_set($userID, $quietHours, $language)
{
    $con = getConnection();
    $uid = (int) $userID;
    $qh = mysqli_real_escape_string($con, $quietHours);
    $lang = mysqli_real_escape_string($con, $language);

    $exists = mysqli_query($con, "SELECT 1 FROM NotificationPreference WHERE UserID=$uid LIMIT 1");
    if (mysqli_num_rows($exists) > 0) {
        mysqli_query($con, "UPDATE NotificationPreference SET QuietHours='$qh', Language='$lang' WHERE UserID=$uid");
    } else {
        mysqli_query($con, "INSERT INTO NotificationPreference (UserID, QuietHours, Language) VALUES ($uid,'$qh','$lang')");
    }
}