
<?php
require_once __DIR__ . '/db.php';

/* ---------------- LOGIN ---------------- */
function login($user){
    $con = getConnection();

    $username = $user['username'];
    $password = $user['password'];

    $sql = "SELECT * FROM `User` WHERE Username = ? OR Email = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);

    if(!$stmt){
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ss", $username, $username);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);

    if($res && mysqli_num_rows($res) == 1){
        $row = mysqli_fetch_assoc($res);

        mysqli_stmt_close($stmt);
        mysqli_close($con);

        if(isset($row['Status']) && $row['Status'] !== 'Active'){
            return false;
        }

        // ✅ PLAIN TEXT CHECK (TESTING)
        if ($password === $row['Password']) {
            return $row; // return full user info
        }
        return false;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return false;
}

/* ---------------- PASSWORD RESET ---------------- */
function storeResetCode($email, $code){
    $con = getConnection();

    $sql = "SELECT UserID FROM `User` WHERE Email = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    if(!$stmt){
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if($res && mysqli_num_rows($res) == 1){
        $user = mysqli_fetch_assoc($res);
        $userID = (int)$user['UserID'];
        mysqli_stmt_close($stmt);

        $sqlInsert = "INSERT INTO PasswordReset (UserID, Code, Status) VALUES (?, ?, 'Pending')";
        $stmt2 = mysqli_prepare($con, $sqlInsert);
        if(!$stmt2){
            mysqli_close($con);
            return false;
        }

        mysqli_stmt_bind_param($stmt2, "is", $userID, $code);
        $ok = mysqli_stmt_execute($stmt2);

        mysqli_stmt_close($stmt2);
        mysqli_close($con);

        return $ok;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return false;
}

function verifyResetCode($email, $code){
    $con = getConnection();

    $sql = "SELECT pr.ResetID
            FROM PasswordReset pr
            JOIN `User` u ON pr.UserID = u.UserID
            WHERE u.Email = ? AND pr.Code = ? AND pr.Status = 'Pending'
            LIMIT 1";

    $stmt = mysqli_prepare($con, $sql);
    if(!$stmt){
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ss", $email, $code);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    if($res && mysqli_num_rows($res) == 1){
        $row = mysqli_fetch_assoc($res);
        $resetID = (int)$row['ResetID'];

        mysqli_stmt_close($stmt);
        mysqli_close($con);
        return $resetID;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return false;
}

function updatePassword($email, $newPassword){
    $con = getConnection();

    // plain text (testing)
    $sql = "UPDATE `User` SET Password = ? WHERE Email = ?";
    $stmt = mysqli_prepare($con, $sql);
    if(!$stmt){
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ss", $newPassword, $email);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $ok;
}

function markCodeUsed($resetID){
    $con = getConnection();

    $sql = "UPDATE PasswordReset SET Status = 'Used' WHERE ResetID = ?";
    $stmt = mysqli_prepare($con, $sql);
    if(!$stmt){
        mysqli_close($con);
        return false;
    }

    $rid = (int)$resetID;
    mysqli_stmt_bind_param($stmt, "i", $rid);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $ok;
}

function getUserByUsername($username){
    $con = getConnection();

    $sql = "SELECT UserID, Name, Username, Role, Status
            FROM `User`
            WHERE Username = ?
            LIMIT 1";

    $stmt = mysqli_prepare($con, $sql);
    if(!$stmt){
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $user = $res ? mysqli_fetch_assoc($res) : false;

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    if(!$user) return false;
    if(isset($user['Status']) && $user['Status'] !== 'Active') return false;

    return $user;
}


/* ---------------- CHECK IF USERNAME OR EMAIL EXISTS ---------------- */
function userExists($username, $email){
    $con = getConnection();

    $sql = "SELECT UserID FROM `User` WHERE Username = ? OR Email = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);

    if(!$stmt){
        mysqli_close($con);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $exists = false;

    if($res && mysqli_num_rows($res) > 0){
        $exists = true;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $exists;
}

/* ---------------- ADD USER / SIGNUP ---------------- */
function addUser($user){
    $con = getConnection();

    $sql = "INSERT INTO `User` (Name, Username, Password, Email, DOB, Gender, Address, Status, Role)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Active', 'Patient')";

    $stmt = mysqli_prepare($con, $sql);
    if(!$stmt){
        mysqli_close($con);
        return false;
    }

    $name    = $user['name'];
    $uname   = $user['username'];
    $pass    = $user['password']; // plain text for now
    $email   = $user['email'];
    $dob     = $user['dob'];
    $gender  = $user['gender'];
    $address = $user['address'];

    mysqli_stmt_bind_param($stmt, "sssssss", $name, $uname, $pass, $email, $dob, $gender, $address);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $ok;
}
?>
