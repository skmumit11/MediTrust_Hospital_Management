<?php
require_once 'models/db16_18.php';
$con = getConnection();
$res = mysqli_query($con, "SHOW TABLES");
while ($row = mysqli_fetch_row($res)) {
    echo "Table: " . $row[0] . "\n";
    $cols = mysqli_query($con, "DESCRIBE " . $row[0]);
    while ($col = mysqli_fetch_assoc($cols)) {
        echo " - " . $col['Field'] . "\n";
    }
}
?>