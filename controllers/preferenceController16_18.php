<?php
require_once('../models/preferenceModel16_18.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = (int) $_POST['userID'];
    $qh = $_POST['quietHours'];
    $lang = $_POST['language'];
    preference_set($userID, $qh, $lang);
    header("Location: ../views/preference_center16_18.php?ok=1");
    exit;
}
