<?php

function getPatientID($conn, $userID) {
    $query = mysqli_query($conn, "SELECT PatientID, Name FROM Patient WHERE UserID = $userID");
    return mysqli_fetch_assoc($query);
}

function getAvailablePlans($conn) {
    return mysqli_query($conn, "SELECT * FROM SubscriptionPlan");
}

function getPaymentMethods($conn, $patientID) {
    return mysqli_query($conn, "SELECT * FROM PaymentMethod WHERE PatientID = $patientID");
}

function getActiveSubscription($conn, $patientID) {
    $query = mysqli_query($conn, "SELECT ps.*, sp.PlanName 
        FROM PatientSubscription ps 
        JOIN SubscriptionPlan sp ON ps.PlanID = sp.PlanID
        WHERE ps.PatientID = $patientID AND ps.Status='Active' 
        ORDER BY ps.SubscriptionID DESC LIMIT 1");
    return mysqli_fetch_assoc($query);
}
?>

<?php
// models/subscriptionModel.php
require_once ('db.php');

/* ---------- Helpers ---------- */

function sub_getUserIdByUsername($username) {
    $conn = getConnection();
    $username = trim($username);
    $sql = "SELECT UserID, Name FROM `User` WHERE Username = '$username'";
    $res = mysqli_query($conn, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    closeConnection($conn);
    return $row ? (int)$row['UserID'] : null;
}

function sub_getPatientIdForUser($userId) {
    $conn = getConnection();
    $userId = (int)$userId;
    $sql = "SELECT PatientID FROM Patient WHERE UserID = $userId";
    $res = mysqli_query($conn, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    closeConnection($conn);
    return $row ? (int)$row['PatientID'] : null;
}

/* ---------- Plans & Active subscription ---------- */

function sub_getSubscriptionPlans() {
    $conn = getConnection();
    $res = $conn->query("SELECT PlanID, PlanName, Price, Duration FROM SubscriptionPlan ORDER BY Price ASC");
    $rows = [];
    if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } $res->free(); }
    closeConnection($conn);
    return $rows;
}

function sub_getActiveSubscription($patientId) {
    $conn = getConnection();
    $patientId = (int)$patientId;
    $sql = "SELECT SubscriptionID, PatientID, PlanID, Status, PaidAt, ExpiryDate
            FROM PatientSubscription
            WHERE PatientID = $patientId AND Status = 'Active'
            ORDER BY SubscriptionID DESC
            LIMIT 1";
    $res = mysqli_query($conn, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    closeConnection($conn);
    return $row;
}

function sub_getPlanById($planId) {
    $conn = getConnection();
    $planId = (int)$planId;
    $sql = "SELECT PlanID, PlanName, Price, Duration FROM SubscriptionPlan WHERE PlanID = $planId";
    $res = mysqli_query($conn, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    closeConnection($conn);
    return $row;
}

/* ---------- Payment Method & Payment ---------- */

function sub_createPaymentMethod($patientId, $holderName, $cardNumber, $expiryDate, $cardType, $isDefault = true) {
    $conn = getConnection();
    
    $patientId = (int)$patientId;
    $holderName = trim($holderName);
    // Mask card number: show only last 4 digits
    $last4 = substr($cardNumber, strlen($cardNumber) - 4);
    $masked = '**** **** **** ' . $last4;
    $masked = trim($masked);
    $expiryDate = trim($expiryDate);
    $cardType = trim($cardType);
    $isDef = $isDefault ? 1 : 0;

    $sql = "INSERT INTO PaymentMethod
        (PatientID, CardHolderName, CardNumberMasked, ExpiryDate, CardType, IsDefault, AddedAt)
        VALUES ($patientId, '$holderName', '$masked', '$expiryDate', '$cardType', $isDef, NOW())";
    
    mysqli_query($conn, $sql);
    $newId = mysqli_insert_id($conn);
    closeConnection($conn);
    return $newId; // PaymentMethodID
}

function sub_createPatientSubscription($patientId, $planId, $paidAt, $expiryDate) {
    $conn = getConnection();
    $patientId = (int)$patientId;
    $planId = (int)$planId;
    $paidAt = trim($paidAt);
    $expiryDate = trim($expiryDate);
    
    $sql = "INSERT INTO PatientSubscription
        (PatientID, PlanID, Status, PaidAt, ExpiryDate)
        VALUES ($patientId, $planId, 'Active', '$paidAt', '$expiryDate')";
    
    mysqli_query($conn, $sql);
    $newId = mysqli_insert_id($conn);
    closeConnection($conn);
    return $newId; // SubscriptionID
}

function sub_addSubscriptionPayment($subscriptionId, $patientId, $amount, $paymentMethod, $paymentStatus, $transactionRef) {
    $conn = getConnection();
    $subscriptionId = (int)$subscriptionId;
    $patientId = (int)$patientId;
    $amount = (float)$amount; // Ensure double/float
    $paymentMethod = trim($paymentMethod);
    $paymentStatus = trim($paymentStatus);
    $transactionRef = trim($transactionRef);

    $sql = "INSERT INTO SubscriptionPayment
        (SubscriptionID, PatientID, Amount, PaymentMethod, PaymentStatus, TransactionRef, PaidAt)
        VALUES ($subscriptionId, $patientId, $amount, '$paymentMethod', '$paymentStatus', '$transactionRef', NOW())";
    
    mysqli_query($conn, $sql);
    $newId = mysqli_insert_id($conn);
    closeConnection($conn);
    return $newId;
}

/* ---------- Cancel ---------- */

function sub_cancelActiveSubscription($subscriptionId) {
    $conn = getConnection();
    $subscriptionId = (int)$subscriptionId;
    $sql = "UPDATE PatientSubscription SET Status='Expired', ExpiryDate=NOW() WHERE SubscriptionID=$subscriptionId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);
    closeConnection($conn);
    return $ok;
}
