<?php
require_once('db.php');

/* ---------------- LOGIN ---------------- */
function login($user){
    $con = getConnection();

    $username = $user['username'];
    $password = $user['password'];

    $sql = "SELECT * FROM User WHERE Username='$username' OR Email='$username' LIMIT 1";
    $result = mysqli_query($con, $sql);

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);

        // 🔴 HASH CHECK DISABLED (TEST ONLY)
        // if(password_verify($password, $row['Password'])){

        // ✅ PLAIN TEXT CHECK
        if ($password === $row['Password']) {
            return $row; // return full user info
        }
    }
    return false;
}

/* ---------------- ADD USER / SIGNUP ---------------- */
function addUser($user){
    $con = getConnection();

    // 🔴 HASHING DISABLED (TEST ONLY)
    // $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO User (Name, Username, Password, Email, DOB, Gender, Address, Status, Role)
            VALUES (
                '{$user['name']}',
                '{$user['username']}',
                '{$user['password']}',   -- plain text password
                '{$user['email']}',
                '{$user['dob']}',
                '{$user['gender']}',
                '{$user['address']}',
                'Active',
                'Patient'
            )";

    return mysqli_query($con, $sql);
}

/* ---------------- PASSWORD RESET ---------------- */
function storeResetCode($email, $code){
    $con = getConnection();
    $sql = "SELECT UserID FROM User WHERE Email='$email' LIMIT 1";
    $res = mysqli_query($con, $sql);

    if(mysqli_num_rows($res) == 1){
        $user = mysqli_fetch_assoc($res);
        $userID = $user['UserID'];

        $sqlInsert = "INSERT INTO PasswordReset (UserID, Code, Status)
                      VALUES ($userID, '$code', 'Pending')";
        return mysqli_query($con, $sqlInsert);
    }
    return false;
}

function verifyResetCode($email, $code){
    $con = getConnection();
    $sql = "SELECT pr.ResetID FROM PasswordReset pr
            JOIN User u ON pr.UserID = u.UserID
            WHERE u.Email='$email' AND pr.Code='$code' AND pr.Status='Pending' LIMIT 1";
    $res = mysqli_query($con, $sql);

    if(mysqli_num_rows($res) == 1){
        return mysqli_fetch_assoc($res)['ResetID'];
    }
    return false;
}

function updatePassword($email, $newPassword){
    $con = getConnection();

    // 🔴 HASHING DISABLED (TEST ONLY)
    // $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "UPDATE User SET Password='$newPassword' WHERE Email='$email'";
    return mysqli_query($con, $sql);
}

function markCodeUsed($resetID){
    $con = getConnection();
    $sql = "UPDATE PasswordReset SET Status='Used' WHERE ResetID=$resetID";
    return mysqli_query($con, $sql);
}
?>
