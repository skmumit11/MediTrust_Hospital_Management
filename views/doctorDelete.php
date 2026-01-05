
<?php
require_once __DIR__ . '/../models/doctorModel.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) { deleteDoctor($id); }
header('Location: admindashboard.php');

