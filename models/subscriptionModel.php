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
require_once __DIR__ . '/db.php';

/* ---------- Helpers ---------- */

function sub_getUserIdByUsername($username) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT UserID, Name FROM `User` WHERE Username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    closeConnection($conn);
    return $row ? (int)$row['UserID'] : null;
}

function sub_getPatientIdForUser($userId) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT PatientID FROM Patient WHERE UserID = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
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
    $stmt = $conn->prepare("SELECT SubscriptionID, PatientID, PlanID, Status, PaidAt, ExpiryDate
                            FROM PatientSubscription
                            WHERE PatientID = ? AND Status = 'Active'
                            ORDER BY SubscriptionID DESC
                            LIMIT 1");
    $stmt->bind_param('i', $patientId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    closeConnection($conn);
    return $row;
}

function sub_getPlanById($planId) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT PlanID, PlanName, Price, Duration FROM SubscriptionPlan WHERE PlanID = ?");
    $stmt->bind_param('i', $planId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    closeConnection($conn);
    return $row;
}

/* ---------- Payment Method & Payment ---------- */

function sub_createPaymentMethod($patientId, $holderName, $cardNumber, $expiryDate, $cardType, $isDefault = true) {
    $conn = getConnection();

    // Mask card number: show only last 4 digits (no regex)
    $last4 = substr($cardNumber, strlen($cardNumber) - 4);
    $masked = '**** **** **** ' . $last4;

    $stmt = $conn->prepare("INSERT INTO PaymentMethod
        (PatientID, CardHolderName, CardNumberMasked, ExpiryDate, CardType, IsDefault, AddedAt)
        VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $isDef = $isDefault ? 1 : 0;
    $stmt->bind_param('issssi', $patientId, $holderName, $masked, $expiryDate, $cardType, $isDef);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    return $newId; // PaymentMethodID
}

function sub_createPatientSubscription($patientId, $planId, $paidAt, $expiryDate) {
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO PatientSubscription
        (PatientID, PlanID, Status, PaidAt, ExpiryDate)
        VALUES (?, ?, 'Active', ?, ?)");
    $stmt->bind_param('iiss', $patientId, $planId, $paidAt, $expiryDate);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    return $newId; // SubscriptionID
}

function sub_addSubscriptionPayment($subscriptionId, $patientId, $amount, $paymentMethod, $paymentStatus, $transactionRef) {
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO SubscriptionPayment
        (SubscriptionID, PatientID, Amount, PaymentMethod, PaymentStatus, TransactionRef, PaidAt)
        VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param('iidsss', $subscriptionId, $patientId, $amount, $paymentMethod, $paymentStatus, $transactionRef);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    return $newId;
}

/* ---------- Cancel ---------- */

function sub_cancelActiveSubscription($subscriptionId) {
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE PatientSubscription SET Status='Expired', ExpiryDate=NOW() WHERE SubscriptionID=?");
    $stmt->bind_param('i', $subscriptionId);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}
