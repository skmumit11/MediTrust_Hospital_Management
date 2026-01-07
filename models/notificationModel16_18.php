<?php
require_once('db16_18.php');

function notif_create($recipientUserID, $channel, $scheduledAt)
{
    $con = getConnection();
    $uid = (int) $recipientUserID;
    $channel = mysqli_real_escape_string($con, $channel);
    $scheduledAt = mysqli_real_escape_string($con, $scheduledAt);

    $sql = "INSERT INTO Notification (RecipientUserID, Channel, ScheduledAt, SentAt)
VALUES ($uid, '$channel', '$scheduledAt', '$scheduledAt')"; // Treated as immediately sent/scheduled
    mysqli_query($con, $sql);
    return mysqli_insert_id($con);
}

function notif_list($status)
{
    $con = getConnection();
    $filter = ($status === 'pending') ? "SentAt IS NULL" : "SentAt IS NOT NULL";
    $sql = "SELECT n.NotificationID, u.Name AS Recipient, n.Channel, n.ScheduledAt, n.SentAt
FROM Notification n
LEFT JOIN User u ON u.UserID = n.RecipientUserID
WHERE $filter ORDER BY n.ScheduledAt ASC";
    return mysqli_query($con, $sql);
}

function notif_mark_sent($notificationID)
{
    $con = getConnection();
    $nid = (int) $notificationID;
    $now = date('Y-m-d H:i:s');
    $sql = "UPDATE Notification SET SentAt='$now' WHERE NotificationID=$nid AND SentAt IS NULL";
    mysqli_query($con, $sql);
}

function notif_get_due_pending()
{
    $con = getConnection();
    // NOW() works, but we rely on DB time.
    $sql = "SELECT * FROM Notification WHERE SentAt IS NULL AND ScheduledAt <= NOW()";
    return mysqli_query($con, $sql);
}
function notif_clear_sent()
{
    $con = getConnection();
    $sql = "DELETE FROM Notification WHERE SentAt IS NOT NULL";
    return mysqli_query($con, $sql);
}