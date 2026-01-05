
<?php
require_once __DIR__ . '/../models/patientModel.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) { deletePatient($id); }
header('Location: admindashboard.php');

