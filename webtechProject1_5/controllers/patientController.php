
<?php
// controllers/patientdashboardController.php
require_once __DIR__ . '/../models/appointmentModel.php';
require_once __DIR__ . '/../models/doctorModel.php';
require_once __DIR__ . '/../models/bedModel.php';
require_once __DIR__ . '/../models/ambulanceModel.php';

// derive a display name in case of guest fallback
$displayName = '';
if (isset($_SESSION['name']) && $_SESSION['name'] !== '') {
    $displayName = $_SESSION['name'];
} elseif (isset($_SESSION['username']) && $_SESSION['username'] !== '') {
    $displayName = $_SESSION['username'];
}

// Upcoming appointments for the user (linked, created, or guest by name)
$upcomingAppointments = getUpcomingAppointmentsForUser($_SESSION['username'] ?? '', $displayName);

// Doctors list (for your cards/table)
$doctorsList = getAllDoctors();

// Bed availability (for cards)
$icuBeds     = countBedsBy("ICU", "Available");
$generalBeds = countBedsBy("General", "Available");
$availableBeds = $icuBeds + $generalBeds;
$bedStatus = ['ICU' => $icuBeds, 'General' => $generalBeds];

// Ambulance list for this user (you already have this on the page)
$ambulanceRequests = am_getMyAmbulanceRequests($_SESSION['username'] ?? '', 50);

// Medical history (if you have a function—example placeholder)
// $medicalHistory = getMedicalHistoryForUser($_SESSION['username'] ?? '', $displayName);

$message = $message ?? "";
$messageType = $messageType ?? "success";
