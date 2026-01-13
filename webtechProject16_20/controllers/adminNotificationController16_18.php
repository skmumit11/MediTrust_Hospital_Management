<?php
require_once('../models/notificationModel16_18.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_log'])) {
        notif_clear_sent();
        $msg = "Sent log cleared successfully.";
    } else {
        $recipientUserID = $_POST['recipientUserID'] ?? '';
        $channel = $_POST['channel'] ?? '';

        // New Inputs
        $date = $_POST['date'] ?? '';
        $hour = $_POST['hour'] ?? '';
        $ampm = $_POST['ampm'] ?? '';

        if ($recipientUserID && $channel && $date && $hour && $ampm) {
            // Convert to 24h format
            $h = (int) $hour;
            if ($ampm === 'PM' && $h < 12) {
                $h += 12;
            }
            if ($ampm === 'AM' && $h === 12) {
                $h = 0;
            }

            // Format for DB: YYYY-MM-DD HH:00:00
            $finalSchedule = sprintf("%s %02d:00:00", $date, $h);

            // Validate Doctor ID
            require_once('../models/userModel16_18.php');
            if (doctor_exists($recipientUserID)) {
                $id = notif_create($recipientUserID, $channel, $finalSchedule);
                $msg = "Notification scheduled (ID: $id) for $finalSchedule";
            } else {
                $msg = "Error: Invalid Doctor ID. Please refer to the list.";
            }
        } else {
            $msg = "All fields required (Date, Hour, AM/PM).";
        }
    } // Close the main else block
} // Close the main POST check

$pending = notif_list('pending');
$sent = notif_list('sent');
require_once('../models/userModel16_18.php');
$doctors = doctor_get_all();

include('../views/admin_notification_create16_18.php');
