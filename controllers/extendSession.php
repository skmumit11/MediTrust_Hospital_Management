<?php
// controllers/extendSession.php

session_start();

// Default timeout (minutes)
$defaultTimeout = 10;

// Extend session by resetting timeout
$_SESSION['timeout'] = $defaultTimeout;

// Send JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'newTimeout' => $_SESSION['timeout']
]);
exit;
