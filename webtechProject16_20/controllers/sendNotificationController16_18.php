<?php
require_once('../models/notificationModel16_18.php');
require_once('../models/preferenceModel16_18.php');
require_once('../models/db16_18.php');

function in_quiet_hours($quietHours, $scheduled)
{
    if (!$quietHours)
        return false;
    $parts = explode('-', $quietHours);
    if (count($parts) !== 2)
        return false;
    $start = strtotime($parts[0]);
    $end = strtotime($parts[1]);
    $t = strtotime(date('H:i', strtotime($scheduled)));
    if ($start <= $end)
        return ($t >= $start && $t < $end);
    return ($t >= $start || $t < $end);
}

$con = getConnection();
// Use model function
$res = notif_get_due_pending();

while ($row = mysqli_fetch_assoc($res)) {
    $pref = preference_get($row['RecipientUserID']);
    if ($pref && in_quiet_hours($pref['QuietHours'], $row['ScheduledAt']))
        continue;

    // Simulated delivery
    notif_mark_sent($row['NotificationID']);
}
echo "Processed pending notifications.";
