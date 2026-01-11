<?php
$host   = "127.0.0.1";   // or 127.0.0.1
$dbname = "meditrust_db";
$dbuser = "root";
$dbpass = "";

/**
 * Get MySQL database connection
 * @return mysqli
 */
function getConnection() {
    global $host, $dbname, $dbuser, $dbpass;

    $con = mysqli_connect($host, $dbuser, $dbpass, $dbname);

    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    return $con;
}
?>
