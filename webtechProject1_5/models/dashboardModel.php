<?php
require_once ("db.php");

function getTotalDoctors() {
    $con = getConnection();
    $res = mysqli_query($con, "SELECT COUNT(*) AS total FROM User WHERE Role='Doctor'");
    $row = mysqli_fetch_assoc($res);
    return $row['total'];
}

function getTotalPatients() {
    $con = getConnection();
    $res = mysqli_query($con, "SELECT COUNT(*) AS total FROM Patient");
    $row = mysqli_fetch_assoc($res);
    return $row['total'];
}
/*
function getAvailableBeds() {
    $con = getConnection();
    $res = mysqli_query($con, "SELECT COUNT(*) AS total FROM BedInventory WHERE Status='Available'");
    $row = mysqli_fetch_assoc($res);
    return $row['total'];
}
*/