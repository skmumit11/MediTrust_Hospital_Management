<?php
require_once 'models/db16_18.php';

$con = getConnection();

// Check if column exists
$checkQuery = "SHOW COLUMNS FROM Notification LIKE 'TargetNumber'";
$result = mysqli_query($con, $checkQuery);

if (mysqli_num_rows($result) == 0) {
    $sql = "ALTER TABLE Notification ADD COLUMN TargetNumber VARCHAR(20) DEFAULT NULL";
    if (mysqli_query($con, $sql)) {
        echo "Successfully added TargetNumber column to Notification table.\n";
    } else {
        echo "Error adding column: " . mysqli_error($con) . "\n";
    }
} else {
    echo "Column TargetNumber already exists.\n";
}
?>