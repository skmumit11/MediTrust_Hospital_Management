
<?php
require_once __DIR__ . '/../models/ambulanceModel.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) { updateAmbulanceStatus($id, 'Dispatched'); }
header('Location: admindashboard.php');
