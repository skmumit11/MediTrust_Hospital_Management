
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once ('../models/patientDashboardModel.php');

/* Must be logged in for dashboard */
if (!isset($_SESSION['UserID']) || (int)$_SESSION['UserID'] <= 0) {
    header("Location: ../views/login.php");
    exit;
}

$userID = (int)$_SESSION['UserID'];

/* PatientID may or may not exist */
$patientID = getPatientIdByUserId($userID);

$message = "";
$messageType = "success";

/* Dropdown allowed values */
$allowedEmergencyTypes = [
    "Road Accident",
    "Breathing Trouble",
    "Heart Attack",
    "Stroke",
    "Pregnancy Emergency",
    "Burn/Fire Injury",
    "Other"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_ambulance'])) {
    $pickup = isset($_POST['pickup_location']) ? trim($_POST['pickup_location']) : '';
    $etype  = isset($_POST['emergency_type']) ? trim($_POST['emergency_type']) : '';
    $phone  = isset($_POST['contact_phone']) ? trim($_POST['contact_phone']) : '';
    $pname  = isset($_POST['patient_name']) ? trim($_POST['patient_name']) : '';

    if ($pickup === '') {
        $message = "Pickup location is required.";
        $messageType = "error";
    } else if ($phone === '') {
        $message = "Contact number is required.";
        $messageType = "error";
    } else if (strlen($phone) < 6) {
        $message = "Contact number seems too short.";
        $messageType = "error";
    } else if ($pname === '') {
        $message = "Patient name is required.";
        $messageType = "error";
    } else {
        $okType = false;
        for ($i = 0; $i < count($allowedEmergencyTypes); $i++) {
            if ($etype === $allowedEmergencyTypes[$i]) {
                $okType = true;
                break;
            }
        }

        if (!$okType) {
            $message = "Please select a valid request type.";
            $messageType = "error";
        } else {
            $ok = createAmbulanceRequestForUser($userID, $patientID, $pname, $phone, $pickup, $etype);

            if ($ok) {
                $message = "Ambulance request submitted successfully.";
                $messageType = "success";
            } else {
                $message = "Failed to submit ambulance request.";
                $messageType = "error";
            }
        }
    }
}

/* Fetch dashboard data */
$upcomingAppointments = ($patientID > 0) ? getUpcomingAppointments($patientID) : [];
$doctorsList = getAllDoctorsList();
$bedStatus = getBedStatus();
$availableBeds = getAvailableBeds();
$medicalHistory = ($patientID > 0) ? getMedicalHistory($patientID) : [];
$ambulanceRequests = getAmbulanceRequestsForUser($userID, $patientID);
?>
