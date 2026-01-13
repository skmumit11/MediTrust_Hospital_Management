<?php
require_once 'models/db16_18.php';
$con = getConnection();
$res = mysqli_query($con, "DESCRIBE Doctor");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        echo $row['Field'] . "\n";
    }
} else {
    echo "Error: " . mysqli_error($con);
}
?>