<?php
require_once('db.php');

/* ---------------- UPDATE PASSWORD BY EMAIL ---------------- */
function updatePasswordByEmail($email, $newPassword){
    $con = getConnection();
    
    // Trim email
    $emailEscaped = trim($email);

    // Hash the password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "UPDATE User SET Password='$hashedPassword' WHERE Email='$emailEscaped'";
    return mysqli_query($con, $sql);
}

/* ---------------- CHECK IF EMAIL EXISTS ---------------- */
function checkEmailExists($email){
    $con = getConnection();
    $emailEscaped = trim($email);
    $sql = "SELECT * FROM User WHERE Email='$emailEscaped' LIMIT 1";
    $result = mysqli_query($con, $sql);
    if(mysqli_num_rows($result) == 1){
        return mysqli_fetch_assoc($result);
    }
    return false;
}
?>
