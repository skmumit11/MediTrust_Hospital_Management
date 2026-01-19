<?php
require_once ('db.php');

/* ---------------- LOGIN ---------------- */
function login($user){
    $con = getConnection();

    $username = trim($user['username']);
    $password = $user['password'];

    $sql = "SELECT * FROM `User` WHERE Username = '$username' OR Email = '$username' LIMIT 1";
    $res = mysqli_query($con, $sql);

    if($res && mysqli_num_rows($res) == 1){
        $row = mysqli_fetch_assoc($res);
        $con->close();

        if(isset($row['Status']) && $row['Status'] !== 'Active'){
            return false;
        }

        // ✅ PLAIN TEXT CHECK (TESTING)
        if ($password === $row['Password']) {
            return $row; // return full user info
        }
        return false;
    }
    $con->close();
    return false;
}

/* ---------------- PASSWORD RESET ---------------- */
function storeResetCode($email, $code){
    $con = getConnection();
    $email = trim($email);
    $code = trim($code);

    $sql = "SELECT UserID FROM `User` WHERE Email = '$email' LIMIT 1";
    $res = mysqli_query($con, $sql);

    if($res && mysqli_num_rows($res) == 1){
        $user = mysqli_fetch_assoc($res);
        $userID = (int)$user['UserID'];
        
        $sqlInsert = "INSERT INTO PasswordReset (UserID, Code, Status) VALUES ($userID, '$code', 'Pending')";
        $ok = mysqli_query($con, $sqlInsert);
        $con->close();

        return $ok;
    }

    $con->close();
    return false;
}

function verifyResetCode($email, $code){
    $con = getConnection();
    $email = trim($email);
    $code = trim($code);

    $sql = "SELECT pr.ResetID
            FROM PasswordReset pr
            JOIN `User` u ON pr.UserID = u.UserID
            WHERE u.Email = '$email' AND pr.Code = '$code' AND pr.Status = 'Pending'
            LIMIT 1";

    $res = mysqli_query($con, $sql);

    if($res && mysqli_num_rows($res) == 1){
        $row = mysqli_fetch_assoc($res);
        $resetID = (int)$row['ResetID'];
        $con->close();
        return $resetID;
    }

    $con->close();
    return false;
}

function updatePassword($email, $newPassword){
    $con = getConnection();
    $email = trim($email);
    $newPassword = trim($newPassword);

    // plain text (testing)
    $sql = "UPDATE `User` SET Password = '$newPassword' WHERE Email = '$email'";
    $ok = mysqli_query($con, $sql);
    $con->close();

    return $ok;
}

function markCodeUsed($resetID){
    $con = getConnection();
    $rid = (int)$resetID;

    $sql = "UPDATE PasswordReset SET Status = 'Used' WHERE ResetID = $rid";
    $ok = mysqli_query($con, $sql);
    $con->close();

    return $ok;
}

function getUserByUsername($username){
    $con = getConnection();
    $username = trim($username);

    $sql = "SELECT UserID, Name, Username, Role, Status
            FROM `User`
            WHERE Username = '$username'
            LIMIT 1";

    $res = mysqli_query($con, $sql);
    $user = $res ? mysqli_fetch_assoc($res) : false;
    $con->close();

    if(!$user) return false;
    if(isset($user['Status']) && $user['Status'] !== 'Active') return false;

    return $user;
}


/* ---------------- CHECK IF USERNAME OR EMAIL EXISTS ---------------- */
function userExists($username, $email){
    $con = getConnection();
    $username = trim($username);
    $email = trim($email);

    $sql = "SELECT UserID FROM `User` WHERE Username = '$username' OR Email = '$email' LIMIT 1";
    $res = mysqli_query($con, $sql);
    $exists = false;

    if($res && mysqli_num_rows($res) > 0){
        $exists = true;
    }
    $con->close();

    return $exists;
}

/* ---------------- ADD USER / SIGNUP ---------------- */
function addUser($user){
    $con = getConnection();
    
    $name    = trim($user['name']);
    $uname   = trim($user['username']);
    $pass    = trim($user['password']); // plain text for now
    $email   = trim($user['email']);
    $dob     = trim($user['dob']);
    $gender  = trim($user['gender']);
    $address = trim($user['address']);

    $sql = "INSERT INTO `User` (Name, Username, Password, Email, DOB, Gender, Address, Status, Role)
            VALUES ('$name', '$uname', '$pass', '$email', '$dob', '$gender', '$address', 'Active', 'Patient')";

    $ok = mysqli_query($con, $sql);
    $con->close();

    return $ok;
}
?>
