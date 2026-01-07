
<?php
// controllers/ambulanceController.php
session_start();
require_once "/../models/ambulanceModel.php";

function cleanText($s) { return trim($s); }

function isValidAmbStatus($s) {
    $allowed = ["Pending","Accepted","Dispatched","Completed"];
    foreach($allowed as $a) {
        if($a === $s) { return true; }
    }
    return false;
}

/* -------------------- Admin actions -------------------- */

function handleAmbulanceStatusUpdate() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $id     = isset($_POST["RequestID"]) ? (int)$_POST["RequestID"] : 0;
    $status = isset($_POST["Status"]) ? cleanText($_POST["Status"]) : "";

    if($id <= 0) { return "Invalid RequestID"; }
    if(!isValidAmbStatus($status)) { return "Invalid Status"; }

    $ok = updateAmbulanceStatus($id, $status);
    if($ok) { header("Location: ../views/admindashboard.php"); exit(); }
    return "Failed to update";
}

function handleAmbulanceDelete() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $id = isset($_POST["RequestID"]) ? (int)$_POST["RequestID"] : 0;
    if($id <= 0) { return "Invalid RequestID"; }

    $ok = deleteAmbulanceRequest($id);
    if($ok) { header("Location: ../views/admindashboard.php"); exit(); }
    return "Failed to delete";
}

/* -------------------- Patient/Guest actions -------------------- */

$message = "";
$messageType = "success";

$prefillName = '';
if (isset($_SESSION['name']) && $_SESSION['name'] !== '') {
    $prefillName = $_SESSION['name'];
} elseif (isset($_SESSION['username']) && $_SESSION['username'] !== '') {
    $prefillName = $_SESSION['username'];
}

function handleAmbulanceCreate() {
    global $message, $messageType, $prefillName;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['request_ambulance'])) {
        return;
    }

    $patient_name    = cleanText($_POST['patient_name'] ?? '');
    $contact_phone   = cleanText($_POST['contact_phone'] ?? '');
    $pickup_location = cleanText($_POST['pickup_location'] ?? '');
    $emergency_type  = cleanText($_POST['emergency_type'] ?? '');

    if ($patient_name === '' || $pickup_location === '' || $emergency_type === '') {
        $message = "Please fill Patient Name, Pickup Location, and Request Type.";
        $messageType = "error";
        return;
    }

    $username = $_SESSION['username'] ?? null;
    $requesterUserId = null;
    $linkedPatientId = null;

    if ($username) {
        $requesterUserId = am_getUserIdByUsername($username);
        if ($requesterUserId !== null) {
            $linkedPatientId = am_getPatientIdForUser($requesterUserId); // may be null
        }
    }

    $requesterGuestId = null;
    if ($linkedPatientId === null) {
        // Anyone (guest) can request: record guest for traceability
        $gcName    = $patient_name;
        $gcPhone   = ($contact_phone === '') ? null : $contact_phone;
        $gcEmail   = null;
        $gcAddress = $pickup_location;
        $requesterGuestId = am_createGuestContact($gcName, $gcPhone, $gcEmail, $gcAddress);
    }

    $newId = am_insertAmbulanceRequest(
        $linkedPatientId,
        $pickup_location,
        $emergency_type,
        'Pending',
        $requesterUserId,
        $requesterGuestId,
        $patient_name,
        $contact_phone
    );

    if ($newId > 0) {
        $message = "Ambulance request submitted successfully (ID: " . $newId . ").";
        $messageType = "success";
    } else {
        $message = "Failed to submit ambulance request.";
        $messageType = "error";
    }
}

function loadMyAmbulanceRequests($limit = 50) {
    $uname = $_SESSION['username'] ?? '';
    return am_getMyAmbulanceRequests($uname, $limit);
}
