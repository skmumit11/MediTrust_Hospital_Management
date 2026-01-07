<?php
require_once('db11_15.php');

function login($user){
    $con = getConnection();
    $username = mysqli_real_escape_string($con, $user['username']);
    $password = mysqli_real_escape_string($con, $user['password']);
    
    // Schema: User table (UserID, Name, Username, Password, Email, Role...)
    $sql = "SELECT * FROM User WHERE (Username='{$username}' OR Email='{$username}') AND Password='{$password}'";
    $result = mysqli_query($con, $sql);
    
    if($result && mysqli_num_rows($result) > 0){
        return mysqli_fetch_assoc($result);
    }
    return false;
}
?>
